<?php

namespace App\Repositories\Eloquent;

use App\Models\Campaign;
use App\Repositories\Contracts\CampaignRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CampaignRepository implements CampaignRepositoryInterface
{
    public function allForWorkspace(int $workspaceId, ?string $platform = null): Collection
    {
        $query = Campaign::where('workspace_id', $workspaceId);

        if ($platform && $platform !== 'all') {
            $query->where('platform', $platform);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function findById(int $id): ?Campaign
    {
        return Campaign::with(['client', 'integration', 'adSets.ads', 'metricSnapshots', 'creatives'])->find($id);
    }

    public function create(array $data): Campaign
    {
        return Campaign::create($data);
    }

    public function updateStatus(int $id, string $status): bool
    {
        $campaign = Campaign::find($id);
        return $campaign ? $campaign->update(['status' => $status]) : false;
    }

    public function updateDailyBudget(int $id, float $budget): bool
    {
        $campaign = Campaign::find($id);
        return $campaign ? $campaign->update(['daily_budget' => $budget]) : false;
    }

    public function getTopPerforming(int $workspaceId, int $limit = 5): Collection
    {
        return Campaign::where('workspace_id', $workspaceId)
            ->orderBy('roas', 'desc')
            ->take($limit)
            ->get();
    }
}
