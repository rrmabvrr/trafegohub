<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\AutomationRule;
use App\Models\AutomationLog;
use App\Models\Workspace;

class AutomationRules extends Component
{
    public int $workspaceId = 1;
    public bool $showModal = false;

    public string $name = '';
    public string $metric = 'CPA';
    public string $condition = 'GREATER';
    public float $threshold = 40;
    public string $action = 'PAUSE_CAMPAIGN';

    public function mount(): void
    {
        $ws = Workspace::first();
        if ($ws) {
            $this->workspaceId = $ws->id;
        }
    }

    public function toggleRule(int $id): void
    {
        $rule = AutomationRule::find($id);
        if ($rule) {
            $rule->update(['is_enabled' => !$rule->is_enabled]);
        }
    }

    public function createRule(): void
    {
        $this->validate([
            'name' => 'required|string',
            'threshold' => 'required|numeric',
        ]);

        AutomationRule::create([
            'workspace_id' => $this->workspaceId,
            'name' => $this->name,
            'platform_filter' => 'ALL',
            'metric' => $this->metric,
            'condition' => $this->condition,
            'threshold' => $this->threshold,
            'time_frame' => 'TODAY',
            'action' => $this->action,
            'is_enabled' => true,
        ]);

        $this->reset(['name', 'showModal']);
    }

    public function render()
    {
        return view('livewire.automation-rules', [
            'rules' => AutomationRule::where('workspace_id', $this->workspaceId)->get(),
            'logs' => AutomationLog::orderBy('created_at', 'desc')->take(5)->get(),
        ])->layout('layouts.app');
    }
}
