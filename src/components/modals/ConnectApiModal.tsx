import React, { useState } from 'react';
import { X, Network, Key, CheckCircle, ShieldCheck, Copy, Check, RefreshCw } from 'lucide-react';
import { Integration, Platform } from '../../types';
import { PlatformBadge } from '../ui/PlatformBadge';
import { useApp } from '../../context/AppContext';

interface ConnectApiModalProps {
  integration: Integration | null;
  onClose: () => void;
}

export const ConnectApiModal: React.FC<ConnectApiModalProps> = ({ integration, onClose }) => {
  const { toggleIntegrationStatus, triggerSync } = useApp();
  const [copied, setCopied] = useState(false);
  const [apiKeyInput, setApiKeyInput] = useState('th_live_sec_901824091823901823');

  if (!integration) return null;

  const handleCopyWebhook = () => {
    if (integration.webhookUrl) {
      navigator.clipboard.writeText(integration.webhookUrl);
      setCopied(true);
      setTimeout(() => setCopied(false), 2000);
    }
  };

  const handleToggle = () => {
    toggleIntegrationStatus(integration.id);
    onClose();
  };

  const handleTestSync = () => {
    triggerSync(integration.id);
    onClose();
  };

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-hub-950/80 backdrop-blur-md">
      <div className="glass-modal w-full max-w-lg rounded-2xl border border-slate-700/80 shadow-2xl p-6 relative">
        <button 
          onClick={onClose}
          className="absolute top-4 right-4 p-2 text-slate-400 hover:text-white rounded-lg hover:bg-slate-800 transition"
        >
          <X className="w-5 h-5" />
        </button>

        <div className="flex items-center gap-3 mb-4">
          <div className="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center">
            <PlatformBadge platform={integration.platform} showName={false} size="lg" />
          </div>
          <div>
            <h2 className="text-lg font-bold text-slate-100">{integration.name}</h2>
            <p className="text-xs text-slate-400">ID da Conta: {integration.accountId}</p>
          </div>
        </div>

        {/* Status indicator */}
        <div className="p-3 rounded-xl bg-slate-900/60 border border-slate-800 mb-4 flex items-center justify-between">
          <div className="flex items-center gap-2">
            <span className={`w-2.5 h-2.5 rounded-full ${integration.status === 'CONNECTED' ? 'bg-emerald-400 animate-pulse' : 'bg-rose-500'}`} />
            <span className="text-xs font-semibold text-slate-200">
              {integration.status === 'CONNECTED' ? 'Conexão API Ativa & Autenticada' : 'Desconectado'}
            </span>
          </div>
          <span className="text-[11px] text-slate-400">{integration.lastSync}</span>
        </div>

        {/* Credentials Form */}
        <div className="space-y-3.5 mb-5">
          <div>
            <label className="block text-xs font-semibold text-slate-300 mb-1">
              Nome da Conta de Anúncios (Ad Account)
            </label>
            <input 
              type="text" 
              readOnly
              value={integration.adAccountName}
              className="w-full px-3.5 py-2 bg-slate-900/80 border border-slate-800 rounded-xl text-xs text-slate-300"
            />
          </div>

          <div>
            <label className="block text-xs font-semibold text-slate-300 mb-1">
              Access Token / Chave de API Oficial
            </label>
            <div className="relative">
              <input 
                type="password" 
                value={apiKeyInput}
                onChange={(e) => setApiKeyInput(e.target.value)}
                className="w-full px-3.5 py-2 bg-slate-900/80 border border-slate-700/80 rounded-xl text-xs text-slate-200 font-mono focus:border-brand-cyan focus:outline-none"
              />
            </div>
          </div>

          {integration.webhookUrl && (
            <div>
              <label className="block text-xs font-semibold text-slate-300 mb-1">
                URL de Webhook para Ingestão de Leads
              </label>
              <div className="flex gap-2">
                <input 
                  type="text" 
                  readOnly
                  value={integration.webhookUrl}
                  className="w-full px-3.5 py-2 bg-slate-900/80 border border-slate-800 rounded-xl text-[11px] text-brand-cyan font-mono"
                />
                <button
                  type="button"
                  onClick={handleCopyWebhook}
                  className="px-3 py-2 bg-slate-800 hover:bg-slate-700 rounded-xl text-xs text-slate-200 font-semibold flex items-center gap-1.5 transition"
                >
                  {copied ? <Check className="w-4 h-4 text-emerald-400" /> : <Copy className="w-4 h-4" />}
                </button>
              </div>
            </div>
          )}
        </div>

        {/* Action Buttons */}
        <div className="flex items-center justify-between pt-3 border-t border-slate-800">
          <button
            type="button"
            onClick={handleToggle}
            className={`px-4 py-2 rounded-xl text-xs font-bold transition ${
              integration.status === 'CONNECTED'
                ? 'bg-rose-500/20 text-rose-400 border border-rose-500/30 hover:bg-rose-500/30'
                : 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 hover:bg-emerald-500/30'
            }`}
          >
            {integration.status === 'CONNECTED' ? 'Desconectar API' : 'Conectar Agora'}
          </button>

          <button
            type="button"
            onClick={handleTestSync}
            className="flex items-center gap-2 px-4 py-2 rounded-xl bg-brand-cyan text-slate-950 text-xs font-bold hover:brightness-110 transition shadow-neon-blue"
          >
            <RefreshCw className="w-3.5 h-3.5" />
            Testar Conexão
          </button>
        </div>
      </div>
    </div>
  );
};
