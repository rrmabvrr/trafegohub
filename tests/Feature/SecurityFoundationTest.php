<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Organization;
use App\Models\User;
use App\Services\Audit\AuditLogger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class SecurityFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_organization_members_can_access_their_organization_and_non_members_cannot(): void
    {
        $organization = Organization::factory()->create();
        $member = User::factory()->create(['role' => UserRole::GERENTE->value]);
        $otherUser = User::factory()->create(['role' => UserRole::ANALISTA->value]);

        $organization->users()->attach($member->id, ['role' => UserRole::GERENTE->value]);

        $this->assertTrue(Gate::forUser($member)->allows('view', [$organization]));
        $this->assertFalse(Gate::forUser($otherUser)->allows('view', [$organization]));
    }

    public function test_audit_logger_records_an_important_action(): void
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN->value]);

        $logger = app(AuditLogger::class);
        $log = $logger->log(
            'updated_campaign',
            'Campaign',
            42,
            ['status' => 'paused'],
            $user,
            7,
            '203.0.113.10',
            'Mozilla/5.0'
        );

        $this->assertNotNull($log);
        $this->assertSame('updated_campaign', $log->action);
        $this->assertSame('Campaign', $log->resource_type);
        $this->assertSame(7, $log->organization_id);
        $this->assertSame('203.0.113.10', $log->ip_address);
    }
}
