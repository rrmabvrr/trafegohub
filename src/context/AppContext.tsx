import React, { createContext, useContext, useState, useEffect } from 'react';
import { 
  Campaign, 
  Integration, 
  Creative, 
  Lead, 
  AutomationRule, 
  Workspace, 
  NotificationItem, 
  Platform, 
  DateRangeKey, 
  LeadStatus 
} from '../types';
import { 
  initialWorkspaces, 
  initialIntegrations, 
  initialCampaigns, 
  initialCreatives, 
  initialLeads, 
  initialAutomationRules, 
  initialNotifications 
} from '../mock/initialData';

interface AppContextType {
  workspaces: Workspace[];
  currentWorkspace: Workspace;
  setCurrentWorkspace: (ws: Workspace) => void;
  
  campaigns: Campaign[];
  integrations: Integration[];
  creatives: Creative[];
  leads: Lead[];
  automationRules: AutomationRule[];
  notifications: NotificationItem[];
  
  activeTab: string;
  setActiveTab: (tab: string) => void;
  
  dateRange: DateRangeKey;
  setDateRange: (range: DateRangeKey) => void;
  
  selectedPlatform: Platform | 'all';
  setSelectedPlatform: (p: Platform | 'all') => void;
  
  searchQuery: string;
  setSearchQuery: (q: string) => void;
  
  isSyncing: boolean;
  
  // Actions
  toggleCampaignStatus: (id: string) => void;
  updateCampaignBudget: (id: string, budget: number) => void;
  duplicateCampaign: (id: string) => void;
  addCampaign: (campaign: Omit<Campaign, 'id'>) => void;
  
  toggleIntegrationStatus: (id: string) => void;
  triggerSync: (id?: string) => void;
  
  updateLeadStatus: (id: string, status: LeadStatus) => void;
  addLead: (lead: Omit<Lead, 'id'>) => void;
  
  addAutomationRule: (rule: Omit<AutomationRule, 'id' | 'triggerCount'>) => void;
  toggleAutomationRule: (id: string) => void;
  
  markNotificationRead: (id: string) => void;
  markAllNotificationsRead: () => void;
}

const AppContext = createContext<AppContextType | undefined>(undefined);

export const AppProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const [workspaces] = useState<Workspace[]>(initialWorkspaces);
  const [currentWorkspace, setCurrentWorkspace] = useState<Workspace>(initialWorkspaces[0]);
  
  const [campaigns, setCampaigns] = useState<Campaign[]>(() => {
    const saved = localStorage.getItem('th_campaigns');
    return saved ? JSON.parse(saved) : initialCampaigns;
  });

  const [integrations, setIntegrations] = useState<Integration[]>(() => {
    const saved = localStorage.getItem('th_integrations');
    return saved ? JSON.parse(saved) : initialIntegrations;
  });

  const [creatives] = useState<Creative[]>(initialCreatives);

  const [leads, setLeads] = useState<Lead[]>(() => {
    const saved = localStorage.getItem('th_leads');
    return saved ? JSON.parse(saved) : initialLeads;
  });

  const [automationRules, setAutomationRules] = useState<AutomationRule[]>(() => {
    const saved = localStorage.getItem('th_rules');
    return saved ? JSON.parse(saved) : initialAutomationRules;
  });

  const [notifications, setNotifications] = useState<NotificationItem[]>(initialNotifications);

  const [activeTab, setActiveTab] = useState<string>('dashboard');
  const [dateRange, setDateRange] = useState<DateRangeKey>('30d');
  const [selectedPlatform, setSelectedPlatform] = useState<Platform | 'all'>('all');
  const [searchQuery, setSearchQuery] = useState<string>('');
  const [isSyncing, setIsSyncing] = useState<boolean>(false);

  // Sync to local storage
  useEffect(() => {
    localStorage.setItem('th_campaigns', JSON.stringify(campaigns));
  }, [campaigns]);

  useEffect(() => {
    localStorage.setItem('th_integrations', JSON.stringify(integrations));
  }, [integrations]);

  useEffect(() => {
    localStorage.setItem('th_leads', JSON.stringify(leads));
  }, [leads]);

  useEffect(() => {
    localStorage.setItem('th_rules', JSON.stringify(automationRules));
  }, [automationRules]);

  // Campaign Actions
  const toggleCampaignStatus = (id: string) => {
    setCampaigns(prev => prev.map(c => {
      if (c.id === id) {
        const nextStatus = c.status === 'ACTIVE' ? 'PAUSED' : 'ACTIVE';
        return { ...c, status: nextStatus };
      }
      return c;
    }));
  };

  const updateCampaignBudget = (id: string, budget: number) => {
    setCampaigns(prev => prev.map(c => {
      if (c.id === id) {
        return { ...c, dailyBudget: budget };
      }
      return c;
    }));
  };

  const duplicateCampaign = (id: string) => {
    const target = campaigns.find(c => c.id === id);
    if (!target) return;
    const duplicated: Campaign = {
      ...target,
      id: `cmp-${Date.now()}`,
      name: `${target.name} (Cópia)`,
      status: 'PAUSED',
      totalSpend: 0,
      impressions: 0,
      clicks: 0,
      conversions: 0,
      revenue: 0,
      roas: 0
    };
    setCampaigns(prev => [duplicated, ...prev]);
  };

  const addCampaign = (campaignData: Omit<Campaign, 'id'>) => {
    const newCamp: Campaign = {
      ...campaignData,
      id: `cmp-${Date.now()}`
    };
    setCampaigns(prev => [newCamp, ...prev]);
  };

  // Integration Actions
  const toggleIntegrationStatus = (id: string) => {
    setIntegrations(prev => prev.map(item => {
      if (item.id === id) {
        const nextStatus = item.status === 'CONNECTED' ? 'DISCONNECTED' : 'CONNECTED';
        return { 
          ...item, 
          status: nextStatus,
          lastSync: nextStatus === 'CONNECTED' ? 'Agora mesmo' : item.lastSync
        };
      }
      return item;
    }));
  };

  const triggerSync = (id?: string) => {
    setIsSyncing(true);
    setTimeout(() => {
      setIntegrations(prev => prev.map(item => {
        if (!id || item.id === id) {
          if (item.status === 'CONNECTED') {
            return { ...item, lastSync: 'Agora mesmo' };
          }
        }
        return item;
      }));
      setIsSyncing(false);
      
      setNotifications(prev => [
        {
          id: `notif-${Date.now()}`,
          title: 'Sincronização Concluída',
          description: 'Métricas de campanhas sincronizadas com APIs oficiais.',
          timestamp: 'Agora mesmo',
          type: 'info',
          isRead: false
        },
        ...prev
      ]);
    }, 1500);
  };

  // Lead Actions
  const updateLeadStatus = (id: string, status: LeadStatus) => {
    setLeads(prev => prev.map(l => l.id === id ? { ...l, status } : l));
  };

  const addLead = (leadData: Omit<Lead, 'id'>) => {
    const newLead: Lead = {
      ...leadData,
      id: `ld-${Date.now()}`
    };
    setLeads(prev => [newLead, ...prev]);
  };

  // Automation Actions
  const addAutomationRule = (ruleData: Omit<AutomationRule, 'id' | 'triggerCount'>) => {
    const newRule: AutomationRule = {
      ...ruleData,
      id: `rule-${Date.now()}`,
      triggerCount: 0
    };
    setAutomationRules(prev => [newRule, ...prev]);
  };

  const toggleAutomationRule = (id: string) => {
    setAutomationRules(prev => prev.map(r => r.id === id ? { ...r, isEnabled: !r.isEnabled } : r));
  };

  // Notification Actions
  const markNotificationRead = (id: string) => {
    setNotifications(prev => prev.map(n => n.id === id ? { ...n, isRead: true } : n));
  };

  const markAllNotificationsRead = () => {
    setNotifications(prev => prev.map(n => ({ ...n, isRead: true })));
  };

  return (
    <AppContext.Provider value={{
      workspaces,
      currentWorkspace,
      setCurrentWorkspace,
      campaigns,
      integrations,
      creatives,
      leads,
      automationRules,
      notifications,
      activeTab,
      setActiveTab,
      dateRange,
      setDateRange,
      selectedPlatform,
      setSelectedPlatform,
      searchQuery,
      setSearchQuery,
      isSyncing,
      toggleCampaignStatus,
      updateCampaignBudget,
      duplicateCampaign,
      addCampaign,
      toggleIntegrationStatus,
      triggerSync,
      updateLeadStatus,
      addLead,
      addAutomationRule,
      toggleAutomationRule,
      markNotificationRead,
      markAllNotificationsRead
    }}>
      {children}
    </AppContext.Provider>
  );
};

export const useApp = () => {
  const context = useContext(AppContext);
  if (!context) {
    throw new Error('useApp must be used within an AppProvider');
  }
  return context;
};
