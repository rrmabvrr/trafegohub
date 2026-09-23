<?php

use Illuminate\Support\Facades\Schedule;
use App\Jobs\SyncPlatformMetricsJob;
use App\Jobs\EvaluateAutomationRulesJob;

// Sincroniza métricas das APIs de anúncios a cada 15 minutos
Schedule::job(new SyncPlatformMetricsJob)->everyFifteenMinutes();

// Avalia regras de automação (CPA limite, ajuste de orçamento) a cada hora
Schedule::job(new EvaluateAutomationRulesJob)->hourly();
