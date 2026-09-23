<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('integrations', function (Blueprint $table): void {
            $table->string('platform', 32)->change();
            $table->index('platform');
        });

        Schema::table('campaigns', function (Blueprint $table): void {
            $table->string('platform', 32)->change();
            $table->index('platform');
        });
    }

    public function down(): void
    {
        DB::statement("UPDATE integrations SET platform = 'meta' WHERE platform NOT IN ('meta', 'google', 'tiktok', 'linkedin', 'kwai')");
        DB::statement("UPDATE campaigns SET platform = 'meta' WHERE platform NOT IN ('meta', 'google', 'tiktok', 'linkedin', 'kwai')");

        Schema::table('integrations', function (Blueprint $table): void {
            $table->dropIndex(['platform']);
            $table->enum('platform', ['meta', 'google', 'tiktok', 'linkedin', 'kwai'])->change();
        });

        Schema::table('campaigns', function (Blueprint $table): void {
            $table->dropIndex(['platform']);
            $table->enum('platform', ['meta', 'google', 'tiktok', 'linkedin', 'kwai'])->change();
        });
    }
};