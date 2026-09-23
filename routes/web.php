<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;
use App\Livewire\CampaignsManager;
use App\Livewire\IntegrationsHub;
use App\Livewire\LeadsCrm;
use App\Livewire\CreativeAnalytics;
use App\Livewire\AutomationRules;
use App\Livewire\ReportsBuilder;

Route::get('/', Dashboard::class)->name('dashboard');
Route::get('/campanhas', CampaignsManager::class)->name('campaigns');
Route::get('/conexoes', IntegrationsHub::class)->name('integrations');
Route::get('/leads', LeadsCrm::class)->name('leads');
Route::get('/criativos', CreativeAnalytics::class)->name('creatives');
Route::get('/automacoes', AutomationRules::class)->name('automation');
Route::get('/relatorios', ReportsBuilder::class)->name('reports');
