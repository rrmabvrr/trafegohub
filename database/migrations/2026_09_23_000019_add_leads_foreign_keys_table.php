<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table): void {
            $table->foreign('client_id')->references('id')->on('clients')->nullOnDelete();
            $table->foreign('organization_id')->references('id')->on('organizations')->nullOnDelete();
            $table->foreign('campaign_id')->references('id')->on('campaigns')->nullOnDelete();
            $table->foreign('ad_id')->references('id')->on('ads')->nullOnDelete();
            $table->foreign('ad_set_id')->references('id')->on('ad_sets')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table): void {
            $table->dropForeign(['client_id']);
            $table->dropForeign(['organization_id']);
            $table->dropForeign(['campaign_id']);
            $table->dropForeign(['ad_id']);
            $table->dropForeign(['ad_set_id']);
        });
    }
};
