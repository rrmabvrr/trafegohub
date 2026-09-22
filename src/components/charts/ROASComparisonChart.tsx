import React from 'react';
import { 
  ResponsiveContainer, 
  BarChart, 
  Bar, 
  XAxis, 
  YAxis, 
  Tooltip, 
  CartesianGrid, 
  Cell 
} from 'recharts';
import { useApp } from '../../context/AppContext';

export const ROASComparisonChart: React.FC = () => {
  const { campaigns } = useApp();

  // Aggregate ROAS by platform
  const stats: Record<string, { totalSpend: number; totalRevenue: number }> = {};
  
  campaigns.forEach(c => {
    if (!stats[c.platform]) {
      stats[c.platform] = { totalSpend: 0, totalRevenue: 0 };
    }
    stats[c.platform].totalSpend += c.totalSpend;
    stats[c.platform].totalRevenue += c.revenue;
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

  const data = Object.keys(stats).map(key => {
    const s = stats[key];
    const roas = s.totalSpend > 0 ? Number((s.totalRevenue / s.totalSpend).toFixed(2)) : 0;
    return {
      platformKey: key,
      platform: platformNames[key] || key,
      roas,
      spend: s.totalSpend,
      revenue: s.totalRevenue,
      color: platformColors[key] || '#00F2FE'
    };
  }).sort((a, b) => b.roas - a.roas);

  return (
    <div className="w-full h-72">
      <ResponsiveContainer width="100%" height="100%">
        <BarChart data={data} margin={{ top: 10, right: 10, left: -20, bottom: 0 }}>
          <CartesianGrid strokeDasharray="3 3" stroke="#1e2942" vertical={false} />
          <XAxis 
            dataKey="platform" 
            stroke="#64748b" 
            tick={{ fill: '#94a3b8', fontSize: 11 }}
            axisLine={{ stroke: '#1e2942' }}
          />
          <YAxis 
            stroke="#64748b" 
            tick={{ fill: '#94a3b8', fontSize: 11 }}
            tickFormatter={(v) => `${v}x`}
            axisLine={{ stroke: '#1e2942' }}
          />
          <Tooltip 
            contentStyle={{ 
              backgroundColor: 'rgba(11, 15, 25, 0.95)', 
              borderColor: '#334155', 
              borderRadius: '12px',
              fontSize: '12px'
            }}
            formatter={(value: any) => [`${value}x`, 'ROAS Médio']}
          />
          <Bar dataKey="roas" radius={[8, 8, 0, 0]}>
            {data.map((entry, index) => (
              <Cell key={`cell-bar-${index}`} fill={entry.color} />
            ))}
          </Bar>
        </BarChart>
      </ResponsiveContainer>
    </div>
  );
};
