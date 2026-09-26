<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ad_sets', function (Blueprint $table): void {
            if (! Schema::hasColumn('ad_sets', 'budget')) {
                $table->decimal('budget', 12, 2)->nullable()->after('daily_budget');
            }

            if (! Schema::hasColumn('ad_sets', 'strategy')) {
                $table->string('strategy')->nullable()->after('budget');
            }

            if (! Schema::hasColumn('ad_sets', 'audience')) {
                $table->text('audience')->nullable()->after('strategy');
            }

            if (! Schema::hasColumn('ad_sets', 'placements')) {
                $table->json('placements')->nullable()->after('audience');
            }

            if (! Schema::hasColumn('ad_sets', 'synced_at')) {
                $table->timestamp('synced_at')->nullable()->after('placements');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ad_sets', function (Blueprint $table): void {
            $columns = ['budget', 'strategy', 'audience', 'placements', 'synced_at'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('ad_sets', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
