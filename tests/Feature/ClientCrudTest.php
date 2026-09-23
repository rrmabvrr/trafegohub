<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Livewire\ClientsManager;
use App\Models\Client;
use App\Models\Integration;
use App\Models\Organization;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ClientCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_create_update_and_delete_a_client(): void
    {
        $organization = Organization::create([
            'name' => 'Agência de teste',
            'slug' => 'agencia-de-teste',
        ]);
        $manager = User::factory()->create(['role' => UserRole::GERENTE->value]);
        $organization->users()->attach($manager);

        $this->actingAs($manager);

        Livewire::test(ClientsManager::class)
            ->set('name', 'Cliente Inicial')
            ->set('legalName', 'Cliente Inicial LTDA')
            ->set('document', '12.345.678/0001-90')
            ->set('email', 'contato@cliente.test')
            ->set('phone', '(11) 3000-0000')
            ->set('whatsapp', '(11) 99999-0000')
            ->set('address', 'Rua das Flores, 100')
            ->set('notes', 'Conta principal')
            ->call('createClient')
            ->assertHasNoErrors()
            ->assertSet('showModal', false);

        $client = Client::firstOrFail();
        $this->assertSame('Cliente Inicial LTDA', $client->legal_name);
        $this->assertSame('active', $client->status);

        $workspace = Workspace::create([
            'organization_id' => $organization->id,
            'client_id' => $client->id,
            'name' => 'Workspace do cliente',
            'client_name' => $client->name,
        ]);
        Integration::create([
            'workspace_id' => $workspace->id,
            'organization_id' => $organization->id,
            'platform' => 'meta',
            'name' => 'Meta Ads',
            'account_id' => 'act-client-test',
            'ad_account_name' => 'Conta do cliente',
        ]);
        $this->assertCount(1, $client->advertisingAccounts);

        Livewire::test(ClientsManager::class)
            ->call('editClient', $client->id)
            ->set('name', 'Cliente Atualizado')
            ->set('status', 'inactive')
            ->call('updateClient')
            ->assertHasNoErrors();

        $this->assertSame('Cliente Atualizado', $client->fresh()->name);
        $this->assertSame('inactive', $client->fresh()->status);

        Livewire::test(ClientsManager::class)
            ->call('deleteClient', $client->id);

        $this->assertDatabaseMissing('clients', ['id' => $client->id]);
    }

    public function test_analyst_cannot_create_a_client(): void
    {
        $organization = Organization::create([
            'name' => 'Agência de teste',
            'slug' => 'agencia-de-teste',
        ]);
        $analyst = User::factory()->create(['role' => UserRole::ANALISTA->value]);
        $organization->users()->attach($analyst);

        $this->actingAs($analyst);

        Livewire::test(ClientsManager::class)
            ->set('name', 'Cliente não autorizado')
            ->call('createClient')
            ->assertForbidden();

        $this->assertDatabaseCount('clients', 0);
    }
}
