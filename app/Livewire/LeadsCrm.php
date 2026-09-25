<?php

namespace App\Livewire;

use App\Enums\LeadStatus;
use App\Models\Lead;
use App\Models\Workspace;
use Livewire\Component;

class LeadsCrm extends Component
{
    public int $workspaceId = 1;

    public string $viewMode = 'kanban';

    public string $search = '';

    public bool $showAddModal = false;

    public string $newClient = '';

    public string $newName = '';

    public string $newPhone = '';

    public string $newEmail = '';

    public string $newSource = 'formulario';

    public string $newOrigin = 'orgânico';

    public string $newPlatform = 'meta';

    public string $newCampaign = '';

    public string $newAd = '';

    public string $newStatus = 'novo';

    public string $newObservations = '';

    public function mount(): void
    {
        $workspace = Workspace::first();

        if ($workspace) {
            $this->workspaceId = $workspace->id;
        }
    }

    public function updateStatus(int $id, string $newStatus): void
    {
        Lead::whereKey($id)->update(['status' => LeadStatus::normalize($newStatus)->value]);
    }

    public function createLead(): void
    {
        $this->validate([
            'newName' => ['required', 'string', 'max:255'],
            'newEmail' => ['nullable', 'email'],
            'newPhone' => ['nullable', 'string', 'max:30'],
            'newClient' => ['nullable', 'string', 'max:255'],
        ]);

        Lead::create([
            'workspace_id' => $this->workspaceId,
            'client_id' => null,
            'organization_id' => Workspace::find($this->workspaceId)?->organization_id,
            'source' => $this->newSource,
            'origin' => $this->newOrigin,
            'platform' => $this->newPlatform,
            'campaign_name' => $this->newCampaign,
            'ad_name' => $this->newAd,
            'name' => $this->newName,
            'phone' => $this->newPhone,
            'email' => $this->newEmail,
            'status' => LeadStatus::normalize($this->newStatus)->value,
            'lead_date' => now(),
            'observations' => $this->newObservations,
            'metadata' => [
                'integracao' => 'manual',
                'canal' => $this->newSource,
                'origem' => $this->newOrigin,
            ],
        ]);

        $this->reset([
            'newClient',
            'newName',
            'newPhone',
            'newEmail',
            'newSource',
            'newOrigin',
            'newPlatform',
            'newCampaign',
            'newAd',
            'newStatus',
            'newObservations',
            'showAddModal',
        ]);
    }

    public function render()
    {
        $leads = Lead::where('workspace_id', $this->workspaceId)
            ->when($this->search, function ($query): void {
                $query->where(function ($inner): void {
                    $inner->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%')
                        ->orWhere('phone', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy('lead_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.leads-crm', [
            'leads' => $leads,
            'statuses' => LeadStatus::cases(),
        ])->layout('layouts.app');
    }
}
