<div class="space-y-6 pb-12">
    <!-- Welcome Banner -->
    <div class="glass-panel p-6 rounded-2xl border border-brand-cyan/20 relative overflow-hidden flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-cyan/10 border border-brand-cyan/30 text-brand-cyan text-xs font-semibold mb-2">
                Central de Inteligência de Tráfego Pago (Laravel 12 + Livewire)
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-100 tracking-tight">
                Painel Central de inteligência de Anúncios
            </h1>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">
                Métricas unificadas de Meta Ads, Google Ads, TikTok Ads e LinkedIn Ads em tempo real.
            </p>
        </div>

        <button wire:click="triggerSync" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-brand-cyan via-blue-600 to-brand-violet text-slate-950 font-bold text-xs shadow-neon-blue hover:brightness-110 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            <span>Sincronizar APIs Agora</span>
        </button>
    </div>

    <!-- Primary KPI Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- KPI 1 -->
        <div class="glass-panel-interactive p-5 rounded-2xl">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-1">Investimento Total</span>
            <div class="text-2xl font-extrabold text-slate-100">R$ {{ number_format($totalSpend, 2, ',', '.') }}</div>
            <span class="text-xs text-emerald-400 font-bold mt-2 block">+12.4% vs. período anterior</span>
        </div>

        <!-- KPI 2 -->
        <div class="glass-panel-interactive p-5 rounded-2xl">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-1">Faturamento (Vendas)</span>
            <div class="text-2xl font-extrabold text-emerald-400">R$ {{ number_format($totalRevenue, 2, ',', '.') }}</div>
            <span class="text-xs text-emerald-400 font-bold mt-2 block">+24.8% retorno direto</span>
        </div>

        <!-- KPI 3 -->
        <div class="glass-panel-interactive p-5 rounded-2xl">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-1">ROAS Unificado</span>
            <div class="text-2xl font-extrabold gradient-text-cyan">{{ $globalRoas }}x</div>
            <span class="text-xs text-emerald-400 font-bold mt-2 block">+0.85x eficiência acumulada</span>
        </div>

        <!-- KPI 4 -->
        <div class="glass-panel-interactive p-5 rounded-2xl">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-1">CPA Médio</span>
            <div class="text-2xl font-extrabold text-slate-100">R$ {{ $avgCpa }}</div>
            <span class="text-xs text-slate-400 mt-2 block">{{ $totalConversions }} conversões</span>
        </div>
    </div>

    <!-- Top Performing Campaigns Table -->
    <div class="glass-panel p-5 rounded-2xl border border-slate-800">
        <h2 class="text-base font-bold text-slate-100 mb-4">Campanhas com Maior Desempenho (Top ROAS)</h2>
        <div class="overflow-x-auto rounded-xl border border-slate-800">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950 text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="px-4 py-3">Plataforma & Nome</th>
                        <th class="px-4 py-3">Orçamento Diário</th>
                        <th class="px-4 py-3">Investido</th>
                        <th class="px-4 py-3">Conversões</th>
                        <th class="px-4 py-3">CPA</th>
                        <th class="px-4 py-3">ROAS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 text-slate-300">
                    @foreach($topCampaigns as $c)
                    <tr class="hover:bg-slate-800/40">
                        <td class="px-4 py-3">
                            <span class="font-bold text-slate-100 uppercase text-[10px] bg-slate-800 px-2 py-0.5 rounded mr-2">{{ $c->platform }}</span>
                            <span class="font-bold text-slate-200">{{ $c->name }}</span>
                        </td>
                        <td class="px-4 py-3 font-semibold">R$ {{ number_format($c->daily_budget, 2, ',', '.') }}</td>
                        <td class="px-4 py-3">R$ {{ number_format($c->total_spend, 2, ',', '.') }}</td>
                        <td class="px-4 py-3 font-bold text-slate-100">{{ $c->conversions }}</td>
                        <td class="px-4 py-3 text-slate-300">R$ {{ number_format($c->cpl, 2, ',', '.') }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-400 font-extrabold border border-emerald-500/30">
                                {{ $c->roas }}x
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
