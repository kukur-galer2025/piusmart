@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    
    <!-- Breadcrumb / Back Button -->
    <div>
        <a href="{{ route('receivables.index') }}" class="text-sm font-medium text-gray-500 hover:text-emerald-600 transition-colors">
            ⬅️ Kembali ke Data Piutang
        </a>
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 mt-2">{{ __('add_receivable') }}</h1>
    </div>

    <!-- Card Form -->
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-xs">
        <form action="{{ route('receivables.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Pilih Pelanggan -->
            <div>
                <label for="customer_id" class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('customer_name') }}</label>
                <select name="customer_id" id="customer_id" required
                        class="w-full px-3 py-2 text-sm bg-gray-50 border @error('customer_id') border-rose-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all">
                    <option value="">-- Pilih Pelanggan --</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }} (📞 {{ $customer->phone ?? '-' }})
                        </option>
                    @endforeach
                </select>
                @error('customer_id')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Jumlah Piutang -->
            <div>
                <label for="amount" class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('amount') }} (Rp)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-sm font-semibold text-gray-400">Rp</span>
                    <input type="number" name="amount" id="amount" value="{{ old('amount') }}" required min="1" placeholder="0"
                           class="w-full pl-10 pr-4 py-2 text-sm bg-gray-50 border @error('amount') border-rose-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all">
                </div>
                @error('amount')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Grid Tanggal -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Tanggal Transaksi -->
                <div>
                    <label for="transaction_date" class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('transaction_date') }}</label>
                    <input type="date" name="transaction_date" id="transaction_date" value="{{ old('transaction_date', date('Y-m-dis')) }}" required
                           class="w-full px-3 py-2 text-sm bg-gray-50 border @error('transaction_date') border-rose-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all">
                    @error('transaction_date')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tanggal Jatuh Tempo -->
                <div>
                    <label for="due_date" class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('due_date') }}</label>
                    <input type="date" name="due_date" id="due_date" value="{{ old('due_date') }}" required
                           class="w-full px-3 py-2 text-sm bg-gray-50 border @error('due_date') border-rose-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all">
                    @error('due_date')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Catatan Tambahan -->
            <div>
                <label for="notes" class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('notes') }} (Opsional)</label>
                <textarea name="notes" id="notes" rows="3" placeholder="Tambahkan keterangan transaksi jika ada..."
                          class="w-full px-3 py-2 text-sm bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                <a href="{{ route('receivables.index') }}" class="px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2 text-sm font-semibold text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 shadow-xs transition-colors cursor-pointer">
                    Simpan Transaksi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection