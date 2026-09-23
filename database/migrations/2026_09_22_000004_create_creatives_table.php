<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('creatives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->enum('type', ['image', 'video', 'carousel'])->default('video');
            $table->enum('platform', ['meta', 'google', 'tiktok', 'linkedin', 'kwai']);
            $table->string('thumbnail_url')->nullable();
            $table->decimal('ctr', 5, 2)->default(0.00);
            $table->decimal('hook_rate', 5, 2)->default(0.00); // 3s retention %
            $table->decimal('roas', 8, 2)->default(0.00);
            $table->decimal('spend', 12, 2)->default(0.00);
            $table->integer('conversions')->default(0);
            $table->enum('fatigue_level', ['GOOD', 'WARNING', 'CRITICAL'])->default('GOOD');
            $table->text('fatigue_reason')->nullable();
            $table->decimal('frequency', 5, 2)->default(1.00);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creatives');
    }
};
