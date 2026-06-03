@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="space-y-6 max-w-5xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-gray-900 dark:text-white">{{ __('receivable_details') }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('receivable_details_subtitle') }}</p>
        </div>
        <a href="{{ route('receivables.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-200 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 shadow-sm transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 mr-2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" /></svg>
            {{ __('back_to_receivables_data') }}
        </a>
    </div>

    <!-- Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-gray-200 dark:border-slate-700 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 text-gray-100 dark:text-slate-700/50 group-hover:scale-110 transition-transform duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-32 h-32"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div class="relative z-10">
                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('total_debt') }}</h3>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($receivable->amount, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ $receivable->customer->name }} ({{ $receivable->item_name }})</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-gray-200 dark:border-slate-700 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 text-emerald-50 dark:text-emerald-900/20 group-hover:scale-110 transition-transform duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-32 h-32"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div class="relative z-10">
                <h3 class="text-sm font-semibold text-emerald-600 dark:text-emerald-500 uppercase tracking-wider mb-1">{{ __('total_paid') }}</h3>
                <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($receivable->payments->sum('amount'), 0, ',', '.') }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ $receivable->payments->count() }} {{ __('stat_transactions') }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-gray-200 dark:border-slate-700 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 text-rose-50 dark:text-rose-900/20 group-hover:scale-110 transition-transform duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-32 h-32"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            </div>
            <div class="relative z-10">
                <h3 class="text-sm font-semibold text-rose-600 dark:text-rose-500 uppercase tracking-wider mb-1">{{ __('remaining_balance') }}</h3>
                <p class="text-2xl font-bold text-rose-600 dark:text-rose-400">Rp {{ number_format($receivable->remaining_balance, 0, ',', '.') }}</p>
                <div class="mt-2 flex items-center gap-2">
                    @if($receivable->is_paid)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">{{ __('paid') }}</span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-400">{{ __('unpaid') }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Tabel Histori Pembayaran -->
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/50">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-indigo-500"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                        {{ __('payment_history') }}
                    </h3>
                </div>
                <div class="p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-gray-50 dark:bg-slate-700/50 border-b border-gray-200 dark:border-slate-700">
                                <tr>
                                    <th class="px-6 py-4">{{ __('payment_date') }}</th>
                                    <th class="px-6 py-4">{{ __('payment_amount') }}</th>
                                    <th class="px-6 py-4">{{ __('notes') }}</th>
                                    <th class="px-6 py-4 text-center">{{ __('action') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-slate-700/50">
                                @forelse($receivable->payments as $payment)
                                    <tr class="hover:bg-gray-50/70 dark:hover:bg-slate-800/50">
                                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                            {{ $payment->payment_date->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 font-bold text-emerald-600 dark:text-emerald-400 whitespace-nowrap">
                                            Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                            {{ $payment->notes ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-center whitespace-nowrap">
                                            <button type="button" 
                                                    onclick="confirmDeletePayment({{ $payment->id }})"
                                                    class="p-1.5 text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-900/30 rounded-lg hover:bg-rose-600 dark:hover:bg-rose-600 hover:text-white border border-rose-200 dark:border-rose-800/50 cursor-pointer">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                                            </button>
                                            <form id="form-delete-payment-{{ $payment->id }}" action="{{ route('receivable-payments.destroy', $payment->id) }}" method="POST" class="hidden">
                                                @csrf @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-10 text-center text-gray-400 dark:text-slate-500 font-medium">
                                            {{ __('no_payment_history') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Tambah Pembayaran -->
        <div class="lg:col-span-1 space-y-4">
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/50">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-emerald-500"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                        {{ __('add_payment') }}
                    </h3>
                </div>
                <div class="p-6">
                    @if($receivable->remaining_balance <= 0)
                        <div class="bg-emerald-50 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-400 p-4 rounded-xl border border-emerald-200 dark:border-emerald-800/50 text-sm flex gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 flex-shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                            <p>{{ __('payment_completed_msg') }}</p>
                        </div>
                    @else
                        <form action="{{ route('receivable-payments.store', $receivable->id) }}" method="POST" class="space-y-5">
                            @csrf
                            
                            <div>
                                <label for="payment_date" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">{{ __('payment_date') }}</label>
                                <input type="date" id="payment_date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required
                                       class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-900 border border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                @error('payment_date') <p class="text-xs text-rose-500 mt-1.5">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="amount" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">{{ __('payment_amount') }}</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-500 dark:text-gray-400 font-medium">Rp</span>
                                    <input type="number" id="amount" name="amount" value="{{ old('amount', $receivable->remaining_balance) }}" required min="1"
                                           class="w-full pl-11 pr-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-900 border border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <p class="text-xs text-gray-500 mt-1.5">{{ __('amount_helper') }}</p>
                                @error('amount') <p class="text-xs text-rose-500 mt-1.5">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="notes" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">{{ __('notes') }} <span class="text-gray-400 font-normal">{{ __('optional') }}</span></label>
                                <textarea id="notes" name="notes" rows="2" placeholder="{{ __('notes_placeholder') }}"
                                          class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-900 border border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none">{{ old('notes') }}</textarea>
                                @error('notes') <p class="text-xs text-rose-500 mt-1.5">{{ $message }}</p> @enderror
                            </div>

                            <button type="submit" class="w-full inline-flex items-center justify-center px-5 py-3 text-sm font-bold text-white bg-indigo-600 dark:bg-indigo-500 rounded-xl hover:bg-indigo-700 dark:hover:bg-indigo-600 shadow-md shadow-indigo-200 dark:shadow-none transition-all cursor-pointer">
                                💾 {{ __('save_payment') }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: '{{ __('success_title') }}',
            text: '{{ session('success') }}',
            showConfirmButton: false,
            timer: 2500,
            customClass: { popup: 'rounded-2xl dark:bg-slate-800 dark:text-white' }
        });
    @endif

    function confirmDeletePayment(id) {
        Swal.fire({
            title: '{{ __('confirm_delete_payment_title') }}',
            text: '{{ __('confirm_delete_payment_text') }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#9ca3af',
            confirmButtonText: '{{ __('yes_delete') }}',
            cancelButtonText: '{{ __('cancel') }}',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-3xl dark:bg-slate-800 dark:text-white',
                confirmButton: 'rounded-xl px-5 py-2 font-bold shadow-md',
                cancelButton: 'rounded-xl px-5 py-2 font-semibold'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-delete-payment-' + id).submit();
            }
        });
    }
</script>
@endsection
