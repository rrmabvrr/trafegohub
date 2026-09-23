<?php

namespace App\Repositories\Contracts;

use App\Models\Campaign;
use Illuminate\Database\Eloquent\Collection;

interface CampaignRepositoryInterface
{
    public function allForWorkspace(int $workspaceId, ?string $platform = null): Collection;
    public function findById(int $id): ?Campaign;
    public function create(array $data): Campaign;
    public function updateStatus(int $id, string $status): bool;
    public function updateDailyBudget(int $id, float $budget): bool;
    public function getTopPerforming(int $workspaceId, int $limit = 5): Collection;
}
