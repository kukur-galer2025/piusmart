@extends('layouts.app')

@section('content')
<!-- Wrapper Alpine.js untuk menampung state modal konfirmasi -->
<div x-data="{ openModal: false, actionUrl: '', customerName: '' }" class="space-y-6">
    
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">{{ __('receivables_data') }}</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola dan pantau seluruh transaksi piutang pelanggan.</p>
        </div>
        <!-- Tombol Tambah Piutang Baru -->
        <div>
            <a href="{{ route('receivables.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 shadow-xs transition-colors cursor-pointer">
                ➕ {{ __('add_receivable') }}
            </a>
        </div>
    </div>

    <!-- Filter & Search Bar Panel -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
        <form action="{{ route('receivables.index') }}" method="GET" class="flex flex-col md:flex-row gap-3">
            <!-- Input Pencarian -->
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">🔍</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('search') }}"
                       class="w-full pl-9 pr-4 py-2 text-sm bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all">
            </div>

            <!-- Dropdown Filter Status -->
            <div class="w-full md:w-48">
                <select name="status" onchange="this.form.submit()" 
                        class="w-full px-3 py-2 text-sm bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all">
                    <option value="">-- Semua Status --</option>
                    <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>{{ __('unpaid') }}</option>
                    <option value="due_soon" {{ request('status') === 'due_soon' ? 'selected' : '' }}>{{ __('due_soon') }}</option>
                    <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>{{ __('overdue') }}</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>{{ __('paid') }}</option>
                </select>
            </div>

            <!-- Tombol Reset jika ada filter aktif -->
            @if(request()->has('search') || request()->has('status'))
                <a href="{{ route('receivables.index') }}" class="px-4 py-2 text-sm text-center font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Alert Sukses Toast Bawaan Laravel -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl flex items-center">
            <span class="mr-2">✅</span> {{ session('success') }}
        </div>
    @endif

    <!-- Tabel Utama Data Piutang -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200 tracking-wider font-semibold">
                    <tr>
                        <th class="px-6 py-4">{{ __('customer_name') }}</th>
                        <th class="px-6 py-4">{{ __('amount') }}</th>
                        <th class="px-6 py-4">{{ __('transaction_date') }}</th>
                        <th class="px-6 py-4">{{ __('due_date') }}</th>
                        <th class="px-6 py-4 text-center">{{ __('status') }}</th>
                        <th class="px-6 py-4 text-center">{{ __('action') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($receivables as $item)
                        @php
                            $today = \Carbon\Carbon::today();
                            $dueDate = \Carbon\Carbon::parse($item->due_date)->startOfDay();
                            
                            if ($item->is_paid) {
                                $badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                $statusLabel = __('paid');
                            } elseif ($today->gt($dueDate)) {
                                $badgeClass = 'bg-rose-50 text-rose-700 border-rose-200';
                                $statusLabel = __('overdue');
                            } elseif ($today->diffInDays($dueDate, false) <= 3 && $today->diffInDays($dueDate, false) >= 0) {
                                $badgeClass = 'bg-amber-50 text-amber-700 border-amber-200';
                                $statusLabel = __('due_soon');
                            } else {
                                $badgeClass = 'bg-blue-50 text-blue-700 border-blue-200';
                                $statusLabel = __('unpaid');
                            }
                        @endphp
                        <tr class="hover:bg-gray-50/70 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-900">{{ $item->customer->name }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">📞 {{ $item->customer->phone ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 font-bold text-gray-900">
                                Rp {{ number_format($item->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $item->transaction_date->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $item->due_date->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full border {{ $badgeClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if(!$item->is_paid)
                                    <button @click="openModal = true; actionUrl = '{{ route('receivables.mark-as-paid', $item->id) }}'; customerName = '{{ $item->customer->name }}'" 
                                            class="px-3 py-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 rounded-lg hover:bg-emerald-600 hover:text-white border border-emerald-200 transition-all cursor-pointer">
                                        ✓ {{ __('paid') }}
                                    </button>
                                @else
                                    <span class="text-xs text-gray-400 font-medium">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-400 font-medium">
                                Tidak ada data piutang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($receivables->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                {{ $receivables->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL KOMPONEN (Alpine.js) -->
    <div x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 overflow-y-auto" x-cloak>
        <div @click.away="openModal = false" class="bg-white rounded-xl shadow-xl max-w-sm w-full p-6 space-y-4 transform transition-all border border-gray-100">
            <div class="text-center">
                <span class="text-3xl">💰</span>
                <h3 class="text-lg font-bold text-gray-900 mt-2">Konfirmasi Pembayaran</h3>
                <p class="text-sm text-gray-500 mt-1">
                    Apakah Anda yakin ingin menandai piutang atas nama <span class="font-bold text-gray-800" x-text="customerName"></span> sebagai <strong>LUNAS</strong>?
                </p>
            </div>
            <div class="flex gap-3">
                <button @click="openModal = false" class="flex-1 px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors cursor-pointer">
                    Batal
                </button>
                <form :action="actionUrl" method="POST" class="flex-1">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="w-full px-4 py-2 text-sm font-semibold text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-colors cursor-pointer">
                        Ya, Lunas!
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection