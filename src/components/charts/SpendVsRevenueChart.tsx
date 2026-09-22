import React from 'react';
import { 
  ResponsiveContainer, 
  AreaChart, 
  Area, 
  XAxis, 
  YAxis, 
  Tooltip, 
  CartesianGrid, 
  Legend 
} from 'recharts';
import { mockTimeSeriesData } from '../../mock/initialData';

export const SpendVsRevenueChart: React.FC = () => {
  const formatCurrency = (val: number) => `R$ ${val.toLocaleString('pt-BR')}`;

  return (
    <div className="w-full h-72">
      <ResponsiveContainer width="100%" height="100%">
        <AreaChart data={mockTimeSeriesData} margin={{ top: 10, right: 10, left: -10, bottom: 0 }}>
          <defs>
            <linearGradient id="colorInvestido" x1="0" y1="0" x2="0" y2="1">
              <stop offset="5%" stopColor="#00F2FE" stopOpacity={0.4} />
              <stop offset="95%" stopColor="#00F2FE" stopOpacity={0} />
            </linearGradient>
            <linearGradient id="colorFaturamento" x1="0" y1="0" x2="0" y2="1">
              <stop offset="5%" stopColor="#10B981" stopOpacity={0.4} />
              <stop offset="95%" stopColor="#10B981" stopOpacity={0} />
            </linearGradient>
          </defs>
          <CartesianGrid strokeDasharray="3 3" stroke="#1e2942" vertical={false} />
          <XAxis 
            dataKey="date" 
            stroke="#64748b" 
            tick={{ fill: '#94a3b8', fontSize: 11 }}
            axisLine={{ stroke: '#1e2942' }}
          />
          <YAxis 
            stroke="#64748b" 
            tick={{ fill: '#94a3b8', fontSize: 11 }}
            tickFormatter={(v) => `R$${v >= 1000 ? `${(v/1000).toFixed(0)}k` : v}`}
            axisLine={{ stroke: '#1e2942' }}
          />
          <Tooltip 
            contentStyle={{ 
              backgroundColor: 'rgba(11, 15, 25, 0.95)', 
              borderColor: '#334155', 
              borderRadius: '12px',
              boxShadow: '0 10px 25px -5px rgba(0,0,0,0.5)',
              fontSize: '12px'
            }}
            formatter={(value: any, name: any) => [
              name === 'roas' ? `${value}x` : formatCurrency(Number(value)),
              name === 'investido' ? 'Investimento Total' : name === 'faturamento' ? 'Faturamento Retornado' : 'ROAS Médio'
            ]}
          />
          <Legend 
            wrapperStyle={{ paddingTop: '10px', fontSize: '12px' }}
            formatter={(value) => value === 'investido' ? 'Investimento (R$)' : value === 'faturamento' ? 'Faturamento (R$)' : 'ROAS'}
          />
          <Area 
            type="monotone" 
            dataKey="faturamento" 
            stroke="#10B981" 
            strokeWidth={3}
            fillOpacity={1} 
            fill="url(#colorFaturamento)" 
          />
          <Area 
            type="monotone" 
            dataKey="investido" 
            stroke="#00F2FE" 
            strokeWidth={3}
            fillOpacity={1} 
            fill="url(#colorInvestido)" 
          />
        </AreaChart>
      </ResponsiveContainer>
    </div>
  );
};
