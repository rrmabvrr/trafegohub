import React, { useState } from 'react';
import { AppProvider, useApp } from './context/AppContext';
import { Sidebar } from './components/layout/Sidebar';
import { Header } from './components/layout/Header';
import { Dashboard } from './pages/Dashboard';
import { Campaigns } from './pages/Campaigns';
import { Integrations } from './pages/Integrations';
import { Leads } from './pages/Leads';
import { Creatives } from './pages/Creatives';
import { Automation } from './pages/Automation';
import { Reports } from './pages/Reports';
import { NewCampaignModal } from './components/modals/NewCampaignModal';
import { CampaignDetailModal } from './components/modals/CampaignDetailModal';
import { ConnectApiModal } from './components/modals/ConnectApiModal';
import { Campaign, Integration } from './types';

const MainLayout: React.FC = () => {
  const { activeTab } = useApp();
  const [collapsed, setCollapsed] = useState(false);

  const [isNewCampaignModalOpen, setIsNewCampaignModalOpen] = useState(false);
  const [selectedCampaignForDetail, setSelectedCampaignForDetail] = useState<Campaign | null>(null);
  const [selectedIntegrationForConnect, setSelectedIntegrationForConnect] = useState<Integration | null>(null);

  return (
    <div className="min-h-screen bg-hub-950 text-slate-100 flex flex-col selection:bg-brand-cyan/30 selection:text-white">
      {/* Sidebar Navigation */}
      <Sidebar collapsed={collapsed} setCollapsed={setCollapsed} />

      {/* Main Container Area */}
      <div className={`flex-1 flex flex-col transition-all duration-300 ${collapsed ? 'pl-20' : 'pl-64'}`}>
        {/* Header Bar */}
        <Header 
          collapsed={collapsed} 
          setCollapsed={setCollapsed} 
          onOpenNewCampaignModal={() => setIsNewCampaignModalOpen(true)}
        />

        {/* Dynamic Page View */}
        <main className="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
          {activeTab === 'dashboard' && (
            <Dashboard onOpenDetail={(c) => setSelectedCampaignForDetail(c)} />
          )}

          {activeTab === 'campaigns' && (
            <Campaigns 
              onOpenDetail={(c) => setSelectedCampaignForDetail(c)} 
              onOpenNewCampaignModal={() => setIsNewCampaignModalOpen(true)}
            />
          )}

          {activeTab === 'integrations' && (
            <Integrations 
              onOpenConnectModal={(int) => setSelectedIntegrationForConnect(int)} 
            />
          )}

          {activeTab === 'leads' && <Leads />}

          {activeTab === 'creatives' && <Creatives />}

          {activeTab === 'automation' && <Automation />}

          {activeTab === 'reports' && <Reports />}
        </main>
      </div>

      {/* Modals */}
      <NewCampaignModal 
        isOpen={isNewCampaignModalOpen} 
        onClose={() => setIsNewCampaignModalOpen(false)} 
      />

      <CampaignDetailModal 
        campaign={selectedCampaignForDetail} 
        onClose={() => setSelectedCampaignForDetail(null)} 
      />

      <ConnectApiModal 
        integration={selectedIntegrationForConnect} 
        onClose={() => setSelectedIntegrationForConnect(null)} 
      />
    </div>
  );
};

export function App() {
  return (
    <AppProvider>
      <MainLayout />
    </AppProvider>
  );
}

export default App;
