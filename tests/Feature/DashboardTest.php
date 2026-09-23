<?php

namespace Tests\Feature;

use App\Livewire\Dashboard;
use App\Models\Campaign;
use App\Models\CampaignMetricSnapshot;
use App\Models\Integration;
use App\Models\Organization;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_calculates_dashboard_metrics_from_filtered_snapshots(): void
    {
        Carbon::setTestNow('2026-09-23 12:00:00');

        $organization = Organization::create([
            'name' => 'Organização de teste',
            'slug' => 'organizacao-de-teste',
        ]);
        $workspace = Workspace::create([
            'name' => 'Workspace de teste',
            'client_name' => 'Cliente de teste',
            'organization_id' => $organization->id,
        ]);
        $integration = Integration::create([
            'workspace_id' => $workspace->id,
            'platform' => 'meta',
            'name' => 'Meta Ads',
            'account_id' => 'act_test',
            'ad_account_name' => 'Conta de teste',
        ]);
        $campaign = Campaign::create([
            'workspace_id' => $workspace->id,
            'integration_id' => $integration->id,
            'platform' => 'meta',
            'name' => 'Campanha filtrada',
            'start_date' => '2026-09-01',
        ]);

        CampaignMetricSnapshot::create([
            'organization_id' => $organization->id,
            'workspace_id' => $workspace->id,
            'campaign_id' => $campaign->id,
            'integration_id' => $integration->id,
            'date' => '2026-09-23',
            'spend' => 100,
            'impressions' => 10000,
            'reach' => 8000,
            'clicks' => 500,
            'leads' => 20,
            'conversions' => 5,
            'revenue' => 300,
        ]);

        Livewire::test(Dashboard::class)
            ->assertSet('workspaceId', $workspace->id)
            ->assertSee('R$ 100,00')
            ->assertSee('R$ 5,00')
            ->assertSee('R$ 0,20')
            ->assertSee('R$ 10,00')
            ->assertSee('5,00%')
            ->assertSee('3,00x')
            ->assertSee('8,000')
            ->assertSee('500');
    }
}
