import React from 'react';
import { Platform } from '../../types';

interface PlatformBadgeProps {
  platform: Platform;
  showName?: boolean;
  className?: string;
  size?: 'sm' | 'md' | 'lg';
}

export const PlatformBadge: React.FC<PlatformBadgeProps> = ({ 
  platform, 
  showName = true, 
  className = '',
  size = 'md'
}) => {
  const getPlatformConfig = (p: Platform) => {
    switch (p) {
      case 'meta':
        return {
          name: 'Meta Ads',
          bg: 'bg-meta/10 border-meta/30 text-blue-400',
          dot: 'bg-meta',
          icon: '🔵'
        };
      case 'google':
        return {
          name: 'Google Ads',
          bg: 'bg-red-500/10 border-red-500/30 text-red-400',
          dot: 'bg-red-500',
          icon: '🔴'
        };
      case 'tiktok':
        return {
          name: 'TikTok Ads',
          bg: 'bg-tiktok/10 border-tiktok/30 text-rose-400',
          dot: 'bg-tiktok',
          icon: '🎵'
        };
      case 'linkedin':
        return {
          name: 'LinkedIn Ads',
          bg: 'bg-linkedin/10 border-linkedin/30 text-sky-400',
          dot: 'bg-linkedin',
          icon: '💼'
        };
      case 'kwai':
        return {
          name: 'Kwai Ads',
          bg: 'bg-kwai/10 border-kwai/30 text-orange-400',
          dot: 'bg-kwai',
          icon: '🟧'
        };
      default:
        return {
          name: 'Desconhecido',
          bg: 'bg-slate-700/50 border-slate-600 text-slate-300',
          dot: 'bg-slate-400',
          icon: '⚪'
        };
    }
  };

  const config = getPlatformConfig(platform);
  const sizeClasses = size === 'sm' ? 'px-2 py-0.5 text-xs' : size === 'lg' ? 'px-3.5 py-1.5 text-sm' : 'px-2.5 py-1 text-xs';

  return (
    <span className={`inline-flex items-center gap-1.5 rounded-full border font-medium ${config.bg} ${sizeClasses} ${className}`}>
      <span className={`w-1.5 h-1.5 rounded-full ${config.dot}`} />
      <span>{config.icon}</span>
      {showName && <span>{config.name}</span>}
    </span>
  );
};
