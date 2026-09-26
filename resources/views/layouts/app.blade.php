<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.svg') }}">
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

    <!-- Custom UI Styles -->
    <style>
        :root {
            --th-bg: #070b12;
            --th-bg-soft: #0d141d;
            --th-panel: #0f1722;
            --th-panel-alt: #111c2b;
            --th-border: rgba(148, 163, 184, 0.18);
            --th-text: #e5edf7;
            --th-text-soft: #8ca0b6;
            --th-cyan: #00f2fe;
            --th-violet: #7c3aed;
            --th-emerald: #10b981;
            --th-surface: rgba(15, 23, 34, 0.92);
        }

        body {
            background: radial-gradient(circle at top left, rgba(0, 242, 254, 0.08), transparent 22%),
                        radial-gradient(circle at top right, rgba(124, 58, 237, 0.08), transparent 24%),
                        var(--th-bg);
            color: var(--th-text);
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .glass-panel {
            background: linear-gradient(180deg, rgba(15, 23, 34, 0.96), rgba(12, 18, 27, 0.92));
            border: 1px solid var(--th-border);
            box-shadow: 0 10px 24px rgba(2, 6, 23, 0.28);
        }

        .glass-panel-interactive {
            background: linear-gradient(180deg, rgba(15, 23, 34, 0.96), rgba(12, 18, 27, 0.92));
            border: 1px solid var(--th-border);
            transition: all 0.2s ease;
        }

        .glass-panel-interactive:hover {
            background: rgba(17, 28, 43, 0.98);
            border-color: rgba(0, 242, 254, 0.28);
            transform: translateY(-1px);
        }

        .glass-modal {
            background: rgba(8, 12, 18, 0.96);
            border: 1px solid rgba(148, 163, 184, 0.18);
        }

        .gradient-text-cyan {
            background: linear-gradient(135deg, var(--th-cyan) 0%, #7dd3fc 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .shadow-soft {
            box-shadow: 0 8px 20px rgba(2, 6, 23, 0.18);
        }

        .brand-mark {
            background: linear-gradient(135deg, var(--th-cyan) 0%, var(--th-violet) 100%);
            box-shadow: 0 8px 18px rgba(0, 242, 254, 0.22);
        }

        .nav-item {
            border: 1px solid transparent;
        }

        .nav-item.active {
            background: rgba(0, 242, 254, 0.08);
            border-color: rgba(0, 242, 254, 0.18);
            color: var(--th-cyan);
        }

        .nav-item:hover {
            background: rgba(148, 163, 184, 0.05);
            border-color: rgba(148, 163, 184, 0.12);
        }
    </style>

    @livewireStyles
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-hub-950 text-slate-100 min-h-screen font-sans selection:bg-brand-cyan/30">

    <div x-data="{ sidebarOpen: true }" class="flex min-h-screen">
        <!-- Sidebar Navigation -->
        <aside :class="sidebarOpen ? 'w-64' : 'w-20'" class="fixed left-0 top-0 bottom-0 z-30 bg-[#0b1118]/95 border-r border-slate-800/80 flex flex-col justify-between transition-all duration-300">
            <div>
                <!-- Brand Header -->
                <div class="h-16 flex items-center px-4 border-b border-slate-800/80 gap-3">
                    <div class="brand-mark w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0">
                        <img src="{{ asset('favicon.svg') }}" alt="TRAFEGO HUB logo" class="w-7 h-7">
                    </div>
                    <div x-show="sidebarOpen" class="flex flex-col">
                        <span class="font-extrabold text-lg tracking-[-0.06em] gradient-text-cyan leading-none">
                            TRAFEGO<span class="text-slate-100">HUB</span>
                        </span>
                        <span class="text-[10px] text-slate-400 font-medium tracking-[0.16em] uppercase mt-1">
                            SaaS media
                        </span>
                    </div>
                </div>

                <!-- Navigation Links -->
                <nav class="p-3 space-y-1.5">
                    <a href="{{ route('dashboard') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'active' : 'text-slate-400 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9v9a2 2 0 01-2 2H5a2 2 0 01-2-2v-9z"/></svg>
                        <span x-show="sidebarOpen">Dashboard</span>
                    </a>

                    <a href="{{ route('clients') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('clients') ? 'active' : 'text-slate-400 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span x-show="sidebarOpen">Clientes</span>
                    </a>

                    <a href="#" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition text-slate-400 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h10"/></svg>
                        <span x-show="sidebarOpen">Contas de Publicidade</span>
                    </a>

                    <a href="{{ route('campaigns') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('campaigns') ? 'active' : 'text-slate-400 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                        <span x-show="sidebarOpen">Campanhas</span>
                    </a>

                    <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition text-slate-400 hover:text-white hover:bg-slate-800/50">
                        <svg class="w-5 h-5 text-brand-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h10v10H7zM4 4h16v16H4z"/></svg>
                        <span x-show="sidebarOpen">Conjuntos</span>
                    </a>

                    <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition text-slate-400 hover:text-white hover:bg-slate-800/50">
                        <svg class="w-5 h-5 text-brand-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span x-show="sidebarOpen">Anúncios</span>
                    </a>

                    <a href="{{ route('creatives') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('creatives') ? 'active' : 'text-slate-400 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span x-show="sidebarOpen">Criativos</span>
                    </a>

                    <a href="{{ route('leads') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('leads') ? 'active' : 'text-slate-400 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span x-show="sidebarOpen">Leads</span>
                    </a>

                    <a href="{{ route('reports') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('reports') ? 'active' : 'text-slate-400 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 2v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span x-show="sidebarOpen">Relatórios</span>
                    </a>

                    <a href="{{ route('integrations') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('integrations') ? 'active' : 'text-slate-400 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span x-show="sidebarOpen">Conexões</span>
                    </a>

                    <a href="#" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition text-slate-400 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span x-show="sidebarOpen">Sincronizações</span>
                    </a>

                    <a href="#" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition text-slate-400 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                        <span x-show="sidebarOpen">Configurações</span>
                    </a>
                </nav>
            </div>

            <div class="p-3 border-t border-slate-800/80">
                <div x-show="sidebarOpen" class="glass-panel p-3 rounded-xl border border-slate-700/80">
                    <span class="text-[10px] uppercase tracking-[0.18em] text-slate-400 block">Stack</span>
                    <span class="text-xs font-semibold text-slate-200 block mt-1">PHP 8.4 • Redis</span>
                </div>
            </div>
        </aside>

        <!-- Main Wrapper -->
        <div :class="sidebarOpen ? 'pl-64' : 'pl-20'" class="flex-1 flex flex-col transition-all duration-300">
            <!-- Top Header -->
            <header class="h-16 bg-[#0b1118]/90 border-b border-slate-800/80 sticky top-0 z-20 px-4 flex items-center justify-between">
                <button @click="sidebarOpen = !sidebarOpen" class="p-2 text-slate-400 hover:text-slate-100 rounded-lg hover:bg-slate-800/70 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>

                <div class="flex items-center gap-3">
                    <span class="text-xs font-semibold text-slate-300 bg-slate-900/90 px-3 py-1.5 rounded-xl border border-slate-700/80">
                        Agência Alfa • Infoprodutos Master
                    </span>
                    <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-brand-violet to-indigo-500 p-0.5 flex items-center justify-center font-bold text-xs text-white shadow-soft">
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
