import React, { useState } from 'react';
import { FileSpreadsheet, Download, FileText, Check, Sparkles, Send, Printer, Calendar } from 'lucide-react';
import { useApp } from '../context/AppContext';

export const Reports: React.FC = () => {
  const { currentWorkspace, campaigns, leads, dateRange } = useApp();

  const [includeKPIs, setIncludeKPIs] = useState(true);
  const [includeCharts, setIncludeCharts] = useState(true);
  const [includeCampaignsTable, setIncludeCampaignsTable] = useState(true);
  const [includeLeadsSummary, setIncludeLeadsSummary] = useState(true);
  const [clientNotes, setClientNotes] = useState('Excelente desempenho no período com alta eficiência nas campanhas de Search e CBO Meta.');
  const [isExportingPDF, setIsExportingPDF] = useState(false);
  const [isExportingCSV, setIsExportingCSV] = useState(false);

  const totalSpend = campaigns.reduce((acc, c) => acc + c.totalSpend, 0);
  const totalRevenue = campaigns.reduce((acc, c) => acc + c.revenue, 0);
  const roas = totalSpend > 0 ? (totalRevenue / totalSpend).toFixed(2) : '0';

  const handleExportCSV = () => {
    setIsExportingCSV(true);
    setTimeout(() => {
      let csvContent = "data:text/csv;charset=utf-8,";
      csvContent += "Plataforma,Nome da Campanha,Orçamento Diário (R$),Investimento (R$),Conversões,CPA (R$),ROAS\n";
      
      campaigns.forEach(c => {
        csvContent += `"${c.platform}","${c.name}",${c.dailyBudget},${c.totalSpend},${c.conversions},${c.cpl},${c.roas}\n`;
      });

      const encodedUri = encodeURI(csvContent);
      const link = document.createElement("a");
      link.setAttribute("href", encodedUri);
      link.setAttribute("download", `Relatorio_TrafegoHub_${currentWorkspace.clientName.replace(/\s+/g, '_')}.csv`);
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      setIsExportingCSV(false);
    }, 800);
  };

  const handlePrintPDF = () => {
    setIsExportingPDF(true);
    setTimeout(() => {
      window.print();
      setIsExportingPDF(false);
    }, 500);
  };

  return (
    <div className="space-y-6 pb-12">
      {/* Printable CSS overrides */}
      <style>{`
        @media print {
          body * {
            visibility: hidden;
          }
          #printable-report, #printable-report * {
            visibility: visible;
          }
          #printable-report {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            background: white !important;
            color: black !important;
          }
        }
      `}</style>

      {/* Header Banner */}
      <div className="glass-panel p-6 rounded-2xl border border-slate-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
          <h1 className="text-2xl font-extrabold text-slate-100 flex items-center gap-2.5">
            <FileSpreadsheet className="w-6 h-6 text-brand-cyan" />
            Relatórios Executive & Exportação de Dados
          </h1>
          <p className="text-xs sm:text-sm text-slate-400 mt-1">
            Gere relatórios customizados para apresentação de clientes e exporte planilhas CSV.
          </p>
        </div>

        <div className="flex items-center gap-3">
          <button
            onClick={handleExportCSV}
            disabled={isExportingCSV}
            className="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-bold text-xs transition"
          >
            <Download className="w-4 h-4 text-emerald-400" />
            <span>{isExportingCSV ? 'Gerando CSV...' : 'Exportar Dados (CSV)'}</span>
          </button>

          <button
            onClick={handlePrintPDF}
            disabled={isExportingPDF}
            className="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-brand-cyan to-blue-600 text-slate-950 font-bold text-xs shadow-neon-blue hover:brightness-110 transition active:scale-95"
          >
            <Printer className="w-4 h-4" />
            <span>{isExportingPDF ? 'Preparando PDF...' : 'Imprimir / Salvar PDF'}</span>
          </button>
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Report Configuration Controls */}
        <div className="glass-panel p-5 rounded-2xl border border-slate-800 space-y-4">
          <h2 className="text-sm font-bold text-slate-100 flex items-center gap-2">
            <Sparkles className="w-4 h-4 text-brand-cyan" />
            Configurar Seções do Relatório
          </h2>

          <div className="space-y-2 text-xs">
            <label className="flex items-center gap-2.5 p-2.5 rounded-xl bg-slate-900/60 border border-slate-800 cursor-pointer">
              <input 
                type="checkbox" 
                checked={includeKPIs} 
                onChange={(e) => setIncludeKPIs(e.target.checked)} 
                className="rounded accent-brand-cyan"
              />
              <span className="font-semibold text-slate-200">Resumo Executivo & KPIs Globais</span>
            </label>

            <label className="flex items-center gap-2.5 p-2.5 rounded-xl bg-slate-900/60 border border-slate-800 cursor-pointer">
              <input 
                type="checkbox" 
                checked={includeCampaignsTable} 
                onChange={(e) => setIncludeCampaignsTable(e.target.checked)} 
                className="rounded accent-brand-cyan"
              />
              <span className="font-semibold text-slate-200">Tabela de Desempenho por Campanha</span>
            </label>

            <label className="flex items-center gap-2.5 p-2.5 rounded-xl bg-slate-900/60 border border-slate-800 cursor-pointer">
              <input 
                type="checkbox" 
                checked={includeLeadsSummary} 
                onChange={(e) => setIncludeLeadsSummary(e.target.checked)} 
                className="rounded accent-brand-cyan"
              />
              <span className="font-semibold text-slate-200">Resumo de Origem de Leads</span>
            </label>
          </div>

          <div>
            <label className="block text-xs font-semibold text-slate-300 mb-1">Notas e Conclusões do Gestor</label>
            <textarea
              rows={4}
              value={clientNotes}
              onChange={(e) => setClientNotes(e.target.value)}
              className="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-xs text-slate-200 focus:border-brand-cyan focus:outline-none"
            />
          </div>
        </div>

        {/* Live Report Preview Area */}
        <div className="lg:col-span-2 glass-panel p-6 rounded-2xl border border-slate-800" id="printable-report">
          <div className="border-b border-slate-800/80 pb-4 mb-5 flex items-center justify-between">
            <div>
              <span className="text-[10px] uppercase tracking-wider font-extrabold text-brand-cyan">
                Relatório Oficial de Desempenho de Mídia
              </span>
              <h2 className="text-xl font-extrabold text-slate-100">{currentWorkspace.clientName}</h2>
              <p className="text-xs text-slate-400 mt-0.5">{currentWorkspace.name} • Período: Últimos 30 dias</p>
            </div>
            <div className="w-10 h-10 rounded-xl bg-brand-cyan/20 border border-brand-cyan/40 flex items-center justify-center font-black text-brand-cyan">
              TH
            </div>
          </div>

          {/* Section 1: Executive KPIs */}
          {includeKPIs && (
            <div className="mb-6">
              <h3 className="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">1. Indicadores de Resultado</h3>
              <div className="grid grid-cols-3 gap-3">
                <div className="p-3 bg-slate-900/80 rounded-xl border border-slate-800">
                  <span className="text-[10px] text-slate-400 block">Total Investido</span>
                  <span className="text-base font-extrabold text-slate-100">R$ {totalSpend.toLocaleString('pt-BR')}</span>
                </div>
                <div className="p-3 bg-slate-900/80 rounded-xl border border-slate-800">
                  <span className="text-[10px] text-slate-400 block">Faturamento Retornado</span>
                  <span className="text-base font-extrabold text-emerald-400">R$ {totalRevenue.toLocaleString('pt-BR')}</span>
                </div>
                <div className="p-3 bg-slate-900/80 rounded-xl border border-slate-800">
                  <span className="text-[10px] text-slate-400 block">ROAS Médio</span>
                  <span className="text-base font-extrabold text-brand-cyan">{roas}x</span>
                </div>
              </div>
            </div>
          )}

          {/* Section 2: Campaigns Table */}
          {includeCampaignsTable && (
            <div className="mb-6">
              <h3 className="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">2. Desempenho por Campanha</h3>
              <div className="overflow-x-auto rounded-xl border border-slate-800">
                <table className="w-full text-left text-xs">
                  <thead className="bg-slate-950 text-slate-400 text-[10px] uppercase border-b border-slate-800">
                    <tr>
                      <th className="p-2">Plataforma</th>
                      <th className="p-2">Campanha</th>
                      <th className="p-2">Gasto</th>
                      <th className="p-2">Conversões</th>
                      <th className="p-2">ROAS</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-slate-800 text-slate-300">
                    {campaigns.map(c => (
                      <tr key={c.id}>
                        <td className="p-2 font-semibold uppercase">{c.platform}</td>
                        <td className="p-2 font-bold text-slate-200">{c.name}</td>
                        <td className="p-2">R$ {c.totalSpend.toLocaleString('pt-BR')}</td>
                        <td className="p-2 font-bold">{c.conversions}</td>
                        <td className="p-2 font-extrabold text-emerald-400">{c.roas}x</td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          )}

          {/* Section 3: Gestor Notes */}
          {clientNotes && (
            <div>
              <h3 className="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">3. Parecer Técnico do Gestor</h3>
              <div className="p-3 rounded-xl bg-slate-900/60 border border-slate-800 text-xs text-slate-300 leading-relaxed italic">
                "{clientNotes}"
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
};
