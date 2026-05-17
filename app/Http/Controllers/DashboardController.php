<?php

namespace App\Http\Controllers;

use App\Models\Receivable;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan Halaman Utama Dashboard dengan Ringkasan Data & Notifikasi.
     */
    public function index(): View
    {
        $today = Carbon::today();
        $threeDaysFromNow = Carbon::today()->addDays(3);

        // =========================================================================
        // 1. RINGKASAN DATA UTAMA (Mendukung Fitur Poin 1)
        // =========================================================================
        
        // Piutang Aktif (Semua data yang belum lunas)
        $activeCount  = Receivable::where('is_paid', false)->count();
        $activeAmount = Receivable::where('is_paid', false)->sum('amount');

        // Piutang Terlambat (Belum lunas & tanggal jatuh tempo sudah terlewat)
        $overdueCount  = Receivable::where('is_paid', false)->where('due_date', '<', $today)->count();
        $overdueAmount = Receivable::where('is_paid', false)->where('due_date', '<', $today)->sum('amount');

        // Piutang Akan Jatuh Tempo (Belum lunas & jatuh tempo berada di antara hari ini s/d H+3)
        $dueSoonCount  = Receivable::where('is_paid', false)->whereBetween('due_date', [$today, $threeDaysFromNow])->count();
        $dueSoonAmount = Receivable::where('is_paid', false)->whereBetween('due_date', [$today, $threeDaysFromNow])->sum('amount');


        // =========================================================================
        // 2. SISTEM PENGINGAT / NOTIFIKASI OTOMATIS (Mendukung Fitur Poin 5)
        // =========================================================================
        
        $notifications = Receivable::with('customer')
            ->where('is_paid', false)
            ->where('due_date', '<=', $threeDaysFromNow) // Ambil yang telat ATAU H-3 jatuh tempo
            ->orderBy('due_date', 'asc')
            ->take(5) // Batasi hanya menampilkan 5 pengingat paling mendesak
            ->get()
            ->map(function ($receivable) use ($today) {
                $dueDate = Carbon::parse($receivable->due_date)->startOfDay();
                $days = $today->diffInDays($dueDate);

                if ($today->gt($dueDate)) {
                    return [
                        'type'    => 'overdue',
                        'message' => __('warning_overdue', ['name' => $receivable->customer->name, 'days' => $days]),
                    ];
                }

                return [
                    'type'    => 'due_soon',
                    'message' => __('warning_due_soon', ['name' => $receivable->customer->name, 'days' => $days]),
                ];
            });

        // Lempar semua variabel perhitungan ke dalam file Blade dashboard
        return view('dashboard', compact(
            'activeCount', 'activeAmount',
            'overdueCount', 'overdueAmount',
            'dueSoonCount', 'dueSoonAmount',
            'notifications'
        ));
    }
}