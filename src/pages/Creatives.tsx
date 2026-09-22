import React, { useState } from 'react';
import { Image as ImageIcon, AlertTriangle, CheckCircle2, TrendingUp, Zap, Sparkles, Filter, Video, Layers } from 'lucide-react';
import { useApp } from '../context/AppContext';
import { PlatformBadge } from '../components/ui/PlatformBadge';

export const Creatives: React.FC = () => {
  const { creatives } = useApp();
  const [fatigueFilter, setFatigueFilter] = useState<string>('ALL');

  const filteredCreatives = creatives.filter(c => {
    if (fatigueFilter === 'ALL') return true;
    return c.fatigueLevel === fatigueFilter;
  });

  const criticalCount = creatives.filter(c => c.fatigueLevel === 'CRITICAL').length;
  const warningCount = creatives.filter(c => c.fatigueLevel === 'WARNING').length;

  return (
    <div className="space-y-6 pb-12">
      {/* Header Banner */}
      <div className="glass-panel p-6 rounded-2xl border border-slate-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
          <h1 className="text-2xl font-extrabold text-slate-100 flex items-center gap-2.5">
            <ImageIcon className="w-6 h-6 text-brand-cyan" />
            Central de Inteligência de Criativos
          </h1>
          <p className="text-xs sm:text-sm text-slate-400 mt-1">
            Análise preditiva de retenção de público (Hook Rate), CTR e algoritmos de detecção de fadiga de anúncio.
          </p>
        </div>

        <div className="flex items-center gap-2">
          {criticalCount > 0 && (
            <span className="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-500/20 text-rose-400 border border-rose-500/30 text-xs font-bold animate-pulse">
              <AlertTriangle className="w-4 h-4" />
              {criticalCount} Criativo em Fadiga Crítica
            </span>
          )}
        </div>
      </div>

      {/* AI Recommendation Alert */}
      <div className="glass-panel p-4 rounded-2xl border border-brand-violet/30 bg-gradient-to-r from-brand-violet/10 via-purple-900/10 to-transparent flex items-start gap-3">
        <div className="w-9 h-9 rounded-xl bg-brand-violet/20 border border-brand-violet/40 flex items-center justify-center text-purple-300 flex-shrink-0 mt-0.5">
          <Sparkles className="w-5 h-5 text-brand-cyan" />
        </div>
        <div>
          <h3 className="text-xs font-bold text-slate-200 uppercase tracking-wider">Recomendação do Motor de Inteligência Hub</h3>
          <p className="text-xs text-slate-300 mt-0.5 leading-relaxed">
            O criativo <strong className="text-brand-cyan">Criativo #01 - Vídeo Depoimento</strong> apresenta <strong className="text-emerald-400">Hook Rate de 64.8%</strong> e <strong className="text-emerald-400">ROAS de 5.80x</strong>. 
            Recomenda-se criar 3 variações da imagem inicial para ampliar a escala do orçamento.
          </p>
        </div>
      </div>

      {/* Filter Tabs */}
      <div className="glass-panel p-3.5 rounded-2xl border border-slate-800 flex items-center justify-between">
        <div className="flex items-center gap-2">
          <span className="text-xs font-semibold text-slate-400 flex items-center gap-1">
            <Filter className="w-3.5 h-3.5 text-brand-cyan" /> Nível de Fadiga:
          </span>
          {[
            { key: 'ALL', label: 'Todos os Anúncios' },
            { key: 'GOOD', label: 'Excelente (Sem Fadiga)' },
            { key: 'WARNING', label: 'Atenção' },
            { key: 'CRITICAL', label: 'Fadiga Crítica' }
          ].map(f => (
            <button
              key={f.key}
              onClick={() => setFatigueFilter(f.key)}
              className={`px-3 py-1 rounded-xl text-xs font-semibold transition ${
                fatigueFilter === f.key
                  ? 'bg-brand-cyan text-slate-950 font-bold shadow-neon-blue'
                  : 'bg-slate-900/60 text-slate-400 border border-slate-800 hover:text-white'
              }`}
            >
              {f.label}
            </button>
          ))}
        </div>
      </div>

      {/* Creative Assets Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        {filteredCreatives.map(c => {
          const isCritical = c.fatigueLevel === 'CRITICAL';
          const isWarning = c.fatigueLevel === 'WARNING';

          return (
            <div key={c.id} className="glass-panel-interactive rounded-2xl overflow-hidden border border-slate-800 flex flex-col justify-between">
              {/* Asset Media Preview */}
              <div className="relative h-44 bg-slate-950 overflow-hidden">
                <img 
                  src={c.thumbnailUrl} 
                  alt={c.name} 
                  className="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-hub-950 via-transparent to-black/40" />

                <div className="absolute top-3 left-3">
                  <PlatformBadge platform={c.platform} size="sm" />
                </div>

                <div className="absolute top-3 right-3">
                  <span className={`px-2 py-0.5 rounded-full text-[10px] font-extrabold border ${
                    isCritical 
                      ? 'bg-rose-500/80 text-white border-rose-400 animate-pulse'
                      : isWarning
                      ? 'bg-amber-500/80 text-white border-amber-400'
                      : 'bg-emerald-500/80 text-white border-emerald-400'
                  }`}>
                    {isCritical ? 'FADIGA CRÍTICA' : isWarning ? 'ATENÇÃO' : 'EXCELENTE'}
                  </span>
                </div>

                <div className="absolute bottom-3 left-3 right-3 flex items-center justify-between text-xs">
                  <span className="px-2 py-0.5 rounded bg-black/60 backdrop-blur text-slate-300 font-mono text-[10px] uppercase flex items-center gap-1">
                    {c.type === 'video' ? <Video className="w-3 h-3 text-brand-cyan" /> : <Layers className="w-3 h-3 text-purple-400" />}
                    {c.type}
                  </span>
                  <span className="font-extrabold text-emerald-400 bg-black/60 px-2 py-0.5 rounded backdrop-blur">
                    ROAS {c.roas}x
                  </span>
                </div>
              </div>

              {/* Creative Details */}
              <div className="p-4 flex-1 flex flex-col justify-between">
                <div>
                  <h3 className="font-bold text-slate-100 text-xs line-clamp-2 mb-1">{c.name}</h3>
                  <p className="text-[10px] text-slate-400 truncate mb-3">{c.campaignName}</p>

                  <div className="grid grid-cols-2 gap-2 text-xs py-2 border-t border-b border-slate-800/80 mb-3">
                    <div className="bg-slate-900/60 p-2 rounded-lg border border-slate-800/50">
                      <span className="text-[10px] text-slate-400 block">CTR</span>
                      <span className="font-extrabold text-brand-cyan">{c.ctr}%</span>
                    </div>
                    <div className="bg-slate-900/60 p-2 rounded-lg border border-slate-800/50">
                      <span className="text-[10px] text-slate-400 block">Hook Rate (3s)</span>
                      <span className="font-extrabold text-purple-400">{c.hookRate}%</span>
                    </div>
                    <div className="bg-slate-900/60 p-2 rounded-lg border border-slate-800/50">
                      <span className="text-[10px] text-slate-400 block">Frequência</span>
                      <span className="font-extrabold text-slate-200">{c.frequency}x</span>
                    </div>
                    <div className="bg-slate-900/60 p-2 rounded-lg border border-slate-800/50">
                      <span className="text-[10px] text-slate-400 block">Gasto Total</span>
                      <span className="font-extrabold text-slate-200">R$ {c.spend}</span>
                    </div>
                  </div>

                  {c.fatigueReason && (
                    <div className="p-2.5 rounded-xl bg-rose-500/10 border border-rose-500/30 text-[11px] text-rose-300 leading-snug">
                      <AlertTriangle className="w-3.5 h-3.5 inline text-rose-400 mr-1" />
                      {c.fatigueReason}
                    </div>
                  )}
                </div>
              </div>
            </div>
          );
        })}
      </div>
    </div>
  );
};
