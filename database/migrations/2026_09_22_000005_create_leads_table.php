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
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('workspace_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('ad_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('ad_set_id')->nullable()->constrained('ad_sets')->nullOnDelete();

            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();

            $table->string('source')->nullable()->comment('Ex: WhatsApp, CRM, formulário, Meta Leads, Google Leads');
            $table->string('origin')->nullable()->comment('Origem da captação do lead');
            $table->string('platform')->nullable();
            $table->string('channel')->nullable();
            $table->string('campaign_name')->nullable();
            $table->string('ad_name')->nullable();
            $table->string('form_id')->nullable();
            $table->string('external_lead_id')->nullable();

            $table->timestamp('lead_date')->nullable();
            $table->enum('status', ['novo', 'contato', 'negociacao', 'convertido', 'perdido'])->default('novo');
            $table->text('observations')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(['workspace_id', 'status']);
            $table->index(['client_id', 'status']);
            $table->index(['platform', 'lead_date']);
            $table->index(['campaign_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
