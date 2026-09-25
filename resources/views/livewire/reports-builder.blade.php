<div class="space-y-6 pb-12">
    <div class="glass-panel p-6 rounded-2xl border border-slate-800 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-100">Relatórios</h1>
            <p class="text-xs text-slate-400 mt-1">Selecione cliente, período, plataformas e campanhas para gerar o relatório.</p>
        </div>
        <div class="flex gap-2">
            <button wire:click="export('pdf')" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-brand-cyan to-blue-600 text-slate-950 font-bold text-xs shadow-neon-blue">PDF</button>
            <button wire:click="export('xlsx')" class="px-4 py-2.5 rounded-xl border border-slate-700 text-slate-100 font-bold text-xs">Excel</button>
            <button wire:click="export('csv')" class="px-4 py-2.5 rounded-xl border border-slate-700 text-slate-100 font-bold text-xs">CSV</button>
        </div>
    </div>

    <div class="glass-panel p-6 rounded-2xl border border-slate-800">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <label class="text-xs text-slate-300">
                Cliente
                <select wire:model="clientId" class="mt-1 w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-slate-100">
                    <option value="all">Todos</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                    @endforeach
                </select>
            </label>

            <label class="text-xs text-slate-300">
                Período
                <select wire:model="period" class="mt-1 w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-slate-100">
                    <option value="7">Últimos 7 dias</option>
                    <option value="30">Últimos 30 dias</option>
                    <option value="90">Últimos 90 dias</option>
                    <option value="month">Mês atual</option>
                </select>
            </label>

            <label class="text-xs text-slate-300 md:col-span-2">
                Plataformas
                <select wire:model.live="platforms" multiple class="mt-1 w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-slate-100 min-h-[42px]">
                    <option value="meta">Meta</option>
                    <option value="google">Google</option>
                    <option value="tiktok">TikTok</option>
                    <option value="linkedin">LinkedIn</option>
                    <option value="microsoft">Microsoft</option>
                    <option value="pinterest">Pinterest</option>
                </select>
            </label>
        </div>

        <div class="mt-5">
            <div class="mb-2 text-xs text-slate-400">Campanhas</div>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-2">
                @foreach($campaigns as $campaign)
                    <label class="flex items-center gap-2 rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200">
                        <input type="checkbox" wire:model="campaignIds" value="{{ $campaign->id }}">
                        <span>{{ $campaign->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @php
            $kpis = [
                ['label' => 'Investimento', 'value' => 'R$ '.number_format((float) ($totals['investment'] ?? 0), 2, ',', '.'), 'tone' => 'text-slate-100'],
                ['label' => 'Impressões', 'value' => number_format((int) ($totals['impressions'] ?? 0), 0, ',', '.'), 'tone' => 'text-brand-cyan'],
                ['label' => 'Cliques', 'value' => number_format((int) ($totals['clicks'] ?? 0), 0, ',', '.'), 'tone' => 'text-slate-100'],
                ['label' => 'CTR', 'value' => number_format((float) ($totals['ctr'] ?? 0), 2, ',', '.').'%', 'tone' => 'text-emerald-400'],
                ['label' => 'CPC', 'value' => 'R$ '.number_format((float) ($totals['cpc'] ?? 0), 2, ',', '.'), 'tone' => 'text-slate-100'],
                ['label' => 'CPM', 'value' => 'R$ '.number_format((float) ($totals['cpm'] ?? 0), 2, ',', '.'), 'tone' => 'text-slate-100'],
                ['label' => 'Leads', 'value' => number_format((int) ($totals['leads'] ?? 0), 0, ',', '.'), 'tone' => 'text-brand-cyan'],
                ['label' => 'Conversões', 'value' => number_format((int) ($totals['conversions'] ?? 0), 0, ',', '.'), 'tone' => 'text-emerald-400'],
                ['label' => 'CPL', 'value' => 'R$ '.number_format((float) ($totals['cpl'] ?? 0), 2, ',', '.'), 'tone' => 'text-slate-100'],
                ['label' => 'Receita', 'value' => 'R$ '.number_format((float) ($totals['revenue'] ?? 0), 2, ',', '.'), 'tone' => 'text-emerald-400'],
                ['label' => 'ROAS', 'value' => number_format((float) ($totals['roas'] ?? 0), 2, ',', '.').'x', 'tone' => 'text-brand-cyan'],
                ['label' => 'Alcance', 'value' => number_format((int) ($totals['reach'] ?? 0), 0, ',', '.'), 'tone' => 'text-slate-100'],
            ];
        @endphp

        @foreach($kpis as $kpi)
            <div class="glass-panel p-4 rounded-2xl border border-slate-800">
                <div class="text-[10px] uppercase tracking-[0.18em] text-slate-400">{{ $kpi['label'] }}</div>
                <div class="mt-2 text-xl font-extrabold {{ $kpi['tone'] }}">{{ $kpi['value'] }}</div>
            </div>
        @endforeach
    </div>

    <div class="glass-panel p-6 rounded-2xl border border-slate-800 overflow-hidden">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-slate-100">Detalhamento por campanha</h2>
            <span class="text-xs text-slate-400">{{ $rows->count() }} registros</span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-xs text-slate-200">
                <thead class="bg-slate-900/80 text-slate-300">
                    <tr>
                        <th class="px-3 py-2">Campanha</th>
                        <th class="px-3 py-2">Plataforma</th>
                        <th class="px-3 py-2">Investimento</th>
                        <th class="px-3 py-2">Impressões</th>
                        <th class="px-3 py-2">Cliques</th>
                        <th class="px-3 py-2">CTR</th>
                        <th class="px-3 py-2">CPC</th>
                        <th class="px-3 py-2">CPM</th>
                        <th class="px-3 py-2">Leads</th>
                        <th class="px-3 py-2">Conversões</th>
                        <th class="px-3 py-2">CPL</th>
                        <th class="px-3 py-2">Receita</th>
                        <th class="px-3 py-2">ROAS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $row)
                        <tr class="border-t border-slate-800">
                            <td class="px-3 py-2">{{ $row['campaign_name'] }}</td>
                            <td class="px-3 py-2">{{ $row['platform'] }}</td>
                            <td class="px-3 py-2">R$ {{ number_format((float) $row['spend'], 2, ',', '.') }}</td>
                            <td class="px-3 py-2">{{ number_format((int) $row['impressions'], 0, ',', '.') }}</td>
                            <td class="px-3 py-2">{{ number_format((int) $row['clicks'], 0, ',', '.') }}</td>
                            <td class="px-3 py-2">{{ number_format((float) $row['ctr'], 2, ',', '.') }}%</td>
                            <td class="px-3 py-2">R$ {{ number_format((float) $row['cpc'], 2, ',', '.') }}</td>
                            <td class="px-3 py-2">R$ {{ number_format((float) $row['cpm'], 2, ',', '.') }}</td>
                            <td class="px-3 py-2">{{ number_format((int) $row['leads'], 0, ',', '.') }}</td>
                            <td class="px-3 py-2">{{ number_format((int) $row['conversions'], 0, ',', '.') }}</td>
                            <td class="px-3 py-2">R$ {{ number_format((float) $row['cpl'], 2, ',', '.') }}</td>
                            <td class="px-3 py-2">R$ {{ number_format((float) $row['revenue'], 2, ',', '.') }}</td>
                            <td class="px-3 py-2">{{ number_format((float) $row['roas'], 2, ',', '.') }}x</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
