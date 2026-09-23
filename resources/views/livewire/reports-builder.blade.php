<div class="space-y-6 pb-12">
    <div class="glass-panel p-6 rounded-2xl border border-slate-800 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-100">Relatórios Executive & Exportação PDF</h1>
            <p class="text-xs text-slate-400 mt-1">Gere relatórios customizados para apresentação aos clientes.</p>
        </div>
        <button onclick="window.print()" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-brand-cyan to-blue-600 text-slate-950 font-bold text-xs shadow-neon-blue">Imprimir / Salvar PDF</button>
    </div>

    <div class="glass-panel p-6 rounded-2xl border border-slate-800">
        <div class="border-b border-slate-800 pb-4 mb-4 flex justify-between items-center">
            <div>
                <h2 class="text-xl font-extrabold text-slate-100">{{ $workspace->client_name ?? 'Cliente E-commerce' }}</h2>
                <p class="text-xs text-slate-400">Relatório Executivo Oficial de Tráfego Pago</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-brand-cyan/20 flex items-center justify-center font-bold text-brand-cyan">TH</div>
        </div>

        <div class="grid grid-cols-3 gap-3 mb-6">
            <div class="p-3 bg-slate-900 rounded-xl border border-slate-800">
                <span class="text-[10px] text-slate-400 block">Investimento Total</span>
                <span class="text-lg font-bold text-slate-100">R$ {{ number_format($totalSpend, 2, ',', '.') }}</span>
            </div>
            <div class="p-3 bg-slate-900 rounded-xl border border-slate-800">
                <span class="text-[10px] text-slate-400 block">Faturamento Retornado</span>
                <span class="text-lg font-bold text-emerald-400">R$ {{ number_format($totalRevenue, 2, ',', '.') }}</span>
            </div>
            <div class="p-3 bg-slate-900 rounded-xl border border-slate-800">
                <span class="text-[10px] text-slate-400 block">ROAS Médio</span>
                <span class="text-lg font-bold text-brand-cyan">{{ $roas }}x</span>
            </div>
        </div>
    </div>
</div>
