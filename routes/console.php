<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;

// 1. Command Bawaan Laravel
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// 2. Scheduler Dinamis yang mengecek ke Database setiap menit
Schedule::call(function () {
    // Ambil waktu dari DB setiap menit
    $notificationTime = '01:00';
    
    try {
        if (Schema::hasTable('settings')) {
            $notificationTime = Setting::where('key', 'daily_notification_time')->value('value') ?? '01:00';
        }
    } catch (\Exception $e) {
        $notificationTime = '01:00';
    }

    // Cek: Apakah jam sekarang SAMA dengan jam yang diset di DB?
    // Jika ya, jalankan perintah pengecekan piutang
    if (now()->format('H:i') === $notificationTime) {
        Artisan::call('receivables:check-due');
    }
})->everyMinute();