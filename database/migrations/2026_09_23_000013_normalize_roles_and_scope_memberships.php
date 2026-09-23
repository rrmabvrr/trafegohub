<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('role', 32)->default('CLIENTE')->change();
        });

        DB::table('users')->where('role', 'admin')->update(['role' => 'ADMIN']);
        DB::table('users')->where('role', 'gestor')->update(['role' => 'GERENTE']);
        DB::table('users')->where('role', 'cliente')->update(['role' => 'CLIENTE']);

        Schema::table('organization_user', function (Blueprint $table): void {
            $table->foreignId('client_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
        });

        foreach (DB::table('organization_user')->get(['organization_id', 'user_id']) as $membership) {
            if (DB::table('users')->where('id', $membership->user_id)->value('role') !== 'CLIENTE') {
                continue;
            }

            $clientId = DB::table('workspaces')
                ->where('organization_id', $membership->organization_id)
                ->whereNotNull('client_id')
                ->value('client_id');

            if ($clientId) {
                DB::table('organization_user')
                    ->where('organization_id', $membership->organization_id)
                    ->where('user_id', $membership->user_id)
                    ->update(['client_id' => $clientId]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('organization_user', function (Blueprint $table): void {
            $table->dropForeign(['client_id']);
            $table->dropColumn('client_id');
        });

        DB::table('users')->where('role', 'ADMIN')->update(['role' => 'admin']);
        DB::table('users')->where('role', 'GERENTE')->update(['role' => 'gestor']);
        DB::table('users')->where('role', 'CLIENTE')->update(['role' => 'cliente']);

        Schema::table('users', function (Blueprint $table): void {
            $table->enum('role', ['admin', 'gestor', 'cliente'])->default('admin')->change();
        });
    }
};
