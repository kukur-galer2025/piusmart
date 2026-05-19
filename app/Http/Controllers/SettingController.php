<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class SettingController extends Controller
{
    /**
     * Menampilkan halaman pengaturan umum sistem.
     */
    public function index(): View
    {
        // Mengambil konfigurasi jam patroli dari database, jika kosong default ke '01:00'
        $notificationTime = Setting::where('key', 'notification_time')->value('value') ?? '01:00';

        return view('settings.index', compact('notificationTime'));
    }

    /**
     * Memperbarui jam eksekusi pengingat otomatis harian berdasarkan input Admin.
     */
    public function updateNotificationTime(Request $request): RedirectResponse
    {
        // Validasi ketat untuk memastikan input yang masuk berupa format waktu 24 jam yang valid (HH:MM)
        $request->validate([
            'notification_time' => 'required|date_format:H:i',
        ], [
            'notification_time.required' => 'Waktu pengingat wajib ditentukan.',
            'notification_time.date_format' => 'Format penulisan waktu tidak valid.',
        ]);

        // Menggunakan updateOrCreate agar sistem otomatis membuat baris baru jika key belum ada,
        // atau memperbarui nilai lama jika key 'notification_time' sudah ada di database.
        Setting::updateOrCreate(
            ['key' => 'notification_time'],
            ['value' => $request->notification_time]
        );

        return redirect()->back()->with('success', 'Waktu operasional pengingat otomatis berhasil diperbarui!');
    }
}