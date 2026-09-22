import React, { useState } from 'react';
import { 
  Users, 
  Search, 
  Filter, 
  Plus, 
  Tag, 
  DollarSign, 
  Calendar, 
  CheckCircle2, 
  ArrowRight,
  Columns,
  List
} from 'lucide-react';
import { useApp } from '../context/AppContext';
import { LeadStatus, Lead } from '../types';
import { PlatformBadge } from '../components/ui/PlatformBadge';

export const Leads: React.FC = () => {
  const { leads, updateLeadStatus, addLead } = useApp();
  const [viewMode, setViewMode] = useState<'kanban' | 'table'>('kanban');
  const [statusFilter, setStatusFilter] = useState<'ALL' | LeadStatus>('ALL');
  const [search, setSearch] = useState('');
  const [showAddModal, setShowAddModal] = useState(false);

  const [newLeadName, setNewLeadName] = useState('');
  const [newLeadEmail, setNewLeadEmail] = useState('');
  const [newLeadPhone, setNewLeadPhone] = useState('');
  const [newLeadValue, setNewLeadValue] = useState(1497);

  const filteredLeads = leads.filter(l => {
    const matchStatus = statusFilter === 'ALL' || l.status === statusFilter;
    const matchSearch = !search || 
      l.name.toLowerCase().includes(search.toLowerCase()) || 
      l.email.toLowerCase().includes(search.toLowerCase()) ||
      l.campaignName.toLowerCase().includes(search.toLowerCase());
    return matchStatus && matchSearch;
  });

  const columns: { status: LeadStatus; title: string; color: string }[] = [
    { status: 'NEW', title: 'Novos Leads', color: 'border-blue-500 text-blue-400 bg-blue-500/10' },
    { status: 'CONTACTED', title: 'Em Contato', color: 'border-amber-500 text-amber-400 bg-amber-500/10' },
    { status: 'QUALIFIED', title: 'Qualificados', color: 'border-purple-500 text-purple-400 bg-purple-500/10' },
    { status: 'CONVERTED', title: 'Convertidos (Venda)', color: 'border-emerald-500 text-emerald-400 bg-emerald-500/10' },
    { status: 'LOST', title: 'Perdidos', color: 'border-rose-500 text-rose-400 bg-rose-500/10' }
  ];

  const handleCreateLead = (e: React.FormEvent) => {
    e.preventDefault();
    if (!newLeadName || !newLeadEmail) return;

    addLead({
      name: newLeadName,
      email: newLeadEmail,
      phone: newLeadPhone || '(11) 99999-0000',
      platform: 'meta',
      campaignName: '[Meta] Campanha Direta Lead Form',
      utmSource: 'facebook',
      utmMedium: 'cpc',
      utmCampaign: 'lead_form_instant',
      cpl: 15.00,
      value: Number(newLeadValue),
      status: 'NEW',
      date: new Date().toISOString().replace('T', ' ').substring(0, 16),
      city: 'São Paulo - SP'
    });

    setNewLeadName('');
    setNewLeadEmail('');
    setNewLeadPhone('');
    setShowAddModal(false);
  };

  return (
    <div className="space-y-6 pb-12">
      {/* Header Banner */}
      <div className="glass-panel p-6 rounded-2xl border border-slate-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
          <h1 className="text-2xl font-extrabold text-slate-100 flex items-center gap-2.5">
            <Users className="w-6 h-6 text-brand-cyan" />
            Central de Leads & Atribuição de Tráfego
          </h1>
          <p className="text-xs sm:text-sm text-slate-400 mt-1">
            Rastreamento de contatos capturados com origem UTM, valor da oportunidade e pipeline CRM Lite.
          </p>
        </div>

        <div className="flex items-center gap-3">
          <div className="flex items-center p-1 bg-slate-900 border border-slate-800 rounded-xl">
            <button
              onClick={() => setViewMode('kanban')}
              className={`flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition ${
                viewMode === 'kanban' ? 'bg-brand-cyan text-slate-950 font-bold' : 'text-slate-400 hover:text-white'
              }`}
            >
              <Columns className="w-3.5 h-3.5" />
              Kanban
            </button>
            <button
              onClick={() => setViewMode('table')}
              className={`flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition ${
                viewMode === 'table' ? 'bg-brand-cyan text-slate-950 font-bold' : 'text-slate-400 hover:text-white'
              }`}
            >
              <List className="w-3.5 h-3.5" />
              Tabela
            </button>
          </div>

          <button
            onClick={() => setShowAddModal(true)}
            className="flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-gradient-to-r from-brand-cyan via-blue-600 to-brand-violet text-slate-950 font-bold text-xs shadow-neon-blue hover:brightness-110 transition active:scale-95"
          >
            <Plus className="w-4 h-4" />
            Adicionar Lead
          </button>
        </div>
      </div>

      {/* Filter Bar */}
      <div className="glass-panel p-4 rounded-2xl border border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3">
        <div className="relative w-full sm:w-80">
          <Search className="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
          <input 
            type="text"
            placeholder="Buscar por nome, email ou campanha..."
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            className="w-full pl-8 pr-3 py-1.5 bg-slate-950/80 border border-slate-800 rounded-xl text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-brand-cyan transition"
          />
        </div>

        <div className="text-xs text-slate-400 font-medium">
          Total de Leads Rastreados: <span className="font-extrabold text-slate-100">{leads.length}</span>
        </div>
      </div>

      {/* Kanban View */}
      {viewMode === 'kanban' ? (
        <div className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
          {columns.map(col => {
            const colLeads = filteredLeads.filter(l => l.status === col.status);

            return (
              <div key={col.status} className="glass-panel p-3.5 rounded-2xl border border-slate-800 flex flex-col min-h-[500px]">
                <div className="flex items-center justify-between pb-2.5 mb-3 border-b border-slate-800">
                  <div className="flex items-center gap-2">
                    <span className={`px-2.5 py-0.5 rounded-full text-xs font-bold border ${col.color}`}>
                      {col.title}
                    </span>
                  </div>
                  <span className="text-xs font-extrabold text-slate-400 bg-slate-800 px-2 py-0.5 rounded-full">
                    {colLeads.length}
                  </span>
                </div>

                <div className="space-y-3 flex-1 overflow-y-auto">
                  {colLeads.map(lead => (
                    <div key={lead.id} className="p-3.5 rounded-xl bg-slate-900/80 border border-slate-800 hover:border-brand-cyan/40 transition group relative">
                      <div className="flex items-center justify-between mb-1.5">
                        <PlatformBadge platform={lead.platform} size="sm" />
                        <span className="text-[10px] text-slate-500">{lead.date}</span>
                      </div>

                      <h4 className="font-bold text-slate-200 text-xs">{lead.name}</h4>
                      <p className="text-[11px] text-slate-400 truncate">{lead.email}</p>
                      <p className="text-[10px] text-slate-500 mt-0.5">{lead.phone}</p>

                      <div className="mt-2.5 pt-2 border-t border-slate-800/80 flex items-center justify-between text-[11px]">
                        <span className="text-slate-400 flex items-center gap-1">
                          <Tag className="w-3 h-3 text-brand-cyan" />
                          UTM: {lead.utmSource}
                        </span>
                        <span className="font-extrabold text-emerald-400">
                          R$ {lead.value.toLocaleString('pt-BR')}
                        </span>
                      </div>

                      {/* Move status buttons */}
                      <div className="mt-2.5 pt-2 border-t border-slate-800/60 flex items-center justify-between gap-1">
                        <select
                          value={lead.status}
                          onChange={(e) => updateLeadStatus(lead.id, e.target.value as LeadStatus)}
                          className="w-full bg-slate-950 border border-slate-800 text-[10px] text-slate-300 rounded px-1.5 py-1 focus:outline-none"
                        >
                          <option value="NEW">Novo Lead</option>
                          <option value="CONTACTED">Em Contato</option>
                          <option value="QUALIFIED">Qualificado</option>
                          <option value="CONVERTED">Convertido</option>
                          <option value="LOST">Perdido</option>
                        </select>
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            );
          })}
        </div>
      ) : (
        /* Table View */
        <div className="glass-panel rounded-2xl border border-slate-800 overflow-hidden">
          <div className="overflow-x-auto">
            <table className="w-full text-left text-xs">
              <thead className="bg-slate-950/90 text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-800">
                <tr>
                  <th className="px-4 py-3.5">Nome do Lead</th>
                  <th className="px-4 py-3.5">Contato</th>
                  <th className="px-4 py-3.5">Plataforma & Campanha</th>
                  <th className="px-4 py-3.5">Parâmetros UTM</th>
                  <th className="px-4 py-3.5">Custo p/ Lead</th>
                  <th className="px-4 py-3.5">Valor da Oportunidade</th>
                  <th className="px-4 py-3.5">Status CRM</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-800 text-slate-300">
                {filteredLeads.map(lead => (
                  <tr key={lead.id} className="hover:bg-slate-800/40 transition">
                    <td className="px-4 py-3.5 font-bold text-slate-100">{lead.name}</td>
                    <td className="px-4 py-3.5">
                      <div className="flex flex-col">
                        <span>{lead.email}</span>
                        <span className="text-[10px] text-slate-400">{lead.phone}</span>
                      </div>
                    </td>
                    <td className="px-4 py-3.5">
                      <div className="flex flex-col gap-1">
                        <PlatformBadge platform={lead.platform} size="sm" />
                        <span className="text-[10px] text-slate-400 line-clamp-1">{lead.campaignName}</span>
                      </div>
                    </td>
                    <td className="px-4 py-3.5">
                      <div className="text-[10px] text-slate-400 font-mono">
                        <div>source: <span className="text-brand-cyan">{lead.utmSource}</span></div>
                        <div>campaign: <span className="text-purple-400">{lead.utmCampaign}</span></div>
                      </div>
                    </td>
                    <td className="px-4 py-3.5 font-semibold text-slate-300">R$ {lead.cpl.toFixed(2)}</td>
                    <td className="px-4 py-3.5 font-extrabold text-emerald-400">R$ {lead.value.toLocaleString('pt-BR')}</td>
                    <td className="px-4 py-3.5">
                      <select
                        value={lead.status}
                        onChange={(e) => updateLeadStatus(lead.id, e.target.value as LeadStatus)}
                        className="bg-slate-900 border border-slate-700 text-xs text-slate-200 font-bold rounded-lg px-2.5 py-1 focus:outline-none"
                      >
                        <option value="NEW">Novo Lead</option>
                        <option value="CONTACTED">Em Contato</option>
                        <option value="QUALIFIED">Qualificado</option>
                        <option value="CONVERTED">Convertido</option>
                        <option value="LOST">Perdido</option>
                      </select>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      )}

      {/* Add Lead Modal */}
      {showAddModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-hub-950/80 backdrop-blur-md">
          <div className="glass-modal w-full max-w-md rounded-2xl border border-slate-700 p-6 relative">
            <h3 className="text-base font-bold text-slate-100 mb-4">Adicionar Lead Manualmente</h3>
            <form onSubmit={handleCreateLead} className="space-y-3">
              <div>
                <label className="block text-xs text-slate-300 mb-1">Nome Completo</label>
                <input 
                  type="text" 
                  required 
                  value={newLeadName}
                  onChange={(e) => setNewLeadName(e.target.value)}
                  className="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white"
                />
              </div>
              <div>
                <label className="block text-xs text-slate-300 mb-1">Email</label>
                <input 
                  type="email" 
                  required 
                  value={newLeadEmail}
                  onChange={(e) => setNewLeadEmail(e.target.value)}
                  className="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white"
                />
              </div>
              <div>
                <label className="block text-xs text-slate-300 mb-1">Telefone / WhatsApp</label>
                <input 
                  type="text" 
                  value={newLeadPhone}
                  onChange={(e) => setNewLeadPhone(e.target.value)}
                  placeholder="(11) 98888-7777"
                  className="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white"
                />
              </div>
              <div>
                <label className="block text-xs text-slate-300 mb-1">Valor Estimado do Negócio (R$)</label>
                <input 
                  type="number" 
                  value={newLeadValue}
                  onChange={(e) => setNewLeadValue(Number(e.target.value))}
                  className="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white font-bold"
                />
              </div>
              <div className="flex justify-end gap-2 pt-3">
                <button 
                  type="button" 
                  onClick={() => setShowAddModal(false)}
                  className="px-4 py-2 bg-slate-800 text-xs font-semibold rounded-xl text-slate-300"
                >
                  Cancelar
                </button>
                <button 
                  type="submit"
                  className="px-4 py-2 bg-brand-cyan text-slate-950 font-bold text-xs rounded-xl shadow-neon-blue"
                >
                  Salvar Lead
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
};
