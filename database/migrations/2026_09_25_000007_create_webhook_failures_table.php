<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webhook_failures', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('webhook_event_id')->nullable()->constrained('webhook_events')->cascadeOnDelete();
            $table->string('platform');
            $table->string('event_type')->nullable();
            $table->string('request_id')->nullable();
            $table->text('message');
            $table->json('payload')->nullable();
            $table->enum('status', ['failed', 'retrying', 'ignored'])->default('failed');
            $table->timestamps();

            $table->index(['platform', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_failures');
    }
};
