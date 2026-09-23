<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->onDelete('cascade');
            $table->foreignId('integration_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('platform', ['meta', 'google', 'tiktok', 'linkedin', 'kwai']);
            $table->string('name');
            $table->enum('status', ['ACTIVE', 'PAUSED', 'ARCHIVED'])->default('ACTIVE');
            $table->enum('objective', ['SALES', 'LEADS', 'TRAFFIC', 'ENGAGEMENT'])->default('SALES');
            $table->decimal('daily_budget', 12, 2)->default(100.00);
            $table->decimal('total_spend', 12, 2)->default(0.00);
            $table->bigInteger('impressions')->default(0);
            $table->bigInteger('clicks')->default(0);
            $table->decimal('ctr', 5, 2)->default(0.00);
            $table->decimal('cpc', 8, 2)->default(0.00);
            $table->decimal('cpl', 8, 2)->default(0.00);
            $table->integer('conversions')->default(0);
            $table->decimal('roas', 8, 2)->default(0.00);
            $table->decimal('revenue', 12, 2)->default(0.00);
            $table->date('start_date');
            $table->string('target_audience')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
