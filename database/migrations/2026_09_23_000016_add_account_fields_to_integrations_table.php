<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('integrations', function (Blueprint $table): void {
            $table->foreignId('client_id')->nullable()->after('organization_id')->constrained()->nullOnDelete();
            $table->string('currency', 3)->default('BRL')->after('ad_account_name');
            $table->string('timezone')->default('America/Sao_Paulo')->after('currency');
            $table->index(['organization_id', 'client_id']);
        });

    }

    public function down(): void
    {
        Schema::table('integrations', function (Blueprint $table): void {
            $table->dropIndex(['organization_id', 'client_id']);
            $table->dropForeign(['client_id']);
            $table->dropColumn(['client_id', 'currency', 'timezone']);
        });
    }
};
