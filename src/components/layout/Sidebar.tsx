import React from 'react';
import { 
  LayoutDashboard, 
  Megaphone, 
  Network, 
  Users, 
  Image, 
  Zap, 
  FileSpreadsheet, 
  ShieldCheck,
  ChevronRight,
  TrendingUp
} from 'lucide-react';
import { useApp } from '../../context/AppContext';

interface SidebarProps {
  collapsed: boolean;
  setCollapsed: (c: boolean) => void;
}

export const Sidebar: React.FC<SidebarProps> = ({ collapsed }) => {
  const { activeTab, setActiveTab, campaigns, automationRules, notifications } = useApp();

  const unreadNotifsCount = notifications.filter(n => !n.isRead).length;
  const activeCampaignsCount = campaigns.filter(c => c.status === 'ACTIVE').length;
  const activeRulesCount = automationRules.filter(r => r.isEnabled).length;

  const navItems = [
    {
      id: 'dashboard',
      label: 'Painel Geral',
      icon: LayoutDashboard,
      badge: null
    },
    {
      id: 'campaigns',
      label: 'Gestor de Campanhas',
      icon: Megaphone,
      badge: activeCampaignsCount > 0 ? `${activeCampaignsCount} ativas` : null,
      badgeColor: 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30'
    },
    {
      id: 'integrations',
      label: 'Conexões & APIs',
      icon: Network,
      badge: '4 Conectadas',
      badgeColor: 'bg-blue-500/20 text-blue-400 border-blue-500/30'
    },
    {
      id: 'leads',
      label: 'Central de Leads',
      icon: Users,
      badge: 'CRM',
      badgeColor: 'bg-purple-500/20 text-purple-300 border-purple-500/30'
    },
    {
      id: 'creatives',
      label: 'Análise de Criativos',
      icon: Image,
      badge: 'Fadiga Alert',
      badgeColor: 'bg-rose-500/20 text-rose-400 border-rose-500/30'
    },
    {
      id: 'automation',
      label: 'Automação & Regras',
      icon: Zap,
      badge: `${activeRulesCount} Regras`,
      badgeColor: 'bg-amber-500/20 text-amber-400 border-amber-500/30'
    },
    {
      id: 'reports',
      label: 'Relatórios & PDF',
      icon: FileSpreadsheet,
      badge: null
    }
  ];

  return (
    <aside className={`fixed left-0 top-0 bottom-0 z-30 transition-all duration-300 ${collapsed ? 'w-20' : 'w-64'} bg-hub-900/90 backdrop-blur-xl border-r border-slate-800/80 flex flex-col justify-between`}>
      {/* Brand Header */}
      <div>
        <div className="h-16 flex items-center px-4 border-b border-slate-800/80 gap-3">
          <div className="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-cyan via-blue-600 to-brand-violet flex items-center justify-center text-white font-extrabold text-xl shadow-neon-blue flex-shrink-0">
            TH
          </div>
          {!collapsed && (
            <div className="flex flex-col overflow-hidden">
              <span className="font-extrabold text-lg tracking-tight gradient-text-cyan leading-none">
                TRAFEGO<span className="text-white">HUB</span>
              </span>
              <span className="text-[10px] text-slate-400 font-medium tracking-wider uppercase mt-0.5 flex items-center gap-1">
                <span className="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse" />
                V2.5 Enterprise
              </span>
            </div>
          )}
        </div>

        {/* Navigation Menu */}
        <nav className="p-3 space-y-1.5">
          {navItems.map((item) => {
            const Icon = item.icon;
            const isActive = activeTab === item.id;

            return (
              <button
                key={item.id}
                onClick={() => setActiveTab(item.id)}
                className={`w-full flex items-center ${collapsed ? 'justify-center px-0' : 'justify-between px-3'} py-2.5 rounded-xl text-sm font-medium transition-all duration-200 group ${
                  isActive 
                    ? 'bg-gradient-to-r from-brand-cyan/20 to-brand-violet/10 text-brand-cyan border border-brand-cyan/30 shadow-neon-blue/20' 
                    : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50 hover:border-slate-700/50 border border-transparent'
                }`}
                title={collapsed ? item.label : undefined}
              >
                <div className="flex items-center gap-3">
                  <Icon className={`w-5 h-5 transition-transform duration-200 group-hover:scale-110 ${isActive ? 'text-brand-cyan' : 'text-slate-400 group-hover:text-slate-200'}`} />
                  {!collapsed && <span className="truncate">{item.label}</span>}
                </div>

                {!collapsed && item.badge && (
                  <span className={`text-[10px] px-2 py-0.5 rounded-full border font-semibold ${item.badgeColor}`}>
                    {item.badge}
                  </span>
                )}
              </button>
            );
          })}
        </nav>
      </div>

      {/* Sidebar Footer Widget */}
      <div className="p-3 border-t border-slate-800/80">
        {!collapsed ? (
          <div className="glass-panel p-3.5 rounded-xl border border-brand-cyan/20 relative overflow-hidden group">
            <div className="absolute -right-4 -bottom-4 w-20 h-20 bg-brand-cyan/10 rounded-full blur-xl group-hover:bg-brand-cyan/20 transition-all" />
            <div className="flex items-center justify-between mb-2">
              <span className="text-xs font-semibold text-slate-300 flex items-center gap-1.5">
                <TrendingUp className="w-3.5 h-3.5 text-brand-cyan" />
                ROAS Médio do Hub
              </span>
              <span className="text-xs font-extrabold text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-full border border-emerald-500/30">
                5.42x
              </span>
            </div>
            <p className="text-[11px] text-slate-400 leading-snug">
              Investimento total otimizado via inteligência de campanhas.
            </p>
          </div>
        ) : (
          <div className="flex justify-center py-2" title="Conexão API Segura">
            <ShieldCheck className="w-6 h-6 text-emerald-400" />
          </div>
        )}
      </div>
    </aside>
  );
};
