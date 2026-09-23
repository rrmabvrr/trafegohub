<div class="space-y-6 pb-12">
    <!-- Header Banner -->
    <div class="glass-panel p-6 rounded-2xl border border-slate-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 className="text-2xl font-extrabold text-slate-100 flex items-center gap-2">
                Gestor Unificado de Campanhas
            </h1>
            <p className="text-xs sm:text-sm text-slate-400 mt-1">
                Controle central de ativação, otimização de orçamento e escalabilidade.
            </p>
        </div>

        <button wire:click="$set('showCreateModal', true)" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-brand-cyan to-blue-600 text-slate-950 font-bold text-xs shadow-neon-blue hover:brightness-110 transition">
            + Nova Campanha Multi-Plataforma
        </button>
    </div>

    <!-- Toolbar Filters -->
    <div class="glass-panel p-4 rounded-2xl border border-slate-800 space-y-3">
        <div class="flex items-center gap-2 overflow-x-auto">
            @foreach(['all' => 'Todas', 'meta' => 'Meta Ads', 'google' => 'Google Ads', 'tiktok' => 'TikTok Ads', 'linkedin' => 'LinkedIn Ads', 'kwai' => 'Kwai Ads'] as $key => $label)
                <button wire:click="$set('selectedPlatform', '{{ $key }}')" class="px-3 py-1.5 rounded-xl text-xs font-semibold {{ $selectedPlatform === $key ? 'bg-brand-cyan text-slate-950 font-bold shadow-neon-blue' : 'bg-slate-900 text-slate-400 border border-slate-800' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <div class="flex items-center justify-between gap-3 pt-2 border-t border-slate-800">
            <input type="text" wire:model.live="search" placeholder="Filtrar por nome de campanha..." class="px-3.5 py-1.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-slate-200 w-72 focus:outline-none focus:border-brand-cyan">
        </div>
    </div>

    <!-- Campaigns Table -->
    <div class="glass-panel rounded-2xl border border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950 text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-4 py-3.5">Plataforma & Nome</th>
                        <th class="px-4 py-3.5">Orçamento Diário</th>
                        <th class="px-4 py-3.5">Gasto Total</th>
                        <th class="px-4 py-3.5">Cliques & CTR</th>
                        <th class="px-4 py-3.5">Conversões</th>
                        <th class="px-4 py-3.5">ROAS</th>
                        <th class="px-4 py-3.5">Estrutura</th>
                        <th class="px-4 py-3.5 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 text-slate-300">
                    @foreach($campaigns as $c)
                    <tr class="hover:bg-slate-800/40">
                        <td class="px-4 py-3.5">
                            <button wire:click="toggleStatus({{ $c->id }})" class="px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $c->status === 'ACTIVE' ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/40' : 'bg-slate-800 text-slate-400 border-slate-700' }}">
                                {{ $c->status === 'ACTIVE' ? 'ATIVA' : 'PAUSADA' }}
                            </button>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="font-bold text-slate-100 uppercase text-[10px] bg-slate-800 px-2 py-0.5 rounded mr-1.5">{{ $c->platform }}</span>
                            <span class="font-bold text-slate-200">{{ $c->name }}</span>
                        </td>
                        <td class="px-4 py-3.5 font-bold text-slate-200">
                            R$ {{ number_format($c->daily_budget, 2, ',', '.') }}
                        </td>
                        <td class="px-4 py-3.5">R$ {{ number_format($c->total_spend, 2, ',', '.') }}</td>
                        <td class="px-4 py-3.5">
                            <div>{{ number_format($c->clicks) }} cliques</div>
                            <div class="text-[10px] text-purple-400">CTR {{ $c->ctr }}%</div>
                        </td>
                        <td class="px-4 py-3.5 font-extrabold text-slate-100">{{ $c->conversions }}</td>
                        <td class="px-4 py-3.5">
                            <span class="px-2.5 py-1 rounded-full font-extrabold text-xs bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                {{ $c->roas }}x
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-[11px] text-slate-400">
                            {{ $c->adSets->count() }} conjuntos /
                            {{ $c->adSets->sum(fn ($adSet) => $adSet->ads->count()) }} anúncios
                        </td>
                        <td class="px-4 py-3.5 text-right">
                            <button wire:click="duplicate({{ $c->id }})" class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300">Duplicar</button>
                        </td>
                    </tr>
                    @if($c->adSets->isNotEmpty())
                    <tr class="bg-slate-950/60">
                        <td colspan="9" class="px-6 py-3">
                            <div class="space-y-2">
                                @foreach($c->adSets as $adSet)
                                <div class="flex flex-col gap-1 border-l-2 border-brand-cyan/40 pl-3">
                                    <div class="flex items-center justify-between text-[11px]">
                                        <span class="font-semibold text-slate-200">{{ $adSet->name }}</span>
                                        <span class="text-slate-500">{{ $adSet->ads->count() }} anúncios</span>
                                    </div>
                                    <div class="flex flex-wrap gap-x-4 gap-y-1 text-[10px] text-slate-500">
                                        @foreach($adSet->ads as $ad)
                                        <span>{{ $ad->name }} <span class="text-slate-600">({{ $ad->status }})</span></span>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Campaign Modal -->
    @if($showCreateModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-hub-950/80 backdrop-blur-md">
        <div class="glass-modal w-full max-w-lg rounded-2xl border border-slate-700 p-6">
            <h3 class="text-base font-bold text-slate-100 mb-4">Lançar Nova Campanha</h3>
            <form wire:submit.prevent="createCampaign" class="space-y-3">
                <div>
                    <label class="block text-xs text-slate-300 mb-1">Nome da Campanha</label>
                    <input type="text" wire:model="newCampaignName" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-slate-300 mb-1">Plataforma</label>
                        <select wire:model="newPlatform" class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white">
                            <option value="meta">Meta Ads</option>
                            <option value="google">Google Ads</option>
                            <option value="tiktok">TikTok Ads</option>
                            <option value="linkedin">LinkedIn Ads</option>
                            <option value="kwai">Kwai Ads</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-slate-300 mb-1">Orçamento Diário (R$)</label>
                        <input type="number" wire:model="newDailyBudget" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white font-bold">
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-3">
                    <button type="button" wire:click="$set('showCreateModal', false)" class="px-4 py-2 bg-slate-800 text-xs font-semibold rounded-xl text-slate-300">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-brand-cyan text-slate-950 font-bold text-xs rounded-xl shadow-neon-blue">Publicar Campanha</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
