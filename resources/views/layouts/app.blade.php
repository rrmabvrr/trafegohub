<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TRAFEGO HUB | Central de Inteligência de Anúncios</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (CDN for zero-npm shared hosting compatibility) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        hub: { 950: '#07090e', 900: '#0b0f19', 850: '#111726', 800: '#161f33', 700: '#253454' },
                        meta: '#0668E1', google: '#EA4335', tiktok: '#FE2C55', linkedin: '#0A66C2', kwai: '#FF5000',
                        brand: { cyan: '#00F2FE', violet: '#7928CA', emerald: '#10B981', rose: '#F43F5E', amber: '#F59E0B' }
                    },
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] }
                }
            }
        }
    </script>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap-utilities.min.css" rel="stylesheet">
    
    <!-- Chart.js & ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Custom Glassmorphism Styles -->
    <style>
        body { background-color: #07090e; color: #e2e8f0; font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-panel { background: rgba(16, 24, 40, 0.65); backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.08); box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37); }
        .glass-panel-interactive { background: rgba(16, 24, 40, 0.65); backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.08); transition: all 0.25s ease; }
        .glass-panel-interactive:hover { background: rgba(26, 38, 64, 0.75); border-color: rgba(0, 242, 254, 0.3); transform: translateY(-2px); }
        .glass-modal { background: rgba(11, 15, 25, 0.94); backdrop-filter: blur(24px); border: 1px solid rgba(255, 255, 255, 0.12); }
        .gradient-text-cyan { background: linear-gradient(135deg, #00F2FE 0%, #4FACFE 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .shadow-neon-blue { box-shadow: 0 0 20px rgba(0, 242, 254, 0.25); }
    </style>

    @livewireStyles
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-hub-950 text-slate-100 min-h-screen font-sans selection:bg-brand-cyan/30">

    <div x-data="{ sidebarOpen: true }" class="flex min-h-screen">
        <!-- Sidebar Navigation -->
        <aside :class="sidebarOpen ? 'w-64' : 'w-20'" class="fixed left-0 top-0 bottom-0 z-30 bg-hub-900/90 backdrop-blur-xl border-r border-slate-800/80 flex flex-col justify-between transition-all duration-300">
            <div>
                <!-- Brand Header -->
                <div class="h-16 flex items-center px-4 border-b border-slate-800/80 gap-3">
                    <div className="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-cyan to-brand-violet flex items-center justify-center text-slate-950 font-black text-xl shadow-neon-blue flex-shrink-0">
                        TH
                    </div>
                    <div x-show="sidebarOpen" class="flex flex-col">
                        <span class="font-extrabold text-lg tracking-tight gradient-text-cyan leading-none">
                            TRAFEGO<span class="text-white">HUB</span>
                        </span>
                        <span class="text-[10px] text-slate-400 font-medium tracking-wider uppercase mt-0.5 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            Laravel 12 Livewire
                        </span>
                    </div>
                </div>

                <!-- Navigation Links -->
                <nav class="p-3 space-y-1.5">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-brand-cyan/20 text-brand-cyan border border-brand-cyan/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                        <svg class="w-5 h-5 text-brand-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        <span x-show="sidebarOpen">Painel Geral</span>
                    </a>

                    <a href="{{ route('campaigns') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('campaigns') ? 'bg-brand-cyan/20 text-brand-cyan border border-brand-cyan/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                        <svg class="w-5 h-5 text-brand-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                        <span x-show="sidebarOpen">Gestor de Campanhas</span>
                    </a>

                    <a href="{{ route('integrations') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('integrations') ? 'bg-brand-cyan/20 text-brand-cyan border border-brand-cyan/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                        <svg class="w-5 h-5 text-brand-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span x-show="sidebarOpen">Conexões & APIs</span>
                    </a>

                    <a href="{{ route('leads') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('leads') ? 'bg-brand-cyan/20 text-brand-cyan border border-brand-cyan/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                        <svg class="w-5 h-5 text-brand-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span x-show="sidebarOpen">Central de Leads</span>
                    </a>

                    <a href="{{ route('creatives') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('creatives') ? 'bg-brand-cyan/20 text-brand-cyan border border-brand-cyan/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                        <svg class="w-5 h-5 text-brand-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span x-show="sidebarOpen">Análise de Criativos</span>
                    </a>

                    <a href="{{ route('automation') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('automation') ? 'bg-brand-cyan/20 text-brand-cyan border border-brand-cyan/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                        <svg class="w-5 h-5 text-brand-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                        <span x-show="sidebarOpen">Automação & Regras</span>
                    </a>

                    <a href="{{ route('reports') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('reports') ? 'bg-brand-cyan/20 text-brand-cyan border border-brand-cyan/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                        <svg class="w-5 h-5 text-brand-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 2v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span x-show="sidebarOpen">Relatórios & PDF</span>
                    </a>
                </nav>
            </div>

            <div class="p-3 border-t border-slate-800/80">
                <div x-show="sidebarOpen" class="glass-panel p-3 rounded-xl border border-brand-cyan/20">
                    <span class="text-xs font-semibold text-slate-300 block">Hostinger Optimization</span>
                    <span class="text-[10px] text-emerald-400 font-bold block mt-0.5">PHP 8.4 • Redis Active</span>
                </div>
            </div>
        </aside>

        <!-- Main Wrapper -->
        <div :class="sidebarOpen ? 'pl-64' : 'pl-20'" class="flex-1 flex flex-col transition-all duration-300">
            <!-- Top Header -->
            <header class="h-16 bg-hub-900/80 backdrop-blur-xl border-b border-slate-800/80 sticky top-0 z-20 px-4 flex items-center justify-between">
                <button @click="sidebarOpen = !sidebarOpen" class="p-2 text-slate-400 hover:text-white rounded-lg hover:bg-slate-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>

                <div class="flex items-center gap-3">
                    <span class="text-xs font-bold text-slate-300 bg-slate-800 px-3 py-1 rounded-xl border border-slate-700">
                        Agência Alfa • Infoprodutos Master
                    </span>
                    <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-brand-violet to-purple-500 p-0.5 flex items-center justify-center font-bold text-xs text-brand-cyan">
                        GA
                    </div>
                </div>
            </header>

            <!-- Main Livewire Page View -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>
