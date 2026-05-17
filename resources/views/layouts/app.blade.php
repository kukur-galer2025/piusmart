<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Piusmart Panel</title>
    
    <!-- Menggunakan Tailwind CSS & JS via Vite Lokal -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js via CDN (Simple & Valid sesuai Dokumentasi) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Mencegah kilatan komponen Alpine sebelum ter-load sempurna -->
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-gray-50 font-sans text-gray-900" x-data="{ sidebarOpen: false }">

    <div class="flex h-screen overflow-hidden relative">
        
        <!-- ========================================================================= -->
        <!-- BACKDROP OVERLAY (Khusus Mobile: Berfungsi meredupkan layar & menutup menu) -->
        <!-- ========================================================================= -->
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false" 
             class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-xs md:hidden" 
             x-cloak>
        </div>

        <!-- ========================================================================= -->
        <!-- SIDEBAR COMPONENT (Z-Index dinaikkan agar berada di atas backdrop) -->
        <!-- ========================================================================= -->
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-white transform transition-transform duration-300 ease-in-out md:translate-x-0 md:relative flex flex-col h-full"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" x-cloak>
            
            <!-- Logo Brand -->
            <div class="flex items-center justify-between h-16 px-6 bg-slate-950 border-b border-slate-800 shrink-0">
                <span class="text-xl font-bold tracking-wider text-emerald-400">PIUSMART</span>
                <!-- Tombol Close internal sidebar (Hanya muncul di HP) -->
                <button @click="sidebarOpen = false" class="md:hidden text-gray-400 hover:text-white p-1 rounded-md focus:outline-none">
                    ✕
                </button>
            </div>

            <!-- Navigasi Menu (Sudah bersih dari link duplikat) -->
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <span class="mr-3 text-base">📊</span> {{ __('dashboard') }}
                </a>
                
                <a href="{{ route('receivables.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('receivables.index') || request()->routeIs('receivables.create') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <span class="mr-3 text-base">💸</span> {{ __('receivables_data') }}
                </a>
            </nav>
        </aside>

        <!-- ========================================================================= -->
        <!-- MAIN CONTENT AREA -->
        <!-- ========================================================================= -->
        <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
            
            <!-- NAVBAR ATAS -->
            <header class="flex items-center justify-between h-16 px-4 md:px-6 bg-white border-b border-gray-200 shrink-0 shadow-xs">
                <!-- Hamburger Menu (Hanya muncul di HP untuk pemicu buka sidebar) -->
                <button @click="sidebarOpen = true" class="text-gray-600 p-2 rounded-lg hover:bg-gray-100 focus:outline-none md:hidden cursor-pointer">
                    ☰
                </button>

                <div class="flex items-center space-x-3 md:space-x-4 ml-auto">
                    
                    <!-- DROPDOWN NOTIFIKASI LONCENG -->
                    <div class="relative" x-data="{ notifOpen: false }">
                        <!-- Tombol Lonceng Notifikasi -->
                        <button @click="notifOpen = !notifOpen" 
                                class="relative p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition-all focus:outline-none cursor-pointer">
                            <span class="text-xl">🔔</span>
                            <!-- Badge Counter Angka Notifikasi Kritis -->
                            @if(isset($globalNotifications) && $globalNotifications->isNotEmpty())
                                <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-rose-500 text-[10px] font-bold text-white shadow-xs">
                                    {{ $globalNotifications->count() }}
                                </span>
                            @endif
                        </button>

                        <!-- Panel Dropdown List Pesan Peringatan -->
                        <div x-show="notifOpen" 
                             @click.away="notifOpen = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                             class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-xl shadow-xl border border-gray-200 py-2 z-50 overflow-hidden"
                             x-cloak>
                            
                            <div class="px-4 py-2.5 bg-gray-50 border-b border-gray-100 font-bold text-xs text-gray-500 uppercase tracking-wider">
                                {{ __('notification') }}
                            </div>

                            <div class="max-h-72 overflow-y-auto divide-y divide-gray-100">
                                @if(!isset($globalNotifications) || $globalNotifications->isEmpty())
                                    <div class="px-4 py-6 text-center text-sm text-gray-400 font-medium">
                                        {{ __('no_notification') }}
                                    </div>
                                @else
                                    @foreach($globalNotifications as $notif)
                                        <div class="flex items-start px-4 py-3 hover:bg-gray-50 transition-colors space-x-3">
                                            <span class="mt-0.5 text-sm shrink-0">
                                                {{ $notif['type'] === 'overdue' ? '🔴' : '🟡' }}
                                            </span>
                                            <p class="text-xs font-medium leading-relaxed {{ $notif['type'] === 'overdue' ? 'text-rose-700' : 'text-gray-700' }}">
                                                {{ $notif['message'] }}
                                            </p>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- LANGUAGE SWITCHER -->
                    <div class="flex bg-gray-100 rounded-lg p-1 border border-gray-200 text-xs font-semibold select-none">
                        <a href="{{ route('language.switch', 'id') }}" 
                           class="px-2.5 py-1.5 md:px-3 rounded-md transition-all {{ app()->getLocale() == 'id' ? 'bg-white text-emerald-600 shadow-xs' : 'text-gray-500 hover:text-gray-900' }}">
                            ID
                        </a>
                        <a href="{{ route('language.switch', 'en') }}" 
                           class="px-2.5 py-1.5 md:px-3 rounded-md transition-all {{ app()->getLocale() == 'en' ? 'bg-white text-emerald-600 shadow-xs' : 'text-gray-500 hover:text-gray-900' }}">
                            EN
                        </a>
                    </div>

                    <div class="h-5 w-px bg-gray-200"></div>

                    <!-- USER PROFILE -->
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center font-bold text-sm shadow-xs select-none">
                            A
                        </div>
                        <span class="text-sm font-medium text-gray-700 hidden sm:inline">Admin</span>
                    </div>
                </div>
            </header>

            <!-- KONTEN HALAMAN UTAMA (Mendukung scroll independen) -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 md:p-8 bg-gray-50">
                @yield('content')
            </main>
        </div>
    </div>

</body>
</html>