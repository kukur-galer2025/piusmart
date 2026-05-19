<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      x-data="{ darkMode: localStorage.getItem('theme') === 'dark' }" 
      x-init="$watch('darkMode', val => localStorage.setItem('theme', val ? 'dark' : 'light'))" 
      :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Piusmart Panel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        .no-scrollbar::-webkit-scrollbar { display: none !important; width: 0 !important; height: 0 !important; }
        .no-scrollbar { -ms-overflow-style: none !important; scrollbar-width: none !important; -webkit-overflow-scrolling: touch !important; }
    </style>
    {{-- Inisialisasi Alpine.store SEBELUM Alpine dimuat (pakai document.addEventListener) --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('modals', {
                confirmMarkAll: false,
                aboutOpen: false,
                sidebarOpen: false,
            });
        });
    </script>
</head>
<body class="bg-gray-50 dark:bg-slate-900 font-sans text-gray-900 dark:text-gray-100">

    <div class="flex h-screen overflow-hidden relative">
        
        {{-- Overlay mobile --}}
        <div x-data x-show="$store.modals.sidebarOpen" 
             x-transition.opacity 
             @click="$store.modals.sidebarOpen = false" 
             class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm md:hidden" x-cloak>
        </div>

        {{-- Sidebar --}}
        <aside x-data
               class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-slate-800 border-r border-gray-200 dark:border-slate-700/50 transform transition-transform duration-300 md:translate-x-0 md:relative flex flex-col h-full shadow-2xl md:shadow-none"
               :class="$store.modals.sidebarOpen ? 'translate-x-0' : '-translate-x-full'" x-cloak>
            
            <div class="flex items-center justify-between h-16 px-5 bg-white dark:bg-slate-800 border-b border-gray-100 dark:border-slate-700/50 shrink-0">
                <a href="{{ route('dashboard') }}" class="flex items-center hover:opacity-80">
                    <img src="{{ asset('images/logo.png') }}" alt="Piusmart Logo" class="h-9 w-auto object-contain block dark:hidden drop-shadow-sm">
                    <img src="{{ asset('images/logo.png') }}" alt="Piusmart Logo" class="h-9 w-auto object-contain hidden dark:block filter brightness-0 invert">
                </a>
                <button @click="$store.modals.sidebarOpen = false" class="md:hidden text-gray-400 hover:text-rose-500 p-1.5 rounded-md hover:bg-gray-100 dark:hover:bg-slate-700">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <nav class="flex-1 px-3 py-5 space-y-1 overflow-y-auto no-scrollbar">
                <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-sm font-semibold rounded-xl {{ request()->routeIs('dashboard') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'text-gray-500 dark:text-gray-400 hover:bg-emerald-50 dark:hover:bg-slate-700 hover:text-emerald-700 dark:hover:text-emerald-400' }}">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" /></svg>
                    {{ __('dashboard') }}
                </a>
                <a href="{{ route('receivables.index') }}" class="flex items-center px-4 py-3 text-sm font-semibold rounded-xl {{ request()->routeIs('receivables.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'text-gray-500 dark:text-gray-400 hover:bg-emerald-50 dark:hover:bg-slate-700 hover:text-emerald-700 dark:hover:text-emerald-400' }}">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" /></svg>
                    {{ __('receivables_data') }}
                </a>
                <a href="{{ route('customers.index') }}" class="flex items-center px-4 py-3 text-sm font-semibold rounded-xl {{ request()->routeIs('customers.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'text-gray-500 dark:text-gray-400 hover:bg-emerald-50 dark:hover:bg-slate-700 hover:text-emerald-700 dark:hover:text-emerald-400' }}">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" /></svg>
                    {{ __('customer_data') }}
                </a>
                <a href="{{ route('settings.index') }}" class="flex items-center px-4 py-3 text-sm font-semibold rounded-xl {{ request()->routeIs('settings.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'text-gray-500 dark:text-gray-400 hover:bg-emerald-50 dark:hover:bg-slate-700 hover:text-emerald-700 dark:hover:text-emerald-400' }}">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.468-.47-1.082-.76-1.76-.84l-1.92-.224A1.688 1.688 0 0 1 5.25 13.1v-2.2a1.688 1.688 0 0 1 1.41-1.676l1.92-.224c.678-.08 1.292-.37 1.76-.84.47-.468.76-1.082.84-1.76l.224-1.92A1.688 1.688 0 0 1 13.1 5.25h2.2a1.688 1.688 0 0 1 1.676 1.41l.224 1.92c.08.678.37 1.292.84 1.76.468.47 1.082.76 1.76.84l1.92.224A1.688 1.688 0 0 1 23.25 13.1v2.2a1.688 1.688 0 0 1-1.41 1.676l-1.92.224c-.678.08-1.292.37-1.76.84-.47.468-.76 1.082-.84 1.76l-.224 1.92A1.688 1.688 0 0 1 15.3 23.25h-2.2a1.688 1.688 0 0 1-1.676-1.41l-.224-1.92c-.08-.678-.37-1.292-.84-1.76Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                    {{ __('settings') }}
                </a>
            </nav>

            <div class="p-3 mt-auto border-t border-gray-100 dark:border-slate-700/50">
                <div x-data="{ timeString: '', updateClock() { this.timeString = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false }); } }"
                     x-init="updateClock(); setInterval(() => updateClock(), 1000)"
                     class="flex items-center justify-center gap-2 px-4 py-3 text-sm font-bold text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl shadow-sm w-full">
                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    <span x-text="timeString" class="tabular-nums tracking-wider"></span>
                </div>
            </div>
        </aside>

        {{-- Main Area --}}
        <div class="flex flex-col flex-1 min-w-0 overflow-hidden relative">
            
            {{-- Header --}}
            <header class="flex items-center justify-between h-16 px-4 md:px-6 bg-white dark:bg-slate-800 border-b border-gray-200 dark:border-slate-700/50 shrink-0 relative z-20">
                <button x-data @click="$store.modals.sidebarOpen = true" class="text-gray-600 dark:text-gray-400 p-1.5 md:hidden rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                </button>

                <div class="flex items-center gap-1 sm:gap-2 ml-auto relative">
                    
                    {{-- Dark mode toggle --}}
                    <button @click="darkMode = !darkMode" class="p-2 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-slate-700 dark:hover:text-emerald-400 rounded-full cursor-pointer">
                        <svg x-show="!darkMode" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" /></svg>
                        <svg x-show="darkMode" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" x-cloak><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" /></svg>
                    </button>
                    
                    @php
                        $unreadCountDB = Auth::check() ? Auth::user()->unreadNotifications->count() : 0;
                        $dbNotifications = Auth::check() ? Auth::user()->unreadNotifications->take(5) : collect();
                    @endphp

                    {{-- Notif Bell --}}
                    <div class="relative" x-data="{ notifOpen: false }">
                        <button @click="notifOpen = !notifOpen" class="relative p-2 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-slate-700 dark:hover:text-emerald-400 rounded-full cursor-pointer">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" /></svg>
                            @if($unreadCountDB > 0)
                                <span class="absolute top-1 right-1 flex h-3.5 w-3.5 items-center justify-center rounded-full bg-rose-500 text-[8px] font-bold text-white ring-2 ring-white dark:ring-slate-800">
                                    {{ $unreadCountDB > 9 ? '9+' : $unreadCountDB }}
                                </span>
                            @endif
                        </button>

                        {{-- Dropdown Notif --}}
                        <div x-show="notifOpen" @click.away="notifOpen = false" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="fixed top-[70px] right-3 left-3 sm:absolute sm:inset-auto sm:top-auto sm:left-auto sm:-right-2 sm:mt-2 sm:w-96 bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-slate-700 z-[90] overflow-hidden origin-top-right" x-cloak>
                            
                            <div class="px-4 py-3 border-b border-gray-100 dark:border-slate-700 flex items-center justify-between">
                                <span class="font-bold text-sm text-gray-800 dark:text-gray-100">{{ __('notification') }}</span>
                                <div class="flex items-center gap-2">
                                    @if($unreadCountDB > 0)
                                        {{-- Gunakan Alpine.store — bekerja lintas scope --}}
                                        <button type="button"
                                                @click="$store.modals.confirmMarkAll = true; notifOpen = false"
                                                class="text-[10px] font-bold text-emerald-600 hover:bg-emerald-50 dark:text-emerald-400 dark:hover:bg-slate-700 px-2.5 py-1 rounded-md border border-emerald-200/50 dark:border-emerald-500/30 cursor-pointer whitespace-nowrap">
                                            {{ __('mark_all_read') }}
                                        </button>
                                    @endif
                                    <button @click="notifOpen = false" class="text-gray-400 hover:text-rose-500 p-1 rounded-md hover:bg-rose-50 dark:hover:bg-rose-900/30 cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="no-scrollbar overflow-y-auto divide-y divide-gray-50 dark:divide-slate-700/50" style="max-height: 280px;">
                                @if($unreadCountDB === 0)
                                    <div class="px-6 py-8 text-center">
                                        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">{{ __('no_notification') }}</p>
                                    </div>
                                @else
                                    @foreach($dbNotifications as $notif)
                                        <a href="{{ route('notifications.index') }}" class="flex px-4 py-3.5 hover:bg-slate-50 dark:hover:bg-slate-700/50 gap-3 block">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200 leading-snug">
                                                    <span class="font-bold">{{ $notif->data['customer_name'] ?? '' }}</span> - {{ __('warning_due_soon') }}
                                                </p>
                                                <p class="text-xs text-gray-400 mt-0.5">{{ $notif->created_at->diffForHumans() }}</p>
                                            </div>
                                        </a>
                                    @endforeach
                                @endif
                            </div>

                            <div class="px-4 py-3 border-t border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-700/30 text-center">
                                <a href="{{ route('notifications.index') }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800">
                                    {{ __('view_all') }} &rarr;
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Language switcher --}}
                    <div class="flex bg-gray-100 dark:bg-slate-900/80 rounded-lg p-0.5 border border-gray-200 dark:border-slate-700 text-[10px] sm:text-xs font-bold select-none text-gray-500 dark:text-gray-400 shrink-0">
                        <a href="{{ route('language.switch', 'id') }}" class="px-2 py-1.5 rounded-md {{ app()->getLocale() == 'id' ? 'bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400 shadow-sm' : 'hover:text-gray-900 dark:hover:text-gray-200' }}">ID</a>
                        <a href="{{ route('language.switch', 'en') }}" class="px-2 py-1.5 rounded-md {{ app()->getLocale() == 'en' ? 'bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400 shadow-sm' : 'hover:text-gray-900 dark:hover:text-gray-200' }}">EN</a>
                    </div>

                    {{-- Profile --}}
                    <div class="relative pl-1 sm:pl-2 border-l border-gray-200 dark:border-slate-700" x-data="{ profileOpen: false }">
                        <button @click="profileOpen = !profileOpen" class="flex items-center space-x-1.5 focus:outline-none p-1 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-emerald-500 to-teal-400 text-white flex items-center justify-center font-bold text-sm shadow-sm ring-2 ring-white dark:ring-slate-800">
                                {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                            </div>
                        </button>
                        <div x-show="profileOpen" @click.away="profileOpen = false" x-transition 
                             class="absolute right-0 mt-2 w-48 bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-100 dark:border-slate-700 py-1.5 z-50" x-cloak>
                            <div class="px-4 py-2.5 border-b border-gray-100 dark:border-slate-700 mb-1">
                                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">{{ __('login_as') }}</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ Auth::user()->email ?? 'admin@piusmart.com' }}</p>
                            </div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left flex items-center px-4 py-2.5 text-sm font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-slate-700/50 cursor-pointer">
                                    <svg class="w-4 h-4 mr-2.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" /></svg>
                                    {{ __('sign_out') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50/50 dark:bg-slate-900/50 relative z-10 flex flex-col no-scrollbar">
                <div class="p-4 sm:p-6 md:p-8 flex-1">
                    @yield('content')
                </div>
            </main>

            <footer class="shrink-0 px-4 py-3 sm:py-4 bg-white dark:bg-slate-800 border-t border-gray-200 dark:border-slate-700/50 text-center flex flex-col items-center justify-center relative z-20">
                <p class="text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300">
                    &copy; {{ date('Y') }} Piusmart Executive. {{ __('footer_rights') }}
                </p>
                <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    {{ __('footer_desc') }}
                </p>
                <button x-data @click="$store.modals.aboutOpen = true" class="mt-1.5 text-[10px] sm:text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline focus:outline-none uppercase tracking-wider cursor-pointer">
                    {{ __('about_us') }}
                </button>
            </footer>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- MODALS — di luar .flex.h-screen, pakai Alpine.store          --}}
    {{-- ============================================================ --}}

    {{-- Modal Konfirmasi Tandai Semua Dibaca --}}
    <div x-data x-show="$store.modals.confirmMarkAll"
         class="fixed inset-0 z-[200] flex items-end sm:items-center justify-center bg-slate-900/60 backdrop-blur-sm"
         x-cloak>
        <div @click.away="$store.modals.confirmMarkAll = false"
             x-show="$store.modals.confirmMarkAll"
             x-transition:enter="transition ease-out duration-250"
             x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-90"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-90"
             class="bg-white dark:bg-slate-800 rounded-t-3xl sm:rounded-3xl shadow-2xl w-full sm:max-w-sm p-6 text-center">

            <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-indigo-50 dark:bg-indigo-900/30 mb-4">
                <svg class="h-7 w-7 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                </svg>
            </div>

            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('mark_all_title') }}</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ __('mark_all_confirm_text', ['count' => $unreadCountDB]) }}</p>

            <div class="flex gap-3 mt-6">
                <button @click="$store.modals.confirmMarkAll = false"
                        class="flex-1 px-4 py-3 text-sm font-semibold text-gray-700 dark:text-slate-200 bg-gray-100 dark:bg-slate-700 rounded-xl hover:bg-gray-200 dark:hover:bg-slate-600 cursor-pointer">
                    {{ __('cancel') }}
                </button>
                <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="flex-1 m-0">
                    @csrf
                    <button type="submit"
                            class="w-full px-4 py-3 text-sm font-bold text-white bg-emerald-600 dark:bg-emerald-500 rounded-xl hover:bg-emerald-700 shadow-md shadow-emerald-600/20 cursor-pointer">
                        {{ __('yes_mark_all') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal About --}}
    <div x-data x-show="$store.modals.aboutOpen"
         class="fixed inset-0 z-[200] flex items-end sm:items-center justify-center bg-slate-900/70 backdrop-blur-sm" x-cloak>
        <div @click.away="$store.modals.aboutOpen = false"
             x-show="$store.modals.aboutOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
             class="bg-white dark:bg-slate-800 rounded-t-3xl sm:rounded-3xl shadow-2xl w-full sm:max-w-md overflow-hidden">
            <div class="relative bg-emerald-600 px-6 py-7 text-center text-white">
                <button @click="$store.modals.aboutOpen = false" class="absolute top-4 right-4 text-white/70 hover:text-white bg-black/10 hover:bg-black/20 p-1.5 rounded-full cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
                <div class="w-14 h-14 bg-white rounded-full flex items-center justify-center mx-auto mb-3 shadow-lg">
                    <svg class="w-7 h-7 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                </div>
                <h3 class="text-xl font-extrabold tracking-tight">{{ __('dev_team') }}</h3>
                <p class="text-sm font-medium text-emerald-100 mt-1">Piusmart Executive System</p>
            </div>
            <div class="p-4 sm:p-5 bg-slate-50 dark:bg-slate-900 space-y-3">
                @foreach([['adinda', 'Adinda Khansa Oktaviana', '2202030127'], ['revina', 'Revina Diah Ayu Ningtias', '2402030021'], ['mesya', 'Mesya Cindy Audi As Alwa', '2402030023']] as [$img, $name, $nim])
                <div class="flex items-center gap-3 sm:gap-4 p-3 sm:p-4 rounded-2xl bg-white dark:bg-slate-800 shadow-sm border border-slate-100 dark:border-slate-700/50">
                    <div class="w-11 h-11 rounded-full overflow-hidden shrink-0 ring-2 ring-emerald-100 dark:ring-slate-700">
                        <img src="{{ asset('images/' . $img . '.png') }}" alt="{{ $name }}" class="w-full h-full object-cover">
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-slate-800 dark:text-white leading-tight truncate">{{ $name }}</p>
                        <p class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 mt-0.5">NIM: {{ $nim }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="px-5 py-4 bg-white dark:bg-slate-800 border-t border-slate-100 dark:border-slate-700">
                <button @click="$store.modals.aboutOpen = false" class="w-full py-2.5 text-sm font-bold text-slate-700 dark:text-slate-200 bg-slate-100 dark:bg-slate-700 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-600 cursor-pointer">{{ __('close_panel') }}</button>
            </div>
        </div>
    </div>

</body>
</html>