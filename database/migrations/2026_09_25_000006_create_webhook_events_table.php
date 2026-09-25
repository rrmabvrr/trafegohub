<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webhook_events', function (Blueprint $table): void {
            $table->id();
            $table->string('platform');
            $table->string('event_type')->nullable();
            $table->string('provider_event_id')->nullable();
            $table->string('request_id')->nullable();
            $table->boolean('signature_valid')->default(false);
            $table->enum('status', ['received', 'queued', 'processed', 'failed'])->default('received');
            $table->json('payload')->nullable();
            $table->json('headers')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->unsignedInteger('retry_count')->default(0);
            $table->timestamps();

            $table->unique(['platform', 'provider_event_id']);
            $table->index(['platform', 'status']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_events');
    }
};
