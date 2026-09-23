<?php

namespace App\Jobs;

use App\Models\Ad;
use App\Models\AdSet;
use App\Models\Campaign;
use App\Models\Integration;
use App\Services\AdvertisingPlatformManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncPlatformMetricsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public ?int $integrationId = null) {}

    public function handle(AdvertisingPlatformManager $platformManager): void
    {
        Log::info('Starting official advertising platform synchronization.');

        $integrations = Integration::where('status', 'CONNECTED');

        if ($this->integrationId) {
            $integrations->where('id', $this->integrationId);
        }

        foreach ($integrations->get() as $integration) {
            if (blank($integration->access_token)) {
                $integration->update(['status' => 'ERROR']);
                Log::warning('Integration skipped because no official adapter or access token is configured.', [
                    'integration_id' => $integration->id,
                    'platform' => $integration->platform,
                ]);

                continue;
            }

            $integration->update(['status' => 'SYNCING']);

            try {
                $platform = $platformManager->for($integration);

                DB::transaction(function () use ($integration, $platform): void {
                    foreach ($platform->getCampaigns($integration) as $campaignData) {
                        $campaign = Campaign::updateOrCreate(
                            [
                                'integration_id' => $integration->id,
                                'external_id' => (string) $campaignData['id'],
                            ],
                            array_merge([
                                'workspace_id' => $integration->workspace_id,
                                'platform' => $integration->platform,
                                'name' => $campaignData['name'] ?? 'Unnamed campaign',
                                'status' => $this->normalizeStatus($campaignData['status'] ?? null),
                                'objective' => $this->normalizeObjective($campaignData['objective'] ?? null),
                                'daily_budget' => $this->budget($campaignData['daily_budget'] ?? 0),
                                'start_date' => $this->date($campaignData['start_time'] ?? null) ?? now()->toDateString(),
                            ], $this->metrics($campaignData)),
                        );

                        foreach ($campaignData['adsets']['data'] ?? [] as $adSetData) {
                            $adSet = AdSet::updateOrCreate(
                                [
                                    'campaign_id' => $campaign->id,
                                    'external_id' => (string) $adSetData['id'],
                                ],
                                array_merge([
                                    'name' => $adSetData['name'] ?? 'Unnamed ad set',
                                    'status' => $this->normalizeStatus($adSetData['status'] ?? null),
                                    'daily_budget' => $this->budget($adSetData['daily_budget'] ?? 0),
                                    'start_date' => $this->date($adSetData['start_time'] ?? null),
                                    'end_date' => $this->date($adSetData['end_time'] ?? null),
                                ], $this->metrics($adSetData)),
                            );

                            foreach ($adSetData['ads']['data'] ?? [] as $adData) {
                                Ad::updateOrCreate(
                                    [
                                        'ad_set_id' => $adSet->id,
                                        'external_id' => (string) $adData['id'],
                                    ],
                                    array_merge([
                                        'name' => $adData['name'] ?? 'Unnamed ad',
                                        'status' => $this->normalizeStatus($adData['status'] ?? null),
                                    ], $this->metrics($adData)),
                                );
                            }
                        }
                    }
                });

                $integration->update([
                    'status' => 'CONNECTED',
                    'last_synced_at' => now(),
                ]);
            } catch (\Throwable $exception) {
                $integration->update(['status' => 'ERROR']);
                Log::error('Advertising platform synchronization failed.', [
                    'integration_id' => $integration->id,
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        Log::info('Official advertising platform synchronization completed.');
    }

    /**
     * @param  array<string, mixed>  $source
     * @return array<string, float|int>
     */
    private function metrics(array $source): array
    {
        $insight = $source['insights']['data'][0] ?? [];
        $spend = (float) ($insight['spend'] ?? 0);
        $impressions = (int) ($insight['impressions'] ?? 0);
        $clicks = (int) ($insight['clicks'] ?? 0);
        $conversions = (int) ($insight['conversions'] ?? 0);
        $revenue = $this->purchaseRoasRevenue($insight['purchase_roas'] ?? null);

        return [
            'total_spend' => $spend,
            'impressions' => $impressions,
            'clicks' => $clicks,
            'ctr' => (float) ($insight['ctr'] ?? 0),
            'cpc' => (float) ($insight['cpc'] ?? 0),
            'cpl' => $conversions > 0 ? $spend / $conversions : 0,
            'conversions' => $conversions,
            'roas' => $spend > 0 ? $revenue / $spend : 0,
            'revenue' => $revenue,
        ];
    }

    private function purchaseRoasRevenue(mixed $purchaseRoas): float
    {
        if (is_array($purchaseRoas) && isset($purchaseRoas[0]['value'])) {
            return (float) $purchaseRoas[0]['value'];
        }

        return 0;
    }

    private function budget(mixed $budget): float
    {
        return (float) $budget / 100;
    }

    private function date(mixed $date): ?string
    {
        return filled($date) ? Carbon::parse($date)->toDateString() : null;
    }

    private function normalizeStatus(?string $status): string
    {
        return match ($status) {
            'PAUSED' => 'PAUSED',
            'DELETED', 'ARCHIVED' => 'ARCHIVED',
            default => 'ACTIVE',
        };
    }

    private function normalizeObjective(?string $objective): string
    {
        return match ($objective) {
            'LEADS', 'OUTCOME_LEADS' => 'LEADS',
            'TRAFFIC', 'OUTCOME_TRAFFIC' => 'TRAFFIC',
            'ENGAGEMENT', 'OUTCOME_ENGAGEMENT' => 'ENGAGEMENT',
            default => 'SALES',
        };
    }
}
