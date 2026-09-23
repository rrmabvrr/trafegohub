<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Lead;
use App\Models\Workspace;

class LeadsCrm extends Component
{
    public int $workspaceId = 1;
    public string $viewMode = 'kanban';
    public string $search = '';

    public bool $showAddModal = false;
    public string $newName = '';
    public string $newEmail = '';
    public string $newPhone = '';
    public float $newValue = 1497;

    public function mount(): void
    {
        $ws = Workspace::first();
        if ($ws) {
            $this->workspaceId = $ws->id;
        }
    }

    public function updateStatus(int $id, string $newStatus): void
    {
        Lead::where('id', $id)->update(['status' => $newStatus]);
    }

    public function createLead(): void
    {
        $this->validate([
            'newName' => 'required|string',
            'newEmail' => 'required|email',
        ]);

        Lead::create([
            'workspace_id' => $this->workspaceId,
            'name' => $this->newName,
            'email' => $this->newEmail,
            'phone' => $this->newPhone ?: '(11) 98888-7777',
            'platform' => 'meta',
            'utm_source' => 'facebook',
            'utm_medium' => 'cpc',
            'utm_campaign' => 'lead_form_direct',
            'cpl' => 15.00,
            'deal_value' => $this->newValue,
            'status' => 'NEW',
            'city' => 'São Paulo - SP',
        ]);

        $this->reset(['newName', 'newEmail', 'newPhone', 'showAddModal']);
    }

    public function render()
    {
        $leads = Lead::where('workspace_id', $this->workspaceId)
            ->when($this->search, fn($q) => $q->where('name', 'like', '%'.$this->search.'%')->orWhere('email', 'like', '%'.$this->search.'%'))
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.leads-crm', [
            'leads' => $leads
        ])->layout('layouts.app');
    }
}
