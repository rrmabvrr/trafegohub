<?php

namespace Database\Seeders;

use App\Models\AutomationRule;
use App\Models\Campaign;
use App\Models\Client;
use App\Models\Creative;
use App\Models\Integration;
use App\Models\Lead;
use App\Models\Organization;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $organization = Organization::create([
            'name' => 'Agência Alfa Marketing',
            'slug' => 'agencia-alfa-marketing',
        ]);

        $client = Client::create([
            'organization_id' => $organization->id,
            'name' => 'Infoprodutos Master E-commerce',
        ]);

        // 1. Create Workspace compatibility context
        $workspace = Workspace::create([
            'name' => 'Agência Alfa Marketing',
            'client_name' => 'Infoprodutos Master E-commerce',
            'organization_id' => $organization->id,
            'client_id' => $client->id,
            'currency' => 'BRL',
            'timezone' => 'America/Sao_Paulo',
        ]);

        // 2. Create Master Admin User
        $admin = User::create([
            'name' => 'Gestor de Tráfego Senior',
            'email' => 'admin@trafegohub.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'active_workspace_id' => $workspace->id,
            'active_organization_id' => $organization->id,
        ]);

        $organization->users()->attach($admin, ['role' => 'owner']);

        // 3. Create Integrations
        $metaInt = Integration::create([
            'workspace_id' => $workspace->id,
            'platform' => 'meta',
            'name' => 'Meta Ads (Facebook & Instagram)',
            'account_id' => 'act_409182749102',
            'ad_account_name' => 'Infoprodutos BM Master',
            'status' => 'CONNECTED',
            'webhook_url' => 'https://api.trafegohub.io/v1/webhooks/meta/wh_8f9a01b2',
            'last_synced_at' => now()->subMinutes(5),
        ]);

        $googleInt = Integration::create([
            'workspace_id' => $workspace->id,
            'platform' => 'google',
            'name' => 'Google Ads (Pesquisa & PMax)',
            'account_id' => '839-102-9481',
            'ad_account_name' => 'Google Search & Display Account',
            'status' => 'CONNECTED',
            'webhook_url' => 'https://api.trafegohub.io/v1/webhooks/google/wh_3c4d5e6f',
            'last_synced_at' => now()->subMinutes(12),
        ]);

        $tiktokInt = Integration::create([
            'workspace_id' => $workspace->id,
            'platform' => 'tiktok',
            'name' => 'TikTok Ads Manager',
            'account_id' => '719284019284019',
            'ad_account_name' => 'TikTok For Business Brazil',
            'status' => 'CONNECTED',
            'last_synced_at' => now()->subMinutes(20),
        ]);

        $linkedinInt = Integration::create([
            'workspace_id' => $workspace->id,
            'platform' => 'linkedin',
            'name' => 'LinkedIn Campaign Manager',
            'account_id' => '509281740',
            'ad_account_name' => 'B2B Corporate Leads Account',
            'status' => 'CONNECTED',
            'last_synced_at' => now()->subMinutes(30),
        ]);

        // 4. Create Campaigns
        $cmp1 = Campaign::create([
            'workspace_id' => $workspace->id,
            'integration_id' => $metaInt->id,
            'platform' => 'meta',
            'name' => '[Meta] [CBO] Vendas Infoproduto - VSL 3.0',
            'status' => 'ACTIVE',
            'objective' => 'SALES',
            'daily_budget' => 450.00,
            'total_spend' => 14280.50,
            'impressions' => 482910,
            'clicks' => 19840,
            'ctr' => 4.11,
            'cpc' => 0.72,
            'cpl' => 18.50,
            'conversions' => 384,
            'roas' => 4.85,
            'revenue' => 69260.40,
            'start_date' => '2026-09-01',
            'target_audience' => 'Lookalike 1% Compradores + Interesses Marketing',
        ]);

        $cmp2 = Campaign::create([
            'workspace_id' => $workspace->id,
            'integration_id' => $googleInt->id,
            'platform' => 'google',
            'name' => '[Google] Search - Fundo de Funil [Marca]',
            'status' => 'ACTIVE',
            'objective' => 'SALES',
            'daily_budget' => 300.00,
            'total_spend' => 8940.00,
            'impressions' => 98400,
            'clicks' => 14200,
            'ctr' => 14.43,
            'cpc' => 0.63,
            'cpl' => 12.20,
            'conversions' => 412,
            'roas' => 6.12,
            'revenue' => 54712.80,
            'start_date' => '2026-08-15',
            'target_audience' => 'Busca Exata Palavras-Chave de Alta Intenção',
        ]);

        $cmp3 = Campaign::create([
            'workspace_id' => $workspace->id,
            'integration_id' => $tiktokInt->id,
            'platform' => 'tiktok',
            'name' => '[TikTok] Spark Ads - UGC Vídeo Viral Topo',
            'status' => 'ACTIVE',
            'objective' => 'LEADS',
            'daily_budget' => 250.00,
            'total_spend' => 6200.00,
            'impressions' => 892000,
            'clicks' => 28400,
            'ctr' => 3.18,
            'cpc' => 0.22,
            'cpl' => 9.80,
            'conversions' => 632,
            'roas' => 3.25,
            'revenue' => 20150.00,
            'start_date' => '2026-09-05',
            'target_audience' => 'Gen-Z & Millennials 18-35 anos',
        ]);

        // 5. Create Creatives
        Creative::create([
            'campaign_id' => $cmp1->id,
            'name' => 'Criativo #01 - Vídeo Depoimento Prova Social (VSL 3s)',
            'type' => 'video',
            'platform' => 'meta',
            'thumbnail_url' => 'https://images.unsplash.com/photo-1551836022-d5d88e9218df?auto=format&fit=crop&w=400&q=80',
            'ctr' => 5.42,
            'hook_rate' => 64.80,
            'roas' => 5.80,
            'spend' => 4200.00,
            'conversions' => 142,
            'fatigue_level' => 'GOOD',
            'frequency' => 1.85,
        ]);

        Creative::create([
            'campaign_id' => $cmp1->id,
            'name' => 'Criativo #02 - Imagem Carrossel Benefícios do Produto',
            'type' => 'carousel',
            'platform' => 'meta',
            'thumbnail_url' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=400&q=80',
            'ctr' => 2.10,
            'hook_rate' => 32.10,
            'roas' => 2.10,
            'spend' => 3800.00,
            'conversions' => 48,
            'fatigue_level' => 'CRITICAL',
            'fatigue_reason' => 'Frequência muito alta (4.65) e queda repentina no CTR (-40%). Recomenda-se trocar a imagem.',
            'frequency' => 4.65,
        ]);

        // 6. Create Leads
        Lead::create([
            'workspace_id' => $workspace->id,
            'campaign_id' => $cmp1->id,
            'name' => 'Carlos Eduardo Silva',
            'email' => 'carlos.silva@techcorp.com.br',
            'phone' => '(11) 98765-4321',
            'platform' => 'meta',
            'utm_source' => 'facebook',
            'utm_medium' => 'cpc',
            'utm_campaign' => 'vsl_3_0_cbo',
            'cpl' => 18.50,
            'deal_value' => 1497.00,
            'status' => 'CONVERTED',
            'city' => 'São Paulo - SP',
        ]);

        Lead::create([
            'workspace_id' => $workspace->id,
            'campaign_id' => $cmp2->id,
            'name' => 'Mariana Alcantara',
            'email' => 'mariana.alcantara@gmail.com',
            'phone' => '(21) 99123-8844',
            'platform' => 'google',
            'utm_source' => 'google',
            'utm_medium' => 'search',
            'utm_campaign' => 'keywords_marca_exata',
            'cpl' => 12.20,
            'deal_value' => 2990.00,
            'status' => 'QUALIFIED',
            'city' => 'Rio de Janeiro - RJ',
        ]);

        // 7. Create Automation Rules
        AutomationRule::create([
            'workspace_id' => $workspace->id,
            'name' => 'Pausar automátida se CPA exceder R$ 40,00',
            'platform_filter' => 'ALL',
            'metric' => 'CPA',
            'condition' => 'GREATER',
            'threshold' => 40.00,
            'time_frame' => 'TODAY',
            'action' => 'PAUSE_CAMPAIGN',
            'is_enabled' => true,
            'trigger_count' => 3,
            'last_triggered_at' => now()->subDays(2),
        ]);

        AutomationRule::create([
            'workspace_id' => $workspace->id,
            'name' => 'Escalar orçamento em +20% se ROAS > 4.5',
            'platform_filter' => 'meta',
            'metric' => 'ROAS',
            'condition' => 'GREATER',
            'threshold' => 4.50,
            'time_frame' => '7D',
            'action' => 'INCREASE_BUDGET',
            'action_value' => 20.00,
            'is_enabled' => true,
            'trigger_count' => 7,
            'last_triggered_at' => now()->subHours(5),
        ]);
    }
}
