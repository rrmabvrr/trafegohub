<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('platforms')) {
            Schema::create('platforms', function (Blueprint $table): void {
                $table->id();
                $table->string('slug')->unique();
                $table->string('name');
                $table->string('display_name')->nullable();
                $table->string('provider')->nullable();
                $table->boolean('active')->default(true);
                $table->json('config')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('connections')) {
            Schema::create('connections', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
                $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('workspace_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('platform_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('name');
                $table->string('type')->default('oauth');
                $table->string('status')->default('connected');
                $table->string('external_account_id')->nullable();
                $table->text('access_token')->nullable();
                $table->text('refresh_token')->nullable();
                $table->timestamp('token_expires_at')->nullable();
                $table->timestamp('connected_at')->nullable();
                $table->timestamp('last_synced_at')->nullable();
                $table->json('scopes')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['organization_id', 'client_id', 'status']);
                $table->index(['organization_id', 'workspace_id']);
                $table->index(['platform_id', 'status']);
                $table->unique(
                    ['organization_id', 'platform_id', 'external_account_id'],
                    'connections_org_platform_account_unique'
                );
            });
        }

        if (! Schema::hasTable('ad_accounts')) {
            Schema::create('ad_accounts', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
                $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('platform_id')->constrained()->cascadeOnDelete();
                $table->foreignId('connection_id')->nullable()->constrained()->nullOnDelete();
                $table->string('external_account_id');
                $table->string('name');
                $table->string('currency', 3)->default('BRL');
                $table->string('timezone')->nullable();
                $table->string('status')->default('active');
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['organization_id', 'client_id', 'status']);
                $table->index(['platform_id', 'status']);
                $table->unique(
                    ['organization_id', 'platform_id', 'external_account_id'],
                    'ad_accounts_org_platform_account_unique'
                );
            });
        }

        if (! Schema::hasTable('ad_groups')) {
            Schema::create('ad_groups', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
                $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
                $table->foreignId('ad_account_id')->nullable()->constrained()->nullOnDelete();
                $table->string('external_id')->nullable();
                $table->string('name');
                $table->string('status')->default('ACTIVE');
                $table->decimal('daily_budget', 12, 2)->default(0);
                $table->decimal('budget', 12, 2)->default(0);
                $table->json('placements')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->unique(['campaign_id', 'external_id']);
                $table->index(['campaign_id', 'status']);
                $table->index(['organization_id', 'client_id', 'status']);
            });
        }

        Schema::table('campaigns', function (Blueprint $table): void {
            if (! Schema::hasColumn('campaigns', 'ad_account_id')) {
                $table->foreignId('ad_account_id')->nullable()->after('integration_id')->constrained()->nullOnDelete();
            }

            if (! Schema::hasColumn('campaigns', 'connection_id')) {
                $table->foreignId('connection_id')->nullable()->after('ad_account_id')->constrained()->nullOnDelete();
            }

            if (! Schema::hasColumn('campaigns', 'platform_id')) {
                $table->foreignId('platform_id')->nullable()->after('connection_id')->constrained()->nullOnDelete();
            }
        });

        Schema::table('ads', function (Blueprint $table): void {
            if (! Schema::hasColumn('ads', 'ad_group_id')) {
                $table->foreignId('ad_group_id')->nullable()->after('ad_set_id')->constrained()->nullOnDelete();
            }
        });

        Schema::table('metrics', function (Blueprint $table): void {
            if (! Schema::hasColumn('metrics', 'connection_id')) {
                $table->foreignId('connection_id')->nullable()->after('integration_id')->constrained()->nullOnDelete();
            }

            if (! Schema::hasColumn('metrics', 'ad_account_id')) {
                $table->foreignId('ad_account_id')->nullable()->after('connection_id')->constrained()->nullOnDelete();
            }

            if (! Schema::hasColumn('metrics', 'platform_id')) {
                $table->foreignId('platform_id')->nullable()->after('ad_account_id')->constrained()->nullOnDelete();
            }

            if (! Schema::hasColumn('metrics', 'creative_id')) {
                $table->foreignId('creative_id')->nullable()->after('ad_id')->constrained()->nullOnDelete();
            }

            if (! Schema::hasIndex('metrics', ['organization_id', 'client_id', 'date'])) {
                $table->index(['organization_id', 'client_id', 'date']);
            }

            if (! Schema::hasIndex('metrics', ['platform_id', 'date'])) {
                $table->index(['platform_id', 'date']);
            }

            if (! Schema::hasIndex('metrics', ['ad_account_id', 'date'])) {
                $table->index(['ad_account_id', 'date']);
            }

            if (! Schema::hasIndex('metrics', ['campaign_id', 'date'])) {
                $table->index(['campaign_id', 'date']);
            }
        });

        Schema::table('leads', function (Blueprint $table): void {
            if (! Schema::hasColumn('leads', 'connection_id')) {
                $table->foreignId('connection_id')->nullable()->after('organization_id')->constrained()->nullOnDelete();
            }

            if (! Schema::hasColumn('leads', 'platform_id')) {
                $table->foreignId('platform_id')->nullable()->after('connection_id')->constrained()->nullOnDelete();
            }

            if (! Schema::hasColumn('leads', 'ad_group_id')) {
                $table->foreignId('ad_group_id')->nullable()->after('ad_id')->constrained()->nullOnDelete();
            }

            if (! Schema::hasIndex('leads', ['organization_id', 'lead_date'])) {
                $table->index(['organization_id', 'lead_date']);
            }

            if (! Schema::hasIndex('leads', ['platform_id', 'lead_date'])) {
                $table->index(['platform_id', 'lead_date']);
            }
        });

        Schema::table('sync_logs', function (Blueprint $table): void {
            if (! Schema::hasColumn('sync_logs', 'connection_id')) {
                $table->foreignId('connection_id')->nullable()->after('integration_id')->constrained()->nullOnDelete();
            }

            if (! Schema::hasColumn('sync_logs', 'ad_account_id')) {
                $table->foreignId('ad_account_id')->nullable()->after('connection_id')->constrained()->nullOnDelete();
            }

            if (! Schema::hasIndex('sync_logs', ['organization_id', 'status', 'started_at'])) {
                $table->index(['organization_id', 'status', 'started_at']);
            }
        });

        Schema::table('webhook_events', function (Blueprint $table): void {
            if (! Schema::hasColumn('webhook_events', 'organization_id')) {
                $table->foreignId('organization_id')->nullable()->after('id')->constrained()->nullOnDelete();
            }

            if (! Schema::hasColumn('webhook_events', 'connection_id')) {
                $table->foreignId('connection_id')->nullable()->after('organization_id')->constrained()->nullOnDelete();
            }

            if (! Schema::hasColumn('webhook_events', 'platform_id')) {
                $table->foreignId('platform_id')->nullable()->after('connection_id')->constrained()->nullOnDelete();
            }

            if (! Schema::hasIndex('webhook_events', ['organization_id', 'status'])) {
                $table->index(['organization_id', 'status']);
            }

            if (! Schema::hasIndex('webhook_events', ['platform_id', 'created_at'])) {
                $table->index(['platform_id', 'created_at']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('webhook_events', function (Blueprint $table): void {
            if (Schema::hasIndex('webhook_events', ['organization_id', 'status'])) {
                $table->dropIndex(['organization_id', 'status']);
            }
            if (Schema::hasIndex('webhook_events', ['platform_id', 'created_at'])) {
                $table->dropIndex(['platform_id', 'created_at']);
            }
            if (Schema::hasColumn('webhook_events', 'organization_id')) {
                $table->dropConstrainedForeignId('organization_id');
            }
            if (Schema::hasColumn('webhook_events', 'connection_id')) {
                $table->dropConstrainedForeignId('connection_id');
            }
            if (Schema::hasColumn('webhook_events', 'platform_id')) {
                $table->dropConstrainedForeignId('platform_id');
            }
        });

        Schema::table('sync_logs', function (Blueprint $table): void {
            if (Schema::hasIndex('sync_logs', ['organization_id', 'status', 'started_at'])) {
                $table->dropIndex(['organization_id', 'status', 'started_at']);
            }
            if (Schema::hasColumn('sync_logs', 'connection_id')) {
                $table->dropConstrainedForeignId('connection_id');
            }
            if (Schema::hasColumn('sync_logs', 'ad_account_id')) {
                $table->dropConstrainedForeignId('ad_account_id');
            }
        });

        Schema::table('leads', function (Blueprint $table): void {
            if (Schema::hasIndex('leads', ['organization_id', 'lead_date'])) {
                $table->dropIndex(['organization_id', 'lead_date']);
            }
            if (Schema::hasIndex('leads', ['platform_id', 'lead_date'])) {
                $table->dropIndex(['platform_id', 'lead_date']);
            }
            if (Schema::hasColumn('leads', 'connection_id')) {
                $table->dropConstrainedForeignId('connection_id');
            }
            if (Schema::hasColumn('leads', 'platform_id')) {
                $table->dropConstrainedForeignId('platform_id');
            }
            if (Schema::hasColumn('leads', 'ad_group_id')) {
                $table->dropConstrainedForeignId('ad_group_id');
            }
        });

        Schema::table('metrics', function (Blueprint $table): void {
            if (Schema::hasIndex('metrics', ['organization_id', 'client_id', 'date'])) {
                $table->dropIndex(['organization_id', 'client_id', 'date']);
            }
            if (Schema::hasIndex('metrics', ['platform_id', 'date'])) {
                $table->dropIndex(['platform_id', 'date']);
            }
            if (Schema::hasIndex('metrics', ['ad_account_id', 'date'])) {
                $table->dropIndex(['ad_account_id', 'date']);
            }
            if (Schema::hasIndex('metrics', ['campaign_id', 'date'])) {
                $table->dropIndex(['campaign_id', 'date']);
            }
            if (Schema::hasColumn('metrics', 'connection_id')) {
                $table->dropConstrainedForeignId('connection_id');
            }
            if (Schema::hasColumn('metrics', 'ad_account_id')) {
                $table->dropConstrainedForeignId('ad_account_id');
            }
            if (Schema::hasColumn('metrics', 'platform_id')) {
                $table->dropConstrainedForeignId('platform_id');
            }
            if (Schema::hasColumn('metrics', 'creative_id')) {
                $table->dropConstrainedForeignId('creative_id');
            }
        });

        Schema::table('ads', function (Blueprint $table): void {
            if (Schema::hasColumn('ads', 'ad_group_id')) {
                $table->dropConstrainedForeignId('ad_group_id');
            }
        });

        Schema::table('campaigns', function (Blueprint $table): void {
            if (Schema::hasColumn('campaigns', 'ad_account_id')) {
                $table->dropConstrainedForeignId('ad_account_id');
            }
            if (Schema::hasColumn('campaigns', 'connection_id')) {
                $table->dropConstrainedForeignId('connection_id');
            }
            if (Schema::hasColumn('campaigns', 'platform_id')) {
                $table->dropConstrainedForeignId('platform_id');
            }
        });

        Schema::dropIfExists('ad_groups');
        Schema::dropIfExists('ad_accounts');
        Schema::dropIfExists('connections');
        Schema::dropIfExists('platforms');
    }
};
