<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('integrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->onDelete('cascade');
            $table->enum('platform', ['meta', 'google', 'tiktok', 'linkedin', 'kwai']);
            $table->string('name');
            $table->string('account_id');
            $table->string('ad_account_name');
            $table->enum('status', ['CONNECTED', 'DISCONNECTED', 'ERROR', 'SYNCING'])->default('CONNECTED');
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->string('webhook_url')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('integrations');
    }
};
