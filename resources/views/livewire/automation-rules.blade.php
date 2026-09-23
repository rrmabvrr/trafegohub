<div class="space-y-6 pb-12">
    <div class="glass-panel p-6 rounded-2xl border border-slate-800 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-100">Motor de Automações & Regras Inteligentes</h1>
            <p class="text-xs text-slate-400 mt-1">Proteção de orçamento e escala automatizada.</p>
        </div>
        <button wire:click="$set('showModal', true)" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-brand-cyan to-blue-600 text-slate-950 font-bold text-xs shadow-neon-blue">+ Criar Regra</button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        @foreach($rules as $rule)
        <div class="glass-panel p-5 rounded-2xl border border-slate-800 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-brand-cyan/10 text-brand-cyan">{{ $rule->metric }}</span>
                    <button wire:click="toggleRule({{ $rule->id }})" class="px-2 py-0.5 rounded text-[10px] font-bold {{ $rule->is_enabled ? 'bg-emerald-500/20 text-emerald-400' : 'bg-slate-800 text-slate-400' }}">
                        {{ $rule->is_enabled ? 'ATIVA' : 'PAUSADA' }}
                    </button>
                </div>
                <h3 class="font-bold text-slate-100 text-sm mb-2">{{ $rule->name }}</h3>
                <p class="text-xs text-slate-400">Condição: <strong>{{ $rule->metric }} {{ $rule->condition === 'GREATER' ? '>' : '<' }} {{ $rule->threshold }}</strong></p>
            </div>
        </div>
        @endforeach
    </div>
</div>
