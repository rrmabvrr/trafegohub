<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('metrics', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('workspace_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('integration_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('ad_set_id')->nullable()->constrained('ad_sets')->cascadeOnDelete();
            $table->foreignId('ad_id')->nullable()->constrained()->cascadeOnDelete();

            $table->string('platform', 32)->nullable();
            $table->string('account_name')->nullable();
            $table->date('date');
            $table->enum('period', ['day', 'week', 'month'])->default('day');

            $table->unsignedBigInteger('impressions')->default(0);
            $table->unsignedBigInteger('reach')->default(0);
            $table->decimal('frequency', 10, 2)->default(0);
            $table->unsignedBigInteger('clicks')->default(0);
            $table->unsignedBigInteger('link_clicks')->default(0);
            $table->decimal('spend', 12, 2)->default(0);
            $table->decimal('cpm', 12, 2)->default(0);
            $table->decimal('cpc', 12, 2)->default(0);
            $table->decimal('ctr', 12, 2)->default(0);
            $table->unsignedBigInteger('leads')->default(0);
            $table->unsignedBigInteger('conversions')->default(0);
            $table->decimal('revenue', 12, 2)->default(0);
            $table->decimal('roas', 12, 2)->default(0);
            $table->timestamps();

            $table->index(['date', 'period']);
            $table->index(['platform', 'date']);
            $table->index(['campaign_id', 'date']);
            $table->index(['client_id', 'date']);
            $table->index(['organization_id', 'date']);
            $table->index(['ad_set_id', 'date']);
            $table->index(['ad_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('metrics');
    }
};
