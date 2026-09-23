<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Integration;
use App\Models\Workspace;

class IntegrationsHub extends Component
{
    public int $workspaceId = 1;

    public function mount(): void
    {
        $ws = Workspace::first();
        if ($ws) {
            $this->workspaceId = $ws->id;
        }
    }

    public function toggleConnection(int $id): void
    {
        $integration = Integration::find($id);
        if ($integration) {
            $nextStatus = $integration->status === 'CONNECTED' ? 'DISCONNECTED' : 'CONNECTED';
            $integration->update([
                'status' => $nextStatus,
                'last_synced_at' => $nextStatus === 'CONNECTED' ? now() : $integration->last_synced_at,
            ]);
        }
    }

    public function render()
    {
        return view('livewire.integrations-hub', [
            'integrations' => Integration::where('workspace_id', $this->workspaceId)->get()
        ])->layout('layouts.app');
    }
}
