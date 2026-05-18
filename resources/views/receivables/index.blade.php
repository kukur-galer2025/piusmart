@extends('layouts.app')

@section('content')
<div x-data="{ 
        openModal: false, actionUrl: '', customerName: '', 
        openDeleteModal: false, deleteUrl: '',
        search: '{{ request('search') }}',
        status: '{{ request('status') }}',
        isSearching: false,
        
        fetchData() {
            this.isSearching = true;
            
            fetch(`{{ route('receivables.index') }}?search=${this.search}&status=${this.status}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.text())
            .then(html => {
                // Suntikkan data ke dalam wrapper
                document.getElementById('table-wrapper').innerHTML = html;
                this.isSearching = false;
                
                window.history.pushState({}, '', `?search=${this.search}&status=${this.status}`);
            });
        }
    }" 
    class="space-y-6">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">{{ __('receivables_data') }}</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola dan pantau seluruh transaksi piutang pelanggan.</p>
        </div>
        <div>
            <a href="{{ route('receivables.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 shadow-xs transition-colors cursor-pointer">
                ➕ {{ __('add_receivable') }}
            </a>
        </div>
    </div>

    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
        <div class="flex flex-col md:flex-row gap-3 relative">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">🔍</span>
                <input type="text" x-model="search" @input.debounce.500ms="fetchData()" placeholder="{{ __('search') }}"
                       class="w-full pl-9 pr-10 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all">
                
                <span x-show="isSearching" class="absolute inset-y-0 right-0 flex items-center pr-3" x-cloak>
                    <svg class="animate-spin h-4 w-4 text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </span>
            </div>

            <div class="w-full md:w-48">
                <select x-model="status" @change="fetchData()" 
                        class="w-full px-3 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all">
                    <option value="">-- Semua Status --</option>
                    <option value="unpaid">{{ __('unpaid') }}</option>
                    <option value="due_soon">{{ __('due_soon') }}</option>
                    <option value="overdue">{{ __('overdue') }}</option>
                    <option value="paid">{{ __('paid') }}</option>
                </select>
            </div>

            <button x-show="search !== '' || status !== ''" 
                    @click="search = ''; status = ''; fetchData()" 
                    class="px-4 py-2.5 text-sm text-center font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors cursor-pointer" x-cloak>
                Reset
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl flex items-center">
            <span class="mr-2">✅</span> {{ session('success') }}
        </div>
    @endif

    <div class="relative bg-white rounded-xl border border-gray-200 shadow-xs overflow-hidden">
        
        <div x-show="isSearching" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="absolute inset-0 z-10 bg-white/40 backdrop-blur-[2px] flex items-center justify-center pointer-events-none" x-cloak>
            
            <div class="bg-white px-5 py-2.5 rounded-full shadow-lg border border-emerald-100 flex items-center gap-3 transform -translate-y-4">
                <svg class="animate-spin h-5 w-5 text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span class="text-sm font-bold text-emerald-700 tracking-wide animate-pulse">Memuat Data...</span>
            </div>
        </div>

        <div id="table-wrapper" class="transition-all duration-300 ease-in-out origin-top" :class="isSearching ? 'scale-[0.99] opacity-40 blur-[1px]' : 'scale-100 opacity-100 blur-0'">
            @include('receivables.partials.table')
        </div>

    </div>

    <div x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto" x-cloak>
        <div @click.away="openModal = false" class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-6 space-y-4">
            <div class="text-center">
                <span class="text-3xl">💰</span>
                <h3 class="text-lg font-bold text-gray-900 mt-2">Konfirmasi Pembayaran</h3>
                <p class="text-sm text-gray-500 mt-1">
                    Tandai piutang <span class="font-bold text-gray-800" x-text="customerName"></span> sebagai <strong>LUNAS</strong>?
                </p>
            </div>
            <div class="flex gap-3">
                <button @click="openModal = false" class="flex-1 px-4 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors cursor-pointer">Batal</button>
                <form :action="actionUrl" method="POST" class="flex-1">
                    @csrf @method('PATCH')
                    <button type="submit" class="w-full px-4 py-2.5 text-sm font-semibold text-white bg-emerald-600 rounded-xl hover:bg-emerald-700 shadow-sm transition-colors cursor-pointer">Ya, Lunas!</button>
                </form>
            </div>
        </div>
    </div>

    <div x-show="openDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto" x-cloak>
        <div @click.away="openDeleteModal = false" class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-6 space-y-5">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-rose-100 mb-4">
                    <svg class="h-6 w-6 text-rose-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Hapus Data Piutang</h3>
                <p class="text-sm text-gray-500 mt-2">
                    Anda yakin ingin menghapus data piutang <span class="font-bold text-gray-800" x-text="customerName"></span>? Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>
            <div class="flex gap-3 mt-5">
                <button @click="openDeleteModal = false" class="flex-1 px-4 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors cursor-pointer">Batal</button>
                <form :action="deleteUrl" method="POST" class="flex-1">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2.5 text-sm font-semibold text-white bg-rose-600 rounded-xl hover:bg-rose-700 shadow-sm transition-colors cursor-pointer">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection