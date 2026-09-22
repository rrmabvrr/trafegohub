import React, { useState } from 'react';
import { X, Sparkles, Megaphone, DollarSign, Target, Layers, Check } from 'lucide-react';
import { useApp } from '../../context/AppContext';
import { Platform, CampaignObjective } from '../../types';
import { PlatformBadge } from '../ui/PlatformBadge';

interface NewCampaignModalProps {
  isOpen: boolean;
  onClose: () => void;
}

export const NewCampaignModal: React.FC<NewCampaignModalProps> = ({ isOpen, onClose }) => {
  const { addCampaign } = useApp();

  const [name, setName] = useState('');
  const [platform, setPlatform] = useState<Platform>('meta');
  const [objective, setObjective] = useState<CampaignObjective>('SALES');
  const [dailyBudget, setDailyBudget] = useState<number>(300);
  const [targetAudience, setTargetAudience] = useState('');

  if (!isOpen) return null;

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!name.trim()) return;

    addCampaign({
      name,
      platform,
      status: 'ACTIVE',
      objective,
      dailyBudget: Number(dailyBudget),
      totalSpend: 0,
      impressions: 1200,
      clicks: 85,
      ctr: 7.08,
      cpc: 0.85,
      cpl: 15.00,
      conversions: 8,
      roas: 4.20,
      revenue: 2520,
      startDate: new Date().toISOString().split('T')[0],
      adSetCount: 2,
      adCount: 4,
      targetAudience: targetAudience || 'Público Alvo Personalizado'
    });

    onClose();
  };

  const platformsList: Platform[] = ['meta', 'google', 'tiktok', 'linkedin', 'kwai'];
  const objectivesList: { key: CampaignObjective; label: string; desc: string }[] = [
    { key: 'SALES', label: 'Vendas & Conversões', desc: 'Otimizado para ROI máximo e compras diretas' },
    { key: 'LEADS', label: 'Geração de Leads', desc: 'Captura de contatos qualificados e formulários' },
    { key: 'TRAFFIC', label: 'Tráfego para Site', desc: 'Maximização de cliques no link e visitas' },
    { key: 'ENGAGEMENT', label: 'Engajamento', desc: 'Mensagens no WhatsApp, curtidas e comentários' }
  ];

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-hub-950/80 backdrop-blur-md">
      <div className="glass-modal w-full max-w-2xl rounded-2xl border border-slate-700/80 shadow-2xl p-6 relative overflow-hidden animate-in fade-in zoom-in duration-200">
        <button 
          onClick={onClose}
          className="absolute top-4 right-4 p-2 text-slate-400 hover:text-white rounded-lg hover:bg-slate-800 transition"
        >
          <X className="w-5 h-5" />
        </button>

        <div className="flex items-center gap-3 mb-5">
          <div className="w-10 h-10 rounded-xl bg-brand-cyan/10 border border-brand-cyan/30 flex items-center justify-center text-brand-cyan">
            <Megaphone className="w-5 h-5" />
          </div>
          <div>
            <h2 className="text-lg font-bold text-slate-100 flex items-center gap-2">
              Lançar Nova Campanha Multi-Plataforma
              <span className="text-[10px] px-2 py-0.5 rounded-full bg-brand-violet/20 border border-brand-violet/40 text-purple-300 font-semibold">
                Hub Wizard
              </span>
            </h2>
            <p className="text-xs text-slate-400">Configure os parâmetros gerais e publique diretamente nas APIs oficiais.</p>
          </div>
        </div>

        <form onSubmit={handleSubmit} className="space-y-4">
          {/* Campaign Name */}
          <div>
            <label className="block text-xs font-semibold text-slate-300 mb-1">
              Nome da Campanha
            </label>
            <input 
              type="text"
              required
              placeholder="Ex: [Meta] [CBO] Lançamento Produto X - Setembro 2026"
              value={name}
              onChange={(e) => setName(e.target.value)}
              className="w-full px-3.5 py-2 bg-slate-900/80 border border-slate-700/80 rounded-xl text-xs text-slate-100 focus:border-brand-cyan focus:outline-none transition"
            />
          </div>

          {/* Platform Selector */}
          <div>
            <label className="block text-xs font-semibold text-slate-300 mb-1.5">
              Selecione a Plataforma de Anúncios
            </label>
            <div className="grid grid-cols-2 sm:grid-cols-5 gap-2">
              {platformsList.map(p => (
                <button
                  type="button"
                  key={p}
                  onClick={() => setPlatform(p)}
                  className={`p-2.5 rounded-xl border text-left transition flex flex-col items-center justify-center gap-1 ${
                    platform === p 
                      ? 'bg-brand-cyan/10 border-brand-cyan text-brand-cyan shadow-neon-blue/20 font-bold' 
                      : 'bg-slate-900/50 border-slate-800 text-slate-400 hover:border-slate-700'
                  }`}
                >
                  <PlatformBadge platform={p} showName={true} size="sm" />
                </button>
              ))}
            </div>
          </div>

          {/* Objective Selector */}
          <div>
            <label className="block text-xs font-semibold text-slate-300 mb-1.5">
              Objetivo de Marketing
            </label>
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-2">
              {objectivesList.map(obj => (
                <button
                  type="button"
                  key={obj.key}
                  onClick={() => setObjective(obj.key)}
                  className={`p-3 rounded-xl border text-left transition ${
                    objective === obj.key 
                      ? 'bg-slate-800 border-brand-cyan text-slate-100 shadow-sm' 
                      : 'bg-slate-900/50 border-slate-800 text-slate-400 hover:border-slate-700'
                  }`}
                >
                  <div className="flex items-center justify-between mb-0.5">
                    <span className="text-xs font-bold">{obj.label}</span>
                    {objective === obj.key && <Check className="w-4 h-4 text-brand-cyan" />}
                  </div>
                  <span className="text-[10px] text-slate-400 leading-tight block">{obj.desc}</span>
                </button>
              ))}
            </div>
          </div>

          {/* Daily Budget & Audience */}
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label className="block text-xs font-semibold text-slate-300 mb-1">
                Orçamento Diário (R$)
              </label>
              <div className="relative">
                <span className="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400">R$</span>
                <input 
                  type="number"
                  min="10"
                  step="10"
                  required
                  value={dailyBudget}
                  onChange={(e) => setDailyBudget(Number(e.target.value))}
                  className="w-full pl-8 pr-3.5 py-2 bg-slate-900/80 border border-slate-700/80 rounded-xl text-xs text-slate-100 font-bold focus:border-brand-cyan focus:outline-none transition"
                />
              </div>
            </div>

            <div>
              <label className="block text-xs font-semibold text-slate-300 mb-1">
                Público-Alvo / Segmentação
              </label>
              <input 
                type="text"
                placeholder="Ex: LAL 1% Compradores + Idades 25-45"
                value={targetAudience}
                onChange={(e) => setTargetAudience(e.target.value)}
                className="w-full px-3.5 py-2 bg-slate-900/80 border border-slate-700/80 rounded-xl text-xs text-slate-100 focus:border-brand-cyan focus:outline-none transition"
              />
            </div>
          </div>

          {/* Actions */}
          <div className="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
            <button
              type="button"
              onClick={onClose}
              className="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-300 transition"
            >
              Cancelar
            </button>
            <button
              type="submit"
              className="flex items-center gap-2 px-5 py-2 rounded-xl bg-gradient-to-r from-brand-cyan via-blue-600 to-brand-violet text-slate-950 font-bold text-xs shadow-neon-blue hover:brightness-110 transition active:scale-95"
            >
              <Sparkles className="w-4 h-4" />
              Publicar Campanha
            </button>
          </div>
        </form>
      </div>
    </div>
  );
};
