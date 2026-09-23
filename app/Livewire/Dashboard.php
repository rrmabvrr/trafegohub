<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Workspace;
use App\Models\Campaign;
use App\Models\Integration;
use App\Models\Lead;
use App\Jobs\SyncPlatformMetricsJob;

class Dashboard extends Component
{
    public int $workspaceId = 1;
    public string $dateRange = '30d';

    public function mount(): void
    {
        $workspace = Workspace::first();
        if ($workspace) {
            $this->workspaceId = $workspace->id;
        }
    }

    public function triggerSync(): void
    {
        SyncPlatformMetricsJob::dispatch();
        $this->dispatch('notify', ['message' => 'Sincronização de métricas solicitada com sucesso.']);
    }

    public function render()
    {
        $campaigns = Campaign::where('workspace_id', $this->workspaceId)->get();
        $integrations = Integration::where('workspace_id', $this->workspaceId)->get();

        $totalSpend = $campaigns->sum('total_spend');
        $totalRevenue = $campaigns->sum('revenue');
        $globalRoas = $totalSpend > 0 ? number_format($totalRevenue / $totalSpend, 2) : '0';
        $totalConversions = $campaigns->sum('conversions');
        $avgCpa = $totalConversions > 0 ? number_format($totalSpend / $totalConversions, 2) : '0';

        $topCampaigns = $campaigns->sortByDesc('roas')->take(4);

        return view('livewire.dashboard', [
            'totalSpend' => $totalSpend,
            'totalRevenue' => $totalRevenue,
            'globalRoas' => $globalRoas,
            'totalConversions' => $totalConversions,
            'avgCpa' => $avgCpa,
            'topCampaigns' => $topCampaigns,
            'integrations' => $integrations,
        ])->layout('layouts.app');
    }
}
