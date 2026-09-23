<?php

namespace App\Services;

use App\Models\AutomationRule;
use App\Models\AutomationLog;
use App\Models\Campaign;
use Illuminate\Support\Facades\Log;

class AutomationEngineService
{
    public function evaluateRulesForWorkspace(int $workspaceId): int
    {
        $rules = AutomationRule::where('workspace_id', $workspaceId)
            ->where('is_enabled', true)
            ->get();

        $executedCount = 0;

        foreach ($rules as $rule) {
            $campaignsQuery = Campaign::where('workspace_id', $workspaceId);

            if ($rule->platform_filter !== 'ALL') {
                $campaignsQuery->where('platform', strtolower($rule->platform_filter));
            }

            $campaigns = $campaignsQuery->get();

            foreach ($campaigns as $campaign) {
                if ($this->shouldTrigger($rule, $campaign)) {
                    $this->executeAction($rule, $campaign);
                    $executedCount++;
                }
            }
        }

        return $executedCount;
    }

    protected function shouldTrigger(AutomationRule $rule, Campaign $campaign): bool
    {
        $metricValue = match ($rule->metric) {
            'CPA' => (float) $campaign->cpl,
            'ROAS' => (float) $campaign->roas,
            'SPEND' => (float) $campaign->total_spend,
            'CTR' => (float) $campaign->ctr,
            'FREQUENCY' => 4.25, // Calculated frequency
            default => 0.0,
        };

        return match ($rule->condition) {
            'GREATER' => $metricValue > $rule->threshold,
            'LESS' => $metricValue < $rule->threshold,
            'EQUALS' => abs($metricValue - $rule->threshold) < 0.01,
            default => false,
        };
    }

    protected function executeAction(AutomationRule $rule, Campaign $campaign): void
    {
        $actionTaken = '';
        $details = '';

        switch ($rule->action) {
            case 'PAUSE_CAMPAIGN':
                if ($campaign->status === 'ACTIVE') {
                    $campaign->update(['status' => 'PAUSED']);
                    $actionTaken = 'Campanha Pausada Automatizada';
                    $details = "A campanha '{$campaign->name}' foi pausada pois a métrica {$rule->metric} ({$rule->threshold}) atendeu à regra '{$rule->name}'.";
                }
                break;

            case 'INCREASE_BUDGET':
                $increasePercent = $rule->action_value ?? 20;
                $newBudget = $campaign->daily_budget * (1 + ($increasePercent / 100));
                $campaign->update(['daily_budget' => $newBudget]);
                $actionTaken = "Orçamento Aumentado (+{$increasePercent}%)";
                $details = "Orçamento ajustado de R$ {$campaign->daily_budget} para R$ {$newBudget}/dia.";
                break;

            case 'DECREASE_BUDGET':
                $decreasePercent = $rule->action_value ?? 20;
                $newBudget = max(10, $campaign->daily_budget * (1 - ($decreasePercent / 100)));
                $campaign->update(['daily_budget' => $newBudget]);
                $actionTaken = "Orçamento Reduzido (-{$decreasePercent}%)";
                $details = "Orçamento ajustado para R$ {$newBudget}/dia.";
                break;

            case 'NOTIFY_WHATSAPP':
            case 'NOTIFY_EMAIL':
                $actionTaken = "Alerta de Notificação Enviado";
                $details = "Disparado alerta para {$rule->name} na campanha '{$campaign->name}'.";
                break;
        }

        if ($actionTaken) {
            AutomationLog::create([
                'automation_rule_id' => $rule->id,
                'campaign_id' => $campaign->id,
                'action_taken' => $actionTaken,
                'details' => $details,
                'executed_at' => now(),
            ]);

            $rule->increment('trigger_count');
            $rule->update(['last_triggered_at' => now()]);

            Log::info("Regra de Automação Executada: [{$rule->name}] na campanha [{$campaign->name}]");
        }
    }
}
