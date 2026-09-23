<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('automation_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('platform_filter')->default('ALL');
            $table->enum('metric', ['CPA', 'ROAS', 'SPEND', 'CTR', 'FREQUENCY']);
            $table->enum('condition', ['GREATER', 'LESS', 'EQUALS']);
            $table->decimal('threshold', 10, 2);
            $table->enum('time_frame', ['TODAY', '7D', '30D'])->default('TODAY');
            $table->enum('action', ['PAUSE_CAMPAIGN', 'INCREASE_BUDGET', 'DECREASE_BUDGET', 'NOTIFY_WHATSAPP', 'NOTIFY_EMAIL']);
            $table->decimal('action_value', 8, 2)->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->integer('trigger_count')->default(0);
            $table->timestamp('last_triggered_at')->nullable();
            $table->timestamps();
        });

        Schema::create('automation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('automation_rule_id')->constrained()->onDelete('cascade');
            $table->foreignId('campaign_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action_taken');
            $table->text('details');
            $table->timestamp('executed_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('automation_logs');
        Schema::dropIfExists('automation_rules');
    }
};
