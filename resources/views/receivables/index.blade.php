@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* Pasukan anti-scrollbar & Pelicin Scrolling untuk HP */
    .no-scrollbar::-webkit-scrollbar { 
        display: none !important; 
        width: 0 !important; 
        height: 0 !important; 
        -webkit-appearance: none !important; 
    }
    .no-scrollbar { 
        -ms-overflow-style: none !important; 
        scrollbar-width: none !important; 
        -webkit-overflow-scrolling: touch !important; 
    }
</style>

<div x-data="{ 
        search: '{{ request('search') }}',
        status: '{{ request('status') }}',
        month: '{{ request('month') }}',
        year: '{{ request('year') }}',
        isSearching: false,
        
        fetchData() {
            this.isSearching = true;
            const url = `{{ route('receivables.index') }}?search=${this.search}&status=${this.status}&month=${this.month}&year=${this.year}`;
            
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(response => response.text())
            .then(html => {
                setTimeout(() => {
                    document.getElementById('table-wrapper').innerHTML = html;
                    window.history.pushState({}, '', url);
                    this.isSearching = false;
                }, 150); 
            });
        }
    }" 
    class="space-y-6">
    
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-gray-900 dark:text-white transition-colors duration-300">{{ __('receivables_data') }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 transition-colors duration-300">Kelola dan pantau seluruh transaksi piutang pelanggan.</p>
        </div>
        
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full lg:w-auto">
            <a :href="'{{ route('receivables.export.pdf') }}?search=' + search + '&status=' + status + '&month=' + month + '&year=' + year" 
               title="Unduh berkas PDF"
               class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-200 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 shadow-sm cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" fill="currentColor" class="w-4 h-4 mr-2 text-rose-600 dark:text-rose-400">
                    <path d="M128 64C92.7 64 64 92.7 64 128L64 512C64 547.3 92.7 576 128 576L208 576L208 464C208 428.7 236.7 400 272 400L448 400L448 234.5C448 217.5 441.3 201.2 429.3 189.2L322.7 82.7C310.7 70.7 294.5 64 277.5 64L128 64zM389.5 240L296 240C282.7 240 272 229.3 272 216L272 122.5L389.5 240zM272 444C261 444 252 453 252 464L252 592C252 603 261 612 272 612C283 612 292 603 292 592L292 564L304 564C337.1 564 364 537.1 364 504C364 470.9 337.1 444 304 444L272 444zM304 524L292 524L292 484L304 484C315 484 324 493 324 504C324 515 315 524 304 524zM400 444C389 444 380 453 380 464L380 592C380 603 389 612 400 612L432 612C460.7 612 484 588.7 484 560L484 496C484 467.3 460.7 444 432 444L400 444zM420 572L420 484L432 484C438.6 484 444 489.4 444 496L444 560C444 566.6 438.6 572 432 572L420 572zM508 464L508 592C508 603 517 612 528 612C539 612 548 603 548 592L548 548L576 548C587 548 596 539 596 528C596 517 587 508 576 508L548 508L548 484L576 484C587 484 596 475 596 464C596 453 587 444 576 444L528 444C517 444 508 453 508 464z"/>
                </svg>
                Cetak PDF
            </a>

            <a :href="'{{ route('receivables.export.excel') }}?search=' + search + '&status=' + status + '&month=' + month + '&year=' + year" 
               title="Unduh berkas Excel"
               class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-200 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 shadow-sm cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 mr-2 text-emerald-600 dark:text-emerald-400"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m6.75 12H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                Export Excel
            </a>

            <a href="{{ route('receivables.create') }}" 
               class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-white bg-emerald-600 dark:bg-emerald-500 rounded-lg hover:bg-emerald-700 dark:hover:bg-emerald-600 shadow-sm cursor-pointer">
                ➕ {{ __('add_receivable') }}
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm transition-colors duration-300">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 relative">
            <div class="relative w-full">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 dark:text-gray-500">🔍</span>
                <input type="text" x-model="search" @input.debounce.500ms="fetchData()" placeholder="{{ __('search') }}"
                       class="w-full pl-9 pr-10 py-2.5 text-sm bg-gray-50 dark:bg-slate-900 border border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white dark:focus:bg-slate-800">
                <span x-show="isSearching" class="absolute inset-y-0 right-0 flex items-center pr-3" x-cloak>
                    <svg class="animate-spin h-4 w-4 text-emerald-500 dark:text-emerald-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </span>
            </div>

            <div class="w-full">
                <select x-model="status" @change="fetchData()" 
                        class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-slate-900 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-200 font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white dark:focus:bg-slate-800">
                    <option value="">-- Semua Status --</option>
                    <option value="unpaid">{{ __('unpaid') }}</option>
                    <option value="due_soon">{{ __('due_soon') }}</option>
                    <option value="overdue">{{ __('overdue') }}</option>
                    <option value="paid">{{ __('paid') }}</option>
                </select>
            </div>

            <div class="w-full">
                <select x-model="month" @change="fetchData()" 
                        class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-slate-900 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-200 font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white dark:focus:bg-slate-800">
                    <option value="">-- Semua Bulan --</option>
                    @foreach(range(1, 12) as $m)
                        <option value="{{ sprintf('%02d', $m) }}">
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="w-full flex gap-2">
                <select x-model="year" @change="fetchData()" 
                        class="w-full flex-1 px-3 py-2.5 text-sm bg-gray-50 dark:bg-slate-900 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-200 font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white dark:focus:bg-slate-800">
                    <option value="">-- Semua Tahun --</option>
                    @for($y = \Carbon\Carbon::now()->year + 1; $y >= 2024; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>

                <button x-show="search !== '' || status !== '' || month !== '' || year !== ''" 
                        @click="search = ''; status = ''; month = ''; year = ''; fetchData()" 
                        class="px-3 py-2.5 text-sm font-semibold text-gray-600 dark:text-slate-300 bg-gray-100 dark:bg-slate-700 rounded-lg hover:bg-gray-200 dark:hover:bg-slate-600 cursor-pointer" x-cloak>
                    ✕
                </button>
            </div>
        </div>
    </div>

    <div class="relative bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden transition-colors duration-300">
        
        <div x-show="isSearching" 
             x-transition.opacity.duration.200ms
             class="absolute inset-0 z-10 bg-white/60 dark:bg-slate-900/60 flex items-center justify-center pointer-events-none" x-cloak>
            <div class="bg-white dark:bg-slate-800 px-5 py-2.5 rounded-full shadow-md border border-emerald-100 dark:border-slate-600 flex items-center gap-3 transform -translate-y-4">
                <svg class="animate-spin h-5 w-5 text-emerald-500 dark:text-emerald-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span class="text-sm font-bold text-emerald-700 dark:text-emerald-400 tracking-wide animate-pulse">Memuat Data...</span>
            </div>
        </div>

        <div id="table-wrapper" :class="isSearching ? 'opacity-40' : 'opacity-100'" class="transition-opacity duration-200">
            @include('receivables.partials.table')
        </div>

    </div>
</div>

<script>
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            showConfirmButton: false,
            timer: 2500,
            customClass: { popup: 'rounded-2xl dark:bg-slate-800 dark:text-white' }
        });
    @endif

    function confirmPaid(id, customerName, amount) {
        Swal.fire({
            title: 'Konfirmasi Pelunasan',
            html: `Tandai piutang <strong>${customerName}</strong> sebesar <span class="text-emerald-600 dark:text-emerald-400 font-extrabold">${amount}</span> sebagai <span class="text-emerald-600 dark:text-emerald-400 font-bold">LUNAS</span>?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#059669',
            cancelButtonColor: '#9ca3af',
            confirmButtonText: 'Ya, Lunas!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-3xl dark:bg-slate-800 dark:text-white transition-colors duration-300',
                confirmButton: 'rounded-xl px-5 py-2 font-bold shadow-md',
                cancelButton: 'rounded-xl px-5 py-2 font-semibold'
            }
        }).then((result) => {
            if (result.isConfirmed) { document.getElementById('form-paid-' + id).submit(); }
        });
    }

    function confirmUnpaid(id, customerName, amount) {
        Swal.fire({
            title: 'Batalkan Pelunasan?',
            html: `Status piutang <strong>${customerName}</strong> sebesar <span class="text-rose-600 dark:text-rose-400 font-extrabold">${amount}</span> akan dikembalikan menjadi <span class="text-rose-600 dark:text-rose-400 font-bold">BELUM LUNAS</span>.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#9ca3af',
            confirmButtonText: 'Ya, Batalkan',
            cancelButtonText: 'Tutup',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-3xl dark:bg-slate-800 dark:text-white transition-colors duration-300',
                confirmButton: 'rounded-xl px-5 py-2 font-bold shadow-md',
                cancelButton: 'rounded-xl px-5 py-2 font-semibold'
            }
        }).then((result) => {
            if (result.isConfirmed) { document.getElementById('form-unpaid-' + id).submit(); }
        });
    }

    function confirmDelete(id, customerName) {
        Swal.fire({
            title: 'Hapus Data Piutang',
            html: `Anda yakin ingin menghapus data <strong>${customerName}</strong> secara permanen?<br><span class="text-sm text-rose-500 dark:text-rose-400 mt-1 block">Tindakan ini tidak dapat dibatalkan.</span>`,
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#9ca3af',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-3xl dark:bg-slate-800 dark:text-white transition-colors duration-300',
                confirmButton: 'rounded-xl px-5 py-2 font-bold shadow-md',
                cancelButton: 'rounded-xl px-5 py-2 font-semibold'
            }
        }).then((result) => {
            if (result.isConfirmed) { document.getElementById('form-delete-' + id).submit(); }
        });
    }
</script>
@endsection