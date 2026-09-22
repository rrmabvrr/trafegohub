export type Platform = 'meta' | 'google' | 'tiktok' | 'linkedin' | 'kwai';

export type CampaignStatus = 'ACTIVE' | 'PAUSED' | 'ARCHIVED' | 'PENDING';

export type CampaignObjective = 'SALES' | 'LEADS' | 'TRAFFIC' | 'ENGAGEMENT' | 'REACH';

export interface AdSet {
  id: string;
  campaignId: string;
  name: string;
  budget: number;
  status: 'ACTIVE' | 'PAUSED';
  impressions: number;
  clicks: number;
  conversions: number;
  roas: number;
}

export interface Creative {
  id: string;
  campaignId: string;
  campaignName: string;
  name: string;
  type: 'image' | 'video' | 'carousel';
  platform: Platform;
  thumbnailUrl: string;
  ctr: number; // %
  hookRate: number; // 3s view rate %
  roas: number;
  spend: number;
  conversions: number;
  fatigueLevel: 'GOOD' | 'WARNING' | 'CRITICAL';
  fatigueReason?: string;
  frequency: number;
}

export interface Campaign {
  id: string;
  name: string;
  platform: Platform;
  status: CampaignStatus;
  objective: CampaignObjective;
  dailyBudget: number;
  totalSpend: number;
  impressions: number;
  clicks: number;
  ctr: number; // %
  cpc: number; // R$
  cpl: number; // R$ (Cost per Lead)
  conversions: number;
  roas: number;
  revenue: number;
  startDate: string;
  endDate?: string;
  adSetCount: number;
  adCount: number;
  targetAudience: string;
}

export interface Integration {
  id: string;
  platform: Platform;
  name: string;
  accountId: string;
  status: 'CONNECTED' | 'DISCONNECTED' | 'ERROR' | 'SYNCING';
  lastSync: string;
  tokenExpiry: string;
  activeCampaignsCount: number;
  currency: string;
  adAccountName: string;
  webhookUrl?: string;
  apiKey?: string;
}

export type LeadStatus = 'NEW' | 'CONTACTED' | 'QUALIFIED' | 'CONVERTED' | 'LOST';

export interface Lead {
  id: string;
  name: string;
  email: string;
  phone: string;
  platform: Platform;
  campaignName: string;
  utmSource: string;
  utmMedium: string;
  utmCampaign: string;
  cpl: number;
  value: number;
  status: LeadStatus;
  date: string;
  city?: string;
}

export interface AutomationRule {
  id: string;
  name: string;
  platformFilter: 'ALL' | Platform;
  metric: 'CPA' | 'ROAS' | 'SPEND' | 'CTR' | 'FREQUENCY';
  condition: 'GREATER' | 'LESS' | 'EQUALS';
  threshold: number;
  timeFrame: 'TODAY' | '7D' | '30D';
  action: 'PAUSE_CAMPAIGN' | 'INCREASE_BUDGET' | 'DECREASE_BUDGET' | 'NOTIFY_WHATSAPP' | 'NOTIFY_EMAIL';
  actionValue?: number;
  isEnabled: boolean;
  lastTriggered?: string;
  triggerCount: number;
}

export interface Workspace {
  id: string;
  name: string;
  clientName: string;
  currency: string;
  timezone: string;
}

export interface NotificationItem {
  id: string;
  title: string;
  description: string;
  timestamp: string;
  type: 'warning' | 'info' | 'success' | 'alert';
  isRead: boolean;
}

export type DateRangeKey = 'today' | '7d' | '30d' | 'month' | 'compare';
