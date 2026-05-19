<?php

namespace App\Http\Controllers;

use App\Models\Receivable;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth; // Ditambahkan untuk membaca data notifikasi user

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today();
        $threeDaysFromNow = Carbon::today()->addDays(3);

        // --- 0. DATA NOTIFIKASI PENGINGAT OTOMATIS (URGENT ALERTS BANNER) ---
        // Mengambil 5 notifikasi belum dibaca teratas milik Admin untuk dipajang di Dashboard
        $urgentAlerts = Auth::check() ? Auth::user()->unreadNotifications->take(5) : collect();


        // --- 1. METRIK KOTAK ATAS & GRAFIK 1 (KOMPOSISI AKTIF) ---
        $activeReceivables = Receivable::where('is_paid', false)->get();
        $activeCount = $activeReceivables->count();
        $activeAmount = $activeReceivables->sum('amount');

        $dueSoonReceivables = Receivable::where('is_paid', false)
            ->whereBetween('due_date', [$today, $threeDaysFromNow])
            ->get();
        $dueSoonCount = $dueSoonReceivables->count();
        $dueSoonAmount = $dueSoonReceivables->sum('amount');

        $overdueReceivables = Receivable::where('is_paid', false)
            ->where('due_date', '<', $today)
            ->get();
        $overdueCount = $overdueReceivables->count();
        $overdueAmount = $overdueReceivables->sum('amount');


        // --- 2. DATA GRAFIK: LUNAS VS BELUM LUNAS ---
        $paidAmount = Receivable::where('is_paid', true)->sum('amount');
        $unpaidAmount = $activeAmount; // Sama dengan total yang belum dibayar


        // --- 3. DATA GRAFIK: TREN 6 BULAN TERAKHIR ---
        $trendMonths = [];
        $trendTotals = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = Carbon::today()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::today()->subMonths($i)->endOfMonth();
            
            $trendMonths[] = $monthStart->translatedFormat('M Y'); // Contoh: "Mei 2026"
            $trendTotals[] = Receivable::whereBetween('transaction_date', [$monthStart, $monthEnd])->sum('amount');
        }


        // --- 4. DATA GRAFIK: TOP 5 PELANGGAN DENGAN PIUTANG TERBESAR ---
        // Menggunakan Collection Laravel agar aman di semua jenis Database
        $unpaidWithCustomers = Receivable::with('customer')->where('is_paid', false)->get();
        
        $topCustomersData = $unpaidWithCustomers->groupBy('customer_id')->map(function ($rows) {
            return [
                'name' => $rows->first()->customer->name ?? 'Pelanggan Dihapus',
                'total' => $rows->sum('amount')
            ];
        })->sortByDesc('total')->take(5);

        // Ubah menjadi array untuk JavaScript grafik
        $topCustomerNames = $topCustomersData->pluck('name')->values()->toArray();
        $topCustomerTotals = $topCustomersData->pluck('total')->values()->toArray();


        return view('dashboard', compact(
            'urgentAlerts', // Disuntikkan ke dalam view dashboard
            'activeCount', 'activeAmount',
            'dueSoonCount', 'dueSoonAmount',
            'overdueCount', 'overdueAmount',
            'paidAmount', 'unpaidAmount',
            'trendMonths', 'trendTotals',
            'topCustomerNames', 'topCustomerTotals'
        ));
    }
}