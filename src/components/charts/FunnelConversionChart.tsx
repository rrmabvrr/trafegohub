import React from 'react';
import { Eye, MousePointerClick, UserCheck, ShoppingCart } from 'lucide-react';
import { useApp } from '../../context/AppContext';

export const FunnelConversionChart: React.FC = () => {
  const { campaigns } = useApp();

  const totalImpressions = campaigns.reduce((acc, c) => acc + c.impressions, 0);
  const totalClicks = campaigns.reduce((acc, c) => acc + c.clicks, 0);
  const totalConversions = campaigns.reduce((acc, c) => acc + c.conversions, 0);
  // Estimate leads as ~1.5x conversions for funnel demo
  const estimatedLeads = Math.round(totalConversions * 1.8);

  const ctr = totalImpressions > 0 ? ((totalClicks / totalImpressions) * 100).toFixed(2) : '0';
  const clickToLeadRate = totalClicks > 0 ? ((estimatedLeads / totalClicks) * 100).toFixed(2) : '0';
  const leadToSaleRate = estimatedLeads > 0 ? ((totalConversions / estimatedLeads) * 100).toFixed(2) : '0';

  const steps = [
    {
      title: 'Impressões Totais',
      value: totalImpressions.toLocaleString('pt-BR'),
      icon: Eye,
      color: 'from-blue-500 to-cyan-400',
      textColor: 'text-cyan-400',
      width: '100%',
      rate: null
    },
    {
      title: 'Cliques no Anúncio',
      value: totalClicks.toLocaleString('pt-BR'),
      icon: MousePointerClick,
      color: 'from-cyan-400 to-indigo-500',
      textColor: 'text-brand-cyan',
      width: '78%',
      rate: `CTR ${ctr}%`
    },
    {
      title: 'Leads Capturados',
      value: estimatedLeads.toLocaleString('pt-BR'),
      icon: UserCheck,
      color: 'from-indigo-500 to-purple-500',
      textColor: 'text-purple-400',
      width: '56%',
      rate: `Taxa ${clickToLeadRate}%`
    },
    {
      title: 'Vendas / Conversões',
      value: totalConversions.toLocaleString('pt-BR'),
      icon: ShoppingCart,
      color: 'from-purple-500 to-emerald-400',
      textColor: 'text-emerald-400',
      width: '38%',
      rate: `Taxa ${leadToSaleRate}%`
    }
  ];

  return (
    <div className="space-y-3.5 py-1">
      {steps.map((step, idx) => {
        const Icon = step.icon;
        return (
          <div key={idx} className="relative">
            <div className="flex items-center justify-between text-xs mb-1.5 px-1">
              <span className="font-semibold text-slate-300 flex items-center gap-1.5">
                <Icon className={`w-3.5 h-3.5 ${step.textColor}`} />
                {step.title}
              </span>
              <div className="flex items-center gap-2">
                {step.rate && (
                  <span className="text-[10px] px-2 py-0.5 rounded-full bg-slate-800 text-slate-400 border border-slate-700">
                    {step.rate}
                  </span>
                )}
                <span className={`font-extrabold ${step.textColor}`}>{step.value}</span>
              </div>
            </div>

            {/* Funnel Progress Bar */}
            <div className="h-3 w-full bg-slate-900/80 rounded-full overflow-hidden p-0.5 border border-slate-800/80">
              <div 
                className={`h-full rounded-full bg-gradient-to-r ${step.color} transition-all duration-500 shadow-sm`}
                style={{ width: step.width }}
              />
            </div>
          </div>
        );
      })}
    </div>
  );
};
