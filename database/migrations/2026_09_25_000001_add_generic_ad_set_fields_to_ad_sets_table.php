<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ad_sets', function (Blueprint $table): void {
            $table->decimal('budget', 12, 2)->nullable()->after('daily_budget');
            $table->string('strategy')->nullable()->after('budget');
            $table->text('audience')->nullable()->after('strategy');
            $table->json('placements')->nullable()->after('audience');
            $table->timestamp('synced_at')->nullable()->after('placements');
        });
    }

    public function down(): void
    {
        Schema::table('ad_sets', function (Blueprint $table): void {
            $table->dropColumn(['budget', 'strategy', 'audience', 'placements', 'synced_at']);
        });
    }
};
