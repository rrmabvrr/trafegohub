<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sync_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('workspace_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('integration_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('platform')->nullable();
            $table->string('account_name')->nullable();
            $table->date('date');
            $table->enum('status', ['started', 'success', 'failed'])->default('started');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->unsignedBigInteger('records_processed')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['platform', 'date']);
            $table->index(['integration_id', 'date']);
            $table->index(['status', 'started_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_logs');
    }
};
