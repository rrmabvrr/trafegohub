<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaigns', function (Blueprint $table): void {
            $table->foreignId('client_id')->nullable()->after('organization_id')->constrained()->nullOnDelete();
            $table->string('currency', 3)->default('BRL')->after('daily_budget');
            $table->date('end_date')->nullable()->after('start_date');
            $table->timestamp('synced_at')->nullable()->after('updated_at');
            $table->string('status', 32)->default('ACTIVE')->change();
        });
    }

    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table): void {
            $table->dropForeign(['client_id']);
            $table->dropColumn(['client_id', 'currency', 'end_date', 'synced_at']);
        });
    }
};
