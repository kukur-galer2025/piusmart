<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// 1. Ambil waktu dari DB dengan aman
$notificationTime = '01:00';
try {
    if (Schema::hasTable('settings')) {
        // Pastikan key-nya sesuai dengan yang ada di controller kamu saat nyimpen data
        $notificationTime = Setting::where('key', 'daily_notification_time')->value('value') ?? '01:00';
    }
} catch (\Exception $e) {
    $notificationTime = '01:00';
}

// 2. Gunakan dailyAt() bawaan Laravel (Lebih akurat dari manual if H:i)
Schedule::command('receivables:check-due')
        ->timezone('Asia/Jakarta') // Kunci agar cocok dengan WIB
        ->dailyAt($notificationTime);