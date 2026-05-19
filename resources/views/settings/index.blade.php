@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-gray-900 dark:text-white transition-colors">{{ __('settings') }}</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 transition-colors">Konfigurasi preferensi sistem pengingat otomatis Piusmart Panel.</p>
    </div>

    @if(session('success') && session('success') != __('notif_marked_read'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-sm font-medium flex items-center gap-2.5 shadow-sm transition-colors">
            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <div class="space-y-1">
            <h2 class="text-base font-bold text-gray-900 dark:text-white transition-colors">Sistem Pengingat</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed transition-colors">Atur jadwal patroli harian otomatis. Mesin server akan bergerak menyisir seluruh piutang aktif yang belum lunas tepat pada jam yang Anda tentukan.</p>
        </div>

        <div class="md:col-span-2">
            <div class="bg-white dark:bg-slate-800 p-5 sm:p-6 md:p-8 rounded-2xl border border-gray-100 dark:border-slate-700 shadow-sm transition-colors duration-300">
                <form action="{{ route('settings.notification.update') }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PATCH')
                    
                    <div>
                        <label for="notification_time" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 transition-colors">
                            Waktu Eksekusi Harian
                        </label>
                        
                        <div class="relative max-w-xs">
                            <input type="time" name="notification_time" id="notification_time" 
                                   value="{{ old('notification_time', $notificationTime) }}" required
                                   class="w-full px-4 py-2.5 text-sm font-bold text-gray-900 dark:text-white bg-gray-50 dark:bg-slate-900 border @error('notification_time') border-rose-500 @else border-gray-200 dark:border-slate-600 @enderror rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white dark:focus:bg-slate-800 transition-all duration-300 [color-scheme:light] dark:[color-scheme:dark]">
                        </div>

                        @error('notification_time')
                            <p class="text-xs text-rose-600 dark:text-rose-400 mt-1.5 flex items-center gap-1 font-medium transition-colors">
                                <svg class="w-3.5 h-3.5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>
                                {{ $message }}
                            </p>
                        @enderror
                        
                        <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-2 font-medium transition-colors">
                            💡 Pengingat di dashboard dan ikon lonceng bagian atas website akan langsung diperbarui secara otomatis begitu memasuki waktu ini.
                        </p>
                    </div>

                    <div class="flex items-center justify-end pt-4 border-t border-gray-100 dark:border-slate-700 mt-2 transition-colors">
                        <button type="submit" class="w-full sm:w-auto px-6 py-2.5 text-sm font-bold text-white bg-slate-900 hover:bg-slate-800 dark:bg-emerald-600 dark:hover:bg-emerald-700 rounded-xl shadow-sm transition-colors cursor-pointer text-center">
                            Simpan Perubahan Waktu
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection