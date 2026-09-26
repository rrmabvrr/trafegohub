<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Jobs\SyncPlatformMetricsJob;
use App\Models\Campaign;
use App\Models\Client;
use App\Models\Integration;
use App\Models\Metric;
use App\Models\Organization;
use App\Models\User;
use App\Models\Workspace;
use App\Services\AdvertisingPlatformManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AutomatedIntegrationCoverageTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_access_the_dashboard_and_is_recognized_by_auth(): void
    {
        $organization = Organization::create([
            'name' => 'Agência autenticada',
            'slug' => 'agencia-autenticada',
        ]);
        $user = User::factory()->create([
            'role' => UserRole::GERENTE->value,
            'email' => 'gerente@test.com',
        ]);
        $organization->users()->attach($user->id, ['role' => UserRole::GERENTE->value]);

        $this->actingAs($user);

        $this->assertTrue(Auth::check());
        $this->assertTrue(Gate::forUser($user)->allows('view', $organization));
        $this->get(route('dashboard'))->assertOk();
    }

    public function test_organization_membership_is_enforced_for_members_and_non_members(): void
    {
        $organization = Organization::create([
            'name' => 'Agência de membros',
            'slug' => 'agencia-de-membros',
        ]);
        $member = User::factory()->create(['role' => UserRole::GERENTE->value]);
        $nonMember = User::factory()->create(['role' => UserRole::ANALISTA->value]);

        $organization->users()->attach($member->id, ['role' => UserRole::GERENTE->value]);

        $this->assertTrue(Gate::forUser($member)->allows('view', $organization));
        $this->assertFalse(Gate::forUser($nonMember)->allows('view', $organization));
    }

    public function test_client_permissions_are_scoped_to_the_organization_and_role(): void
    {
        $organization = Organization::create([
            'name' => 'Agência de clientes',
            'slug' => 'agencia-de-clientes',
        ]);
        $manager = User::factory()->create(['role' => UserRole::GERENTE->value]);
        $analyst = User::factory()->create(['role' => UserRole::ANALISTA->value]);
        $organization->users()->attach($manager->id, ['role' => UserRole::GERENTE->value]);
        $organization->users()->attach($analyst->id, ['role' => UserRole::ANALISTA->value]);

        $client = Client::create([
            'organization_id' => $organization->id,
            'name' => 'Cliente X',
        ]);

        $this->assertTrue(Gate::forUser($manager)->allows('create', [Client::class, $organization->id]));
        $this->assertFalse(Gate::forUser($analyst)->allows('create', [Client::class, $organization->id]));
        $this->assertTrue(Gate::forUser($manager)->allows('update', $client));
    }

    public function test_campaign_permissions_follow_organization_membership_and_client_scope(): void
    {
        $organization = Organization::create([
            'name' => 'Agência de campanhas',
            'slug' => 'agencia-de-campanhas',
        ]);

        $manager = User::factory()->create(['role' => UserRole::GERENTE->value]);
        $clientUser = User::factory()->create(['role' => UserRole::CLIENTE->value]);
        $organization->users()->attach($manager->id, ['role' => UserRole::GERENTE->value]);

        $client = Client::create([
            'organization_id' => $organization->id,
            'name' => 'Cliente da campanha',
        ]);
        $organization->users()->attach($clientUser->id, ['client_id' => $client->id]);

        $workspace = Workspace::create([
            'organization_id' => $organization->id,
            'client_id' => $client->id,
            'name' => 'Workspace clientes',
            'client_name' => $client->name,
        ]);

        $campaign = Campaign::create([
            'organization_id' => $organization->id,
            'workspace_id' => $workspace->id,
            'client_id' => $client->id,
            'name' => 'Campanha restrita',
            'platform' => 'meta',
            'status' => 'ACTIVE',
            'objective' => 'LEADS',
            'start_date' => now()->toDateString(),
        ]);

        $this->assertTrue(Gate::forUser($manager)->allows('view', $campaign));
        $this->assertTrue(Gate::forUser($clientUser)->allows('view', $campaign));
        $this->assertFalse(Gate::forUser($clientUser)->allows('update', $campaign));
    }

    public function test_metrics_can_be_created_and_filtered_by_organization_and_date(): void
    {
        $organization = Organization::create([
            'name' => 'Agência de métricas',
            'slug' => 'agencia-de-metricas',
        ]);
        $workspace = Workspace::create([
            'organization_id' => $organization->id,
            'name' => 'Workspace métricas',
            'client_name' => 'Cliente',
        ]);
        $campaign = Campaign::create([
            'organization_id' => $organization->id,
            'workspace_id' => $workspace->id,
            'name' => 'Campanha métrica',
            'platform' => 'meta',
            'status' => 'ACTIVE',
            'objective' => 'SALES',
            'start_date' => '2026-09-01',
        ]);

        Metric::create([
            'organization_id' => $organization->id,
            'workspace_id' => $workspace->id,
            'campaign_id' => $campaign->id,
            'platform' => 'meta',
            'account_name' => 'Conta principal',
            'date' => '2026-09-24',
            'period' => 'day',
            'impressions' => 1000,
            'clicks' => 80,
            'spend' => 120.5,
            'ctr' => 8.0,
            'cpc' => 1.5,
            'leads' => 10,
            'conversions' => 7,
            'revenue' => 420.0,
            'roas' => 3.49,
        ]);

        $this->assertDatabaseHas('metrics', [
            'organization_id' => $organization->id,
            'campaign_id' => $campaign->id,
            'platform' => 'meta',
            'date' => '2026-09-24 00:00:00',
        ]);

        $this->assertSame(1, Metric::where('organization_id', $organization->id)
            ->whereDate('date', '2026-09-24')
            ->count());
    }

    public function test_platform_sync_uses_fake_http_and_no_real_external_call_is_made(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://graph.facebook.com/v21.0/act_123/campaigns*' => Http::response([
                'data' => [[
                    'id' => 'campaign-123',
                    'name' => 'Campanha fake',
                    'status' => 'ACTIVE',
                    'objective' => 'OUTCOME_SALES',
                    'daily_budget' => '5000',
                    'insights' => ['data' => [[
                        'spend' => '125.50',
                        'impressions' => '10000',
                        'clicks' => '400',
                        'ctr' => '4.00',
                        'cpc' => '0.31',
                        'conversions' => '10',
                        'purchase_roas' => [['value' => '3.20']],
                    ]]],
                    'adsets' => ['data' => [[
                        'id' => 'adset-123',
                        'name' => 'Adset fake',
                        'status' => 'ACTIVE',
                        'daily_budget' => '2500',
                        'ads' => ['data' => [[
                            'id' => 'ad-123',
                            'name' => 'Ad fake',
                            'status' => 'ACTIVE',
                        ]]],
                    ]]],
                ]],
            ]),
        ]);

        $organization = Organization::create(['name' => 'Agência sync', 'slug' => 'agencia-sync']);
        $workspace = Workspace::create([
            'organization_id' => $organization->id,
            'name' => 'Workspace sync',
            'client_name' => 'Cliente sync',
        ]);
        $integration = Integration::create([
            'organization_id' => $organization->id,
            'workspace_id' => $workspace->id,
            'platform' => 'meta',
            'name' => 'Meta fake',
            'account_id' => 'act_123',
            'ad_account_name' => 'Meta account',
            'status' => 'CONNECTED',
            'access_token' => 'fake-token',
        ]);

        app(AdvertisingPlatformManager::class);
        (new SyncPlatformMetricsJob($integration->id))->handle(app(AdvertisingPlatformManager::class));

        $this->assertDatabaseHas('campaigns', ['external_id' => 'campaign-123']);
        $this->assertDatabaseHas('ad_sets', ['external_id' => 'adset-123']);
        $this->assertDatabaseHas('ads', ['external_id' => 'ad-123']);
        Http::assertSentCount(1);
    }

    public function test_webhook_validation_accepts_signed_payloads_and_rejects_invalid_ones(): void
    {
        config(['services.webhooks.meta.secret' => 'super-secret']);

        $payload = [
            'entry' => [[
                'changes' => [[
                    'value' => [
                        'leadgen_id' => 'lead-42',
                        'form_id' => 'form-1',
                    ],
                ]],
            ]],
        ];

        $signature = 'sha256='.hash_hmac('sha256', json_encode($payload), 'super-secret');

        $this->postJson('/api/webhooks/meta', $payload, [
            'x-hub-signature-256' => $signature,
            'x-request-id' => 'req-123',
        ])->assertStatus(202);

        $this->assertDatabaseHas('webhook_events', ['platform' => 'meta', 'status' => 'processed']);

        $invalidResponse = $this->postJson('/api/webhooks/meta', $payload, [
            'x-hub-signature-256' => 'sha256=invalid',
        ]);

        $invalidResponse->assertStatus(401);
        $this->assertSame('Invalid webhook signature.', $invalidResponse->json('message'));
    }

    public function test_connectors_are_resolved_by_platform_name_without_real_api_usage(): void
    {
        $manager = app(AdvertisingPlatformManager::class);

        foreach (['meta', 'google', 'tiktok', 'linkedin'] as $platform) {
            $integration = new Integration(['platform' => $platform]);
            $resolved = $manager->for($integration);

            $this->assertSame($platform, $resolved->platform());
        }
    }

    public function test_permission_gate_blocks_access_for_unprivileged_users(): void
    {
        $organization = Organization::create([
            'name' => 'Agência permissões',
            'slug' => 'agencia-permissoes',
        ]);
        $admin = User::factory()->create(['role' => UserRole::ADMIN->value]);
        $analyst = User::factory()->create(['role' => UserRole::ANALISTA->value]);
        $organization->users()->attach($admin->id, ['role' => UserRole::ADMIN->value]);
        $organization->users()->attach($analyst->id, ['role' => UserRole::ANALISTA->value]);

        $client = Client::create([
            'organization_id' => $organization->id,
            'name' => 'Cliente com restrição',
        ]);

        $this->assertTrue(Gate::forUser($admin)->allows('update', $client));
        $this->assertFalse(Gate::forUser($analyst)->allows('update', $client));
    }
}
