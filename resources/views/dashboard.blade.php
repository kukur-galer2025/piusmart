@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 sm:space-y-8">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-gray-900">{{ __('dashboard') }}</h1>
            <p class="text-sm text-gray-500 mt-1">Ringkasan kondisi finansial dan piutang usaha per hari ini.</p>
        </div>
        <div>
            <a href="{{ route('receivables.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-emerald-700 bg-emerald-100 rounded-lg hover:bg-emerald-200 transition-colors cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 mr-1.5"><path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" /></svg>
                Catat Piutang
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:gap-5 sm:grid-cols-2 lg:grid-cols-3">
        
        <div class="relative bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100 p-5 sm:p-6 group hover:shadow-md transition-all duration-300">
            <div class="absolute right-0 top-0 w-24 h-24 bg-blue-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
            
            <div class="relative flex items-start space-x-3 sm:space-x-4">
                <div class="p-3 bg-blue-100 text-blue-600 rounded-xl shadow-inner shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 sm:w-6 sm:h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                </div>
                <div>
                    <p class="text-[11px] sm:text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('active_receivables') }}</p>
                    <p class="text-xl sm:text-2xl font-black text-gray-900 mt-1 tracking-tight">Rp {{ number_format($activeAmount, 0, ',', '.') }}</p>
                    <div class="flex items-center mt-1.5 text-[11px] sm:text-xs font-medium text-gray-500">
                        <span class="px-1.5 py-0.5 rounded-md bg-gray-100 text-gray-600 mr-1.5">{{ $activeCount }}</span> Transaksi berjalan
                    </div>
                </div>
            </div>
        </div>

        <div class="relative bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100 p-5 sm:p-6 group hover:shadow-md transition-all duration-300">
            <div class="absolute right-0 top-0 w-24 h-24 bg-amber-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
            
            <div class="relative flex items-start space-x-3 sm:space-x-4">
                <div class="p-3 bg-amber-100 text-amber-600 rounded-xl shadow-inner shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 sm:w-6 sm:h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                </div>
                <div>
                    <p class="text-[11px] sm:text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('due_soon') }} (H-3)</p>
                    <p class="text-xl sm:text-2xl font-black text-gray-900 mt-1 tracking-tight">Rp {{ number_format($dueSoonAmount, 0, ',', '.') }}</p>
                    <div class="flex items-center mt-1.5 text-[11px] sm:text-xs font-medium text-amber-600">
                        <span class="px-1.5 py-0.5 rounded-md bg-amber-100 mr-1.5">{{ $dueSoonCount }}</span> Perlu ditagih
                    </div>
                </div>
            </div>
        </div>

        <div class="relative bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100 p-5 sm:p-6 group hover:shadow-md transition-all duration-300">
            <div class="absolute right-0 top-0 w-24 h-24 bg-rose-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
            
            <div class="relative flex items-start space-x-3 sm:space-x-4">
                <div class="p-3 bg-rose-100 text-rose-600 rounded-xl shadow-inner shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 sm:w-6 sm:h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
                <div>
                    <p class="text-[11px] sm:text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('overdue') }}</p>
                    <p class="text-xl sm:text-2xl font-black text-rose-600 mt-1 tracking-tight">Rp {{ number_format($overdueAmount, 0, ',', '.') }}</p>
                    <div class="flex items-center mt-1.5 text-[11px] sm:text-xs font-bold text-rose-600">
                        <span class="px-1.5 py-0.5 rounded-md bg-rose-100 mr-1.5">{{ $overdueCount }}</span> Lewat batas waktu!
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden">
        
        <div class="px-5 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50">
            <div class="flex items-center">
                <div class="p-1.5 bg-indigo-100 text-indigo-600 rounded-lg mr-3 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M5.25 9a6.75 6.75 0 0 1 13.5 0v.75c0 2.123.8 4.057 2.118 5.52a.75.75 0 0 1-.297 1.206c-1.544.57-3.16.99-4.831 1.243a3.75 3.75 0 1 1-7.48 0 24.585 24.585 0 0 1-4.831-1.244.75.75 0 0 1-.298-1.205A8.217 8.217 0 0 0 5.25 9.75V9Zm4.502 8.9a2.25 2.25 0 1 0 4.496 0 25.057 25.057 0 0 1-4.496 0Z" clip-rule="evenodd" /></svg>
                </div>
                <h2 class="text-[13px] sm:text-sm font-bold text-gray-800 uppercase tracking-wider">{{ __('notification') }} Mendesak</h2>
            </div>
            <a href="{{ route('receivables.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition-colors bg-indigo-50 px-3 py-1.5 rounded-lg sm:bg-transparent sm:px-0 sm:py-0 self-start sm:self-auto">Lihat Semua &rarr;</a>
        </div>
        
        <div class="p-3 sm:p-4">
            @if($notifications->isEmpty())
                <div class="flex flex-col items-center justify-center py-8 sm:py-10">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 sm:w-12 sm:h-12 text-gray-300 mb-3"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    <p class="text-sm text-gray-500 font-medium">{{ __('no_notification') }}</p>
                    <p class="text-[11px] sm:text-xs text-gray-400 mt-1 text-center">Semua piutang aman dan terkendali.</p>
                </div>
            @else
                <div class="space-y-2.5">
                    @foreach($notifications as $notif)
                        <div class="flex items-start p-3.5 sm:p-4 rounded-xl transition-colors {{ $notif['type'] === 'overdue' ? 'bg-rose-50 hover:bg-rose-100/70 border border-rose-100' : 'bg-amber-50 hover:bg-amber-100/70 border border-amber-100' }}">
                            <div class="shrink-0 mt-0.5 mr-3 sm:mr-4">
                                @if($notif['type'] === 'overdue')
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 sm:w-6 sm:h-6 text-rose-500"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" /></svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 sm:w-6 sm:h-6 text-amber-500"><path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" /></svg>
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="text-[13px] sm:text-sm font-semibold leading-relaxed {{ $notif['type'] === 'overdue' ? 'text-rose-800' : 'text-amber-800' }}">
                                    {{ $notif['message'] }}
                                </p>
                                <p class="text-[11px] sm:text-xs mt-1 {{ $notif['type'] === 'overdue' ? 'text-rose-500' : 'text-amber-600' }}">
                                    Harap segera hubungi pelanggan yang bersangkutan.
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    
</div>
@endsection