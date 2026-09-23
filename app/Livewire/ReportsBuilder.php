<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Workspace;
use App\Models\Campaign;

class ReportsBuilder extends Component
{
    public int $workspaceId = 1;
    public bool $includeKPIs = true;
    public bool $includeCampaignsTable = true;
    public string $clientNotes = 'Excelente desempenho no período com alta eficiência nas campanhas de Search e CBO Meta.';

    public function mount(): void
    {
        $ws = Workspace::first();
        if ($ws) {
            $this->workspaceId = $ws->id;
        }
    }

    public function render()
    {
        $workspace = Workspace::find($this->workspaceId);
        $campaigns = Campaign::where('workspace_id', $this->workspaceId)->get();

        $totalSpend = $campaigns->sum('total_spend');
        $totalRevenue = $campaigns->sum('revenue');
        $roas = $totalSpend > 0 ? number_format($totalRevenue / $totalSpend, 2) : '0';

        return view('livewire.reports-builder', [
            'workspace' => $workspace,
            'campaigns' => $campaigns,
            'totalSpend' => $totalSpend,
            'totalRevenue' => $totalRevenue,
            'roas' => $roas,
        ])->layout('layouts.app');
    }
}
