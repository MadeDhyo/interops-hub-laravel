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
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                        display: ['Montserrat', 'sans-serif'],
                    },
                    colors: {
                        divhub: {
                            navy: '#0B132B',      // Biru dongker pekat untuk background utama
                            card: '#1C2541',      // Biru dongker terang untuk sidebar dan modal
                            border: '#3A506B',    // Garis pembatas elegan
                            gold: '#D4AF37',      // Kuning emas resmi Polri
                            goldlight: '#FDE047', // Emas terang untuk efek hover
                            blue: '#1D4ED8',      // Biru Interpol
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #0B132B;
            color: #F3F4F6;
        }
        h1, h2, h3, h4, h5, h6, .font-display {
            font-family: 'Montserrat', sans-serif;
        }
    </style>
</head>
<body class="bg-divhub-navy text-gray-100 font-sans min-h-screen">

    <nav class="bg-divhub-card border-b border-divhub-border px-6 py-4 flex justify-between items-center fixed w-full top-0 z-50 shadow-md">
        <div class="flex items-center space-x-3">
            <div class="bg-divhub-navy text-divhub-gold p-2 rounded-lg border border-divhub-gold/40 shadow-[0_0_10px_rgba(212,175,55,0.2)]">
                <i class="fas fa-globe-asia text-xl"></i> 
            </div>
            <span class="text-xl font-display font-bold tracking-widest text-white">INTEROPS<span class="text-divhub-gold">HUB</span></span>
        </div>
        
        @if(Auth::check())
        <div class="flex items-center space-x-5">
            <div class="text-right">
                <p class="text-sm font-semibold text-white">{{ Auth::user()->nama_lengkap }}</p>
                <p class="text-xs text-divhub-gold capitalize tracking-wide">{{ Auth::user()->role }}</p>
            </div>
            <a href="{{ url('/logout') }}" class="text-gray-400 hover:text-red-500 transition-colors duration-200">
                <i class="fas fa-sign-out-alt text-lg"></i>
            </a>
        </div>
        @endif
    </nav>

    <div class="flex pt-20 min-h-screen">
        @if(Auth::check())
            <aside class="w-64 bg-divhub-card border-r border-divhub-border p-6 flex flex-col justify-between fixed h-[calc(100vh-5rem)]">
                <div class="space-y-6">
                    <div class="space-y-2">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 mb-3">Sistem Utama</p>
                        
                        <a href="{{ url('/dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-all duration-200 {{ Request::is('dashboard') ? 'bg-divhub-navy text-divhub-gold border-l-4 border-divhub-gold font-medium' : 'text-gray-300 hover:bg-divhub-navy hover:text-white' }}">
                            <i class="fas fa-chart-pie w-5"></i>
                            <span>Dashboard</span>
                        </a>
                        
                        <a href="{{ url('/surat-masuk') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-all duration-200 {{ Request::is('surat-masuk*') ? 'bg-divhub-navy text-divhub-gold border-l-4 border-divhub-gold font-medium' : 'text-gray-300 hover:bg-divhub-navy hover:text-white' }}">
                            <i class="fas fa-inbox w-5"></i>
                            <span>Surat Masuk</span>
                        </a>
                        
                        <a href="{{ url('/surat-keluar') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-all duration-200 {{ Request::is('surat-keluar*') ? 'bg-divhub-navy text-divhub-gold border-l-4 border-divhub-gold font-medium' : 'text-gray-300 hover:bg-divhub-navy hover:text-white' }}">
                            <i class="fas fa-paper-plane w-5"></i>
                            <span>Surat Keluar</span>
                        </a>
                    </div>

                    @can('akses-admin')
                    <div class="space-y-2 pt-5 border-t border-divhub-border">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 mb-3">Audit & Kontrol</p>
                        
                        <a href="{{ url('/user-management') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-all duration-200 {{ Request::is('user-management*') ? 'bg-divhub-navy text-divhub-blue border-l-4 border-divhub-blue font-medium' : 'text-gray-300 hover:bg-divhub-navy hover:text-white' }}">
                            <i class="fas fa-users-cog w-5"></i>
                            <span>Manajemen Personel</span>
                        </a>
                        
                        <a href="{{ url('/activity-logs') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-all duration-200 {{ Request::is('activity-logs*') ? 'bg-divhub-navy text-divhub-blue border-l-4 border-divhub-blue font-medium' : 'text-gray-300 hover:bg-divhub-navy hover:text-white' }}">
                            <i class="fas fa-history w-5"></i>
                            <span>Log Aktivitas</span>
                        </a>
                    </div>
                    @endcan
                </div>
                
                <div class="border-t border-divhub-border pt-4 text-center text-xs text-gray-400 font-display font-semibold tracking-wide">
                    &copy; {{ date('Y') }} DIVHUBINTER POLRI
                </div>
            </aside>

            <main class="flex-1 ml-64 p-8 bg-divhub-navy overflow-y-auto">
                @yield('content')
            </main>
        @else
            <main class="flex-1 p-8 bg-divhub-navy flex items-center justify-center">
                @yield('content')
            </main>
        @endif
    </div>

    @stack('scripts')
</body>
</html>