<?php

use App\Http\Controllers\IntegrationOAuthController;
use App\Livewire\AutomationRules;
use App\Livewire\CampaignsManager;
use App\Livewire\ClientsManager;
use App\Livewire\CreativeAnalytics;
use App\Livewire\Dashboard;
use App\Livewire\IntegrationsHub;
use App\Livewire\LeadsCrm;
use App\Livewire\ReportsBuilder;
use Illuminate\Support\Facades\Route;

Route::get('/', Dashboard::class)->name('dashboard');
Route::get('/campanhas', CampaignsManager::class)->name('campaigns');
Route::get('/clientes', ClientsManager::class)->name('clients');
Route::get('/conexoes', IntegrationsHub::class)->name('integrations');
Route::middleware('auth')->group(function (): void {
    Route::get('/conexoes/{integration}/oauth/redirect', [IntegrationOAuthController::class, 'redirect'])->name('integrations.oauth.redirect');
    Route::get('/conexoes/oauth/callback', [IntegrationOAuthController::class, 'callback'])->name('integrations.oauth.callback');
});
Route::get('/leads', LeadsCrm::class)->name('leads');
Route::get('/criativos', CreativeAnalytics::class)->name('creatives');
Route::get('/automacoes', AutomationRules::class)->name('automation');
Route::get('/relatorios', ReportsBuilder::class)->name('reports');
