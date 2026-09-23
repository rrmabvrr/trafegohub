<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiTenancyTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_organization_can_own_users_and_clients(): void
    {
        $organization = Organization::create([
            'name' => 'Acme Agency',
            'slug' => 'acme-agency',
        ]);
        $user = User::factory()->create();
        $client = Client::create([
            'organization_id' => $organization->id,
            'name' => 'Acme Client',
        ]);

        $organization->users()->attach($user, ['role' => 'owner']);

        $this->assertTrue($organization->users->contains($user));
        $this->assertTrue($organization->clients->contains($client));
        $this->assertSame('owner', $organization->users->first()->pivot->role);
    }
}
