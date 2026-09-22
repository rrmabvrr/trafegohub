import React from 'react';
import { 
  DollarSign, 
  TrendingUp, 
  Target, 
  Users, 
  ArrowUpRight, 
  ArrowDownRight, 
  Sparkles, 
  Zap, 
  Megaphone, 
  ShieldCheck,
  Award
} from 'lucide-react';
import { useApp } from '../context/AppContext';
import { SpendVsRevenueChart } from '../components/charts/SpendVsRevenueChart';
import { PlatformDistributionChart } from '../components/charts/PlatformDistributionChart';
import { ROASComparisonChart } from '../components/charts/ROASComparisonChart';
import { FunnelConversionChart } from '../components/charts/FunnelConversionChart';
import { PlatformBadge } from '../components/ui/PlatformBadge';

export const Dashboard: React.FC<{ onOpenDetail: (c: any) => void }> = ({ onOpenDetail }) => {
  const { campaigns, integrations, leads, setActiveTab } = useApp();

  const totalSpend = campaigns.reduce((acc, c) => acc + c.totalSpend, 0);
  const totalRevenue = campaigns.reduce((acc, c) => acc + c.revenue, 0);
  const globalROAS = totalSpend > 0 ? (totalRevenue / totalSpend).toFixed(2) : '0';
  const totalConversions = campaigns.reduce((acc, c) => acc + c.conversions, 0);
  const avgCPA = totalConversions > 0 ? (totalSpend / totalConversions).toFixed(2) : '0';
  const totalClicks = campaigns.reduce((acc, c) => acc + c.clicks, 0);
  const totalImpressions = campaigns.reduce((acc, c) => acc + c.impressions, 0);
  const globalCTR = totalImpressions > 0 ? ((totalClicks / totalImpressions) * 100).toFixed(2) : '0';

  // Top 4 performing campaigns by ROAS
  const topCampaigns = [...campaigns].sort((a, b) => b.roas - a.roas).slice(0, 4);

  return (
    <div className="space-y-6 pb-12">
      {/* Welcome Banner */}
      <div className="glass-panel p-6 rounded-2xl border border-brand-cyan/20 relative overflow-hidden flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div className="absolute -top-24 -right-24 w-72 h-72 bg-gradient-to-br from-brand-cyan/20 to-brand-violet/20 rounded-full blur-3xl pointer-events-none" />
        <div>
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-cyan/10 border border-brand-cyan/30 text-brand-cyan text-xs font-semibold mb-2">
            <Sparkles className="w-3.5 h-3.5" />
            Visão Geral de Desempenho Multi-Canal
          </div>
          <h1 className="text-2xl sm:text-3xl font-extrabold text-slate-100 tracking-tight">
            Painel Central de Inteligência de Anúncios
          </h1>
          <p className="text-xs sm:text-sm text-slate-400 mt-1 max-w-2xl">
            Métricas unificadas de Meta Ads, Google Ads, TikTok Ads e LinkedIn Ads em tempo real.
          </p>
        </div>

        <div className="flex items-center gap-3">
          <button 
            onClick={() => setActiveTab('campaigns')}
            className="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-brand-cyan to-blue-600 text-slate-950 font-bold text-xs shadow-neon-blue hover:brightness-110 transition active:scale-95"
          >
            <Megaphone className="w-4 h-4" />
            Gerenciar Campanhas
          </button>
        </div>
      </div>

      {/* Primary KPI Grid */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {/* KPI 1: Investimento Total */}
        <div className="glass-panel-interactive p-5 rounded-2xl relative overflow-hidden">
          <div className="flex items-center justify-between mb-3">
            <span className="text-xs font-semibold text-slate-400 uppercase tracking-wider">Investimento Total</span>
            <div className="w-9 h-9 rounded-xl bg-blue-500/10 border border-blue-500/30 flex items-center justify-center text-brand-cyan">
              <DollarSign className="w-5 h-5" />
            </div>
          </div>
          <div className="text-2xl font-extrabold text-slate-100">
            R$ {totalSpend.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}
          </div>
          <div className="flex items-center gap-1.5 mt-2 text-xs">
            <span className="flex items-center font-bold text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-full">
              <ArrowUpRight className="w-3.5 h-3.5 mr-0.5" />
              +12.4%
            </span>
            <span className="text-slate-400">vs. período anterior</span>
          </div>
        </div>

        {/* KPI 2: Faturamento Retornado */}
        <div className="glass-panel-interactive p-5 rounded-2xl relative overflow-hidden">
          <div className="flex items-center justify-between mb-3">
            <span className="text-xs font-semibold text-slate-400 uppercase tracking-wider">Faturamento (Vendas)</span>
            <div className="w-9 h-9 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400">
              <TrendingUp className="w-5 h-5" />
            </div>
          </div>
          <div className="text-2xl font-extrabold text-emerald-400">
            R$ {totalRevenue.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}
          </div>
          <div className="flex items-center gap-1.5 mt-2 text-xs">
            <span className="flex items-center font-bold text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-full">
              <ArrowUpRight className="w-3.5 h-3.5 mr-0.5" />
              +24.8%
            </span>
            <span className="text-slate-400">retorno direto</span>
          </div>
        </div>

        {/* KPI 3: ROAS Global */}
        <div className="glass-panel-interactive p-5 rounded-2xl relative overflow-hidden">
          <div className="flex items-center justify-between mb-3">
            <span className="text-xs font-semibold text-slate-400 uppercase tracking-wider">ROAS Unificado</span>
            <div className="w-9 h-9 rounded-xl bg-purple-500/10 border border-purple-500/30 flex items-center justify-center text-brand-violet">
              <Zap className="w-5 h-5 text-purple-400" />
            </div>
          </div>
          <div className="text-2xl font-extrabold gradient-text-brand">
            {globalROAS}x
          </div>
          <div className="flex items-center gap-1.5 mt-2 text-xs">
            <span className="flex items-center font-bold text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-full">
              <ArrowUpRight className="w-3.5 h-3.5 mr-0.5" />
              +0.85x
            </span>
            <span className="text-slate-400">eficiência acumulada</span>
          </div>
        </div>

        {/* KPI 4: CPA Médio & Leads */}
        <div className="glass-panel-interactive p-5 rounded-2xl relative overflow-hidden">
          <div className="flex items-center justify-between mb-3">
            <span className="text-xs font-semibold text-slate-400 uppercase tracking-wider">CPA Médio por Venda</span>
            <div className="w-9 h-9 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400">
              <Target className="w-5 h-5" />
            </div>
          </div>
          <div className="text-2xl font-extrabold text-slate-100">
            R$ {avgCPA}
          </div>
          <div className="flex items-center gap-1.5 mt-2 text-xs">
            <span className="flex items-center font-bold text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-full">
              <ArrowDownRight className="w-3.5 h-3.5 mr-0.5" />
              -8.3%
            </span>
            <span className="text-slate-400">{totalConversions} conversões</span>
          </div>
        </div>
      </div>

      {/* Main Charts Section: Spend vs Revenue & Platform Distribution */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Main Timeline Chart */}
        <div className="lg:col-span-2 glass-panel p-5 rounded-2xl border border-slate-800">
          <div className="flex items-center justify-between mb-4">
            <div>
              <h2 className="text-base font-bold text-slate-100 flex items-center gap-2">
                <TrendingUp className="w-4 h-4 text-brand-cyan" />
                Evolução Temporal: Investimento vs. Retorno
              </h2>
              <p className="text-xs text-slate-400">Comparativo acumulado de gastos e faturamento nos últimos dias</p>
            </div>
            <span className="text-[11px] px-2.5 py-1 rounded-full bg-slate-800 text-slate-300 font-semibold">
              ROAS Médio: {globalROAS}x
            </span>
          </div>
          <SpendVsRevenueChart />
        </div>

        {/* Platform Spend Distribution */}
        <div className="glass-panel p-5 rounded-2xl border border-slate-800 flex flex-col justify-between">
          <div>
            <h2 className="text-base font-bold text-slate-100 mb-1">
              Distribuição do Orçamento
            </h2>
            <p className="text-xs text-slate-400 mb-2">Divisão percentual de gastos por canal publicitário</p>
            <PlatformDistributionChart />
          </div>
        </div>
      </div>

      {/* ROAS Comparison & Funnel Conversion Section */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* ROAS by Platform Bar Chart */}
        <div className="glass-panel p-5 rounded-2xl border border-slate-800">
          <div className="flex items-center justify-between mb-4">
            <div>
              <h2 className="text-base font-bold text-slate-100">
                Ranking de Eficiência (ROAS por Canal)
              </h2>
              <p className="text-xs text-slate-400">Retorno sobre investimento em cada plataforma de publicidade</p>
            </div>
          </div>
          <ROASComparisonChart />
        </div>

        {/* Funnel Conversion */}
        <div className="glass-panel p-5 rounded-2xl border border-slate-800">
          <div className="flex items-center justify-between mb-4">
            <div>
              <h2 className="text-base font-bold text-slate-100">
                Funil Global de Conversão
              </h2>
              <p className="text-xs text-slate-400">Visualização de etapas de atração, clique, captura e conversão final</p>
            </div>
            <span className="text-xs font-bold text-brand-cyan bg-brand-cyan/10 px-2.5 py-1 rounded-full border border-brand-cyan/30">
              CTR Médio {globalCTR}%
            </span>
          </div>
          <FunnelConversionChart />
        </div>
      </div>

      {/* Top Performing Campaigns Table */}
      <div className="glass-panel p-5 rounded-2xl border border-slate-800">
        <div className="flex items-center justify-between mb-4">
          <div className="flex items-center gap-2">
            <Award className="w-5 h-5 text-amber-400" />
            <h2 className="text-base font-bold text-slate-100">Campanhas com Maior Desempenho (Top ROAS)</h2>
          </div>
          <button 
            onClick={() => setActiveTab('campaigns')}
            className="text-xs font-semibold text-brand-cyan hover:underline"
          >
            Ver Todas ({campaigns.length}) →
          </button>
        </div>

        <div className="overflow-x-auto rounded-xl border border-slate-800 bg-slate-900/60">
          <table className="w-full text-left text-xs">
            <thead className="bg-slate-950/80 text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-800">
              <tr>
                <th className="px-4 py-3">Plataforma & Nome</th>
                <th className="px-4 py-3">Orçamento Diário</th>
                <th className="px-4 py-3">Investido</th>
                <th className="px-4 py-3">Conversões</th>
                <th className="px-4 py-3">CPA</th>
                <th className="px-4 py-3">ROAS</th>
                <th className="px-4 py-3 text-right">Ação</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-800 text-slate-300">
              {topCampaigns.map(c => (
                <tr key={c.id} className="hover:bg-slate-800/40 transition">
                  <td className="px-4 py-3">
                    <div className="flex items-center gap-2">
                      <PlatformBadge platform={c.platform} size="sm" />
                      <span className="font-bold text-slate-200 line-clamp-1">{c.name}</span>
                    </div>
                  </td>
                  <td className="px-4 py-3 font-semibold">R$ {c.dailyBudget.toFixed(2)}</td>
                  <td className="px-4 py-3">R$ {c.totalSpend.toLocaleString('pt-BR')}</td>
                  <td className="px-4 py-3 font-extrabold text-slate-100">{c.conversions}</td>
                  <td className="px-4 py-3 font-semibold text-slate-300">R$ {c.cpl.toFixed(2)}</td>
                  <td className="px-4 py-3">
                    <span className="px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 font-extrabold">
                      {c.roas}x
                    </span>
                  </td>
                  <td className="px-4 py-3 text-right">
                    <button 
                      onClick={() => onOpenDetail(c)}
                      className="text-xs text-brand-cyan hover:underline font-semibold"
                    >
                      Detalhes
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
};
