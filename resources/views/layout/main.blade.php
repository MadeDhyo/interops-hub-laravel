<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'InterOps-Hub')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Special+Elite&family=Inter:wght@300;400;500;600;700&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Special Elite', 'cursive'],
                        mono: ['IBM Plex Mono', 'monospace'],
                    },
                    colors: {
                        ops: {
                            abyss:   '#060d19',
                            deep:    '#0b1628',
                            panel:   'rgba(11,22,40,0.7)',
                            border:  'rgba(0,198,255,0.15)',
                            cyan:    '#00c6ff',
                            violet:  '#a78bfa',
                            gold:    '#f59e0b',
                            red:     '#ff3333',
                            parchment: '#e8d5a0',
                        }
                    },
                    backdropBlur: {
                        glass: '20px',
                    }
                }
            }
        }
    </script>

    <style>
        /* === ANIMATED MESH GRADIENT BACKGROUND === */
        @keyframes meshShift {
            0%   { background-position: 0% 50%; }
            50%  { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        body {
            font-family: 'Inter', sans-serif;
            color: #e2e8f0;
            min-height: 100vh;
            background-color: #060d19;
            background-image:
                radial-gradient(ellipse at 20% 20%, rgba(0,198,255,0.07) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 80%, rgba(167,139,250,0.06) 0%, transparent 50%),
                radial-gradient(ellipse at 50% 100%, rgba(245,158,11,0.04) 0%, transparent 50%);
            background-size: 200% 200%;
            animation: meshShift 30s ease infinite;
        }

        h1, h2, h3, h4, h5, h6, .font-display {
            font-family: 'Special Elite', cursive;
            letter-spacing: 0.04em;
        }

        /* === GLASSMORPHISM BASE === */
        .glass {
            background: rgba(11, 22, 40, 0.65);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(0, 198, 255, 0.12);
        }

        .glass-card {
            background: rgba(11, 22, 40, 0.55);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(0, 198, 255, 0.10);
        }

        /* === STAMP BADGE (Classified aesthetic) === */
        .stamp {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-family: 'IBM Plex Mono', monospace;
            font-weight: 600;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            padding: 3px 10px;
            border-radius: 3px;
            border-width: 1.5px;
            border-style: solid;
            transform: rotate(-1.5deg);
            position: relative;
        }
        .stamp::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 3px;
            opacity: 0.08;
        }
        .stamp-pending { border-color: #f59e0b; color: #f59e0b; }
        .stamp-disposisi { border-color: #00c6ff; color: #00c6ff; }
        .stamp-red { border-color: #ff3333; color: #ff3333; }

        /* === SIDEBAR ACTIVE GLOW LINE === */
        .nav-active {
            background: rgba(0, 198, 255, 0.08);
            border-left: 2px solid #00c6ff;
            color: #00c6ff;
            text-shadow: 0 0 12px rgba(0,198,255,0.4);
        }
        .nav-item {
            border-left: 2px solid transparent;
            transition: all 0.2s ease;
        }
        .nav-item:hover {
            background: rgba(0,198,255,0.05);
            border-left-color: rgba(0,198,255,0.4);
            color: #e2e8f0;
        }

        /* === CORNER BRACKET DECORATION === */
        .bracket-box {
            position: relative;
        }
        .bracket-box::before, .bracket-box::after {
            content: '';
            position: absolute;
            width: 10px;
            height: 10px;
            border-color: rgba(0,198,255,0.4);
            border-style: solid;
        }
        .bracket-box::before { top: -1px; left: -1px; border-width: 1px 0 0 1px; }
        .bracket-box::after  { bottom: -1px; right: -1px; border-width: 0 1px 1px 0; }

        /* === SCROLLBAR === */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(0,198,255,0.2); border-radius: 2px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(0,198,255,0.4); }

        /* === INPUT FOCUS GLOW === */
        .ops-input {
            background: rgba(6,13,25,0.8);
            border: 1px solid rgba(0,198,255,0.15);
            color: #e2e8f0;
            transition: all 0.2s;
        }
        .ops-input:focus {
            outline: none;
            border-color: rgba(0,198,255,0.5);
            box-shadow: 0 0 0 3px rgba(0,198,255,0.08), 0 0 12px rgba(0,198,255,0.1);
        }
        .ops-input::placeholder { color: rgba(148,163,184,0.5); }

        /* === SELECT DARK === */
        select.ops-input option { background: #0b1628; }

        /* === PRIMARY BUTTON === */
        .btn-primary {
            background: linear-gradient(135deg, #00c6ff, #0072ff);
            color: #060d19;
            font-weight: 700;
            letter-spacing: 0.04em;
            transition: all 0.2s;
            box-shadow: 0 0 20px rgba(0,198,255,0.2);
        }
        .btn-primary:hover {
            box-shadow: 0 0 30px rgba(0,198,255,0.4);
            transform: translateY(-1px);
        }

        /* === CLASSIFIED HEADER LABEL === */
        .section-eyebrow {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 10px;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: rgba(0,198,255,0.6);
        }
    </style>
</head>
<body>

    <!-- TOP NAVBAR -->
    <nav class="glass px-6 py-3 flex justify-between items-center fixed w-full top-0 z-50 shadow-lg shadow-black/30">
        <div class="flex items-center space-x-3">
            <img src="{{ asset('images/logo-divhubinter.png') }}" alt="Logo Divhubinter" class="h-9 w-auto object-contain drop-shadow-[0_0_8px_rgba(245,158,11,0.4)]">
            <div>
                <span class="font-display text-lg tracking-widest text-white">INTEROPS<span class="text-ops-gold">HUB</span></span>
                <p class="section-eyebrow" style="font-size:9px; margin-top:-2px;">DIVHUBINTER POLRI · SISTEM KEARSIPAN TERPADU</p>
            </div>
        </div>

        @if(Auth::check())
        <div class="flex items-center space-x-4">
            <div class="text-right">
                <p class="text-sm font-semibold text-white leading-tight">{{ Auth::user()->nama_lengkap }}</p>
                <p class="section-eyebrow capitalize" style="color: rgba(167,139,250,0.8);">{{ Auth::user()->role }}</p>
            </div>
            <div class="w-px h-8 bg-ops-border"></div>
            <a href="{{ url('/logout') }}" title="Keluar dari Sistem" class="text-slate-500 hover:text-red-400 transition-colors duration-200 p-2">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
        @endif
    </nav>

    <div class="flex pt-[60px] min-h-screen">
        @if(Auth::check())
            <!-- SIDEBAR -->
            <aside class="glass w-60 flex flex-col justify-between fixed h-[calc(100vh-60px)] shadow-xl shadow-black/40 z-40">
                <div class="p-5 space-y-6 overflow-y-auto">
                    <!-- CORE SYSTEM -->
                    <div>
                        <p class="section-eyebrow mb-3 px-3">Sistem Utama</p>
                        <nav class="space-y-1">
                            <a href="{{ url('/dashboard') }}" class="nav-item {{ Request::is('dashboard') ? 'nav-active' : 'text-slate-400' }} flex items-center space-x-3 px-3 py-2.5 rounded-r-lg text-sm">
                                <i class="fas fa-chart-pie w-4 text-center opacity-70"></i>
                                <span>Dashboard</span>
                            </a>
                            <a href="{{ url('/surat-masuk') }}" class="nav-item {{ Request::is('surat-masuk*') ? 'nav-active' : 'text-slate-400' }} flex items-center space-x-3 px-3 py-2.5 rounded-r-lg text-sm">
                                <i class="fas fa-inbox w-4 text-center opacity-70"></i>
                                <span>Surat Masuk</span>
                            </a>
                            <a href="{{ url('/surat-keluar') }}" class="nav-item {{ Request::is('surat-keluar*') ? 'nav-active' : 'text-slate-400' }} flex items-center space-x-3 px-3 py-2.5 rounded-r-lg text-sm">
                                <i class="fas fa-paper-plane w-4 text-center opacity-70"></i>
                                <span>Surat Keluar</span>
                            </a>
                        </nav>
                    </div>

                    @can('akses-admin')
                    <div class="border-t border-ops-border pt-5">
                        <p class="section-eyebrow mb-3 px-3" style="color: rgba(167,139,250,0.6);">Audit &amp; Kontrol</p>
                        <nav class="space-y-1">
                            <a href="{{ url('/user-management') }}" class="nav-item {{ Request::is('user-management*') ? 'nav-active' : 'text-slate-400' }} flex items-center space-x-3 px-3 py-2.5 rounded-r-lg text-sm">
                                <i class="fas fa-users-cog w-4 text-center opacity-70"></i>
                                <span>Manajemen Personel</span>
                            </a>
                            <a href="{{ url('/activity-logs') }}" class="nav-item {{ Request::is('activity-logs*') ? 'nav-active' : 'text-slate-400' }} flex items-center space-x-3 px-3 py-2.5 rounded-r-lg text-sm">
                                <i class="fas fa-history w-4 text-center opacity-70"></i>
                                <span>Log Aktivitas</span>
                            </a>
                        </nav>
                    </div>
                    @endcan
                </div>

                <!-- SIDEBAR FOOTER -->
                <div class="p-5 border-t border-ops-border">
                    <p class="font-mono text-[10px] text-slate-600 text-center tracking-widest uppercase">&copy; {{ date('Y') }} DIVHUBINTER POLRI</p>
                </div>
            </aside>

            <main class="flex-1 ml-60 p-8 overflow-y-auto">
                @yield('content')
            </main>

        @else
            <main class="flex-1 p-8 flex items-center justify-center">
                @yield('content')
            </main>
        @endif
    </div>

    @stack('scripts')
</body>
</html>