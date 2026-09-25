<?php

namespace App\Jobs;

use App\Models\Ad;
use App\Models\AdSet;
use App\Models\Campaign;
use App\Models\Integration;
use App\Models\Metric;
use App\Models\SyncLog;
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
            $syncLog = SyncLog::create([
                'integration_id' => $integration->id,
                'organization_id' => $integration->organization_id,
                'workspace_id' => $integration->workspace_id,
                'platform' => $integration->platform,
                'account_name' => $integration->ad_account_name ?: $integration->name,
                'date' => now()->toDateString(),
                'status' => 'started',
                'started_at' => now(),
                'records_processed' => 0,
            ]);

            if (blank($integration->access_token)) {
                $integration->update([
                    'status' => 'ERROR',
                    'last_error' => 'Token de acesso não configurado.',
                ]);

                $syncLog->update([
                    'status' => 'failed',
                    'finished_at' => now(),
                    'error_message' => 'Token de acesso não configurado.',
                ]);

                Log::warning('Integration skipped because no official adapter or access token is configured.', [
                    'integration_id' => $integration->id,
                    'platform' => $integration->platform,
                ]);

                continue;
            }

            $integration->update(['status' => 'SYNCING']);

            try {
                $platform = $platformManager->for($integration);
                $recordsProcessed = 0;

                DB::transaction(function () use ($integration, $platform, &$recordsProcessed): void {
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

                        $this->persistNormalizedMetric(
                            $integration,
                            campaignId: $campaign->id,
                            source: $campaignData,
                        );

                        $recordsProcessed++;

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

                            $this->persistNormalizedMetric(
                                $integration,
                                campaignId: $campaign->id,
                                adSetId: $adSet->id,
                                source: $adSetData,
                            );

                            $recordsProcessed++;

                            foreach ($adSetData['ads']['data'] ?? [] as $adData) {
                                $ad = Ad::updateOrCreate(
                                    [
                                        'ad_set_id' => $adSet->id,
                                        'external_id' => (string) $adData['id'],
                                    ],
                                    array_merge([
                                        'name' => $adData['name'] ?? 'Unnamed ad',
                                        'status' => $this->normalizeStatus($adData['status'] ?? null),
                                    ], $this->metrics($adData)),
                                );

                                $this->persistNormalizedMetric(
                                    $integration,
                                    campaignId: $campaign->id,
                                    adSetId: $adSet->id,
                                    adId: $ad->id,
                                    source: $adData,
                                );

                                $recordsProcessed++;
                            }
                        }
                    }
                });

                $syncLog->update([
                    'status' => 'success',
                    'finished_at' => now(),
                    'records_processed' => $recordsProcessed,
                ]);

                $integration->update([
                    'status' => 'CONNECTED',
                    'last_synced_at' => now(),
                    'last_error' => null,
                ]);
            } catch (\Throwable $exception) {
                $integration->update([
                    'status' => 'ERROR',
                    'last_error' => $exception->getMessage(),
                ]);

                $syncLog->update([
                    'status' => 'failed',
                    'finished_at' => now(),
                    'records_processed' => $syncLog->records_processed,
                    'error_message' => $exception->getMessage(),
                ]);

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

    private function persistNormalizedMetric(Integration $integration, ?int $campaignId = null, ?int $adSetId = null, ?int $adId = null, array $source = []): void
    {
        $metrics = $this->metrics($source);
        $insight = $source['insights']['data'][0] ?? [];
        $date = $this->date($source['date'] ?? $source['start_time'] ?? $source['created_time'] ?? null) ?? now()->toDateString();

        Metric::updateOrCreate(
            [
                'organization_id' => $integration->organization_id,
                'workspace_id' => $integration->workspace_id,
                'integration_id' => $integration->id,
                'campaign_id' => $campaignId,
                'ad_set_id' => $adSetId,
                'ad_id' => $adId,
                'platform' => $integration->platform,
                'account_name' => $integration->ad_account_name ?: $integration->name,
                'date' => $date,
                'period' => 'day',
            ],
            [
                'organization_id' => $integration->organization_id,
                'workspace_id' => $integration->workspace_id,
                'client_id' => $integration->client_id,
                'integration_id' => $integration->id,
                'campaign_id' => $campaignId,
                'ad_set_id' => $adSetId,
                'ad_id' => $adId,
                'platform' => $integration->platform,
                'account_name' => $integration->ad_account_name ?: $integration->name,
                'date' => $date,
                'period' => 'day',
                'impressions' => (int) ($insight['impressions'] ?? $metrics['impressions'] ?? 0),
                'reach' => (int) ($insight['reach'] ?? 0),
                'frequency' => (float) ($insight['frequency'] ?? 0),
                'clicks' => (int) ($insight['clicks'] ?? $metrics['clicks'] ?? 0),
                'link_clicks' => (int) ($insight['inline_link_clicks'] ?? $insight['clicks'] ?? $metrics['clicks'] ?? 0),
                'spend' => (float) ($insight['spend'] ?? $metrics['total_spend'] ?? 0),
                'cpm' => (float) ($insight['cpm'] ?? 0),
                'cpc' => (float) ($insight['cpc'] ?? $metrics['cpc'] ?? 0),
                'ctr' => (float) ($insight['ctr'] ?? $metrics['ctr'] ?? 0),
                'leads' => (int) ($insight['leads'] ?? 0),
                'conversions' => (int) ($insight['conversions'] ?? $metrics['conversions'] ?? 0),
                'revenue' => (float) ($insight['revenue'] ?? $metrics['revenue'] ?? 0),
                'roas' => (float) ($insight['roas'] ?? $metrics['roas'] ?? 0),
            ],
        );
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
