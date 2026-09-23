<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->onDelete('cascade');
            $table->foreignId('campaign_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->enum('platform', ['meta', 'google', 'tiktok', 'linkedin', 'kwai'])->default('meta');
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->decimal('cpl', 8, 2)->default(0.00);
            $table->decimal('deal_value', 12, 2)->default(0.00);
            $table->enum('status', ['NEW', 'CONTACTED', 'QUALIFIED', 'CONVERTED', 'LOST'])->default('NEW');
            $table->string('city')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
