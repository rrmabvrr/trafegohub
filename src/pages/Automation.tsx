import React, { useState } from 'react';
import { Zap, Plus, Play, Pause, AlertCircle, CheckCircle2, Sliders, History, ArrowRight } from 'lucide-react';
import { useApp } from '../context/AppContext';
import { Platform } from '../types';

export const Automation: React.FC = () => {
  const { automationRules, addAutomationRule, toggleAutomationRule } = useApp();

  const [showAddRuleModal, setShowAddRuleModal] = useState(false);
  const [ruleName, setRuleName] = useState('');
  const [metric, setMetric] = useState<'CPA' | 'ROAS' | 'SPEND' | 'CTR' | 'FREQUENCY'>('CPA');
  const [condition, setCondition] = useState<'GREATER' | 'LESS' | 'EQUALS'>('GREATER');
  const [threshold, setThreshold] = useState<number>(40);
  const [action, setAction] = useState<'PAUSE_CAMPAIGN' | 'INCREASE_BUDGET' | 'DECREASE_BUDGET' | 'NOTIFY_WHATSAPP' | 'NOTIFY_EMAIL'>('PAUSE_CAMPAIGN');

  const handleCreateRule = (e: React.FormEvent) => {
    e.preventDefault();
    if (!ruleName) return;

    addAutomationRule({
      name: ruleName,
      platformFilter: 'ALL',
      metric,
      condition,
      threshold: Number(threshold),
      timeFrame: 'TODAY',
      action,
      isEnabled: true
    });

    setRuleName('');
    setShowAddRuleModal(false);
  };

  const auditLogs = [
    { id: 'log-1', time: 'Hoje às 14:10', rule: 'Pausar automática se CPA exceder R$ 40,00', campaign: '[Meta] Lançamento 2026', actionText: 'Campanha Pausada (CPA atingiu R$ 44.50)' },
    { id: 'log-2', time: 'Hoje às 09:15', rule: 'Escalar orçamento em +20% se ROAS > 4.5', campaign: '[Google] Search - Fundo de Funil', actionText: 'Orçamento expandido de R$ 250 para R$ 300/dia' },
    { id: 'log-3', time: 'Ontem às 18:30', rule: 'Alerta de Fadiga se Frequência > 4.0', campaign: '[Meta] [CBO] Vendas Infoproduto', actionText: 'Alerta enviado ao WhatsApp do Gestor' }
  ];

  return (
    <div className="space-y-6 pb-12">
      {/* Header Banner */}
      <div className="glass-panel p-6 rounded-2xl border border-slate-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
          <h1 className="text-2xl font-extrabold text-slate-100 flex items-center gap-2.5">
            <Zap className="w-6 h-6 text-brand-cyan" />
            Motor de Automações & Regras Inteligentes
          </h1>
          <p className="text-xs sm:text-sm text-slate-400 mt-1">
            Defina regras condicionais para proteger o orçamento e escalar campanhas automaticamente 24 horas por dia.
          </p>
        </div>

        <button
          onClick={() => setShowAddRuleModal(true)}
          className="flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-gradient-to-r from-brand-cyan via-blue-600 to-brand-violet text-slate-950 font-bold text-xs shadow-neon-blue hover:brightness-110 transition active:scale-95"
        >
          <Plus className="w-4 h-4" />
          Criar Nova Regra
        </button>
      </div>

      {/* Rules Cards List */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        {automationRules.map(rule => {
          const isEnabled = rule.isEnabled;

          return (
            <div key={rule.id} className="glass-panel-interactive p-5 rounded-2xl border border-slate-800 flex flex-col justify-between">
              <div>
                <div className="flex items-center justify-between mb-3">
                  <span className="text-[10px] px-2.5 py-0.5 rounded-full font-bold bg-brand-cyan/10 text-brand-cyan border border-brand-cyan/30">
                    Gatilho: {rule.metric}
                  </span>

                  <button
                    onClick={() => toggleAutomationRule(rule.id)}
                    className={`flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold border transition ${
                      isEnabled 
                        ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/40' 
                        : 'bg-slate-800 text-slate-400 border-slate-700'
                    }`}
                  >
                    {isEnabled ? <Play className="w-3 h-3 fill-emerald-400" /> : <Pause className="w-3 h-3" />}
                    <span>{isEnabled ? 'ATIVA' : 'DESATIVADA'}</span>
                  </button>
                </div>

                <h3 className="font-bold text-slate-100 text-sm mb-2">{rule.name}</h3>

                {/* Rule visual flow */}
                <div className="p-3 rounded-xl bg-slate-900/80 border border-slate-800 text-xs space-y-1.5 mb-4">
                  <div className="flex items-center justify-between text-slate-300">
                    <span className="text-slate-400">Condição:</span>
                    <span className="font-mono font-bold text-brand-cyan">
                      {rule.metric} {rule.condition === 'GREATER' ? '>' : rule.condition === 'LESS' ? '<' : '='} {rule.threshold}
                    </span>
                  </div>
                  <div className="flex items-center justify-between text-slate-300">
                    <span className="text-slate-400">Ação Automatizada:</span>
                    <span className="font-semibold text-purple-400">
                      {rule.action.replace('_', ' ')}
                    </span>
                  </div>
                </div>
              </div>

              <div className="flex items-center justify-between pt-2 border-t border-slate-800 text-[11px] text-slate-400">
                <span>Disparada: {rule.triggerCount} vezes</span>
                <span>Última: {rule.lastTriggered || 'Nunca'}</span>
              </div>
            </div>
          );
        })}
      </div>

      {/* Execution Audit Log */}
      <div className="glass-panel p-5 rounded-2xl border border-slate-800">
        <h2 className="text-base font-bold text-slate-100 mb-4 flex items-center gap-2">
          <History className="w-4 h-4 text-brand-cyan" />
          Histórico de Execuções Automáticas (Audit Log)
        </h2>

        <div className="space-y-2">
          {auditLogs.map(log => (
            <div key={log.id} className="p-3 rounded-xl bg-slate-900/60 border border-slate-800 text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-2">
              <div className="flex items-center gap-2">
                <CheckCircle2 className="w-4 h-4 text-emerald-400 flex-shrink-0" />
                <div>
                  <span className="font-bold text-slate-200">{log.rule}</span>
                  <span className="text-slate-400 block text-[11px]">Campanha: {log.campaign}</span>
                </div>
              </div>

              <div className="flex items-center gap-3">
                <span className="text-emerald-400 font-semibold text-[11px] bg-emerald-500/10 px-2 py-0.5 rounded border border-emerald-500/20">
                  {log.actionText}
                </span>
                <span className="text-[10px] text-slate-500">{log.time}</span>
              </div>
            </div>
          ))}
        </div>
      </div>

      {/* Add Rule Modal */}
      {showAddRuleModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-hub-950/80 backdrop-blur-md">
          <div className="glass-modal w-full max-w-lg rounded-2xl border border-slate-700 p-6 relative">
            <h3 className="text-base font-bold text-slate-100 mb-4">Criar Regra de Automação</h3>
            <form onSubmit={handleCreateRule} className="space-y-3.5">
              <div>
                <label className="block text-xs text-slate-300 mb-1">Nome da Regra</label>
                <input 
                  type="text" 
                  required 
                  placeholder="Ex: Pausar anúncio se CPA > R$ 50"
                  value={ruleName}
                  onChange={(e) => setRuleName(e.target.value)}
                  className="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white"
                />
              </div>

              <div className="grid grid-cols-3 gap-2">
                <div>
                  <label className="block text-xs text-slate-300 mb-1">Métrica</label>
                  <select 
                    value={metric}
                    onChange={(e) => setMetric(e.target.value as any)}
                    className="w-full px-2 py-2 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white"
                  >
                    <option value="CPA">CPA (R$)</option>
                    <option value="ROAS">ROAS (x)</option>
                    <option value="CTR">CTR (%)</option>
                    <option value="SPEND">Gasto (R$)</option>
                    <option value="FREQUENCY">Frequência</option>
                  </select>
                </div>

                <div>
                  <label className="block text-xs text-slate-300 mb-1">Condição</label>
                  <select 
                    value={condition}
                    onChange={(e) => setCondition(e.target.value as any)}
                    className="w-full px-2 py-2 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white"
                  >
                    <option value="GREATER">Maior que (&gt;)</option>
                    <option value="LESS">Menor que (&lt;)</option>
                    <option value="EQUALS">Igual a (=)</option>
                  </select>
                </div>

                <div>
                  <label className="block text-xs text-slate-300 mb-1">Valor Limite</label>
                  <input 
                    type="number" 
                    step="0.1"
                    value={threshold}
                    onChange={(e) => setThreshold(Number(e.target.value))}
                    className="w-full px-2 py-2 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white font-bold"
                  />
                </div>
              </div>

              <div>
                <label className="block text-xs text-slate-300 mb-1">Ação Automatizada</label>
                <select 
                  value={action}
                  onChange={(e) => setAction(e.target.value as any)}
                  className="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white font-semibold"
                >
                  <option value="PAUSE_CAMPAIGN">Pausar Campanha</option>
                  <option value="INCREASE_BUDGET">Aumentar Orçamento (+20%)</option>
                  <option value="DECREASE_BUDGET">Diminuir Orçamento (-20%)</option>
                  <option value="NOTIFY_WHATSAPP">Enviar Alerta no WhatsApp</option>
                </select>
              </div>

              <div className="flex justify-end gap-2 pt-3">
                <button 
                  type="button" 
                  onClick={() => setShowAddRuleModal(false)}
                  className="px-4 py-2 bg-slate-800 text-xs font-semibold rounded-xl text-slate-300"
                >
                  Cancelar
                </button>
                <button 
                  type="submit"
                  className="px-4 py-2 bg-brand-cyan text-slate-950 font-bold text-xs rounded-xl shadow-neon-blue"
                >
                  Ativar Regra
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
};
