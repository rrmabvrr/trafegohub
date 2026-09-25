<?php

use App\Http\Controllers\WebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'status' => 'online',
        'service' => 'TRAFEGO HUB API',
        'version' => '2.5.0',
        'php_version' => PHP_VERSION,
        'timestamp' => now()->toIso8601String(),
    ]);
});

Route::post('/webhooks/{platform}', [WebhookController::class, 'handle'])->name('webhooks.handle');
