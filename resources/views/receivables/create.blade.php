@extends('layouts.app')

@section('content')
<div x-data="{ 
        openCustomerModal: false,
        searchCustomer: '',
        selectedCustomerId: '{{ old('customer_id') }}',
        selectedCustomerName: '@if(old('customer_id')) {{ addslashes($customers->firstWhere('id', old('customer_id'))->name ?? '') }} @endif',
        selectedCustomerPhone: '@if(old('customer_id')) {{ $customers->firstWhere('id', old('customer_id'))->phone ?? '-' }} @endif'
     }"
     class="max-w-2xl mx-auto space-y-6">
    
    <div>
        <a href="{{ route('receivables.index') }}" class="inline-flex items-center text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-400">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 mr-1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
            {{ __('back_to_receivables_data') }}
        </a>
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-gray-900 dark:text-white mt-2">{{ __('add_new_receivable') }}</h1>
    </div>

    <div class="bg-white dark:bg-slate-800 p-5 sm:p-6 md:p-8 rounded-2xl border border-gray-100 dark:border-slate-700 shadow-sm">
        <form action="{{ route('receivables.store') }}" method="POST" class="space-y-5">
            @csrf

            {{-- Customer --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">{{ __('customer_name') }}</label>
                <input type="hidden" name="customer_id" :value="selectedCustomerId" required>
                <div class="relative min-w-0">
                    <button type="button" @click="openCustomerModal = true; searchCustomer = ''"
                            class="w-full flex items-center justify-between pl-11 pr-3 py-2.5 text-sm bg-gray-50 dark:bg-slate-900 border @error('customer_id') border-rose-500 dark:border-rose-500 @else border-gray-200 dark:border-slate-600 @enderror rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white dark:focus:bg-slate-800 text-left cursor-pointer min-w-0">
                        <span x-text="selectedCustomerId ? selectedCustomerName + ' (📞 ' + selectedCustomerPhone + ')' : '{{ __('choose_customer') }}'"
                              :class="selectedCustomerId ? 'text-gray-900 dark:text-white font-medium' : 'text-gray-400 dark:text-gray-500'"
                              class="truncate min-w-0 flex-1">
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-gray-500 dark:text-gray-400 shrink-0 ml-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400 dark:text-slate-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                    </div>
                </div>
                <p class="text-[11px] text-gray-400 dark:text-slate-500 mt-1 font-medium pl-1">💡 {{ __('select_customer_helper') }}</p>
                @error('customer_id')
                    <p class="text-xs text-rose-600 dark:text-rose-400 mt-1.5 flex items-center gap-1 font-medium pl-1">
                        <svg class="w-3.5 h-3.5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Amount --}}
            <div>
                <label for="amount" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">{{ __('amount') }}</label>
                <div class="relative min-w-0">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-gray-400 dark:text-slate-500 shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                        </svg>
                        <span class="text-sm font-semibold text-gray-500 dark:text-slate-400 ml-1">Rp</span>
                    </div>
                    <input type="number" name="amount" id="amount" value="{{ old('amount') }}" required min="1" placeholder="0"
                           class="w-full pl-14 pr-3 py-2.5 text-sm font-medium text-gray-900 dark:text-white bg-gray-50 dark:bg-slate-900 border @error('amount') border-rose-500 dark:border-rose-500 @else border-gray-200 dark:border-slate-600 @enderror rounded-xl focus:outline-none focus:ring-2 @error('amount') focus:ring-rose-500 @else focus:ring-emerald-500 @enderror focus:bg-white dark:focus:bg-slate-800 min-w-0">
                </div>
                <p class="text-[11px] text-gray-400 dark:text-slate-500 mt-1 font-medium pl-1">💡 {{ __('amount_helper') }}</p>
                @error('amount')
                    <p class="text-xs text-rose-600 dark:text-rose-400 mt-1.5 flex items-center gap-1 font-medium pl-1">
                        <svg class="w-3.5 h-3.5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Dates --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="min-w-0">
                    <label for="transaction_date" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">{{ __('transaction_date') }}</label>
                    <div class="relative min-w-0">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400 dark:text-slate-500 shrink-0">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                            </svg>
                        </div>
                        <input type="date" name="transaction_date" id="transaction_date" value="{{ old('transaction_date', date('Y-m-d')) }}" required
                               class="w-full min-w-0 pl-10 pr-2 py-2.5 text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-slate-900 border @error('transaction_date') border-rose-500 dark:border-rose-500 @else border-gray-200 dark:border-slate-600 @enderror rounded-xl focus:outline-none focus:ring-2 @error('transaction_date') focus:ring-rose-500 @else focus:ring-emerald-500 @enderror focus:bg-white dark:focus:bg-slate-800 [color-scheme:light] dark:[color-scheme:dark]">
                    </div>
                    <p class="text-[11px] text-gray-400 dark:text-slate-500 mt-1 font-medium pl-1">💡 {{ __('transaction_date_helper') }}</p>
                    @error('transaction_date')
                        <p class="text-xs text-rose-600 dark:text-rose-400 mt-1.5 flex items-center gap-1 font-medium pl-1">
                            <svg class="w-3.5 h-3.5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="min-w-0">
                    <label for="due_date" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">{{ __('due_date') }}</label>
                    <div class="relative min-w-0">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400 dark:text-slate-500 shrink-0">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </div>
                        <input type="date" name="due_date" id="due_date" value="{{ old('due_date') }}" required
                               class="w-full min-w-0 pl-10 pr-2 py-2.5 text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-slate-900 border @error('due_date') border-rose-500 dark:border-rose-500 @else border-gray-200 dark:border-slate-600 @enderror rounded-xl focus:outline-none focus:ring-2 @error('due_date') focus:ring-rose-500 @else focus:ring-emerald-500 @enderror focus:bg-white dark:focus:bg-slate-800 [color-scheme:light] dark:[color-scheme:dark]">
                    </div>
                    <p class="text-[11px] text-gray-400 dark:text-slate-500 mt-1 font-medium pl-1">💡 {{ __('due_date_helper') }}</p>
                    @error('due_date')
                        <p class="text-xs text-rose-600 dark:text-rose-400 mt-1.5 flex items-center gap-1 font-medium pl-1">
                            <svg class="w-3.5 h-3.5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            {{-- Notes --}}
            <div>
                <label for="notes" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">{{ __('notes') }} {{ __('optional') }}</label>
                <div class="relative min-w-0">
                    <div class="absolute top-3 left-3 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400 dark:text-slate-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m6.75 12H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                    </div>
                    <textarea name="notes" id="notes" rows="3" placeholder="{{ __('notes_placeholder') }}"
                              class="w-full min-w-0 pl-10 pr-3 py-2.5 text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-slate-900 border @error('notes') border-rose-500 dark:border-rose-500 @else border-gray-200 dark:border-slate-600 @enderror rounded-xl focus:outline-none focus:ring-2 @error('notes') focus:ring-rose-500 @else focus:ring-emerald-500 @enderror focus:bg-white dark:focus:bg-slate-800 resize-none">{{ old('notes') }}</textarea>
                </div>
                @error('notes')
                    <p class="text-xs text-rose-600 dark:text-rose-400 mt-1.5 flex items-center gap-1 font-medium pl-1">
                        <svg class="w-3.5 h-3.5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3 pt-5 border-t border-gray-100 dark:border-slate-700 mt-2">
                <a href="{{ route('receivables.index') }}" class="w-full sm:w-auto px-5 py-2.5 text-sm text-center font-semibold text-gray-700 dark:text-slate-200 bg-gray-100 dark:bg-slate-700 rounded-xl hover:bg-gray-200 dark:hover:bg-slate-600">
                    {{ __('cancel') }}
                </a>
                <button type="submit" class="w-full sm:w-auto px-6 py-2.5 text-sm font-semibold text-white bg-emerald-600 dark:bg-emerald-500 rounded-xl hover:bg-emerald-700 dark:hover:bg-emerald-600 shadow-sm cursor-pointer">
                    {{ __('save_transaction') }}
                </button>
            </div>
        </form>
    </div>

    {{-- Customer Modal --}}
    <div x-show="openCustomerModal" 
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         x-cloak>
        
        <div @click.away="openCustomerModal = false" 
             class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl max-w-md w-full p-5 sm:p-6 flex flex-col max-h-[85vh] border border-gray-100 dark:border-slate-700"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-4 sm:translate-y-0" x-transition:enter-end="opacity-100 scale-100 translate-y-0">
            
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-slate-700">
                <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ __('search_customer') }}</h3>
                <button type="button" @click="openCustomerModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1 rounded-md cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="mt-4 relative min-w-0">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 dark:text-gray-500 text-sm">🔍</span>
                <input type="text" x-model="searchCustomer" placeholder="{{ __('type_customer_name') }}"
                       class="w-full min-w-0 pl-9 pr-3 py-2 text-sm bg-gray-50 dark:bg-slate-900 border border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white dark:focus:bg-slate-800">
            </div>

            <div class="mt-4 flex-1 overflow-y-auto divide-y divide-gray-50 dark:divide-slate-700/50 max-h-64 pr-1">
                @forelse($customers as $cust)
                    <div x-show="'{{ strtolower(addslashes($cust->name)) }}'.includes(searchCustomer.toLowerCase())"
                         @click="selectedCustomerId = '{{ $cust->id }}'; 
                                 selectedCustomerName = '{{ addslashes($cust->name) }}'; 
                                 selectedCustomerPhone = '{{ $cust->phone ?? '-' }}'; 
                                 openCustomerModal = false;"
                         class="flex items-center justify-between p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 cursor-pointer group">
                        <div class="min-w-0 pr-2">
                            <div class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate group-hover:text-emerald-600 dark:group-hover:text-emerald-400">{{ $cust->name }}</div>
                            <div class="text-xs text-gray-400 dark:text-slate-500 mt-0.5">📞 {{ $cust->phone ?? '-' }}</div>
                        </div>
                        <div class="shrink-0 pl-2">
                            <span x-show="selectedCustomerId == '{{ $cust->id }}'" class="text-emerald-600 dark:text-emerald-400 font-bold text-sm" x-cloak>✓ {{ __('selected') }}</span>
                            <span x-show="selectedCustomerId != '{{ $cust->id }}'" class="text-xs text-gray-400 dark:text-slate-400 bg-gray-100 dark:bg-slate-800 px-2 py-1 rounded-md group-hover:bg-emerald-50 dark:group-hover:bg-emerald-900/50 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 font-medium">{{ __('select') }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 text-sm text-gray-400 dark:text-slate-500 font-medium">{{ __('no_customer_data_yet') }}</div>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection