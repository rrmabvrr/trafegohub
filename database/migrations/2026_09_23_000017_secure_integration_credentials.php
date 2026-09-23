<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('integrations', function (Blueprint $table): void {
            $table->string('external_account_id')->nullable()->after('account_id');
            $table->timestamp('expires_at')->nullable()->after('token_expires_at');
            $table->json('scopes')->nullable()->after('expires_at');
            $table->foreignId('connected_user_id')->nullable()->after('organization_id')->constrained('users')->nullOnDelete();
            $table->timestamp('connected_at')->nullable()->after('connected_user_id');
            $table->text('last_error')->nullable()->after('last_synced_at');
            $table->index(['organization_id', 'status']);
        });

        foreach (DB::table('integrations')->get(['id', 'access_token', 'refresh_token']) as $integration) {
            $updates = [];

            if (filled($integration->access_token)) {
                $updates['access_token'] = Crypt::encryptString($integration->access_token);
            }

            if (filled($integration->refresh_token)) {
                $updates['refresh_token'] = Crypt::encryptString($integration->refresh_token);
            }

            if ($updates !== []) {
                DB::table('integrations')->where('id', $integration->id)->update($updates);
            }
        }

        DB::table('integrations')->whereNull('external_account_id')->update([
            'external_account_id' => DB::raw('account_id'),
        ]);
    }

    public function down(): void
    {
        Schema::table('integrations', function (Blueprint $table): void {
            $table->dropIndex(['organization_id', 'status']);
            $table->dropForeign(['connected_user_id']);
            $table->dropColumn([
                'external_account_id',
                'expires_at',
                'scopes',
                'connected_user_id',
                'connected_at',
                'last_error',
            ]);
        });
    }
};
