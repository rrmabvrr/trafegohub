import React, { useState } from 'react';
import { 
  Megaphone, 
  Search, 
  Filter, 
  PlusCircle, 
  Play, 
  Pause, 
  Copy, 
  Edit2, 
  ExternalLink, 
  TrendingUp, 
  DollarSign, 
  SlidersHorizontal,
  Check,
  X
} from 'lucide-react';
import { useApp } from '../context/AppContext';
import { Platform, CampaignStatus, Campaign } from '../types';
import { PlatformBadge } from '../components/ui/PlatformBadge';

interface CampaignsProps {
  onOpenDetail: (c: Campaign) => void;
  onOpenNewCampaignModal: () => void;
}

export const Campaigns: React.FC<CampaignsProps> = ({ onOpenDetail, onOpenNewCampaignModal }) => {
  const { 
    campaigns, 
    toggleCampaignStatus, 
    updateCampaignBudget, 
    duplicateCampaign, 
    selectedPlatform, 
    setSelectedPlatform,
    searchQuery,
    setSearchQuery
  } = useApp();

  const [statusFilter, setStatusFilter] = useState<'ALL' | CampaignStatus>('ALL');
  const [editingBudgetId, setEditingBudgetId] = useState<string | null>(null);
  const [newBudgetValue, setNewBudgetValue] = useState<number>(0);

  // Filter campaigns logic
  const filteredCampaigns = campaigns.filter(c => {
    const matchPlatform = selectedPlatform === 'all' || c.platform === selectedPlatform;
    const matchStatus = statusFilter === 'ALL' || c.status === statusFilter;
    const matchSearch = !searchQuery || 
      c.name.toLowerCase().includes(searchQuery.toLowerCase()) || 
      c.targetAudience.toLowerCase().includes(searchQuery.toLowerCase());
    return matchPlatform && matchStatus && matchSearch;
  });

  const handleStartEditBudget = (c: Campaign) => {
    setEditingBudgetId(c.id);
    setNewBudgetValue(c.dailyBudget);
  };

  const handleSaveBudget = (id: string) => {
    if (newBudgetValue > 0) {
      updateCampaignBudget(id, newBudgetValue);
    }
    setEditingBudgetId(null);
  };

  const platformTabs: { key: Platform | 'all'; label: string }[] = [
    { key: 'all', label: 'Todas as Plataformas' },
    { key: 'meta', label: 'Meta Ads' },
    { key: 'google', label: 'Google Ads' },
    { key: 'tiktok', label: 'TikTok Ads' },
    { key: 'linkedin', label: 'LinkedIn Ads' },
    { key: 'kwai', label: 'Kwai Ads' }
  ];

  return (
    <div className="space-y-6 pb-12">
      {/* Top Banner Header */}
      <div className="glass-panel p-6 rounded-2xl border border-slate-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
          <h1 className="text-2xl font-extrabold text-slate-100 flex items-center gap-2.5">
            <Megaphone className="w-6 h-6 text-brand-cyan" />
            Gestor Unificado de Campanhas
          </h1>
          <p className="text-xs sm:text-sm text-slate-400 mt-1">
            Painel de controle central para ativação, controle de orçamentos e otimização contínua de mídia paga.
          </p>
        </div>

        <button
          onClick={onOpenNewCampaignModal}
          className="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-brand-cyan via-blue-600 to-brand-violet text-slate-950 font-bold text-xs shadow-neon-blue hover:brightness-110 transition active:scale-95"
        >
          <PlusCircle className="w-4 h-4" />
          Nova Campanha Multi-Plataforma
        </button>
      </div>

      {/* Filter Toolbar */}
      <div className="glass-panel p-4 rounded-2xl border border-slate-800 space-y-3">
        {/* Platform Tabs */}
        <div className="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
          {platformTabs.map(tab => (
            <button
              key={tab.key}
              onClick={() => setSelectedPlatform(tab.key)}
              className={`px-3.5 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition ${
                selectedPlatform === tab.key 
                  ? 'bg-brand-cyan text-slate-950 shadow-neon-blue' 
                  : 'bg-slate-900/60 text-slate-400 border border-slate-800 hover:text-slate-200'
              }`}
            >
              {tab.label}
            </button>
          ))}
        </div>

        {/* Status Filter & Search */}
        <div className="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2 border-t border-slate-800/80">
          <div className="flex items-center gap-2 w-full sm:w-auto">
            <span className="text-xs font-semibold text-slate-400 flex items-center gap-1">
              <Filter className="w-3.5 h-3.5 text-brand-cyan" /> Status:
            </span>
            {(['ALL', 'ACTIVE', 'PAUSED'] as const).map(st => (
              <button
                key={st}
                onClick={() => setStatusFilter(st)}
                className={`px-3 py-1 rounded-lg text-xs font-medium transition ${
                  statusFilter === st 
                    ? 'bg-slate-800 text-slate-100 border border-slate-700' 
                    : 'text-slate-400 hover:text-slate-200'
                }`}
              >
                {st === 'ALL' ? 'Todas' : st === 'ACTIVE' ? 'Ativas' : 'Pausadas'}
              </button>
            ))}
          </div>

          <div className="relative w-full sm:w-72">
            <Search className="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
            <input 
              type="text"
              placeholder="Filtrar por nome ou público..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              className="w-full pl-8 pr-3 py-1.5 bg-slate-950/80 border border-slate-800 rounded-xl text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-brand-cyan transition"
            />
          </div>
        </div>
      </div>

      {/* Campaigns Main Table */}
      <div className="glass-panel rounded-2xl border border-slate-800 overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full text-left text-xs">
            <thead className="bg-slate-950/90 text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-800">
              <tr>
                <th className="px-4 py-3.5">Status</th>
                <th className="px-4 py-3.5">Canal & Nome da Campanha</th>
                <th className="px-4 py-3.5">Orçamento Diário</th>
                <th className="px-4 py-3.5">Investido</th>
                <th className="px-4 py-3.5">Cliques & CTR</th>
                <th className="px-4 py-3.5">Conversões</th>
                <th className="px-4 py-3.5">CPA / CPL</th>
                <th className="px-4 py-3.5">ROAS</th>
                <th className="px-4 py-3.5 text-right">Ações</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-800/80 text-slate-300">
              {filteredCampaigns.length === 0 ? (
                <tr>
                  <td colSpan={9} className="px-4 py-8 text-center text-slate-500 text-xs">
                    Nenhuma campanha encontrada para os filtros selecionados.
                  </td>
                </tr>
              ) : (
                filteredCampaigns.map(c => {
                  const isActive = c.status === 'ACTIVE';
                  const isEditingBudget = editingBudgetId === c.id;

                  return (
                    <tr key={c.id} className="hover:bg-slate-800/40 transition">
                      {/* Status Toggle Switch */}
                      <td className="px-4 py-3.5">
                        <button
                          onClick={() => toggleCampaignStatus(c.id)}
                          className={`flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border transition ${
                            isActive 
                              ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/40 hover:bg-emerald-500/30' 
                              : 'bg-slate-800 text-slate-400 border-slate-700 hover:text-slate-200'
                          }`}
                          title="Clique para alternar o status da campanha"
                        >
                          {isActive ? <Play className="w-3 h-3 fill-emerald-400" /> : <Pause className="w-3 h-3" />}
                          <span>{isActive ? 'ATIVA' : 'PAUSADA'}</span>
                        </button>
                      </td>

                      {/* Name & Platform */}
                      <td className="px-4 py-3.5">
                        <div className="flex flex-col gap-1 max-w-xs">
                          <PlatformBadge platform={c.platform} size="sm" />
                          <span className="font-bold text-slate-100 line-clamp-1">{c.name}</span>
                          <span className="text-[10px] text-slate-400 truncate">Público: {c.targetAudience}</span>
                        </div>
                      </td>

                      {/* Daily Budget with inline edit */}
                      <td className="px-4 py-3.5">
                        {isEditingBudget ? (
                          <div className="flex items-center gap-1">
                            <input 
                              type="number"
                              value={newBudgetValue}
                              onChange={(e) => setNewBudgetValue(Number(e.target.value))}
                              className="w-20 px-2 py-1 bg-slate-900 border border-brand-cyan rounded text-xs font-bold text-white focus:outline-none"
                            />
                            <button 
                              onClick={() => handleSaveBudget(c.id)}
                              className="p-1 text-emerald-400 hover:bg-emerald-500/20 rounded"
                            >
                              <Check className="w-4 h-4" />
                            </button>
                            <button 
                              onClick={() => setEditingBudgetId(null)}
                              className="p-1 text-slate-400 hover:bg-slate-800 rounded"
                            >
                              <X className="w-4 h-4" />
                            </button>
                          </div>
                        ) : (
                          <div className="flex items-center gap-1.5 group">
                            <span className="font-bold text-slate-200">R$ {c.dailyBudget.toFixed(2)}</span>
                            <button 
                              onClick={() => handleStartEditBudget(c)}
                              className="opacity-0 group-hover:opacity-100 text-slate-400 hover:text-brand-cyan transition"
                              title="Editar orçamento diário"
                            >
                              <Edit2 className="w-3.5 h-3.5" />
                            </button>
                          </div>
                        )}
                      </td>

                      {/* Spend */}
                      <td className="px-4 py-3.5 font-semibold text-slate-300">
                        R$ {c.totalSpend.toLocaleString('pt-BR')}
                      </td>

                      {/* Clicks & CTR */}
                      <td className="px-4 py-3.5">
                        <div className="flex flex-col">
                          <span className="font-bold text-slate-200">{c.clicks.toLocaleString('pt-BR')} cliques</span>
                          <span className="text-[10px] text-purple-400 font-semibold">CTR {c.ctr}%</span>
                        </div>
                      </td>

                      {/* Conversions */}
                      <td className="px-4 py-3.5 font-extrabold text-slate-100">
                        {c.conversions}
                      </td>

                      {/* CPA / CPL */}
                      <td className="px-4 py-3.5 text-slate-300 font-semibold">
                        R$ {c.cpl.toFixed(2)}
                      </td>

                      {/* ROAS */}
                      <td className="px-4 py-3.5">
                        <span className={`px-2.5 py-1 rounded-full font-extrabold text-xs border ${
                          c.roas >= 4.0 
                            ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30'
                            : c.roas >= 2.5
                            ? 'bg-brand-cyan/20 text-brand-cyan border-brand-cyan/30'
                            : 'bg-amber-500/20 text-amber-400 border-amber-500/30'
                        }`}>
                          {c.roas}x
                        </span>
                      </td>

                      {/* Actions */}
                      <td className="px-4 py-3.5 text-right">
                        <div className="flex items-center justify-end gap-1.5">
                          <button
                            onClick={() => duplicateCampaign(c.id)}
                            className="p-1.5 rounded-lg bg-slate-800/80 hover:bg-slate-700 text-slate-300 hover:text-white transition"
                            title="Duplicar Campanha"
                          >
                            <Copy className="w-3.5 h-3.5" />
                          </button>

                          <button
                            onClick={() => onOpenDetail(c)}
                            className="flex items-center gap-1 px-2.5 py-1 rounded-lg bg-brand-cyan/10 hover:bg-brand-cyan/20 text-brand-cyan border border-brand-cyan/30 font-semibold text-[11px] transition"
                          >
                            <span>Detalhes</span>
                            <ExternalLink className="w-3 h-3" />
                          </button>
                        </div>
                      </td>
                    </tr>
                  );
                })
              )}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
};
