<?php

namespace App\Services\Reports;

use App\Models\Campaign;
use App\Models\Metric;
use Illuminate\Support\Collection;

class ReportMetricsService
{
    /**
     * @return array{totals: array<string, float|int>, rows: Collection<int, array<string, mixed>>}
     */
    public function build(array $filters): array
    {
        $campaignQuery = Campaign::query()
            ->withCount('leads')
            ->where('workspace_id', $filters['workspace_id']);

        if (! empty($filters['client_id']) && $filters['client_id'] !== 'all') {
            $campaignQuery->where('client_id', $filters['client_id']);
        }

        if (! empty($filters['platforms'])) {
            $campaignQuery->whereIn('platform', $filters['platforms']);
        }

        if (! empty($filters['campaign_ids'])) {
            $campaignQuery->whereIn('id', $filters['campaign_ids']);
        }

        $campaigns = $campaignQuery->get();
        $campaignIds = $campaigns->pluck('id')->all();

        $metricQuery = Metric::query()
            ->whereIn('campaign_id', $campaignIds)
            ->whereBetween('date', [$filters['start_date'], $filters['end_date']]);

        $rows = $metricQuery->get()->map(function ($metric): array {
            return [
                'campaign_id' => $metric->campaign_id,
                'campaign_name' => $metric->campaign?->name ?? 'Campanha',
                'platform' => $metric->platform,
                'impressions' => (int) $metric->impressions,
                'reach' => (int) $metric->reach,
                'clicks' => (int) $metric->clicks,
                'spend' => (float) $metric->spend,
                'revenue' => (float) $metric->revenue,
                'leads' => (int) $metric->leads,
                'conversions' => (int) $metric->conversions,
                'cpm' => $metric->impressions > 0 ? ($metric->spend / $metric->impressions) * 1000 : 0,
                'ctr' => $metric->impressions > 0 ? ($metric->clicks / $metric->impressions) * 100 : 0,
                'cpc' => $metric->clicks > 0 ? $metric->spend / $metric->clicks : 0,
                'cpl' => $metric->leads > 0 ? $metric->spend / $metric->leads : 0,
                'roas' => $metric->spend > 0 ? $metric->revenue / $metric->spend : 0,
            ];
        });

        $totals = [
            'investment' => (float) $rows->sum('spend'),
            'impressions' => (int) $rows->sum('impressions'),
            'reach' => (int) $rows->sum('reach'),
            'clicks' => (int) $rows->sum('clicks'),
            'ctr' => $rows->sum('impressions') > 0 ? ($rows->sum('clicks') / $rows->sum('impressions')) * 100 : 0,
            'cpc' => $rows->sum('clicks') > 0 ? $rows->sum('spend') / $rows->sum('clicks') : 0,
            'cpm' => $rows->sum('impressions') > 0 ? ($rows->sum('spend') / $rows->sum('impressions')) * 1000 : 0,
            'leads' => (int) $rows->sum('leads'),
            'conversions' => (int) $rows->sum('conversions'),
            'cpl' => $rows->sum('leads') > 0 ? $rows->sum('spend') / $rows->sum('leads') : 0,
            'revenue' => (float) $rows->sum('revenue'),
            'roas' => $rows->sum('spend') > 0 ? $rows->sum('revenue') / $rows->sum('spend') : 0,
        ];

        if ($rows->isEmpty() && $campaigns->isNotEmpty()) {
            $totals = [
                'investment' => (float) $campaigns->sum('total_spend'),
                'impressions' => (int) $campaigns->sum('impressions'),
                'reach' => 0,
                'clicks' => (int) $campaigns->sum('clicks'),
                'ctr' => $campaigns->sum('impressions') > 0 ? ($campaigns->sum('clicks') / $campaigns->sum('impressions')) * 100 : 0,
                'cpc' => $campaigns->sum('clicks') > 0 ? $campaigns->sum('total_spend') / $campaigns->sum('clicks') : 0,
                'cpm' => $campaigns->sum('impressions') > 0 ? ($campaigns->sum('total_spend') / $campaigns->sum('impressions')) * 1000 : 0,
                'leads' => 0,
                'conversions' => (int) $campaigns->sum('conversions'),
                'cpl' => $campaigns->sum('leads_count') > 0 ? $campaigns->sum('total_spend') / $campaigns->sum('leads_count') : 0,
                'revenue' => (float) $campaigns->sum('revenue'),
                'roas' => $campaigns->sum('total_spend') > 0 ? $campaigns->sum('revenue') / $campaigns->sum('total_spend') : 0,
            ];
        }

        return ['totals' => $totals, 'rows' => $rows];
    }
}
