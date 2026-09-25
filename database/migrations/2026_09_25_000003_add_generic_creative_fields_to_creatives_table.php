<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('creatives', function (Blueprint $table): void {
            $table->string('external_id')->nullable()->after('campaign_id');
            $table->string('status')->nullable()->after('platform');
            $table->string('title')->nullable()->after('status');
            $table->text('description')->nullable()->after('title');
            $table->string('url')->nullable()->after('description');
            $table->string('image_url')->nullable()->after('url');
            $table->string('video_url')->nullable()->after('image_url');
            $table->string('cta')->nullable()->after('video_url');
            $table->timestamp('synced_at')->nullable()->after('frequency');
        });
    }

    public function down(): void
    {
        Schema::table('creatives', function (Blueprint $table): void {
            $table->dropColumn(['external_id', 'status', 'title', 'description', 'url', 'image_url', 'video_url', 'cta', 'synced_at']);
        });
    }
};
