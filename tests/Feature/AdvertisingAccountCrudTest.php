<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Livewire\IntegrationsHub;
use App\Models\Client;
use App\Models\Integration;
use App\Models\Organization;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class AdvertisingAccountCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_create_and_toggle_an_advertising_account(): void
    {
        $organization = $this->organization('Agência principal', 'agencia-principal');
        $client = Client::create([
            'organization_id' => $organization->id,
            'name' => 'Empresa XYZ',
        ]);
        $manager = User::factory()->create(['role' => UserRole::GERENTE->value]);
        $organization->users()->attach($manager);

        $this->actingAs($manager);

        Livewire::test(IntegrationsHub::class)
            ->set('clientId', $client->id)
            ->set('platform', 'meta')
            ->set('name', 'Meta Ads')
            ->set('accountId', '123456789')
            ->set('currency', 'BRL')
            ->set('timezone', 'America/Sao_Paulo')
            ->call('createIntegration')
            ->assertHasNoErrors()
            ->assertSet('showModal', false);

        $integration = Integration::firstOrFail();
        Livewire::test(IntegrationsHub::class)
            ->call('saveCredentials', $integration->id, 'access-token', 'refresh-token', 3600, ['ads_read'], '123456789')
            ->assertHasNoErrors();
        $integration = $integration->fresh();
        $this->assertSame($client->id, $integration->client_id);
        $this->assertSame('BRL', $integration->currency);
        $this->assertSame('America/Sao_Paulo', $integration->timezone);
        $this->assertSame('access-token', $integration->access_token);
        $this->assertDatabaseMissing('integrations', ['access_token' => 'access-token']);
        $this->assertNotContains('access_token', array_keys($integration->toArray()));

        $syncedAt = Carbon::parse('2026-09-20 10:00:00');
        $integration->update(['last_synced_at' => $syncedAt]);

        Livewire::test(IntegrationsHub::class)
            ->call('toggleConnection', $integration->id)
            ->assertHasNoErrors();

        $integration = $integration->fresh();
        $this->assertSame('DISCONNECTED', $integration->status);
        $this->assertTrue($integration->last_synced_at->equalTo($syncedAt));
        $this->assertSame('Empresa XYZ', $integration->client->name);
    }

    public function test_a_user_from_another_organization_cannot_manage_an_advertising_account(): void
    {
        $ownerOrganization = $this->organization('Agência proprietária', 'agencia-proprietaria');
        $client = Client::create([
            'organization_id' => $ownerOrganization->id,
            'name' => 'Empresa protegida',
        ]);
        $owner = User::factory()->create(['role' => UserRole::GERENTE->value]);
        $ownerOrganization->users()->attach($owner);
        $workspace = Workspace::create([
            'organization_id' => $ownerOrganization->id,
            'client_id' => $client->id,
            'name' => 'Workspace protegido',
            'client_name' => $client->name,
        ]);
        $integration = Integration::create([
            'organization_id' => $ownerOrganization->id,
            'workspace_id' => $workspace->id,
            'client_id' => $client->id,
            'platform' => 'google',
            'name' => 'Google Ads',
            'account_id' => '987654321',
            'ad_account_name' => 'Google Ads',
            'currency' => 'BRL',
            'timezone' => 'America/Sao_Paulo',
        ]);

        $otherOrganization = $this->organization('Outra agência', 'outra-agencia');
        $otherManager = User::factory()->create(['role' => UserRole::GERENTE->value]);
        $otherOrganization->users()->attach($otherManager);

        $this->actingAs($otherManager);

        $this->expectException(ModelNotFoundException::class);
        Livewire::test(IntegrationsHub::class)
            ->call('toggleConnection', $integration->id);
    }

    private function organization(string $name, string $slug): Organization
    {
        return Organization::create(['name' => $name, 'slug' => $slug]);
    }
}
