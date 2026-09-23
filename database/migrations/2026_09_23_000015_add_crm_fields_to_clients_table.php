<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table): void {
            $table->string('legal_name')->nullable()->after('name');
            $table->string('document', 20)->nullable()->after('legal_name')->index();
            $table->string('email')->nullable()->after('document');
            $table->string('phone', 30)->nullable()->after('email');
            $table->string('whatsapp', 30)->nullable()->after('phone');
            $table->text('address')->nullable()->after('whatsapp');
            $table->string('status', 20)->default('active')->after('address')->index();
            $table->text('notes')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table): void {
            $table->dropIndex(['document']);
            $table->dropIndex(['status']);
            $table->dropColumn([
                'legal_name',
                'document',
                'email',
                'phone',
                'whatsapp',
                'address',
                'status',
                'notes',
            ]);
        });
    }
};
