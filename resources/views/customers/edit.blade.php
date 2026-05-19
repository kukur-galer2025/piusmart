@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    
    <div>
        <a href="{{ route('customers.index') }}" class="inline-flex items-center text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 mr-1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
            Kembali ke Data Pelanggan
        </a>
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-gray-900 dark:text-white mt-2 transition-colors">Edit Data Pelanggan</h1>
    </div>

    <div class="bg-white dark:bg-slate-800 p-5 sm:p-6 md:p-8 rounded-2xl border border-gray-100 dark:border-slate-700 shadow-sm transition-colors duration-300">
        <form action="{{ route('customers.update', $customer->id) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5 transition-colors">Nama Lengkap</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400 dark:text-slate-500"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                    </div>
                    <input type="text" name="name" id="name" value="{{ old('name', $customer->name) }}" required
                           class="w-full pl-11 pr-4 py-2.5 text-sm font-medium text-gray-900 dark:text-white bg-gray-50 dark:bg-slate-900 border @error('name') border-rose-500 dark:border-rose-500 @else border-gray-200 dark:border-slate-600 @enderror rounded-xl focus:outline-none focus:ring-2 @error('name') focus:ring-rose-500 @else focus:ring-emerald-500 @enderror focus:bg-white dark:focus:bg-slate-800 transition-colors">
                </div>
                <p class="text-[11px] text-gray-400 dark:text-slate-500 mt-1 font-medium pl-1 transition-colors">💡 Minimal 3 karakter. Hanya boleh huruf, spasi, titik, koma, atau tanda hubung.</p>
                @error('name') 
                    <p class="text-xs text-rose-600 dark:text-rose-400 mt-1.5 flex items-center gap-1 font-medium pl-1 transition-colors">
                        <svg class="w-3.5 h-3.5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>
                        {{ $message }}
                    </p> 
                @enderror
            </div>

            <div>
                <label for="phone" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5 transition-colors">Nomor HP / WhatsApp (Opsional)</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400 dark:text-slate-500"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" /></svg>
                    </div>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $customer->phone) }}"
                           class="w-full pl-11 pr-4 py-2.5 text-sm font-medium text-gray-900 dark:text-white bg-gray-50 dark:bg-slate-900 border @error('phone') border-rose-500 dark:border-rose-500 @else border-gray-200 dark:border-slate-600 @enderror rounded-xl focus:outline-none focus:ring-2 @error('phone') focus:ring-rose-500 @else focus:ring-emerald-500 @enderror focus:bg-white dark:focus:bg-slate-800 transition-colors">
                </div>
                <p class="text-[11px] text-gray-400 dark:text-slate-500 mt-1 font-medium pl-1 transition-colors">💡 Format valid: 08xx, 62xx, +62xx, atau (+62)xx. Boleh menggunakan spasi atau strip (-).</p>
                @error('phone') 
                    <p class="text-xs text-rose-600 dark:text-rose-400 mt-1.5 flex items-center gap-1 font-medium pl-1 transition-colors">
                        <svg class="w-3.5 h-3.5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>
                        {{ $message }}
                    </p> 
                @enderror
            </div>

            <div>
                <label for="address" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5 transition-colors">Alamat (Opsional)</label>
                <div class="relative">
                    <div class="absolute top-3 left-4 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400 dark:text-slate-500"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>
                    </div>
                    <textarea name="address" id="address" rows="3" placeholder="Masukkan alamat lengkap..."
                              class="w-full pl-11 pr-4 py-2.5 text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-slate-900 border @error('address') border-rose-500 dark:border-rose-500 @else border-gray-200 dark:border-slate-600 @enderror rounded-xl focus:outline-none focus:ring-2 @error('address') focus:ring-rose-500 @else focus:ring-emerald-500 @enderror focus:bg-white dark:focus:bg-slate-800 transition-colors">{{ old('address', $customer->address) }}</textarea>
                </div>
                <p class="text-[11px] text-gray-400 dark:text-slate-500 mt-1 font-medium pl-1 transition-colors">💡 Jika diisi, masukkan alamat lengkap pelanggan minimal 5 karakter.</p>
                @error('address') 
                    <p class="text-xs text-rose-600 dark:text-rose-400 mt-1.5 flex items-center gap-1 font-medium pl-1 transition-colors">
                        <svg class="w-3.5 h-3.5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>
                        {{ $message }}
                    </p> 
                @enderror
            </div>

            <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3 pt-5 border-t border-gray-100 dark:border-slate-700 mt-2 transition-colors">
                <a href="{{ route('customers.index') }}" class="w-full sm:w-auto px-5 py-2.5 text-sm text-center font-semibold text-gray-700 dark:text-slate-200 bg-gray-100 dark:bg-slate-700 rounded-xl hover:bg-gray-200 dark:hover:bg-slate-600 transition-colors">
                    Batal
                </a>
                <button type="submit" class="w-full sm:w-auto px-6 py-2.5 text-sm font-semibold text-white bg-emerald-600 dark:bg-emerald-500 rounded-xl hover:bg-emerald-700 dark:hover:bg-emerald-600 shadow-sm transition-colors cursor-pointer">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection