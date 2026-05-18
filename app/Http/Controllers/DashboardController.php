<?php

namespace App\Http\Controllers;

use App\Models\Receivable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today();
        $threeDaysFromNow = Carbon::today()->addDays(3);

        // 1. Metrik: Piutang Aktif (Belum Lunas)
        $activeReceivables = Receivable::where('is_paid', false)->get();
        $activeCount = $activeReceivables->count();
        $activeAmount = $activeReceivables->sum('amount');

        // 2. Metrik: Akan Jatuh Tempo (H-3 sampai Hari H)
        $dueSoonReceivables = Receivable::where('is_paid', false)
            ->whereBetween('due_date', [$today, $threeDaysFromNow])
            ->get();
        $dueSoonCount = $dueSoonReceivables->count();
        $dueSoonAmount = $dueSoonReceivables->sum('amount');

        // 3. Metrik: Terlambat (Melewati Hari H)
        $overdueReceivables = Receivable::where('is_paid', false)
            ->where('due_date', '<', $today)
            ->get();
        $overdueCount = $overdueReceivables->count();
        $overdueAmount = $overdueReceivables->sum('amount');

        // 4. Bangun Sistem Notifikasi untuk Dashboard
        $notifications = collect();

        // Gabungkan data terlambat dan akan jatuh tempo
        $urgentReceivables = Receivable::with('customer')
            ->where('is_paid', false)
            ->where('due_date', '<=', $threeDaysFromNow)
            ->orderBy('due_date', 'asc')
            ->take(6) // 🟢 BATASI HANYA 6 DATA AGAR DASHBOARD TIDAK KEPANJANGAN
            ->get();

        foreach ($urgentReceivables as $receivable) {
            $dueDate = Carbon::parse($receivable->due_date)->startOfDay();
            
            // MENGGUNAKAN abs() AGAR TIDAK ADA ANGKA MINUS (-18 HARI)
            $days = abs($today->diffInDays($dueDate, false)); 

            if ($today->gt($dueDate)) {
                $notifications->push([
                    'type'    => 'overdue',
                    'message' => __('warning_overdue', ['name' => $receivable->customer->name, 'days' => $days]),
                ]);
            } else {
                $notifications->push([
                    'type'    => 'due_soon',
                    'message' => __('warning_due_soon', ['name' => $receivable->customer->name, 'days' => $days]),
                ]);
            }
        }

        return view('dashboard', compact(
            'activeCount', 'activeAmount',
            'dueSoonCount', 'dueSoonAmount',
            'overdueCount', 'overdueAmount',
            'notifications'
        ));
    }
}