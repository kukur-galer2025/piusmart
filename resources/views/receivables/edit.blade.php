@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    
    <div>
        <a href="{{ route('receivables.index') }}" class="inline-flex items-center text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-400">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 mr-1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
            {{ __('back_to_receivables_data') }}
        </a>
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-gray-900 dark:text-white mt-2">{{ __('edit_receivable_data') }}</h1>
    </div>

    <div class="bg-white dark:bg-slate-800 p-5 sm:p-6 md:p-8 rounded-2xl border border-gray-100 dark:border-slate-700 shadow-sm">
        <form action="{{ route('receivables.update', $receivable->id) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">{{ __('customer_name') }}</label>
                
                <div class="flex items-center w-full px-4 py-2.5 text-sm bg-gray-100 dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-xl text-gray-500 dark:text-gray-400 cursor-not-allowed select-none">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 mr-2.5 text-gray-400 dark:text-slate-500 shrink-0">
                        <path fill-rule="evenodd" d="M12 1.5a5.25 5.25 0 0 0-5.25 5.25v3a3 3 0 0 0-3 3v6.75a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3v-6.75a3 3 0 0 0-3-3v-3c0-2.9-2.35-5.25-5.25-5.25Zm3.75 8.25v-3a3.75 3.75 0 1 0-7.5 0v3h7.5Z" clip-rule="evenodd" />
                    </svg>
                    <span class="truncate font-medium text-gray-600 dark:text-gray-300">{{ $receivable->customer->name }} (📞 {{ $receivable->customer->phone ?? '-' }})</span>
                </div>
                
                <p class="text-[11px] text-amber-600 dark:text-amber-500 mt-1.5 font-medium pl-1">⚠️ {{ __('customer_identity_warning') }}</p>
                <input type="hidden" name="customer_id" value="{{ $receivable->customer_id }}">
            </div>

            <div>
                <label for="amount" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">{{ __('amount') }}</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400 dark:text-slate-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                        </svg>
                        <span class="text-sm font-semibold text-gray-500 dark:text-slate-400 ml-1.5">Rp</span>
                    </div>
                    <input type="number" name="amount" id="amount" value="{{ old('amount', $receivable->amount) }}" required min="1"
                           class="w-full pl-[4.5rem] pr-4 py-2.5 text-sm font-medium text-gray-900 dark:text-white bg-gray-50 dark:bg-slate-900 border @error('amount') border-rose-500 dark:border-rose-500 @else border-gray-200 dark:border-slate-600 @enderror rounded-xl focus:outline-none focus:ring-2 @error('amount') focus:ring-rose-500 @else focus:ring-emerald-500 @enderror focus:bg-white dark:focus:bg-slate-800">
                </div>
                <p class="text-[11px] text-gray-400 dark:text-slate-500 mt-1 font-medium pl-1">💡 {{ __('amount_helper') }}</p>
                @error('amount') 
                    <p class="text-xs text-rose-600 dark:text-rose-400 mt-1.5 flex items-center gap-1 font-medium pl-1">
                        <svg class="w-3.5 h-3.5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>
                        {{ $message }}
                    </p> 
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="transaction_date" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">{{ __('transaction_date') }}</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400 dark:text-slate-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                            </svg>
                        </div>
                        <input type="date" name="transaction_date" id="transaction_date" value="{{ old('transaction_date', $receivable->transaction_date->format('Y-m-d')) }}" required
                               class="w-full pl-11 pr-4 py-2.5 text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-slate-900 border @error('transaction_date') border-rose-500 dark:border-rose-500 @else border-gray-200 dark:border-slate-600 @enderror rounded-xl focus:outline-none focus:ring-2 @error('transaction_date') focus:ring-rose-500 @else focus:ring-emerald-500 @enderror focus:bg-white dark:focus:bg-slate-800 [color-scheme:light] dark:[color-scheme:dark]">
                    </div>
                    <p class="text-[11px] text-gray-400 dark:text-slate-500 mt-1 font-medium pl-1">💡 {{ __('transaction_date_helper') }}</p>
                    @error('transaction_date') 
                        <p class="text-xs text-rose-600 dark:text-rose-400 mt-1.5 flex items-center gap-1 font-medium pl-1">
                            <svg class="w-3.5 h-3.5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>
                            {{ $message }}
                        </p> 
                    @enderror
                </div>

                <div>
                    <label for="due_date" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">{{ __('due_date') }}</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400 dark:text-slate-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </div>
                        <input type="date" name="due_date" id="due_date" value="{{ old('due_date', $receivable->due_date->format('Y-m-d')) }}" required
                               class="w-full pl-11 pr-4 py-2.5 text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-slate-900 border @error('due_date') border-rose-500 dark:border-rose-500 @else border-gray-200 dark:border-slate-600 @enderror rounded-xl focus:outline-none focus:ring-2 @error('due_date') focus:ring-rose-500 @else focus:ring-emerald-500 @enderror focus:bg-white dark:focus:bg-slate-800 [color-scheme:light] dark:[color-scheme:dark]">
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

            <div>
                <label for="notes" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">{{ __('notes') }} {{ __('optional') }}</label>
                <div class="relative">
                    <div class="absolute top-3 left-4 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400 dark:text-slate-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m6.75 12H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                    </div>
                    <textarea name="notes" id="notes" rows="3" placeholder="{{ __('notes_placeholder') }}"
                              class="w-full pl-11 pr-4 py-2.5 text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-slate-900 border @error('notes') border-rose-500 dark:border-rose-500 @else border-gray-200 dark:border-slate-600 @enderror rounded-xl focus:outline-none focus:ring-2 @error('notes') focus:ring-rose-500 @else focus:ring-emerald-500 @enderror focus:bg-white dark:focus:bg-slate-800">{{ old('notes', $receivable->notes) }}</textarea>
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
                    {{ __('save_changes') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection