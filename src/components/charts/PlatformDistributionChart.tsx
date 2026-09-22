import React from 'react';
import { 
  ResponsiveContainer, 
  PieChart, 
  Pie, 
  Cell, 
  Tooltip, 
  Legend 
} from 'recharts';
import { useApp } from '../../context/AppContext';

export const PlatformDistributionChart: React.FC = () => {
  const { campaigns } = useApp();

  // Aggregate spend by platform
  const dataMap: Record<string, number> = {};
  campaigns.forEach(c => {
    dataMap[c.platform] = (dataMap[c.platform] || 0) + c.totalSpend;
  });

  const platformColors: Record<string, string> = {
    meta: '#0668E1',
    google: '#EA4335',
    tiktok: '#FE2C55',
    linkedin: '#0A66C2',
    kwai: '#FF5000'
  };

  const platformNames: Record<string, string> = {
    meta: 'Meta Ads',
    google: 'Google Ads',
    tiktok: 'TikTok Ads',
    linkedin: 'LinkedIn Ads',
    kwai: 'Kwai Ads'
  };

  const data = Object.keys(dataMap).map(key => ({
    name: platformNames[key] || key,
    value: dataMap[key],
    color: platformColors[key] || '#94a3b8'
  }));

  const formatCurrency = (val: number) => `R$ ${val.toLocaleString('pt-BR')}`;

  return (
    <div className="w-full h-72">
      <ResponsiveContainer width="100%" height="100%">
        <PieChart>
          <Pie
            data={data}
            cx="50%"
            cy="50%"
            innerRadius={60}
            outerRadius={90}
            paddingAngle={5}
            dataKey="value"
          >
            {data.map((entry, index) => (
              <Cell key={`cell-${index}`} fill={entry.color} stroke="none" />
            ))}
          </Pie>
          <Tooltip 
            contentStyle={{ 
              backgroundColor: 'rgba(11, 15, 25, 0.95)', 
              borderColor: '#334155', 
              borderRadius: '12px',
              fontSize: '12px'
            }}
            formatter={(val: any) => [formatCurrency(Number(val)), 'Gasto Total']}
          />
          <Legend 
            verticalAlign="bottom" 
            height={36} 
            formatter={(value) => <span className="text-xs text-slate-300 font-medium">{value}</span>}
          />
        </PieChart>
      </ResponsiveContainer>
    </div>
  );
};
