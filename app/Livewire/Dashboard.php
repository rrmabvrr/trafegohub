<?php

namespace App\Livewire;

use App\Jobs\SyncPlatformMetricsJob;
use App\Models\Campaign;
use App\Models\CampaignMetricSnapshot;
use App\Models\Client;
use App\Models\Integration;
use App\Models\Metric;
use App\Models\Workspace;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Livewire\Component;

class Dashboard extends Component
{
    public int $workspaceId = 1;

    public string $dateRange = '30d';

    public string $clientId = 'all';

    public string $platform = 'all';

    public string $integrationId = 'all';

    public string $campaignId = 'all';

    public function mount(): void
    {
        $workspace = Workspace::first();
        if ($workspace) {
            $this->workspaceId = $workspace->id;
        }
    }

    public function triggerSync(): void
    {
        SyncPlatformMetricsJob::dispatch()->onQueue('sync');
        $this->dispatch('notify', ['message' => 'Sincronização de métricas solicitada com sucesso.']);
    }

    public function render()
    {
        $workspace = Workspace::find($this->workspaceId);
        $campaignQuery = Campaign::where('workspace_id', $this->workspaceId);

        if ($this->clientId !== 'all') {
            $campaignQuery->whereHas('workspace', fn ($query) => $query->where('client_id', $this->clientId));
        }

        if ($this->platform !== 'all') {
            $campaignQuery->where('platform', $this->platform);
        }

        if ($this->integrationId !== 'all') {
            $campaignQuery->where('integration_id', $this->integrationId);
        }

        if ($this->campaignId !== 'all') {
            $campaignQuery->whereKey($this->campaignId);
        }

        $campaigns = $campaignQuery->with('integration')->withCount('leads')->get();
        $campaignIds = $campaigns->modelKeys();
        [$periodStart, $periodEnd] = $this->period();
        $campaignRows = $this->campaignTableRows($campaigns, $periodStart, $periodEnd);
        $platformChartData = $this->platformChartData($campaignRows);
        $metricQuery = Metric::whereIn('campaign_id', $campaignIds)
            ->whereBetween('date', [$periodStart, $periodEnd]);
        $snapshots = $metricQuery->orderBy('date')->get();

        if ($snapshots->isEmpty()) {
            $snapshots = CampaignMetricSnapshot::whereIn('campaign_id', $campaignIds)
                ->whereBetween('date', [$periodStart, $periodEnd])
                ->orderBy('date')
                ->get();
        }

        $monthMetricQuery = Metric::whereIn('campaign_id', $campaignIds)
            ->whereBetween('date', [now()->startOfMonth(), now()->endOfDay()]);
        $monthSnapshots = $monthMetricQuery->get();

        if ($monthSnapshots->isEmpty()) {
            $monthSnapshots = CampaignMetricSnapshot::whereIn('campaign_id', $campaignIds)
                ->whereBetween('date', [now()->startOfMonth(), now()->endOfDay()])
                ->get();
        }
        $metrics = $this->metrics($snapshots, $campaigns);
        $todayMetrics = $this->metrics(
            $snapshots->where('date', Carbon::today()),
            collect(),
        );
        $monthMetrics = $this->metrics(
            $monthSnapshots,
            collect(),
        );
        $integrations = Integration::where('workspace_id', $this->workspaceId)->get();
        $clients = Client::where('organization_id', $workspace?->organization_id)->get();
        $campaignOptions = Campaign::where('workspace_id', $this->workspaceId)->orderBy('name')->get(['id', 'name']);

        $topCampaigns = $campaigns->sortByDesc('roas')->take(4);

        $chartData = $this->chartData($snapshots, $metrics);
        $this->dispatch('dashboard-chart-updated', chartData: $chartData);

        return view('livewire.dashboard', [
            'metrics' => $metrics,
            'todayMetrics' => $todayMetrics,
            'monthMetrics' => $monthMetrics,
            'chartData' => $chartData,
            'platformChartData' => $platformChartData,
            'campaignRows' => $campaignRows,
            'topCampaigns' => $topCampaigns,
            'integrations' => $integrations,
            'clients' => $clients,
            'campaignOptions' => $campaignOptions,
            'periodStart' => $periodStart,
            'periodEnd' => $periodEnd,
        ])->layout('layouts.app');
    }

    private function campaignTableRows(Collection $campaigns, Carbon $periodStart, Carbon $periodEnd): Collection
    {
        return $campaigns->map(function (Campaign $campaign) use ($periodStart, $periodEnd): array {
            $metrics = Metric::query()
                ->where('campaign_id', $campaign->id)
                ->whereBetween('date', [$periodStart, $periodEnd])
                ->get();

            $spend = (float) $metrics->sum('spend');
            $revenue = (float) $metrics->sum('revenue');
            $impressions = (int) $metrics->sum('impressions');
            $clicks = (int) $metrics->sum('clicks');
            $leads = (int) $metrics->sum('leads');
            $ctr = $impressions > 0 ? ($clicks / $impressions) * 100 : 0;
            $cpl = $leads > 0 ? $spend / $leads : 0;
            $roas = $spend > 0 ? $revenue / $spend : 0;
            $status = $roas >= 2 ? 'Bom' : ($roas >= 1 ? 'Ativo' : 'Atenção');

            return [
                'campaign_name' => $campaign->name,
                'platform' => strtoupper((string) ($campaign->platform ?? 'N/D')),
                'spend' => $spend,
                'leads' => $leads,
                'cpl' => $cpl,
                'ctr' => $ctr,
                'roas' => $roas,
                'status' => $status,
            ];
        });
    }

    private function platformChartData(Collection $campaignRows): array
    {
        $grouped = $campaignRows->groupBy('platform');

        return [
            'labels' => $grouped->keys()->values()->all(),
            'spend' => $grouped->map(fn (Collection $items) => round((float) $items->sum('spend'), 2))->values()->all(),
            'leads' => $grouped->map(fn (Collection $items) => (int) $items->sum('leads'))->values()->all(),
        ];
    }

    /** @return array{0: Carbon, 1: Carbon} */
    private function period(): array
    {
        $end = now()->endOfDay();

        return match ($this->dateRange) {
            'today' => [now()->startOfDay(), $end],
            '7d' => [now()->subDays(6)->startOfDay(), $end],
            'month' => [now()->startOfMonth(), $end],
            '90d' => [now()->subDays(89)->startOfDay(), $end],
            default => [now()->subDays(29)->startOfDay(), $end],
        };
    }

    /** @param Collection<int, CampaignMetricSnapshot> $snapshots */
    /** @param Collection<int, Campaign> $campaigns */
    private function metrics(Collection $snapshots, Collection $campaigns): array
    {
        if ($snapshots->isEmpty() && $campaigns->isNotEmpty()) {
            return [
                'spend' => (float) $campaigns->sum('total_spend'),
                'revenue' => (float) $campaigns->sum('revenue'),
                'impressions' => (int) $campaigns->sum('impressions'),
                'reach' => 0,
                'clicks' => (int) $campaigns->sum('clicks'),
                'leads' => (int) $campaigns->sum('leads_count'),
                'conversions' => (int) $campaigns->sum('conversions'),
            ] + $this->derivedMetrics(
                (float) $campaigns->sum('total_spend'),
                (float) $campaigns->sum('revenue'),
                (int) $campaigns->sum('impressions'),
                (int) $campaigns->sum('clicks'),
                (int) $campaigns->sum('conversions'),
                (int) $campaigns->sum('leads_count'),
            );
        }

        $spend = (float) $snapshots->sum('spend');
        $revenue = (float) $snapshots->sum('revenue');
        $impressions = (int) $snapshots->sum('impressions');
        $clicks = (int) $snapshots->sum('clicks');
        $conversions = (int) $snapshots->sum('conversions');

        return [
            'spend' => $spend,
            'revenue' => $revenue,
            'impressions' => $impressions,
            'reach' => (int) $snapshots->sum('reach'),
            'clicks' => $clicks,
            'leads' => (int) $snapshots->sum('leads'),
            'conversions' => $conversions,
        ] + $this->derivedMetrics($spend, $revenue, $impressions, $clicks, $conversions, (int) $snapshots->sum('leads'));
    }

    private function derivedMetrics(float $spend, float $revenue, int $impressions, int $clicks, int $conversions, int $leads = 0): array
    {
        return [
            'cpl' => $leads > 0 ? $spend / $leads : 0,
            'cpc' => $clicks > 0 ? $spend / $clicks : 0,
            'cpm' => $impressions > 0 ? ($spend / $impressions) * 1000 : 0,
            'ctr' => $impressions > 0 ? ($clicks / $impressions) * 100 : 0,
            'roas' => $spend > 0 ? $revenue / $spend : 0,
        ];
    }

    private function chartData(Collection $snapshots, array $metrics): array
    {
        if ($snapshots->isEmpty()) {
            return [
                'labels' => ['Período'],
                'spend' => [$metrics['spend']],
                'revenue' => [$metrics['revenue']],
                'leads' => [$metrics['leads']],
            ];
        }

        $grouped = $snapshots->groupBy(fn (CampaignMetricSnapshot $snapshot) => $snapshot->date->toDateString());

        return [
            'labels' => $grouped->keys()->values()->all(),
            'spend' => $grouped->map(fn (Collection $items) => round($items->sum('spend'), 2))->values()->all(),
            'revenue' => $grouped->map(fn (Collection $items) => round($items->sum('revenue'), 2))->values()->all(),
            'leads' => $grouped->map(fn (Collection $items) => $items->sum('leads'))->values()->all(),
        ];
    }
}
