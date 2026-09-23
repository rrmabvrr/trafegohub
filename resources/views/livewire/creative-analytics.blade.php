<div class="space-y-6 pb-12">
    <div class="glass-panel p-6 rounded-2xl border border-slate-800">
        <h1 class="text-2xl font-extrabold text-slate-100">Central de Inteligência de Criativos</h1>
        <p class="text-xs text-slate-400 mt-1">Detecção de fadiga, Hook Rate (3s) e ROI por anúncio.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        @foreach($creatives as $c)
        <div class="glass-panel-interactive rounded-2xl overflow-hidden border border-slate-800 flex flex-col justify-between">
            <div class="relative h-40 bg-slate-950">
                <img src="{{ $c->thumbnail_url }}" alt="{{ $c->name }}" class="w-full h-full object-cover">
                <div class="absolute top-2 right-2 px-2 py-0.5 rounded text-[10px] font-bold {{ $c->fatigue_level === 'CRITICAL' ? 'bg-rose-500 text-white' : 'bg-emerald-500 text-white' }}">
                    {{ $c->fatigue_level }}
                </div>
            </div>

            <div class="p-4">
                <h3 class="font-bold text-slate-100 text-xs mb-1">{{ $c->name }}</h3>
                <div class="grid grid-cols-2 gap-2 text-xs py-2 border-t border-slate-800 mt-2">
                    <div>
                        <span class="text-[10px] text-slate-400 block">CTR</span>
                        <span class="font-bold text-brand-cyan">{{ $c->ctr }}%</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 block">Hook Rate</span>
                        <span class="font-bold text-purple-400">{{ $c->hook_rate }}%</span>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
