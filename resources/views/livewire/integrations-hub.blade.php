<div class="space-y-6 pb-12">
    <div class="glass-panel p-6 rounded-2xl border border-brand-cyan/20 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="text-xs font-semibold uppercase tracking-wider text-brand-cyan mb-2">Conexões multicanal</div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-100">Contas de Publicidade</h1>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">Organize contas Meta, Google, TikTok e outras plataformas por cliente.</p>
        </div>
        <button wire:click="$set('showModal', true)" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-brand-cyan to-blue-600 text-slate-950 font-bold text-xs shadow-neon-blue hover:brightness-110 transition">+ Nova conta</button>
    </div>

    <div class="glass-panel p-4 rounded-2xl border border-slate-800 flex flex-col lg:flex-row gap-3">
        <input type="search" wire:model.live="search" placeholder="Buscar conta, nome ou ID externo..." class="flex-1 px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-slate-200 focus:outline-none focus:border-brand-cyan">
        <select wire:model.live="platformFilter" class="px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-slate-200"><option value="all">Todas as plataformas</option><option value="meta">Meta Ads</option><option value="google">Google Ads</option><option value="tiktok">TikTok Ads</option><option value="linkedin">LinkedIn Ads</option><option value="microsoft">Microsoft Advertising</option><option value="pinterest">Pinterest Ads</option><option value="kwai">Kwai Ads</option></select>
        <select wire:model.live="statusFilter" class="px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-slate-200"><option value="all">Todos os status</option><option value="CONNECTED">Conectadas</option><option value="DISCONNECTED">Desconectadas</option><option value="ERROR">Com erro</option><option value="SYNCING">Sincronizando</option></select>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @forelse($integrations as $item)
            <div wire:key="integration-{{ $item->id }}" class="glass-panel-interactive p-5 rounded-2xl flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3"><span class="font-bold text-xs uppercase px-2.5 py-1 rounded bg-slate-800 text-brand-cyan">{{ $item->platform }} Ads</span><span class="text-[10px] px-2.5 py-1 rounded-full font-bold border {{ $item->status === 'CONNECTED' ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30' : ($item->status === 'ERROR' ? 'bg-rose-500/20 text-rose-400 border-rose-500/30' : 'bg-slate-800 text-slate-400 border-slate-700') }}">{{ $item->status === 'CONNECTED' ? 'CONECTADA' : ($item->status === 'DISCONNECTED' ? 'DESCONECTADA' : $item->status) }}</span></div>
                    <h3 class="font-bold text-slate-100 text-sm mb-1">{{ $item->name }}</h3>
                    <p class="text-xs text-slate-400 mb-3">{{ $item->client?->name ?: $item->workspace?->client?->name ?: 'Cliente não associado' }}</p>
                    <div class="space-y-2 text-xs text-slate-300 py-3 border-t border-b border-slate-800 mb-4">
                        <div class="flex justify-between gap-3"><span class="text-slate-500">ID externo</span><span class="font-mono text-slate-200">{{ $item->external_account_id ?: $item->account_id }}</span></div>
                        <div class="flex justify-between gap-3"><span class="text-slate-500">Moeda / fuso</span><span class="text-right">{{ $item->currency }} · {{ $item->timezone }}</span></div>
                        <div class="flex justify-between gap-3"><span class="text-slate-500">Usuário conectado</span><span class="text-right">{{ $item->connectedUser?->name ?: 'Não informado' }}</span></div>
                        <div class="flex justify-between gap-3"><span class="text-slate-500">Data da conexão</span><span class="text-right">{{ $item->connected_at?->format('d/m/Y H:i') ?: 'Não conectada' }}</span></div>
                        <div class="flex justify-between gap-3"><span class="text-slate-500">Último sincronismo</span><span class="font-semibold text-brand-cyan">{{ $item->last_synced_at ? $item->last_synced_at->diffForHumans() : 'Nunca' }}</span></div>
                        <div class="flex justify-between gap-3"><span class="text-slate-500">Scopes</span><span class="text-right">{{ $item->scopes ? count($item->scopes) . ' permissões' : 'Não informado' }}</span></div>
                        <div class="flex justify-between gap-3"><span class="text-slate-500">Expiração do token</span><span class="text-right">{{ $item->expires_at?->format('d/m/Y H:i') ?: 'Não informada' }}</span></div>
                        @if($item->last_error)<div class="text-[11px] text-rose-300 pt-1">Erro: {{ $item->last_error }}</div>@endif
                    </div>
                </div>
                <div class="grid grid-cols-4 gap-2"><a href="{{ route('integrations.oauth.redirect', $item) }}" class="py-2 rounded-xl bg-brand-cyan/10 hover:bg-brand-cyan/20 text-center text-[10px] font-bold text-brand-cyan border border-brand-cyan/20">OAuth</a><button wire:click="toggleConnection({{ $item->id }})" class="py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-[10px] font-bold text-slate-200 border border-slate-700">{{ $item->status === 'CONNECTED' ? 'Desconectar' : 'Conectar' }}</button><button wire:click="editIntegration({{ $item->id }})" class="py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-[10px] font-bold text-slate-200 border border-slate-700">Editar</button><button wire:click="deleteIntegration({{ $item->id }})" wire:confirm="Excluir esta conta de publicidade?" class="py-2 rounded-xl text-rose-300 hover:bg-rose-500/10 text-[10px] font-bold">Excluir</button></div>
            </div>
        @empty
            <div class="glass-panel rounded-2xl border border-slate-800 p-12 text-center text-slate-500 md:col-span-2 xl:col-span-3">Nenhuma conta de publicidade encontrada.</div>
        @endforelse
    </div>

    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-hub-950/80 backdrop-blur-md"><div class="glass-modal w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-2xl border border-slate-700 p-6">
            <div class="flex items-center justify-between mb-5"><div><h2 class="text-lg font-bold text-slate-100">{{ $editingIntegrationId ? 'Editar conta' : 'Nova conta de publicidade' }}</h2><p class="text-xs text-slate-500 mt-1">Tokens são exibidos apenas como status e nunca em texto aberto.</p></div><button wire:click="closeModal" class="text-slate-400 hover:text-white text-xl" aria-label="Fechar">&times;</button></div>
            <form wire:submit.prevent="{{ $editingIntegrationId ? 'updateIntegration' : 'createIntegration' }}" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div><label class="block text-xs text-slate-300 mb-1">Cliente *</label><select wire:model="clientId" class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white"><option value="">Selecione um cliente</option>@foreach($clients as $client)<option value="{{ $client->id }}">{{ $client->name }}</option>@endforeach</select>@error('clientId')<span class="text-[11px] text-rose-400">{{ $message }}</span>@enderror</div>
                    <div><label class="block text-xs text-slate-300 mb-1">Plataforma *</label><select wire:model="platform" class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white"><option value="meta">Meta Ads</option><option value="google">Google Ads</option><option value="tiktok">TikTok Ads</option><option value="linkedin">LinkedIn Ads</option><option value="microsoft">Microsoft Advertising</option><option value="pinterest">Pinterest Ads</option><option value="kwai">Kwai Ads</option></select></div>
                    <div><label class="block text-xs text-slate-300 mb-1">Nome da conta *</label><input wire:model="name" class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white"></div>
                    <div><label class="block text-xs text-slate-300 mb-1">ID externo *</label><input wire:model="accountId" class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white"></div>
                    <div><label class="block text-xs text-slate-300 mb-1">Status da conexão</label><select wire:model="status" class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white"><option value="CONNECTED">Conectada</option><option value="DISCONNECTED">Desconectada</option><option value="ERROR">Erro</option><option value="SYNCING">Sincronizando</option></select></div>
                    <div><label class="block text-xs text-slate-300 mb-1">Moeda *</label><input wire:model="currency" maxlength="3" class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white uppercase"></div>
                    <div class="sm:col-span-2"><label class="block text-xs text-slate-300 mb-1">Fuso horário *</label><input wire:model="timezone" placeholder="America/Sao_Paulo" class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white"></div>
                    <div class="sm:col-span-2 rounded-xl border border-brand-cyan/20 bg-brand-cyan/5 p-3 text-xs text-slate-300">A conexão OAuth e a troca de tokens devem ser concluídas pela plataforma. Os tokens nunca são exibidos nem mantidos no estado do formulário.</div>
                </div>
                <div class="flex justify-end gap-2 pt-2"><button type="button" wire:click="closeModal" class="px-4 py-2 bg-slate-800 text-xs font-semibold rounded-xl text-slate-300">Cancelar</button><button type="submit" class="px-4 py-2 bg-brand-cyan text-slate-950 font-bold text-xs rounded-xl shadow-neon-blue">Salvar conta</button></div>
            </form>
        </div></div>
    @endif
</div>
