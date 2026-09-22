import React, { useState } from 'react';
import { 
  Bell, 
  RefreshCw, 
  Calendar, 
  ChevronDown, 
  Search, 
  Building2, 
  User, 
  Check, 
  Menu, 
  Sparkles,
  SlidersHorizontal,
  PlusCircle
} from 'lucide-react';
import { useApp } from '../../context/AppContext';
import { DateRangeKey } from '../../types';

interface HeaderProps {
  collapsed: boolean;
  setCollapsed: (c: boolean) => void;
  onOpenNewCampaignModal: () => void;
}

export const Header: React.FC<HeaderProps> = ({ collapsed, setCollapsed, onOpenNewCampaignModal }) => {
  const { 
    workspaces, 
    currentWorkspace, 
    setCurrentWorkspace, 
    dateRange, 
    setDateRange, 
    isSyncing, 
    triggerSync, 
    notifications,
    markAllNotificationsRead,
    searchQuery,
    setSearchQuery
  } = useApp();

  const [showWorkspaceMenu, setShowWorkspaceMenu] = useState(false);
  const [showNotifMenu, setShowNotifMenu] = useState(false);
  const [showDateMenu, setShowDateMenu] = useState(false);

  const unreadNotifs = notifications.filter(n => !n.isRead);

  const dateOptions: { key: DateRangeKey; label: string }[] = [
    { key: 'today', label: 'Hoje (Em tempo real)' },
    { key: '7d', label: 'Últimos 7 dias' },
    { key: '30d', label: 'Últimos 30 dias' },
    { key: 'month', label: 'Este Mês' },
    { key: 'compare', label: 'Comparar Períodos' }
  ];

  return (
    <header className="h-16 bg-hub-900/80 backdrop-blur-xl border-b border-slate-800/80 sticky top-0 z-20 px-4 flex items-center justify-between gap-4">
      {/* Left side: Toggle Sidebar + Workspace Switcher */}
      <div className="flex items-center gap-3">
        <button 
          onClick={() => setCollapsed(!collapsed)}
          className="p-2 text-slate-400 hover:text-white rounded-lg hover:bg-slate-800/60 transition"
        >
          <Menu className="w-5 h-5" />
        </button>

        {/* Workspace Switcher */}
        <div className="relative">
          <button
            onClick={() => setShowWorkspaceMenu(!showWorkspaceMenu)}
            className="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-800/40 hover:bg-slate-800/80 border border-slate-700/60 transition text-left"
          >
            <div className="w-7 h-7 rounded-lg bg-brand-cyan/10 border border-brand-cyan/30 flex items-center justify-center text-brand-cyan">
              <Building2 className="w-4 h-4" />
            </div>
            <div className="hidden sm:flex flex-col">
              <span className="text-xs font-semibold text-slate-200 line-clamp-1">{currentWorkspace.clientName}</span>
              <span className="text-[10px] text-slate-400">{currentWorkspace.name}</span>
            </div>
            <ChevronDown className="w-4 h-4 text-slate-400 ml-1" />
          </button>

          {showWorkspaceMenu && (
            <div className="absolute top-full left-0 mt-2 w-64 glass-modal rounded-xl border border-slate-700/80 shadow-2xl p-1.5 z-50">
              <div className="px-3 py-1.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                Contas de Clientes / Workspaces
              </div>
              <div className="space-y-1">
                {workspaces.map(ws => (
                  <button
                    key={ws.id}
                    onClick={() => {
                      setCurrentWorkspace(ws);
                      setShowWorkspaceMenu(false);
                    }}
                    className={`w-full flex items-center justify-between p-2.5 rounded-lg text-xs font-medium transition ${
                      ws.id === currentWorkspace.id 
                        ? 'bg-brand-cyan/10 text-brand-cyan border border-brand-cyan/30' 
                        : 'text-slate-300 hover:bg-slate-800/60'
                    }`}
                  >
                    <div className="flex flex-col text-left">
                      <span className="font-semibold">{ws.clientName}</span>
                      <span className="text-[10px] opacity-70">{ws.name}</span>
                    </div>
                    {ws.id === currentWorkspace.id && <Check className="w-4 h-4 text-brand-cyan" />}
                  </button>
                ))}
              </div>
            </div>
          )}
        </div>

        {/* Global Search Bar */}
        <div className="relative hidden md:block w-64 lg:w-80">
          <Search className="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
          <input
            type="text"
            placeholder="Buscar campanhas, criativos, leads..."
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            className="w-full pl-9 pr-4 py-1.5 bg-slate-950/60 border border-slate-800 rounded-xl text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-brand-cyan/50 focus:ring-1 focus:ring-brand-cyan/50 transition"
          />
        </div>
      </div>

      {/* Right side: Sync Button, Date Selector, Notifications, Quick Actions */}
      <div className="flex items-center gap-2 sm:gap-3">
        {/* Sync Button */}
        <button
          onClick={() => triggerSync()}
          disabled={isSyncing}
          className={`flex items-center gap-2 px-3 py-1.5 rounded-xl border text-xs font-medium transition ${
            isSyncing 
              ? 'bg-brand-violet/20 border-brand-violet/40 text-brand-violet animate-pulse' 
              : 'bg-slate-800/40 hover:bg-slate-800/80 border-slate-700/60 text-slate-300'
          }`}
          title="Sincronizar métricas das APIs de Anúncios"
        >
          <RefreshCw className={`w-3.5 h-3.5 ${isSyncing ? 'animate-spin text-brand-cyan' : 'text-slate-400'}`} />
          <span className="hidden lg:inline">{isSyncing ? 'Sincronizando APIs...' : 'Sincronizar APIs'}</span>
        </button>

        {/* Date Range Dropdown */}
        <div className="relative">
          <button
            onClick={() => setShowDateMenu(!showDateMenu)}
            className="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-800/40 hover:bg-slate-800/80 border border-slate-700/60 text-xs text-slate-300 font-medium transition"
          >
            <Calendar className="w-3.5 h-3.5 text-brand-cyan" />
            <span className="hidden sm:inline">
              {dateOptions.find(d => d.key === dateRange)?.label}
            </span>
            <ChevronDown className="w-3.5 h-3.5 text-slate-400" />
          </button>

          {showDateMenu && (
            <div className="absolute top-full right-0 mt-2 w-56 glass-modal rounded-xl border border-slate-700/80 shadow-2xl p-1.5 z-50">
              <div className="px-3 py-1 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                Período de Análise
              </div>
              <div className="space-y-0.5">
                {dateOptions.map(opt => (
                  <button
                    key={opt.key}
                    onClick={() => {
                      setDateRange(opt.key);
                      setShowDateMenu(false);
                    }}
                    className={`w-full flex items-center justify-between px-3 py-2 rounded-lg text-xs transition ${
                      dateRange === opt.key 
                        ? 'bg-brand-cyan/10 text-brand-cyan font-semibold' 
                        : 'text-slate-300 hover:bg-slate-800/60'
                    }`}
                  >
                    <span>{opt.label}</span>
                    {dateRange === opt.key && <Check className="w-3.5 h-3.5 text-brand-cyan" />}
                  </button>
                ))}
              </div>
            </div>
          )}
        </div>

        {/* Create Campaign Button */}
        <button
          onClick={onOpenNewCampaignModal}
          className="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-gradient-to-r from-brand-cyan to-blue-600 text-slate-950 font-bold text-xs shadow-neon-blue hover:brightness-110 transition active:scale-95"
        >
          <PlusCircle className="w-4 h-4" />
          <span className="hidden sm:inline">Nova Campanha</span>
        </button>

        {/* Notifications */}
        <div className="relative">
          <button
            onClick={() => setShowNotifMenu(!showNotifMenu)}
            className="relative p-2 rounded-xl bg-slate-800/40 hover:bg-slate-800/80 border border-slate-700/60 text-slate-300 transition"
          >
            <Bell className="w-4 h-4" />
            {unreadNotifs.length > 0 && (
              <span className="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-rose-500 text-white font-extrabold text-[10px] flex items-center justify-center animate-bounce">
                {unreadNotifs.length}
              </span>
            )}
          </button>

          {showNotifMenu && (
            <div className="absolute top-full right-0 mt-2 w-80 sm:w-96 glass-modal rounded-2xl border border-slate-700/80 shadow-2xl p-3 z-50">
              <div className="flex items-center justify-between pb-2 mb-2 border-b border-slate-800">
                <div className="flex items-center gap-2">
                  <Bell className="w-4 h-4 text-brand-cyan" />
                  <span className="font-bold text-sm text-slate-100">Notificações e Alertas</span>
                </div>
                {unreadNotifs.length > 0 && (
                  <button 
                    onClick={markAllNotificationsRead}
                    className="text-[11px] text-brand-cyan hover:underline font-medium"
                  >
                    Marcar todas lidas
                  </button>
                )}
              </div>

              <div className="space-y-2 max-h-72 overflow-y-auto pr-1">
                {notifications.map(n => (
                  <div 
                    key={n.id}
                    className={`p-2.5 rounded-xl border text-xs transition ${
                      !n.isRead 
                        ? 'bg-slate-800/80 border-slate-700' 
                        : 'bg-slate-900/40 border-slate-800/50 opacity-75'
                    }`}
                  >
                    <div className="flex items-center justify-between mb-1">
                      <span className="font-semibold text-slate-200">{n.title}</span>
                      <span className="text-[10px] text-slate-400">{n.timestamp}</span>
                    </div>
                    <p className="text-slate-300 text-[11px] leading-relaxed">{n.description}</p>
                  </div>
                ))}
              </div>
            </div>
          )}
        </div>

        {/* User Profile */}
        <div className="flex items-center gap-2 pl-2 border-l border-slate-800">
          <div className="w-8 h-8 rounded-full bg-gradient-to-tr from-brand-violet to-purple-500 p-0.5 shadow-neon-violet">
            <div className="w-full h-full bg-hub-950 rounded-full flex items-center justify-center font-extrabold text-xs text-brand-cyan">
              GA
            </div>
          </div>
        </div>
      </div>
    </header>
  );
};
