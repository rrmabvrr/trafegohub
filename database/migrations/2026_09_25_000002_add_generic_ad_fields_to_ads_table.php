<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ads', function (Blueprint $table): void {
            if (! Schema::hasColumn('ads', 'title')) {
                $table->string('title')->nullable()->after('status');
            }

            if (! Schema::hasColumn('ads', 'description')) {
                $table->text('description')->nullable()->after('title');
            }

            if (! Schema::hasColumn('ads', 'url')) {
                $table->string('url')->nullable()->after('description');
            }

            if (! Schema::hasColumn('ads', 'image_url')) {
                $table->string('image_url')->nullable()->after('url');
            }

            if (! Schema::hasColumn('ads', 'video_url')) {
                $table->string('video_url')->nullable()->after('image_url');
            }

            if (! Schema::hasColumn('ads', 'cta')) {
                $table->string('cta')->nullable()->after('video_url');
            }

            if (! Schema::hasColumn('ads', 'synced_at')) {
                $table->timestamp('synced_at')->nullable()->after('revenue');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ads', function (Blueprint $table): void {
            $columns = ['title', 'description', 'url', 'image_url', 'video_url', 'cta', 'synced_at'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('ads', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
