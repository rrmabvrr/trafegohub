<?php

namespace App\Repositories\Eloquent;

use App\Models\Lead;
use App\Repositories\Contracts\LeadRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class LeadRepository implements LeadRepositoryInterface
{
    public function allForWorkspace(int $workspaceId): Collection
    {
        return Lead::where('workspace_id', $workspaceId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function create(array $data): Lead
    {
        return Lead::create($data);
    }

    public function updateStatus(int $id, string $status): bool
    {
        $lead = Lead::find($id);
        return $lead ? $lead->update(['status' => $status]) : false;
    }
}
