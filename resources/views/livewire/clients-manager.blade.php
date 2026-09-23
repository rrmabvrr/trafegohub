<div class="space-y-6 pb-12">
    <div class="glass-panel p-6 rounded-2xl border border-brand-cyan/20 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="text-xs font-semibold uppercase tracking-wider text-brand-cyan mb-2">Relacionamento e contas</div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-100">Clientes</h1>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">Centralize dados cadastrais e acompanhe as contas de publicidade.</p>
        </div>
        <button wire:click="$set('showModal', true)" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-brand-cyan to-blue-600 text-slate-950 font-bold text-xs shadow-neon-blue hover:brightness-110 transition">
            + Novo cliente
        </button>
    </div>

    <div class="glass-panel p-4 rounded-2xl border border-slate-800 flex flex-col sm:flex-row gap-3">
        <input type="search" wire:model.live="search" placeholder="Buscar por nome, razão social ou CPF/CNPJ..." class="flex-1 px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-slate-200 focus:outline-none focus:border-brand-cyan">
        <select wire:model.live="statusFilter" class="px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-slate-200">
            <option value="all">Todos os status</option>
            <option value="active">Ativos</option>
            <option value="inactive">Inativos</option>
        </select>
    </div>

    <div class="glass-panel rounded-2xl border border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950 text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="px-4 py-3.5">Cliente</th>
                        <th class="px-4 py-3.5">CPF/CNPJ</th>
                        <th class="px-4 py-3.5">Contato</th>
                        <th class="px-4 py-3.5">Contas de publicidade</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-4 py-3.5 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 text-slate-300">
                    @forelse($clients as $client)
                        <tr wire:key="client-{{ $client->id }}" class="hover:bg-slate-800/40">
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-slate-100">{{ $client->name }}</div>
                                <div class="text-[11px] text-slate-500">{{ $client->legal_name ?: 'Razão social não informada' }}</div>
                            </td>
                            <td class="px-4 py-3.5">{{ $client->document ?: 'Não informado' }}</td>
                            <td class="px-4 py-3.5">
                                <div>{{ $client->email ?: 'Sem e-mail' }}</div>
                                <div class="text-[11px] text-slate-500">{{ $client->whatsapp ?: $client->phone ?: 'Sem telefone' }}</div>
                            </td>
                            <td class="px-4 py-3.5 font-bold text-brand-cyan">{{ $client->advertising_accounts_count }}</td>
                            <td class="px-4 py-3.5">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $client->status === 'active' ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/40' : 'bg-slate-800 text-slate-400 border-slate-700' }}">
                                    {{ $client->status === 'active' ? 'ATIVO' : 'INATIVO' }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                <button wire:click="editClient({{ $client->id }})" class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200">Editar</button>
                                <button wire:click="deleteClient({{ $client->id }})" wire:confirm="Excluir este cliente?" class="px-2.5 py-1.5 rounded-lg text-rose-300 hover:bg-rose-500/10">Excluir</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-12 text-center text-slate-500">Nenhum cliente encontrado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-hub-950/80 backdrop-blur-md">
            <div class="glass-modal w-full max-w-3xl max-h-[90vh] overflow-y-auto rounded-2xl border border-slate-700 p-6">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h2 class="text-lg font-bold text-slate-100">{{ $editingClientId ? 'Editar cliente' : 'Novo cliente' }}</h2>
                        <p class="text-xs text-slate-500 mt-1">Preencha os dados comerciais e os canais de contato.</p>
                    </div>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-white text-xl" aria-label="Fechar">&times;</button>
                </div>
                <form wire:submit.prevent="{{ $editingClientId ? 'updateClient' : 'createClient' }}" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div><label class="block text-xs text-slate-300 mb-1">Nome *</label><input wire:model="name" class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white">@error('name')<span class="text-[11px] text-rose-400">{{ $message }}</span>@enderror</div>
                        <div><label class="block text-xs text-slate-300 mb-1">Razão social</label><input wire:model="legalName" class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white"></div>
                        <div><label class="block text-xs text-slate-300 mb-1">CPF/CNPJ</label><input wire:model="document" class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white"></div>
                        <div><label class="block text-xs text-slate-300 mb-1">E-mail</label><input type="email" wire:model="email" class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white">@error('email')<span class="text-[11px] text-rose-400">{{ $message }}</span>@enderror</div>
                        <div><label class="block text-xs text-slate-300 mb-1">Telefone</label><input wire:model="phone" class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white"></div>
                        <div><label class="block text-xs text-slate-300 mb-1">WhatsApp</label><input wire:model="whatsapp" class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white"></div>
                        <div><label class="block text-xs text-slate-300 mb-1">Status</label><select wire:model="status" class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white"><option value="active">Ativo</option><option value="inactive">Inativo</option></select></div>
                        <div class="sm:col-span-2"><label class="block text-xs text-slate-300 mb-1">Endereço</label><input wire:model="address" class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white"></div>
                        <div class="sm:col-span-2"><label class="block text-xs text-slate-300 mb-1">Observações</label><textarea wire:model="notes" rows="3" class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white"></textarea></div>
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 bg-slate-800 text-xs font-semibold rounded-xl text-slate-300">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-brand-cyan text-slate-950 font-bold text-xs rounded-xl shadow-neon-blue">Salvar cliente</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>