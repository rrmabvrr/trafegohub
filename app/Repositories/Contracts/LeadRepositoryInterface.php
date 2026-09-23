<?php

namespace App\Repositories\Contracts;

use App\Models\Lead;
use Illuminate\Database\Eloquent\Collection;

interface LeadRepositoryInterface
{
    public function allForWorkspace(int $workspaceId): Collection;
    public function create(array $data): Lead;
    public function updateStatus(int $id, string $status): bool;
}
