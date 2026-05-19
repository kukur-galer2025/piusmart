@extends('layouts.app')

@section('content')
<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<div class="max-w-5xl mx-auto space-y-4 sm:space-y-6" 
     x-data="{ 
        activeTab: 'belum_dibaca',
        showConfirmModal: false, 
        showSuccessModal: {{ session('success') ? 'true' : 'false' }},
        successMessage: '{{ session('success') }}'
     }">
    
    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between bg-white dark:bg-slate-800 p-4 sm:p-5 md:p-6 rounded-2xl border border-gray-100 dark:border-slate-700 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="p-2.5 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 sm:w-6 sm:h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" /></svg>
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ __('notification') }}</h1>
                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ __('notification_subtitle') }}</p>
            </div>
        </div>
        
        @if(Auth::user()->unreadNotifications->count() > 0)
            <button @click="showConfirmModal = true" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-bold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800/50 rounded-xl hover:bg-emerald-100 dark:hover:bg-emerald-800 active:scale-95 cursor-pointer shadow-sm w-full sm:w-auto">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 mr-2 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                {{ __('mark_all_read') }}
            </button>
        @endif
    </div>

    @php
        $unreadNotifs = $notifications->whereNull('read_at');
        $readNotifs = $notifications->whereNotNull('read_at');
    @endphp

    {{-- Tabs --}}
    <div class="border-b border-gray-200 dark:border-slate-700">
        <nav class="flex overflow-x-auto no-scrollbar -mb-px" aria-label="Tabs">
            <button @click="activeTab = 'belum_dibaca'"
                    :class="activeTab === 'belum_dibaca' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-200 hover:border-gray-300'"
                    class="flex items-center whitespace-nowrap border-b-2 py-3.5 px-4 text-sm font-bold cursor-pointer outline-none shrink-0">
                {{ __('unread_tab') }}
                <span :class="activeTab === 'belum_dibaca' ? 'bg-rose-100 text-rose-600 dark:bg-rose-900/40 dark:text-rose-400' : 'bg-gray-100 text-gray-600 dark:bg-slate-800 dark:text-slate-400'" 
                      class="ml-2 rounded-full py-0.5 px-2 text-xs font-bold">
                    {{ $unreadNotifs->count() }}
                </span>
            </button>

            <button @click="activeTab = 'sudah_dibaca'"
                    :class="activeTab === 'sudah_dibaca' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-200 hover:border-gray-300'"
                    class="flex items-center whitespace-nowrap border-b-2 py-3.5 px-4 text-sm font-bold cursor-pointer outline-none shrink-0">
                {{ __('history_tab') }}
                <span :class="activeTab === 'sudah_dibaca' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400' : 'bg-gray-100 text-gray-600 dark:bg-slate-800 dark:text-slate-400'" 
                      class="ml-2 rounded-full py-0.5 px-2 text-xs font-bold">
                    {{ $readNotifs->count() }}
                </span>
            </button>
        </nav>
    </div>

    {{-- Tab: Belum Dibaca --}}
    <div x-show="activeTab === 'belum_dibaca'"
         x-transition:enter="transition ease-out duration-200" 
         x-transition:enter-start="opacity-0 translate-y-1" 
         x-transition:enter-end="opacity-100 translate-y-0"
         style="display: none;">
        
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <ul class="divide-y divide-gray-100 dark:divide-slate-700/50">
                @forelse($unreadNotifs as $notif)
                    @php
                        $days = 0;
                        if(isset($notif->data['due_date'])) {
                            $dueDate = \Carbon\Carbon::parse($notif->data['due_date'])->startOfDay();
                            $today = \Carbon\Carbon::today()->startOfDay();
                            $days = (int) abs($today->diffInDays($dueDate, false));
                        }
                    @endphp
                    <li class="p-4 flex gap-3 bg-slate-50/60 dark:bg-slate-800/50 hover:bg-slate-100/80 dark:hover:bg-slate-700/50">
                        {{-- Icon --}}
                        <div class="shrink-0 mt-0.5">
                            @if($notif->data['type'] === 'overdue')
                                <div class="h-9 w-9 sm:h-10 sm:w-10 rounded-full bg-rose-100 dark:bg-rose-900/50 flex items-center justify-center ring-2 ring-white dark:ring-slate-800 shadow-sm">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-rose-600 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                </div>
                            @else
                                <div class="h-9 w-9 sm:h-10 sm:w-10 rounded-full bg-amber-100 dark:bg-amber-900/50 flex items-center justify-center ring-2 ring-white dark:ring-slate-800 shadow-sm">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                </div>
                            @endif
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            {{-- Teks notif --}}
                            <p class="text-sm font-bold text-gray-900 dark:text-gray-100 leading-snug">
                                {{ $notif->data['type'] === 'overdue' ? __('notif_overdue', ['name' => $notif->data['customer_name'] ?? '', 'days' => $days]) : __('notif_due_soon', ['name' => $notif->data['customer_name'] ?? '', 'days' => $days]) }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ __('billing_amount') }} <span class="font-bold text-gray-700 dark:text-gray-300">Rp {{ number_format($notif->data['amount'] ?? 0, 0, ',', '.') }}</span>
                            </p>
                            {{-- Waktu + tombol dalam satu baris di mobile --}}
                            <div class="flex items-center justify-between gap-2 mt-2 flex-wrap">
                                <p class="text-[11px] font-semibold text-rose-500 dark:text-rose-400 flex items-center gap-1">
                                    <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                    {{ $notif->created_at->diffForHumans() }}
                                </p>
                                <form action="{{ route('notifications.mark-read', $notif->id) }}" method="POST" class="shrink-0">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 text-[11px] font-extrabold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200/60 dark:border-emerald-800/50 rounded-lg hover:bg-emerald-600 hover:text-white dark:hover:bg-emerald-700 dark:hover:text-white cursor-pointer shadow-sm whitespace-nowrap">
                                        <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                                        {{ __('mark_as_read') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="py-12 flex flex-col items-center justify-center text-center">
                        <div class="h-12 w-12 rounded-full bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center mb-3 text-emerald-500">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                        </div>
                        <p class="text-sm font-bold text-gray-500 dark:text-gray-400">{{ __('all_caught_up') }}</p>
                    </li>
                @endforelse
            </ul>
        </div>
    </div>

    {{-- Tab: Sudah Dibaca --}}
    <div x-show="activeTab === 'sudah_dibaca'"
         x-transition:enter="transition ease-out duration-200" 
         x-transition:enter-start="opacity-0 translate-y-1" 
         x-transition:enter-end="opacity-100 translate-y-0"
         style="display: none;">
        
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <ul class="divide-y divide-gray-100 dark:divide-slate-700/50">
                @forelse($readNotifs as $notif)
                    @php
                        $days = 0;
                        if(isset($notif->data['due_date'])) {
                            $dueDate = \Carbon\Carbon::parse($notif->data['due_date'])->startOfDay();
                            $today = \Carbon\Carbon::today()->startOfDay();
                            $days = (int) abs($today->diffInDays($dueDate, false));
                        }
                    @endphp
                    <li class="p-4 flex gap-3 bg-white dark:bg-slate-800 opacity-60 hover:opacity-100 transition-opacity">
                        <div class="shrink-0 mt-0.5">
                            <div class="h-9 w-9 rounded-full bg-gray-100 dark:bg-slate-700 flex items-center justify-center ring-2 ring-white dark:ring-slate-800">
                                <svg class="w-4 h-4 text-gray-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0 space-y-1">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300 leading-snug">
                                {{ $notif->data['type'] === 'overdue' ? __('notif_overdue', ['name' => $notif->data['customer_name'] ?? '', 'days' => $days]) : __('notif_due_soon', ['name' => $notif->data['customer_name'] ?? '', 'days' => $days]) }}
                            </p>
                            <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-gray-400 dark:text-slate-500">
                                <span class="font-bold text-gray-500 dark:text-slate-400">Rp {{ number_format($notif->data['amount'] ?? 0, 0, ',', '.') }}</span>
                                <span class="text-[10px] text-gray-300 dark:text-slate-600">•</span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                    {{ $notif->read_at->translatedFormat('d M Y, H:i') }}
                                </span>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="py-12 flex flex-col items-center justify-center text-center">
                        <div class="h-12 w-12 rounded-full bg-gray-50 dark:bg-slate-700/50 flex items-center justify-center mb-3 text-gray-300 dark:text-slate-500">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                        </div>
                        <p class="text-sm font-medium text-gray-400 dark:text-slate-500">{{ __('no_notification_history') }}</p>
                    </li>
                @endforelse
            </ul>
        </div>
    </div>
    
    <div class="mt-4 pt-2">
        {{ $notifications->links() }}
    </div>

    {{-- Modal Konfirmasi Mark All --}}
    <div x-show="showConfirmModal" 
         class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-0 sm:p-4 bg-slate-900/60 backdrop-blur-sm" 
         x-cloak>
        <div @click.away="showConfirmModal = false" 
             x-show="showConfirmModal"
             x-transition:enter="transition ease-out duration-200" 
             x-transition:enter-start="opacity-0 translate-y-8 sm:scale-90" 
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-150" 
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave-end="opacity-0 translate-y-8 sm:scale-90"
             class="bg-white dark:bg-slate-800 rounded-t-3xl sm:rounded-3xl shadow-2xl w-full sm:max-w-sm p-6 text-center">
            
            <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-indigo-50 dark:bg-indigo-900/30 mb-4">
                <svg class="h-7 w-7 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>
            </div>
            
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('mark_all_title') }}</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ __('mark_all_confirm_text', ['count' => $unreadNotifs->count()]) }}</p>
            
            <div class="flex gap-3 mt-6">
                <button @click="showConfirmModal = false" class="flex-1 px-4 py-3 text-sm font-semibold text-gray-700 dark:text-slate-200 bg-gray-100 dark:bg-slate-700 rounded-xl hover:bg-gray-200 dark:hover:bg-slate-600 cursor-pointer">{{ __('cancel') }}</button>
                <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="flex-1 m-0">
                    @csrf
                    <button type="submit" class="w-full px-4 py-3 text-sm font-bold text-white bg-emerald-600 dark:bg-emerald-500 rounded-xl hover:bg-emerald-700 shadow-md shadow-emerald-600/20 cursor-pointer">{{ __('yes_mark_all') }}</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Sukses --}}
    <div x-show="showSuccessModal" 
         class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-0 sm:p-4 bg-slate-900/60 backdrop-blur-sm" 
         x-cloak>
        <div @click.away="showSuccessModal = false" 
             x-show="showSuccessModal"
             x-transition:enter="transition ease-out duration-200" 
             x-transition:enter-start="opacity-0 translate-y-8 sm:scale-90" 
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-150" 
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave-end="opacity-0 translate-y-8 sm:scale-90"
             class="bg-white dark:bg-slate-800 rounded-t-3xl sm:rounded-3xl shadow-2xl w-full sm:max-w-sm p-6 sm:p-8 text-center relative overflow-hidden border-t border-emerald-100 dark:border-slate-700 sm:border">
            
            <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-emerald-400 to-teal-500"></div>
            
            <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-emerald-100 dark:bg-emerald-900/30 mb-5 ring-4 ring-emerald-50 dark:ring-slate-800">
                <svg class="h-7 w-7 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
            </div>
            
            <h3 class="text-xl font-extrabold text-slate-900 dark:text-white">{{ __('success_title') }}</h3>
            <p class="text-sm text-slate-500 dark:text-gray-400 mt-2.5 font-medium leading-relaxed" x-text="successMessage"></p>
            
            <button @click="showSuccessModal = false" class="mt-6 w-full px-4 py-3 text-sm font-bold text-white bg-slate-900 dark:bg-slate-700 rounded-xl hover:bg-slate-800 dark:hover:bg-slate-600 cursor-pointer">{{ __('close') }}</button>
        </div>
    </div>

</div>
@endsection
