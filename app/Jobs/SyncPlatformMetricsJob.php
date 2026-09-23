<?php

namespace App\Jobs;

use App\Models\Integration;
use App\Services\MetaAdsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncPlatformMetricsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public ?int $integrationId = null) {}

    public function handle(MetaAdsService $metaAdsService): void
    {
        Log::info("Iniciando Job de Sincronização de Métricas de Mídia Paga...");

        $integrations = Integration::where('status', 'CONNECTED');

        if ($this->integrationId) {
            $integrations->where('id', $this->integrationId);
        }

        foreach ($integrations->get() as $integration) {
            $integration->update(['status' => 'SYNCING']);

            // Simulate/Fetch API metrics updates
            foreach ($integration->campaigns as $campaign) {
                // Randomize slight live performance fluctuations
                $newClicks = $campaign->clicks + rand(5, 25);
                $newImpressions = $campaign->impressions + rand(100, 500);
                $newSpend = $campaign->total_spend + (rand(10, 50) * 0.5);
                $newConversions = $campaign->conversions + rand(0, 2);
                $newRevenue = $campaign->revenue + ($newConversions * 150);

                $ctr = $newImpressions > 0 ? ($newClicks / $newImpressions) * 100 : 0;
                $roas = $newSpend > 0 ? $newRevenue / $newSpend : 0;

                $campaign->update([
                    'clicks' => $newClicks,
                    'impressions' => $newImpressions,
                    'total_spend' => $newSpend,
                    'conversions' => $newConversions,
                    'revenue' => $newRevenue,
                    'ctr' => round($ctr, 2),
                    'roas' => round($roas, 2),
                ]);
            }

            $integration->update([
                'status' => 'CONNECTED',
                'last_synced_at' => now(),
            ]);
        }

        Log::info("Job de Sincronização Concluído com Sucesso.");
    }
}
