<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('creatives', function (Blueprint $table): void {
            if (! Schema::hasColumn('creatives', 'external_id')) {
                $table->string('external_id')->nullable()->after('campaign_id');
            }

            if (! Schema::hasColumn('creatives', 'status')) {
                $table->string('status')->nullable()->after('platform');
            }

            if (! Schema::hasColumn('creatives', 'title')) {
                $table->string('title')->nullable()->after('status');
            }

            if (! Schema::hasColumn('creatives', 'description')) {
                $table->text('description')->nullable()->after('title');
            }

            if (! Schema::hasColumn('creatives', 'url')) {
                $table->string('url')->nullable()->after('description');
            }

            if (! Schema::hasColumn('creatives', 'image_url')) {
                $table->string('image_url')->nullable()->after('url');
            }

            if (! Schema::hasColumn('creatives', 'video_url')) {
                $table->string('video_url')->nullable()->after('image_url');
            }

            if (! Schema::hasColumn('creatives', 'cta')) {
                $table->string('cta')->nullable()->after('video_url');
            }

            if (! Schema::hasColumn('creatives', 'synced_at')) {
                $table->timestamp('synced_at')->nullable()->after('frequency');
            }
        });
    }

    public function down(): void
    {
        Schema::table('creatives', function (Blueprint $table): void {
            $columns = ['external_id', 'status', 'title', 'description', 'url', 'image_url', 'video_url', 'cta', 'synced_at'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('creatives', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
