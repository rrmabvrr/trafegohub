<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('organization_user', function (Blueprint $table): void {
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role')->default('member');
            $table->timestamps();
            $table->primary(['organization_id', 'user_id']);
        });

        Schema::create('clients', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('external_reference')->nullable();
            $table->timestamps();
            $table->index(['organization_id', 'name']);
        });

        Schema::table('workspaces', function (Blueprint $table): void {
            if (! Schema::hasColumn('workspaces', 'organization_id')) {
                $table->foreignId('organization_id')->nullable()->after('id')->constrained()->nullOnDelete();
            }

            if (! Schema::hasColumn('workspaces', 'client_id')) {
                $table->foreignId('client_id')->nullable()->after('organization_id')->constrained()->nullOnDelete();
            }
        });

        $this->backfillOrganizations();
        $this->addOrganizationToDomainTables();
        $this->backfillUsers();
    }

    public function down(): void
    {
        foreach ([
            'automation_logs',
            'automation_rules',
            'ads',
            'ad_sets',
            'leads',
            'creatives',
            'campaigns',
            'integrations',
        ] as $tableName) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->dropForeign(['organization_id']);
                $table->dropColumn('organization_id');
            });
        }

        Schema::table('workspaces', function (Blueprint $table): void {
            $table->dropForeign(['client_id']);
            $table->dropForeign(['organization_id']);
            $table->dropColumn(['client_id', 'organization_id']);
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->dropForeign(['active_organization_id']);
            $table->dropColumn('active_organization_id');
        });

        Schema::dropIfExists('clients');
        Schema::dropIfExists('organization_user');
        Schema::dropIfExists('organizations');
    }

    private function backfillOrganizations(): void
    {
        foreach (DB::table('workspaces')->get() as $workspace) {
            $organizationId = DB::table('organizations')->insertGetId([
                'name' => $workspace->name,
                'slug' => Str::slug($workspace->name).'-'.$workspace->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $clientId = DB::table('clients')->insertGetId([
                'organization_id' => $organizationId,
                'name' => $workspace->client_name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('workspaces')->where('id', $workspace->id)->update([
                'organization_id' => $organizationId,
                'client_id' => $clientId,
            ]);
        }
    }

    private function addOrganizationToDomainTables(): void
    {
        foreach ([
            'integrations',
            'campaigns',
            'creatives',
            'leads',
            'automation_rules',
            'ad_sets',
            'ads',
            'automation_logs',
        ] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
                if (Schema::hasColumn($tableName, 'organization_id')) {
                    return;
                }

                $table->foreignId('organization_id')->nullable()->after('id')->constrained()->nullOnDelete();
            });
        }

        foreach (DB::table('workspaces')->whereNotNull('organization_id')->get(['id', 'organization_id']) as $workspace) {
            foreach (['integrations', 'campaigns', 'leads', 'automation_rules'] as $tableName) {
                DB::table($tableName)->where('workspace_id', $workspace->id)->update([
                    'organization_id' => $workspace->organization_id,
                ]);
            }
        }

        foreach (DB::table('campaigns')->whereNotNull('organization_id')->get(['id', 'organization_id']) as $campaign) {
            foreach (['creatives', 'ad_sets'] as $tableName) {
                DB::table($tableName)->where('campaign_id', $campaign->id)->update([
                    'organization_id' => $campaign->organization_id,
                ]);
            }
        }

        foreach (DB::table('ad_sets')->whereNotNull('organization_id')->get(['id', 'organization_id']) as $adSet) {
            DB::table('ads')->where('ad_set_id', $adSet->id)->update([
                'organization_id' => $adSet->organization_id,
            ]);
        }

        foreach (DB::table('automation_rules')->whereNotNull('organization_id')->get(['id', 'organization_id']) as $rule) {
            DB::table('automation_logs')->where('automation_rule_id', $rule->id)->update([
                'organization_id' => $rule->organization_id,
            ]);
        }
    }

    private function backfillUsers(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (! Schema::hasColumn('users', 'active_organization_id')) {
                $table->foreignId('active_organization_id')->nullable()->after('active_workspace_id')->constrained('organizations')->nullOnDelete();
            }
        });

        foreach (DB::table('users')->get(['id', 'active_workspace_id']) as $user) {
            $organizationId = DB::table('workspaces')->where('id', $user->active_workspace_id)->value('organization_id')
                ?? DB::table('organizations')->value('id');

            if (! $organizationId) {
                continue;
            }

            DB::table('users')->where('id', $user->id)->update([
                'active_organization_id' => $organizationId,
            ]);
            DB::table('organization_user')->insertOrIgnore([
                'organization_id' => $organizationId,
                'user_id' => $user->id,
                'role' => 'owner',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
};
