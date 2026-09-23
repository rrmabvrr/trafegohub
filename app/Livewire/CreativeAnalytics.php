<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Creative;
use App\Models\Workspace;

class CreativeAnalytics extends Component
{
    public int $workspaceId = 1;
    public string $fatigueFilter = 'ALL';

    public function mount(): void
    {
        $ws = Workspace::first();
        if ($ws) {
            $this->workspaceId = $ws->id;
        }
    }

    public function render()
    {
        $query = Creative::with('campaign');

        if ($this->fatigueFilter !== 'ALL') {
            $query->where('fatigue_level', $this->fatigueFilter);
        }

        return view('livewire.creative-analytics', [
            'creatives' => $query->get(),
            'criticalCount' => Creative::where('fatigue_level', 'CRITICAL')->count(),
        ])->layout('layouts.app');
    }
}
