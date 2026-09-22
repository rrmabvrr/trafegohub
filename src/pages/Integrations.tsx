import React from 'react';
import { Network, RefreshCw, Key, ShieldCheck, CheckCircle2, AlertTriangle, ExternalLink, Globe, Copy, Check } from 'lucide-react';
import { useApp } from '../context/AppContext';
import { Integration } from '../types';
import { PlatformBadge } from '../components/ui/PlatformBadge';

interface IntegrationsProps {
  onOpenConnectModal: (integration: Integration) => void;
}

export const Integrations: React.FC<IntegrationsProps> = ({ onOpenConnectModal }) => {
  const { integrations, triggerSync, isSyncing } = useApp();

  const connectedCount = integrations.filter(i => i.status === 'CONNECTED').length;

  return (
    <div className="space-y-6 pb-12">
      {/* Header Banner */}
      <div className="glass-panel p-6 rounded-2xl border border-slate-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
          <h1 className="text-2xl font-extrabold text-slate-100 flex items-center gap-2.5">
            <Network className="w-6 h-6 text-brand-cyan" />
            Hub de Conexões e APIs de Publicidade
          </h1>
          <p className="text-xs sm:text-sm text-slate-400 mt-1">
            Gerenciamento central de tokens OAuth, chaves de API oficiais e Endpoints de Webhook.
          </p>
        </div>

        <div className="flex items-center gap-3">
          <button
            onClick={() => triggerSync()}
            disabled={isSyncing}
            className={`flex items-center gap-2 px-4 py-2.5 rounded-xl border font-bold text-xs transition ${
              isSyncing
                ? 'bg-brand-violet/20 border-brand-violet/40 text-brand-violet animate-pulse'
                : 'bg-brand-cyan text-slate-950 shadow-neon-blue hover:brightness-110'
            }`}
          >
            <RefreshCw className={`w-4 h-4 ${isSyncing ? 'animate-spin' : ''}`} />
            <span>{isSyncing ? 'Sincronizando Tokens...' : 'Forçar Re-Sincronização API'}</span>
          </button>
        </div>
      </div>

      {/* Connection Overview Stats */}
      <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div className="glass-panel p-4 rounded-xl border border-slate-800 flex items-center gap-3">
          <div className="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400">
            <CheckCircle2 className="w-5 h-5" />
          </div>
          <div>
            <span className="text-xs text-slate-400 font-medium">Conexões Ativas</span>
            <div className="text-lg font-extrabold text-slate-100">{connectedCount} de {integrations.length} Canais</div>
          </div>
        </div>

        <div className="glass-panel p-4 rounded-xl border border-slate-800 flex items-center gap-3">
          <div className="w-10 h-10 rounded-xl bg-blue-500/10 border border-blue-500/30 flex items-center justify-center text-brand-cyan">
            <ShieldCheck className="w-5 h-5" />
          </div>
          <div>
            <span className="text-xs text-slate-400 font-medium">Protocolo de Segurança</span>
            <div className="text-lg font-extrabold text-slate-100">OAuth 2.0 SSL 256-bit</div>
          </div>
        </div>

        <div className="glass-panel p-4 rounded-xl border border-slate-800 flex items-center gap-3">
          <div className="w-10 h-10 rounded-xl bg-purple-500/10 border border-purple-500/30 flex items-center justify-center text-purple-400">
            <Globe className="w-5 h-5" />
          </div>
          <div>
            <span className="text-xs text-slate-400 font-medium">Status do Servidor Hub</span>
            <div className="text-lg font-extrabold text-emerald-400 flex items-center gap-1.5">
              <span className="w-2 h-2 rounded-full bg-emerald-400 animate-pulse" />
              Operacional (99.9%)
            </div>
          </div>
        </div>
      </div>

      {/* Integrations Cards Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        {integrations.map(item => {
          const isConnected = item.status === 'CONNECTED';

          return (
            <div key={item.id} className="glass-panel-interactive p-5 rounded-2xl flex flex-col justify-between relative overflow-hidden">
              <div>
                <div className="flex items-center justify-between mb-3">
                  <PlatformBadge platform={item.platform} size="lg" />
                  <span className={`text-[10px] px-2.5 py-1 rounded-full font-bold border ${
                    isConnected 
                      ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30' 
                      : 'bg-rose-500/20 text-rose-400 border-rose-500/30'
                  }`}>
                    {isConnected ? 'CONECTADO' : 'DESCONECTADO'}
                  </span>
                </div>

                <h3 className="font-bold text-slate-100 text-sm mb-1">{item.name}</h3>
                <p className="text-xs text-slate-400 mb-3">{item.adAccountName}</p>

                <div className="space-y-1.5 text-xs text-slate-300 py-2 border-t border-b border-slate-800/80 mb-4">
                  <div className="flex justify-between">
                    <span className="text-slate-400">ID da Conta:</span>
                    <span className="font-mono text-slate-200">{item.accountId}</span>
                  </div>
                  <div className="flex justify-between">
                    <span className="text-slate-400">Última Sincronização:</span>
                    <span className="font-semibold text-brand-cyan">{item.lastSync}</span>
                  </div>
                  <div className="flex justify-between">
                    <span className="text-slate-400">Expiração do Token:</span>
                    <span className="text-slate-300">{item.tokenExpiry}</span>
                  </div>
                  <div className="flex justify-between">
                    <span className="text-slate-400">Campanhas Ativas:</span>
                    <span className="font-bold text-emerald-400">{item.activeCampaignsCount}</span>
                  </div>
                </div>
              </div>

              <div className="flex items-center gap-2">
                <button
                  onClick={() => onOpenConnectModal(item)}
                  className="w-full flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-bold text-slate-200 border border-slate-700 transition"
                >
                  <Key className="w-3.5 h-3.5 text-brand-cyan" />
                  Configurar Credenciais
                </button>
              </div>
            </div>
          );
        })}
      </div>
    </div>
  );
};
