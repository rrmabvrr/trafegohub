<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Campaign;
use App\Models\Client;
use App\Models\Organization;
use App\Models\Report;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_manage_clients_but_an_analyst_cannot_create_them(): void
    {
        $organization = $this->organization();
        $manager = User::factory()->create(['role' => UserRole::GERENTE->value]);
        $analyst = User::factory()->create(['role' => UserRole::ANALISTA->value]);
        $organization->users()->attach([$manager->id, $analyst->id]);

        $this->assertTrue(Gate::forUser($manager)->allows('create', [Client::class, $organization->id]));
        $this->assertFalse(Gate::forUser($analyst)->allows('create', [Client::class, $organization->id]));
    }

    public function test_client_can_only_view_campaigns_and_reports_for_its_assigned_client(): void
    {
        $organization = $this->organization();
        $ownClient = Client::create(['organization_id' => $organization->id, 'name' => 'Own client']);
        $otherClient = Client::create(['organization_id' => $organization->id, 'name' => 'Other client']);
        $workspace = Workspace::create([
            'organization_id' => $organization->id,
            'client_id' => $ownClient->id,
            'name' => 'Own workspace',
            'client_name' => $ownClient->name,
        ]);
        $campaign = Campaign::create([
            'organization_id' => $organization->id,
            'workspace_id' => $workspace->id,
            'name' => 'Own campaign',
            'platform' => 'meta',
            'start_date' => now()->toDateString(),
        ]);
        $ownReport = Report::create([
            'organization_id' => $organization->id,
            'client_id' => $ownClient->id,
            'title' => 'Own report',
        ]);
        $otherReport = Report::create([
            'organization_id' => $organization->id,
            'client_id' => $otherClient->id,
            'title' => 'Other report',
        ]);
        $clientUser = User::factory()->create(['role' => UserRole::CLIENTE->value]);
        $organization->users()->attach($clientUser, ['client_id' => $ownClient->id]);

        $this->assertTrue(Gate::forUser($clientUser)->allows('view', $campaign));
        $this->assertTrue(Gate::forUser($clientUser)->allows('view', $ownReport));
        $this->assertFalse(Gate::forUser($clientUser)->allows('view', $otherReport));
        $this->assertFalse(Gate::forUser($clientUser)->allows('update', $campaign));
    }

    private function organization(): Organization
    {
        return Organization::create([
            'name' => 'Agency',
            'slug' => fake()->unique()->slug(),
        ]);
    }
}
