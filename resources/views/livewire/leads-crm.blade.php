<div class="space-y-6 pb-12">
    <div class="glass-panel p-6 rounded-2xl border border-slate-800 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-100">Central de Leads</h1>
            <p class="text-xs text-slate-400 mt-1">Fluxo de captação, origem e status de prospecção para WhatsApp, CRM, Meta e Google.</p>
        </div>
        <button wire:click="$set('showAddModal', true)" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-brand-cyan to-blue-600 text-slate-950 font-bold text-xs shadow-neon-blue">+ Adicionar Lead</button>
    </div>

    <div class="glass-panel p-4 rounded-2xl border border-slate-800 flex items-center justify-between gap-4">
        <input type="text" wire:model.live="search" placeholder="Buscar por nome, e-mail ou telefone..." class="px-3 py-1.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white w-full max-w-sm">
        <span class="text-xs text-slate-400">Total: <strong class="text-white">{{ $leads->count() }}</strong></span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        @foreach(['novo' => 'Novo', 'contato' => 'Em Contato', 'negociacao' => 'Negociação', 'convertido' => 'Convertido', 'perdido' => 'Perdido'] as $statusKey => $statusTitle)
            @php $colLeads = $leads->where('status', $statusKey); @endphp
            <div class="glass-panel p-3 rounded-2xl border border-slate-800 min-h-[450px]">
                <div class="pb-2 mb-3 border-b border-slate-800 flex justify-between items-center">
                    <span class="text-xs font-bold text-brand-cyan">{{ $statusTitle }}</span>
                    <span class="text-xs text-slate-400 font-bold bg-slate-800 px-2 py-0.5 rounded-full">{{ $colLeads->count() }}</span>
                </div>

                <div class="space-y-2">
                    @foreach($colLeads as $lead)
                        <div class="p-3 rounded-xl bg-slate-900 border border-slate-800 text-xs">
                            <div class="flex items-center justify-between gap-2">
                                <h4 class="font-bold text-slate-100 truncate">{{ $lead->name }}</h4>
                                <span class="text-[10px] uppercase text-brand-cyan">{{ $lead->platform }}</span>
                            </div>
                            <p class="text-[11px] text-slate-400 truncate">{{ $lead->email ?: $lead->phone ?: 'Sem contato' }}</p>
                            <div class="mt-2 flex flex-wrap gap-2 text-[10px] text-slate-400">
                                <span>{{ $lead->source }}</span>
                                <span>•</span>
                                <span>{{ $lead->campaign_name ?: $lead->ad_name ?: 'Sem campanha' }}</span>
                            </div>
                            <div class="mt-3 pt-2 border-t border-slate-800 flex items-center justify-between gap-2">
                                <select wire:change="updateStatus({{ $lead->id }}, $event.target.value)" class="w-full rounded-lg border border-slate-700 bg-slate-950 px-2 py-1 text-[10px] text-slate-200">
                                    @foreach(['novo', 'contato', 'negociacao', 'convertido', 'perdido'] as $option)
                                        <option value="{{ $option }}" @selected($lead->status === $option)>{{ ucfirst($option) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    @if($showAddModal)
        <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="w-full max-w-2xl rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-2xl">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-lg font-bold text-slate-100">Novo lead</h2>
                    <button wire:click="$set('showAddModal', false)" class="text-slate-400 hover:text-white">✕</button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="text-xs text-slate-300">
                        Cliente
                        <input wire:model="newClient" type="text" class="mt-1 w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-slate-100" placeholder="Cliente/empresa">
                    </label>
                    <label class="text-xs text-slate-300">
                        Nome
                        <input wire:model="newName" type="text" class="mt-1 w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-slate-100" placeholder="Nome do lead" required>
                    </label>
                    <label class="text-xs text-slate-300">
                        Telefone
                        <input wire:model="newPhone" type="text" class="mt-1 w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-slate-100" placeholder="(11) 99999-9999">
                    </label>
                    <label class="text-xs text-slate-300">
                        E-mail
                        <input wire:model="newEmail" type="email" class="mt-1 w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-slate-100" placeholder="contato@email.com">
                    </label>
                    <label class="text-xs text-slate-300">
                        Origem
                        <input wire:model="newOrigin" type="text" class="mt-1 w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-slate-100" placeholder="Google, Meta, WhatsApp...">
                    </label>
                    <label class="text-xs text-slate-300">
                        Plataforma
                        <select wire:model="newPlatform" class="mt-1 w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-slate-100">
                            <option value="meta">Meta</option>
                            <option value="google">Google</option>
                            <option value="whatsapp">WhatsApp</option>
                            <option value="crm">CRM</option>
                            <option value="formulario">Formulário</option>
                        </select>
                    </label>
                    <label class="text-xs text-slate-300">
                        Campanha
                        <input wire:model="newCampaign" type="text" class="mt-1 w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-slate-100" placeholder="Campanha ou funnel">
                    </label>
                    <label class="text-xs text-slate-300">
                        Anúncio
                        <input wire:model="newAd" type="text" class="mt-1 w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-slate-100" placeholder="Anúncio / criativo">
                    </label>
                    <label class="text-xs text-slate-300">
                        Fonte
                        <select wire:model="newSource" class="mt-1 w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-slate-100">
                            <option value="formulario">Formulário</option>
                            <option value="whatsapp">WhatsApp</option>
                            <option value="crm">CRM</option>
                            <option value="meta_leads">Meta Leads</option>
                            <option value="google_leads">Google Leads</option>
                            <option value="manual">Manual</option>
                        </select>
                    </label>
                    <label class="text-xs text-slate-300">
                        Status
                        <select wire:model="newStatus" class="mt-1 w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-slate-100">
                            @foreach(['novo', 'contato', 'negociacao', 'convertido', 'perdido'] as $status)
                                <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="md:col-span-2 text-xs text-slate-300">
                        Observações
                        <textarea wire:model="newObservations" rows="3" class="mt-1 w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-slate-100" placeholder="Detalhes do atendimento, interesse, condições e próximo passo..."></textarea>
                    </label>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" wire:click="$set('showAddModal', false)" class="px-4 py-2.5 rounded-xl border border-slate-700 text-slate-200 text-xs font-bold">Cancelar</button>
                    <button type="button" wire:click="createLead" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-brand-cyan to-blue-600 text-slate-950 text-xs font-bold">Salvar lead</button>
                </div>
            </div>
        </div>
    @endif
</div>
