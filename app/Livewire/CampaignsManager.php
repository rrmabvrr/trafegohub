<?php

namespace App\Livewire;

use App\Models\Campaign;
use App\Models\Workspace;
use App\Services\MetaAdsService;
use Livewire\Component;

class CampaignsManager extends Component
{
    public int $workspaceId = 1;

    public string $selectedPlatform = 'all';

    public string $statusFilter = 'ALL';

    public string $search = '';

    public ?int $editingCampaignId = null;

    public float $editingBudget = 0;

    public bool $showCreateModal = false;

    public string $newCampaignName = '';

    public string $newPlatform = 'meta';

    public string $newObjective = 'SALES';

    public float $newDailyBudget = 300;

    public string $newAudience = '';

    public function mount(): void
    {
        $ws = Workspace::first();
        if ($ws) {
            $this->workspaceId = $ws->id;
        }
    }

    public function toggleStatus(int $campaignId, MetaAdsService $metaAdsService): void
    {
        $campaign = Campaign::with('integration')->find($campaignId);

        if ($campaign) {
            $nextStatus = $campaign->status === 'ACTIVE' ? 'PAUSED' : 'ACTIVE';

            if ($campaign->external_id) {
                if ($campaign->platform !== 'meta' || ! $campaign->integration?->access_token) {
                    $this->dispatch('notify', ['message' => 'Esta plataforma ainda não possui uma API oficial configurada.']);

                    return;
                }

                $updatedRemotely = $metaAdsService->updateCampaignStatus(
                    $campaign->integration,
                    $campaign->external_id,
                    $nextStatus,
                );

                if (! $updatedRemotely) {
                    $this->dispatch('notify', ['message' => 'Não foi possível atualizar a campanha na plataforma.']);

                    return;
                }
            }

            $campaign->update(['status' => $nextStatus]);
        }
    }

    public function editBudget(int $campaignId, float $currentBudget): void
    {
        $this->editingCampaignId = $campaignId;
        $this->editingBudget = $currentBudget;
    }

    public function saveBudget(): void
    {
        if ($this->editingCampaignId && $this->editingBudget > 0) {
            Campaign::where('id', $this->editingCampaignId)
                ->update(['daily_budget' => $this->editingBudget]);
        }
        $this->editingCampaignId = null;
    }

    public function duplicate(int $campaignId): void
    {
        $c = Campaign::find($campaignId);
        if ($c) {
            $replica = $c->replicate();
            $replica->name = $c->name.' (Cópia)';
            $replica->status = 'PAUSED';
            $replica->total_spend = 0;
            $replica->conversions = 0;
            $replica->revenue = 0;
            $replica->roas = 0;
            $replica->save();
        }
    }

    public function createCampaign(): void
    {
        $this->validate([
            'newCampaignName' => 'required|string|max:255',
            'newDailyBudget' => 'required|numeric|min:10',
        ]);

        Campaign::create([
            'workspace_id' => $this->workspaceId,
            'name' => $this->newCampaignName,
            'platform' => $this->newPlatform,
            'objective' => $this->newObjective,
            'daily_budget' => $this->newDailyBudget,
            'status' => 'ACTIVE',
            'start_date' => now()->toDateString(),
            'target_audience' => $this->newAudience ?: 'Público Alvo Personalizado',
        ]);

        $this->reset(['newCampaignName', 'newAudience', 'showCreateModal']);
    }

    public function render()
    {
        $query = Campaign::with('adSets.ads')
            ->where('workspace_id', $this->workspaceId);

        if ($this->selectedPlatform !== 'all') {
            $query->where('platform', $this->selectedPlatform);
        }

        if ($this->statusFilter !== 'ALL') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->search) {
            $query->where('name', 'like', '%'.$this->search.'%');
        }

        return view('livewire.campaigns-manager', [
            'campaigns' => $query->orderBy('created_at', 'desc')->get(),
        ])->layout('layouts.app');
    }
}
