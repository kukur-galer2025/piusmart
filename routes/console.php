<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;

// 1. Command Bawaan Laravel (Tetap Dipertahankan)
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// 2. Otomatisasi Penjadwalan Notifikasi Piutang Secara Dinamis
// Menggunakan fallback '01:00' jika Admin belum mengatur waktu di database
$notificationTime = '01:00'; 

try {
    // Proteksi agar tidak crash saat menjalankan 'php artisan migrate' pertama kali
    if (Schema::hasTable('settings')) {
        $notificationTime = Setting::where('key', 'notification_time')->value('value') ?? '01:00';
    }
} catch (\Exception $e) {
    $notificationTime = '01:00'; 
}

// Laravel Scheduler mengeksekusi perintah patroli piutang sesuai jam pilihan Admin
Schedule::command('receivables:check-due')->dailyAt($notificationTime);