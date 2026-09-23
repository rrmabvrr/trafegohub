<div class="space-y-6 pb-12">
    <div class="glass-panel p-6 rounded-2xl border border-brand-cyan/20 flex flex-col xl:flex-row xl:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-cyan/10 border border-brand-cyan/30 text-brand-cyan text-xs font-semibold mb-2">
                Central de inteligência de tráfego pago
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-100 tracking-tight">Dashboard de performance</h1>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">Métricas consolidadas por período, cliente, plataforma, conta e campanha.</p>
        </div>

        <button wire:click="triggerSync" wire:loading.attr="disabled" class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-brand-cyan via-blue-600 to-brand-violet text-slate-950 font-bold text-xs shadow-neon-blue hover:brightness-110 transition disabled:opacity-50">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            <span wire:loading.remove wire:target="triggerSync">Sincronizar APIs</span>
            <span wire:loading wire:target="triggerSync">Sincronizando...</span>
        </button>
    </div>

    <div class="glass-panel p-4 rounded-2xl border border-slate-800">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <label class="text-[11px] text-slate-400 uppercase font-semibold">Período
                <select wire:model.live="dateRange" class="mt-1 w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-slate-200">
                    <option value="today">Hoje</option><option value="7d">Últimos 7 dias</option><option value="30d">Últimos 30 dias</option><option value="month">Este mês</option><option value="90d">Últimos 90 dias</option>
                </select>
            </label>
            <label class="text-[11px] text-slate-400 uppercase font-semibold">Cliente
                <select wire:model.live="clientId" class="mt-1 w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-slate-200"><option value="all">Todos os clientes</option>@foreach($clients as $client)<option value="{{ $client->id }}">{{ $client->name }}</option>@endforeach</select>
            </label>
            <label class="text-[11px] text-slate-400 uppercase font-semibold">Plataforma
                <select wire:model.live="platform" class="mt-1 w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-slate-200"><option value="all">Todas as plataformas</option><option value="meta">Meta Ads</option><option value="google">Google Ads</option><option value="tiktok">TikTok Ads</option><option value="linkedin">LinkedIn Ads</option><option value="microsoft">Microsoft Advertising</option><option value="pinterest">Pinterest Ads</option></select>
            </label>
            <label class="text-[11px] text-slate-400 uppercase font-semibold">Conta
                <select wire:model.live="integrationId" class="mt-1 w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-slate-200"><option value="all">Todas as contas</option>@foreach($integrations as $integration)<option value="{{ $integration->id }}">{{ $integration->ad_account_name }}</option>@endforeach</select>
            </label>
            <label class="text-[11px] text-slate-400 uppercase font-semibold">Campanha
                <select wire:model.live="campaignId" class="mt-1 w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-slate-200"><option value="all">Todas as campanhas</option>@foreach($campaignOptions as $campaignOption)<option value="{{ $campaignOption->id }}">{{ $campaignOption->name }}</option>@endforeach</select>
            </label>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $cards = [
                ['label' => 'Investimento total', 'value' => 'R$ '.number_format($metrics['spend'], 2, ',', '.'), 'tone' => 'text-slate-100'],
                ['label' => 'Investimento hoje', 'value' => 'R$ '.number_format($todayMetrics['spend'], 2, ',', '.'), 'tone' => 'text-cyan-300'],
                ['label' => 'Investimento no mês', 'value' => 'R$ '.number_format($monthMetrics['spend'], 2, ',', '.'), 'tone' => 'text-blue-300'],
                ['label' => 'Leads', 'value' => number_format($metrics['leads']), 'tone' => 'text-amber-300'],
                ['label' => 'Conversões', 'value' => number_format($metrics['conversions']), 'tone' => 'text-emerald-300'],
                ['label' => 'CPL', 'value' => 'R$ '.number_format($metrics['cpl'], 2, ',', '.'), 'tone' => 'text-slate-100'],
                ['label' => 'CPC', 'value' => 'R$ '.number_format($metrics['cpc'], 2, ',', '.'), 'tone' => 'text-slate-100'],
                ['label' => 'CPM', 'value' => 'R$ '.number_format($metrics['cpm'], 2, ',', '.'), 'tone' => 'text-slate-100'],
                ['label' => 'CTR', 'value' => number_format($metrics['ctr'], 2, ',', '.').'%', 'tone' => 'text-violet-300'],
                ['label' => 'ROAS', 'value' => number_format($metrics['roas'], 2, ',', '.').'x', 'tone' => 'text-emerald-300'],
                ['label' => 'Impressões', 'value' => number_format($metrics['impressions']), 'tone' => 'text-slate-100'],
                ['label' => 'Alcance', 'value' => number_format($metrics['reach']), 'tone' => 'text-slate-100'],
                ['label' => 'Cliques', 'value' => number_format($metrics['clicks']), 'tone' => 'text-slate-100'],
            ];
        @endphp
        @foreach($cards as $card)
            <div class="glass-panel-interactive p-5 rounded-2xl"><span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block mb-1">{{ $card['label'] }}</span><div class="text-2xl font-extrabold {{ $card['tone'] }}">{{ $card['value'] }}</div></div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
        <div class="glass-panel p-5 rounded-2xl border border-slate-800 xl:col-span-2" wire:ignore>
            <div class="flex items-center justify-between mb-4"><div><h2 class="text-base font-bold text-slate-100">Investimento e receita</h2><p class="text-xs text-slate-500">Série diária do período selecionado</p></div><span class="text-[10px] text-slate-500">{{ $periodStart->format('d/m/Y') }} - {{ $periodEnd->format('d/m/Y') }}</span></div>
            <div class="h-64" x-data="dashboardChart(@js($chartData))" x-init="init()"><canvas x-ref="canvas"></canvas></div>
        </div>
        <div class="glass-panel p-5 rounded-2xl border border-slate-800" wire:ignore>
            <h2 class="text-base font-bold text-slate-100 mb-1">Leads por dia</h2><p class="text-xs text-slate-500 mb-4">Distribuição no período filtrado</p>
            <div class="h-64" x-data="dashboardLeadsChart(@js($chartData))" x-init="init()"><canvas x-ref="canvas"></canvas></div>
        </div>
    </div>

    <div class="glass-panel p-5 rounded-2xl border border-slate-800">
        <h2 class="text-base font-bold text-slate-100 mb-4">Campanhas com maior ROAS</h2>
        <div class="overflow-x-auto rounded-xl border border-slate-800"><table class="w-full text-left text-xs"><thead class="bg-slate-950 text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-800"><tr><th class="px-4 py-3">Plataforma e campanha</th><th class="px-4 py-3">Investido</th><th class="px-4 py-3">Cliques</th><th class="px-4 py-3">Conversões</th><th class="px-4 py-3">ROAS</th></tr></thead><tbody class="divide-y divide-slate-800 text-slate-300">
            @foreach($topCampaigns as $campaign)<tr class="hover:bg-slate-800/40"><td class="px-4 py-3"><span class="font-bold text-slate-100 uppercase text-[10px] bg-slate-800 px-2 py-0.5 rounded mr-2">{{ $campaign->platform }}</span>{{ $campaign->name }}</td><td class="px-4 py-3">R$ {{ number_format($campaign->total_spend, 2, ',', '.') }}</td><td class="px-4 py-3">{{ number_format($campaign->clicks) }}</td><td class="px-4 py-3 font-bold">{{ number_format($campaign->conversions) }}</td><td class="px-4 py-3 text-emerald-300 font-bold">{{ number_format($campaign->roas, 2, ',', '.') }}x</td></tr>@endforeach
        </tbody></table></div>
    </div>

    @script
    <script>
        $wire.on('dashboard-chart-updated', (event) => window.dispatchEvent(new CustomEvent('dashboard-chart-updated', { detail: event.chartData })));
    </script>
    @endscript
</div>
