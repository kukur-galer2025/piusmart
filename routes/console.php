<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// 1. Ambil waktu dari DB
$notificationTime = '01:00';
try {
    if (Schema::hasTable('settings')) {
        // PERBAIKAN: Ubah menjadi 'notification_time' sesuai dengan gambar database kamu
        $notificationTime = Setting::where('key', 'notification_time')->value('value') ?? '01:00';
    }
} catch (\Exception $e) {
    $notificationTime = '01:00';
}

// 2. Eksekusi setiap hari pada jam tersebut
Schedule::command('receivables:check-due')
        ->timezone('Asia/Jakarta')
        ->dailyAt($notificationTime);