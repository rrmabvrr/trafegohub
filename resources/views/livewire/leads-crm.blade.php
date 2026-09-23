<div class="space-y-6 pb-12">
    <div class="glass-panel p-6 rounded-2xl border border-slate-800 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-100">Central de Leads & CRM Lite</h1>
            <p class="text-xs text-slate-400 mt-1">Atribuição UTM, status de negociação e valor de pipeline.</p>
        </div>
        <button wire:click="$set('showAddModal', true)" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-brand-cyan to-blue-600 text-slate-950 font-bold text-xs shadow-neon-blue">+ Adicionar Lead</button>
    </div>

    <div class="glass-panel p-4 rounded-2xl border border-slate-800 flex items-center justify-between">
        <input type="text" wire:model.live="search" placeholder="Buscar lead..." class="px-3 py-1.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white w-72">
        <span class="text-xs text-slate-400">Total: <strong class="text-white">{{ $leads->count() }}</strong></span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        @foreach(['NEW' => 'Novos Leads', 'CONTACTED' => 'Em Contato', 'QUALIFIED' => 'Qualificados', 'CONVERTED' => 'Convertidos', 'LOST' => 'Perdidos'] as $statusKey => $statusTitle)
        @php $colLeads = $leads->where('status', $statusKey); @endphp
        <div class="glass-panel p-3 rounded-2xl border border-slate-800 min-h-[450px]">
            <div class="pb-2 mb-3 border-b border-slate-800 flex justify-between items-center">
                <span class="text-xs font-bold text-brand-cyan">{{ $statusTitle }}</span>
                <span class="text-xs text-slate-400 font-bold bg-slate-800 px-2 py-0.5 rounded-full">{{ $colLeads->count() }}</span>
            </div>

            <div class="space-y-2">
                @foreach($colLeads as $lead)
                <div class="p-3 rounded-xl bg-slate-900 border border-slate-800 text-xs">
                    <h4 class="font-bold text-slate-100">{{ $lead->name }}</h4>
                    <p class="text-[11px] text-slate-400 truncate">{{ $lead->email }}</p>
                    <div class="mt-2 pt-2 border-t border-slate-800 flex justify-between items-center">
                        <span class="text-[10px] text-brand-cyan font-mono">UTM: {{ $lead->utm_source }}</span>
                        <span class="font-extrabold text-emerald-400">R$ {{ number_format($lead->deal_value, 2, ',', '.') }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</div>
