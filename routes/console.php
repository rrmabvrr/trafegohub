<?php

use App\Jobs\EvaluateAutomationRulesJob;
use App\Jobs\SyncPlatformMetricsJob;
use Illuminate\Support\Facades\Schedule;

// Sincroniza métricas das APIs de anúncios a cada 15 minutos.
// A fila da job é definida na própria classe para evitar erros de agendamento.
Schedule::job(new SyncPlatformMetricsJob)->everyFifteenMinutes();

// Avalia regras de automação (CPA limite, ajuste de orçamento) a cada hora.
Schedule::job(new EvaluateAutomationRulesJob)->hourly();
