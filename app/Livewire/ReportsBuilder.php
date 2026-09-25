<?php

namespace App\Livewire;

use App\Models\Campaign;
use App\Models\Client;
use App\Models\Metric;
use App\Models\Workspace;
use App\Services\Reports\ReportMetricsService;
use Livewire\Component;

class ReportsBuilder extends Component
{
    public int $workspaceId = 1;

    public string $clientId = 'all';

    public string $period = '30';

    public array $platforms = [];

    public array $campaignIds = [];

    public bool $includeKPIs = true;

    public bool $includeCampaignsTable = true;

    public string $clientNotes = 'Excelente desempenho no período com alta eficiência nas campanhas de Search e CBO Meta.';

    public function mount(): void
    {
        $workspace = Workspace::first();

        if ($workspace) {
            $this->workspaceId = $workspace->id;
        }
    }

    public function updatedPeriod(): void
    {
        $this->resetPage();
    }

    public function render(ReportMetricsService $reportMetricsService)
    {
        $workspace = Workspace::find($this->workspaceId);
        $clients = Client::where('organization_id', $workspace?->organization_id)->get();
        $campaigns = Campaign::where('workspace_id', $this->workspaceId)
            ->when($this->clientId !== 'all', fn ($query) => $query->where('client_id', $this->clientId))
            ->when(! empty($this->platforms), fn ($query) => $query->whereIn('platform', $this->platforms))
            ->orderBy('name')
            ->get();

        [$startDate, $endDate] = $this->dateRange();
        $filters = [
            'workspace_id' => $this->workspaceId,
            'client_id' => $this->clientId,
            'platforms' => $this->platforms,
            'campaign_ids' => $this->campaignIds,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];

        ['totals' => $totals, 'rows' => $rows] = $reportMetricsService->build($filters);

        return view('livewire.reports-builder', [
            'workspace' => $workspace,
            'clients' => $clients,
            'campaigns' => $campaigns,
            'totals' => $totals,
            'rows' => $rows,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ])->layout('layouts.app');
    }

    public function export(string $format): void
    {
        $this->dispatch('report-export', format: $format, filters: [
            'workspace_id' => $this->workspaceId,
            'client_id' => $this->clientId,
            'platforms' => $this->platforms,
            'campaign_ids' => $this->campaignIds,
            'start_date' => $this->dateRange()[0],
            'end_date' => $this->dateRange()[1],
        ]);
    }

    /** @return array{0: string, 1: string} */
    private function dateRange(): array
    {
        $endDate = now()->toDateString();

        return match ($this->period) {
            '7' => [now()->subDays(6)->toDateString(), $endDate],
            '30' => [now()->subDays(29)->toDateString(), $endDate],
            '90' => [now()->subDays(89)->toDateString(), $endDate],
            'month' => [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()],
            default => [now()->subDays(29)->toDateString(), $endDate],
        };
    }
}
