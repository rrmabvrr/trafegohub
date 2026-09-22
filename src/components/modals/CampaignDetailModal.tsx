import React from 'react';
import { X, Layers, Image, DollarSign, TrendingUp, Target, Calendar } from 'lucide-react';
import { Campaign } from '../../types';
import { PlatformBadge } from '../ui/PlatformBadge';

interface CampaignDetailModalProps {
  campaign: Campaign | null;
  onClose: () => void;
}

export const CampaignDetailModal: React.FC<CampaignDetailModalProps> = ({ campaign, onClose }) => {
  if (!campaign) return null;

  const mockAdSets = [
    { id: 'as-1', name: 'Conjunto 01 - LAL 1% Compradores', budget: campaign.dailyBudget * 0.5, status: 'ACTIVE', spend: campaign.totalSpend * 0.55, conversions: Math.round(campaign.conversions * 0.6), roas: campaign.roas + 0.4 },
    { id: 'as-2', name: 'Conjunto 02 - Interesses Marketing & Ecommerce', budget: campaign.dailyBudget * 0.3, status: 'ACTIVE', spend: campaign.totalSpend * 0.30, conversions: Math.round(campaign.conversions * 0.3), roas: campaign.roas - 0.2 },
    { id: 'as-3', name: 'Conjunto 03 - Retargeting Envolvidos 30D', budget: campaign.dailyBudget * 0.2, status: 'ACTIVE', spend: campaign.totalSpend * 0.15, conversions: Math.round(campaign.conversions * 0.1), roas: campaign.roas + 1.1 }
  ];

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-hub-950/80 backdrop-blur-md">
      <div className="glass-modal w-full max-w-4xl max-h-[90vh] overflow-y-auto rounded-2xl border border-slate-700/80 shadow-2xl p-6 relative">
        <button 
          onClick={onClose}
          className="absolute top-4 right-4 p-2 text-slate-400 hover:text-white rounded-lg hover:bg-slate-800 transition"
        >
          <X className="w-5 h-5" />
        </button>

        {/* Modal Header */}
        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 mb-6 border-b border-slate-800">
          <div>
            <div className="flex items-center gap-2 mb-1">
              <PlatformBadge platform={campaign.platform} />
              <span className={`text-[10px] px-2 py-0.5 rounded-full font-extrabold ${
                campaign.status === 'ACTIVE' 
                  ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' 
                  : 'bg-amber-500/20 text-amber-400 border border-amber-500/30'
              }`}>
                {campaign.status === 'ACTIVE' ? 'EM EXECUÇÃO' : 'PAUSADA'}
              </span>
            </div>
            <h2 className="text-xl font-extrabold text-slate-100">{campaign.name}</h2>
            <p className="text-xs text-slate-400 mt-0.5 flex items-center gap-2">
              <span>Segmentação: {campaign.targetAudience}</span>
              <span>•</span>
              <span>Início: {campaign.startDate}</span>
            </p>
          </div>
        </div>

        {/* KPI Grid */}
        <div className="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
          <div className="glass-panel p-3.5 rounded-xl border border-slate-800">
            <span className="text-[11px] font-semibold text-slate-400">Investimento Total</span>
            <div className="text-lg font-extrabold text-slate-100 mt-1">R$ {campaign.totalSpend.toLocaleString('pt-BR')}</div>
            <span className="text-[10px] text-slate-500">Diário: R$ {campaign.dailyBudget.toFixed(2)}</span>
          </div>

          <div className="glass-panel p-3.5 rounded-xl border border-slate-800">
            <span className="text-[11px] font-semibold text-slate-400">Faturamento Retornado</span>
            <div className="text-lg font-extrabold text-emerald-400 mt-1">R$ {campaign.revenue.toLocaleString('pt-BR')}</div>
            <span className="text-[10px] text-emerald-500 font-semibold">ROAS: {campaign.roas}x</span>
          </div>

          <div className="glass-panel p-3.5 rounded-xl border border-slate-800">
            <span className="text-[11px] font-semibold text-slate-400">Conversões / Leads</span>
            <div className="text-lg font-extrabold text-brand-cyan mt-1">{campaign.conversions}</div>
            <span className="text-[10px] text-slate-400">CPA: R$ {campaign.cpl.toFixed(2)}</span>
          </div>

          <div className="glass-panel p-3.5 rounded-xl border border-slate-800">
            <span className="text-[11px] font-semibold text-slate-400">Taxa de Clique (CTR)</span>
            <div className="text-lg font-extrabold text-purple-400 mt-1">{campaign.ctr}%</div>
            <span className="text-[10px] text-slate-400">CPC: R$ {campaign.cpc.toFixed(2)}</span>
          </div>
        </div>

        {/* Ad Sets Table */}
        <div className="mb-6">
          <h3 className="text-sm font-bold text-slate-200 mb-3 flex items-center gap-2">
            <Layers className="w-4 h-4 text-brand-cyan" />
            Conjuntos de Anúncios ({mockAdSets.length})
          </h3>

          <div className="overflow-x-auto rounded-xl border border-slate-800 bg-slate-900/60">
            <table className="w-full text-left text-xs">
              <thead className="bg-slate-950/80 text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-800">
                <tr>
                  <th className="px-3.5 py-2.5">Conjunto</th>
                  <th className="px-3.5 py-2.5">Orçamento</th>
                  <th className="px-3.5 py-2.5">Gasto</th>
                  <th className="px-3.5 py-2.5">Conversões</th>
                  <th className="px-3.5 py-2.5">ROAS</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-800 text-slate-300">
                {mockAdSets.map(as => (
                  <tr key={as.id} className="hover:bg-slate-800/40">
                    <td className="px-3.5 py-2.5 font-semibold text-slate-200">{as.name}</td>
                    <td className="px-3.5 py-2.5">R$ {as.budget.toFixed(2)}/dia</td>
                    <td className="px-3.5 py-2.5">R$ {as.spend.toLocaleString('pt-BR')}</td>
                    <td className="px-3.5 py-2.5 font-bold text-slate-200">{as.conversions}</td>
                    <td className="px-3.5 py-2.5 font-extrabold text-emerald-400">{as.roas.toFixed(2)}x</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>

        {/* Footer actions */}
        <div className="flex justify-end pt-3 border-t border-slate-800">
          <button
            onClick={onClose}
            className="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-200 transition"
          >
            Fechar Detalhes
          </button>
        </div>
      </div>
    </div>
  );
};
