<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ darkMode: localStorage.getItem('theme') === 'dark' }"
      x-init="$watch('darkMode', val => localStorage.setItem('theme', val ? 'dark' : 'light'))"
      :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Piusmart Executive</title>
    @vite(['resources/css/app.css'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-screen bg-white dark:bg-slate-900 font-sans text-slate-900 dark:text-slate-100 selection:bg-emerald-100 dark:selection:bg-emerald-900/50 selection:text-emerald-900 dark:selection:text-emerald-100 flex">

    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-slate-950 items-center justify-center">
        
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden opacity-60 pointer-events-none">
            <div class="absolute -top-[20%] -left-[10%] w-[70%] h-[70%] rounded-full bg-emerald-500/30 blur-[80px]"></div>
            <div class="absolute bottom-[-20%] -right-[10%] w-[60%] h-[60%] rounded-full bg-teal-500/30 blur-[80px]"></div>
        </div>

        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCI+PHBhdGggZD0iTTAgMGg0MHY0MEgwek0wIDBoMXY0MEgwek0wIDBoNDB2MUgweiIgZmlsbD0iI2ZmZiIgZmlsbC1vcGFjaXR5PSIwLjAzIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiLz48L3N2Zz4=')] pointer-events-none"></div>

        <div class="relative z-10 w-full max-w-lg px-12 text-white">
            <img src="{{ asset('images/logo.png') }}" alt="Piusmart Logo" class="h-12 w-auto object-contain mb-8 filter brightness-0 invert drop-shadow-md">
            
            <h2 class="text-4xl font-extrabold tracking-tight mb-5 leading-tight">
                {!! __('hero_title') !!}
            </h2>
            <p class="text-slate-400 text-lg leading-relaxed">
                {!! __('hero_desc') !!}
            </p>

            <div class="mt-12 p-6 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div>
                        <p class="text-white font-bold">{{ __('encrypted_system') }}</p>
                        <p class="text-slate-400 text-sm mt-0.5">{{ __('exclusive_access') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="w-full lg:w-1/2 flex flex-col justify-center items-center p-6 sm:p-12 md:p-16 xl:p-24 bg-white dark:bg-slate-900 relative">

        <div class="absolute top-6 right-6 flex items-center gap-2 sm:gap-3 z-50">
            <button @click="darkMode = !darkMode" class="p-2 text-slate-400 hover:text-emerald-600 bg-slate-50 hover:bg-emerald-50 dark:bg-slate-800 dark:hover:bg-slate-700 dark:hover:text-emerald-400 rounded-full border border-slate-200 dark:border-slate-700 shadow-sm cursor-pointer">
                <svg x-show="!darkMode" class="w-4 h-4 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" /></svg>
                <svg x-show="darkMode" class="w-4 h-4 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" x-cloak><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" /></svg>
            </button>
            
            <div class="flex bg-slate-100 dark:bg-slate-800 rounded-xl p-1 border border-slate-200 dark:border-slate-700 text-[10px] sm:text-xs font-bold select-none text-slate-500 dark:text-slate-400 shadow-sm">
                <a href="{{ route('language.switch', 'id') }}" class="px-2.5 py-1.5 rounded-lg {{ app()->getLocale() == 'id' ? 'bg-white dark:bg-slate-700 text-emerald-600 dark:text-emerald-400 shadow-sm' : 'hover:text-slate-800 dark:hover:text-slate-200' }}">ID</a>
                <a href="{{ route('language.switch', 'en') }}" class="px-2.5 py-1.5 rounded-lg {{ app()->getLocale() == 'en' ? 'bg-white dark:bg-slate-700 text-emerald-600 dark:text-emerald-400 shadow-sm' : 'hover:text-slate-800 dark:hover:text-slate-200' }}">EN</a>
            </div>
        </div>

        <div class="absolute top-0 left-0 w-full h-64 bg-gradient-to-b from-emerald-50/70 dark:from-emerald-900/10 to-transparent lg:hidden pointer-events-none"></div>

        <div class="w-full max-w-md relative z-10 mt-12 sm:mt-0">
            
            <div class="lg:hidden flex justify-center mb-10">
                <img src="{{ asset('images/logo.png') }}" alt="Piusmart Logo" class="h-10 sm:h-12 w-auto object-contain block dark:hidden">
                <img src="{{ asset('images/logo.png') }}" alt="Piusmart Logo" class="h-10 sm:h-12 w-auto object-contain hidden dark:block filter brightness-0 invert">
            </div>

            <div class="mb-10 text-center lg:text-left">
                <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">{{ __('login_title') }}</h1>
                <p class="text-slate-500 dark:text-slate-400 mt-2 font-medium">{{ __('login_subtitle') }}</p>
            </div>

            @if(session('success'))
                <div class="p-4 mb-6 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-100 dark:border-emerald-800/50 text-emerald-700 dark:text-emerald-400 text-sm font-semibold rounded-2xl flex items-center shadow-sm">
                    <svg class="w-5 h-5 mr-3 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            @error('email')
                <div class="p-4 mb-6 bg-rose-50 dark:bg-rose-900/30 border border-rose-100 dark:border-rose-800/50 text-rose-600 dark:text-rose-400 text-sm font-semibold rounded-2xl flex items-start shadow-sm">
                    <svg class="w-5 h-5 mr-3 mt-0.5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ $message }}</span>
                </div>
            @enderror

            <form action="{{ route('login.process') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="space-y-2">
                    <label for="email" class="block text-sm font-bold text-slate-700 dark:text-slate-300 ml-1">{{ __('email_address') }}</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none group-focus-within:text-emerald-500 dark:group-focus-within:text-emerald-400 text-slate-400 dark:text-slate-500">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                            </svg>
                        </div>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus placeholder="admin@piusmart.com"
                               class="w-full pl-12 pr-5 py-4 text-[15px] font-medium text-slate-900 dark:text-white bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl focus:outline-none focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 dark:focus:ring-emerald-500/20 transition-shadow duration-200 placeholder-slate-400 dark:placeholder-slate-500 shadow-sm hover:border-slate-300 dark:hover:border-slate-600 [color-scheme:light] dark:[color-scheme:dark]">
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="password" class="block text-sm font-bold text-slate-700 dark:text-slate-300 ml-1">{{ __('password') }}</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none group-focus-within:text-emerald-500 dark:group-focus-within:text-emerald-400 text-slate-400 dark:text-slate-500">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>
                        </div>
                        <input type="password" name="password" id="password" required placeholder="••••••••"
                               class="w-full pl-12 pr-5 py-4 text-[15px] font-black tracking-widest text-slate-900 dark:text-white bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl focus:outline-none focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 dark:focus:ring-emerald-500/20 transition-shadow duration-200 placeholder-slate-400 dark:placeholder-slate-500 shadow-sm hover:border-slate-300 dark:hover:border-slate-600 [color-scheme:light] dark:[color-scheme:dark]">
                    </div>
                </div>

                <div class="pt-1">
                    <label class="inline-flex items-center gap-3 cursor-pointer group">
                        <div class="relative flex items-center justify-center">
                            <input type="checkbox" name="remember" class="peer w-5 h-5 text-emerald-600 bg-slate-50 dark:bg-slate-800 border-slate-300 dark:border-slate-600 rounded focus:ring-emerald-500/20 focus:ring-offset-0 cursor-pointer">
                        </div>
                        <span class="text-sm font-semibold text-slate-600 dark:text-slate-400 group-hover:text-slate-900 dark:group-hover:text-slate-200 select-none">{{ __('remember_me') }}</span>
                    </label>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full flex items-center justify-center py-4 px-4 text-[15px] font-bold text-white bg-emerald-600 dark:bg-emerald-500 rounded-2xl hover:bg-emerald-700 dark:hover:bg-emerald-600 shadow-xl shadow-emerald-600/20 dark:shadow-emerald-900/30 transition-transform transition-shadow duration-200 transform hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900 cursor-pointer">
                        {{ __('access_dashboard') }}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 ml-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </button>
                </div>
            </form>

            <p class="text-center lg:text-left text-xs text-slate-400 dark:text-slate-500 font-semibold mt-12 tracking-wide uppercase">
                &copy; {{ date('Y') }} PIUSMART EXECUTIVE
            </p>
        </div>
    </div>

</body>
</html>