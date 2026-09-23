<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ad_sets', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->string('external_id');
            $table->string('name');
            $table->string('status')->default('ACTIVE');
            $table->decimal('daily_budget', 12, 2)->default(0);
            $table->decimal('total_spend', 12, 2)->default(0);
            $table->unsignedBigInteger('impressions')->default(0);
            $table->unsignedBigInteger('clicks')->default(0);
            $table->decimal('ctr', 5, 2)->default(0);
            $table->decimal('cpc', 8, 2)->default(0);
            $table->decimal('cpl', 8, 2)->default(0);
            $table->unsignedInteger('conversions')->default(0);
            $table->decimal('roas', 8, 2)->default(0);
            $table->decimal('revenue', 12, 2)->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();

            $table->unique(['campaign_id', 'external_id']);
            $table->index(['campaign_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_sets');
    }
};
