<div class="space-y-6 pb-12">
    <div class="glass-panel p-6 rounded-2xl border border-slate-800 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-100">Hub de Conexões e APIs de Publicidade</h1>
            <p class="text-xs text-slate-400 mt-1">Gerenciamento de credenciais OAuth, tokens de acesso e Webhooks.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($integrations as $item)
        <div class="glass-panel-interactive p-5 rounded-2xl flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="font-bold text-xs uppercase px-2.5 py-1 rounded bg-slate-800 text-brand-cyan">{{ $item->platform }}</span>
                    <span class="text-[10px] px-2.5 py-1 rounded-full font-bold border {{ $item->status === 'CONNECTED' ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30' : 'bg-rose-500/20 text-rose-400 border-rose-500/30' }}">
                        {{ $item->status }}
                    </span>
                </div>
                <h3 class="font-bold text-slate-100 text-sm mb-1">{{ $item->name }}</h3>
                <p class="text-xs text-slate-400 mb-3">{{ $item->ad_account_name }}</p>

                <div class="space-y-1 text-xs text-slate-300 py-2 border-t border-b border-slate-800 mb-4">
                    <div class="flex justify-between">
                        <span class="text-slate-400">ID da Conta:</span>
                        <span class="font-mono text-slate-200">{{ $item->account_id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Última Sincronização:</span>
                        <span class="font-semibold text-brand-cyan">{{ $item->last_synced_at ? $item->last_synced_at->diffForHumans() : 'Nunca' }}</span>
                    </div>
                </div>
            </div>

            <button wire:click="toggleConnection({{ $item->id }})" class="w-full py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-bold text-slate-200 border border-slate-700">
                {{ $item->status === 'CONNECTED' ? 'Desconectar API' : 'Conectar Agora' }}
            </button>
        </div>
        @endforeach
    </div>
</div>
