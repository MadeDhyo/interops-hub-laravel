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
</head>
<body class="bg-gray-900 text-gray-100 font-sans min-h-screen">

    <nav class="bg-gray-800 border-b border-gray-700 px-6 py-4 flex justify-between items-center fixed w-full top-0 z-50">
        <div class="flex items-center space-x-3">
            <div class="bg-indigo-600 text-white p-2 rounded-lg">
                <i class="fas fa-hubspot text-xl"></i>
            </div>
            <span class="text-xl font-bold tracking-wider text-white">InterOps<span class="text-indigo-400">-Hub</span></span>
        </div>
        
        @if(Auth::check())
        <div class="flex items-center space-x-4">
            <div class="text-right">
                <p class="text-sm font-semibold text-white">{{ Auth::user()->nama_lengkap }}</p>
                <p class="text-xs text-gray-400 capitalize">{{ Auth::user()->role }}</p>
            </div>
            <a href="{{ url('/logout') }}" class="text-gray-400 hover:text-red-400 transition-colors duration-200">
                <i class="fas fa-sign-out-alt text-lg"></i>
            </a>
        </div>
        @endif
    </nav>

    <div class="flex pt-20 min-h-screen">
        @if(Auth::check())
            <aside class="w-64 bg-gray-800 border-r border-gray-700 p-6 flex flex-col justify-between fixed h-[calc(100vh-5rem)]">
                <div class="space-y-6">
                    <div class="space-y-1">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-4 mb-2">Menu Utama</p>
                        
                        <a href="{{ url('/dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-all duration-200 {{ Request::is('dashboard') ? 'bg-indigo-600/20 text-indigo-400 border-l-4 border-indigo-500 font-medium' : 'text-gray-400 hover:bg-gray-700/50 hover:text-gray-200' }}">
                            <i class="fas fa-chart-pie w-5"></i>
                            <span>Dashboard</span>
                        </a>
                        
                        <a href="{{ url('/surat-masuk') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-all duration-200 {{ Request::is('surat-masuk*') ? 'bg-indigo-600/20 text-indigo-400 border-l-4 border-indigo-500 font-medium' : 'text-gray-400 hover:bg-gray-700/50 hover:text-gray-200' }}">
                            <i class="fas fa-inbox w-5"></i>
                            <span>Surat Masuk</span>
                        </a>
                        
                        <a href="{{ url('/surat-keluar') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-all duration-200 {{ Request::is('surat-keluar*') ? 'bg-indigo-600/20 text-indigo-400 border-l-4 border-indigo-500 font-medium' : 'text-gray-400 hover:bg-gray-700/50 hover:text-gray-200' }}">
                            <i class="fas fa-paper-plane w-5"></i>
                            <span>Surat Keluar</span>
                        </a>
                    </div>

                    @can('akses-admin')
                    <div class="space-y-1 pt-4 border-t border-gray-700/50">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-4 mb-2">Sistem & Audit</p>
                        
                        <a href="{{ url('/user-management') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-all duration-200 {{ Request::is('user-management*') ? 'bg-indigo-600/20 text-indigo-400 border-l-4 border-indigo-500 font-medium' : 'text-gray-400 hover:bg-gray-700/50 hover:text-gray-200' }}">
                            <i class="fas fa-users-cog w-5 text-indigo-400"></i>
                            <span>Kelola Pengguna</span>
                        </a>
                        
                        <a href="{{ url('/activity-logs') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-all duration-200 {{ Request::is('activity-logs*') ? 'bg-indigo-600/20 text-indigo-400 border-l-4 border-indigo-500 font-medium' : 'text-gray-400 hover:bg-gray-700/50 hover:text-gray-200' }}">
                            <i class="fas fa-history w-5 text-amber-400"></i>
                            <span>Log Aktivitas</span>
                        </a>
                    </div>
                    @endcan
                </div>
                
                <div class="border-t border-gray-700 pt-4 text-center text-xs text-gray-500">
                    &copy; {{ date('Y') }} InterOps-Hub v1.0
                </div>
            </aside>

            <main class="flex-1 ml-64 p-8 bg-gray-900 overflow-y-auto">
                @yield('content')
            </main>
        @else
            <main class="flex-1 p-8 bg-gray-900 flex items-center justify-center">
                @yield('content')
            </main>
        @endif
    </div>

    @stack('scripts')
</body>
</html>