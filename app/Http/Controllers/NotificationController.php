<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // Menampilkan halaman khusus semua notifikasi
    public function index()
    {
        // Mengambil semua notifikasi milik Admin yang sedang login
        $notifications = Auth::user()->notifications()->paginate(15);
        return view('notifications.index', compact('notifications'));
    }

    // Menandai semua notifikasi menjadi "Sudah Dibaca"
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return redirect()->back()->with('success', __('notif_marked_read'));
    }

    // Menandai satu notifikasi saja
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        
        // PERBAIKAN: Lempar flash message success agar modal muncul
        return redirect()->back()->with('success', __('notif_marked_read_single'));
    }
}