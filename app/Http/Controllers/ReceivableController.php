<?php

namespace App\Http\Controllers;

use App\Models\Receivable;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ReceivableController extends Controller
{
    /**
     * Menampilkan daftar piutang dengan fitur pencarian dan berbagai filter.
     */
    public function index(Request $request): View|string
    {
        $query = Receivable::with('customer');

        // 1. Fitur Pencarian Berdasarkan Nama Pelanggan
        if ($request->filled('search')) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        // 2. Fitur Filter Berdasarkan Status
        if ($request->filled('status')) {
            $status = $request->status;
            $today = Carbon::today();
            $threeDaysFromNow = Carbon::today()->addDays(3);

            if ($status === 'paid') {
                $query->where('is_paid', true);
            } elseif ($status === 'overdue') {
                $query->where('is_paid', false)->where('due_date', '<', $today);
            } elseif ($status === 'due_soon') {
                $query->where('is_paid', false)->whereBetween('due_date', [$today, $threeDaysFromNow]);
            } elseif ($status === 'unpaid') {
                $query->where('is_paid', false);
            }
        }

        // 3. Fitur Filter Berdasarkan Bulan Transaksi
        if ($request->filled('month')) {
            $query->whereMonth('transaction_date', $request->month);
        }

        // 4. Fitur Filter Berdasarkan Tahun Transaksi
        if ($request->filled('year')) {
            $query->whereYear('transaction_date', $request->year);
        }

        // Ambil data dengan pagination (10 data per halaman)
        $receivables = $query->latest()->paginate(10)->withQueryString();

        // JIKA REQUEST DARI AJAX (Pencarian Real-time Alpine), KEMBALIKAN TABEL SAJA
        if ($request->ajax()) {
            return view('receivables.partials.table', compact('receivables'))->render();
        }

        // JIKA REQUEST BIASA (Load halaman pertama kali), KEMBALIKAN SELURUH HALAMAN
        return view('receivables.index', compact('receivables'));
    }

    /**
     * Menampilkan halaman form tambah piutang.
     */
    public function create(): View
    {
        $customers = Customer::orderBy('name', 'asc')->get();
        return view('receivables.create', compact('customers'));
    }

    /**
     * Menyimpan data piutang baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id'      => 'required|exists:customers,id',
            'amount'           => 'required|numeric|min:1',
            'transaction_date' => 'required|date',
            'due_date'         => 'required|date|after_or_equal:transaction_date',
            'notes'            => 'nullable|string',
        ], [
            'customer_id.required'      => 'Pelanggan wajib dipilih.',
            'customer_id.exists'        => 'Pelanggan yang dipilih tidak valid atau tidak terdaftar.',
            'amount.required'           => 'Nominal piutang wajib diisi.',
            'amount.numeric'            => 'Nominal piutang harus berupa angka murni.',
            'amount.min'                => 'Nominal piutang minimal Rp 1.',
            'transaction_date.required' => 'Tanggal transaksi wajib diisi.',
            'transaction_date.date'     => 'Format tanggal transaksi tidak valid.',
            'due_date.required'         => 'Tanggal jatuh tempo wajib diisi.',
            'due_date.date'             => 'Format tanggal jatuh tempo tidak valid.',
            'due_date.after_or_equal'   => 'Tanggal jatuh tempo tidak boleh mundur dari tanggal transaksi.',
        ]);

        Receivable::create([
            'customer_id'      => $validated['customer_id'],
            'amount'           => $validated['amount'],
            'transaction_date' => $validated['transaction_date'],
            'due_date'         => $validated['due_date'],
            'is_paid'          => false,
            'notes'            => $validated['notes'],
        ]);

        return redirect()->route('receivables.index')->with('success', 'Piutang baru berhasil disimpan!');
    }

    /**
     * Menampilkan form edit data piutang.
     */
    public function edit(int $id): View
    {
        $receivable = Receivable::findOrFail($id);
        $customers = Customer::orderBy('name', 'asc')->get();
        
        return view('receivables.edit', compact('receivable', 'customers'));
    }

    /**
     * Memperbarui data piutang di database.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id'      => 'required|exists:customers,id',
            'amount'           => 'required|numeric|min:1',
            'transaction_date' => 'required|date',
            'due_date'         => 'required|date|after_or_equal:transaction_date',
            'notes'            => 'nullable|string',
        ], [
            'customer_id.required'      => 'Pelanggan wajib dipilih.',
            'customer_id.exists'        => 'Pelorggan yang dipilih tidak valid.',
            'amount.required'           => 'Nominal piutang wajib diisi.',
            'amount.numeric'            => 'Nominal piutang harus berupa angka murni.',
            'amount.min'                => 'Nominal piutang minimal Rp 1.',
            'transaction_date.required' => 'Tanggal transaksi wajib diisi.',
            'transaction_date.date'     => 'Format tanggal transaksi tidak valid.',
            'due_date.required'         => 'Tanggal jatuh tempo wajib diisi.',
            'due_date.date'             => 'Format tanggal jatuh tempo tidak valid.',
            'due_date.after_or_equal'   => 'Tanggal jatuh tempo tidak boleh mundur dari tanggal transaksi.',
        ]);

        $receivable = Receivable::findOrFail($id);
        $receivable->update([
            'customer_id'      => $validated['customer_id'],
            'amount'           => $validated['amount'],
            'transaction_date' => $validated['transaction_date'],
            'due_date'         => $validated['due_date'],
            'notes'            => $validated['notes'],
        ]);

        return redirect()->route('receivables.index')->with('success', 'Data piutang berhasil diperbarui!');
    }

    /**
     * Menghapus data piutang secara permanen.
     */
    public function destroy(int $id): RedirectResponse
    {
        $receivable = Receivable::findOrFail($id);
        $receivable->delete();

        return redirect()->back()->with('success', 'Data piutang berhasil dihapus!');
    }

    /**
     * Aksi Cepat: Mengubah status piutang menjadi LUNAS.
     */
    public function markAsPaid(int $id): RedirectResponse
    {
        $receivable = Receivable::findOrFail($id);
        $receivable->update(['is_paid' => true]);

        return redirect()->back()->with('success', 'Piutang berhasil ditandai LUNAS!');
    }

    /**
     * Aksi Cepat: Mengubah status piutang menjadi BELUM LUNAS.
     */
    public function markAsUnpaid(int $id): RedirectResponse
    {
        $receivable = Receivable::findOrFail($id);
        $receivable->update(['is_paid' => false]);

        return redirect()->back()->with('success', 'Status piutang dikembalikan menjadi BELUM LUNAS!');
    }

    /**
     * Export data piutang ke dokumen PDF (Sesuai Filter Aktif - FIXED TYPECASTING).
     */
    public function exportPdf(Request $request)
    {
        $query = Receivable::with('customer');

        // Filter Pencarian Nama
        if ($request->filled('search')) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        // Filter Status & Mapping Label untuk Dokumen Cetak (Multilingual)
        $filterStatus = __('pdf_filter_all_status');
        if ($request->filled('status')) {
            $status = $request->status;
            $today = Carbon::today();
            if ($status === 'paid') { 
                $query->where('is_paid', true); 
                $filterStatus = __('pdf_filter_paid');
            } elseif ($status === 'overdue') { 
                $query->where('is_paid', false)->where('due_date', '<', $today); 
                $filterStatus = __('pdf_filter_overdue');
            } elseif ($status === 'due_soon') { 
                $query->where('is_paid', false)->whereBetween('due_date', [$today, Carbon::today()->addDays(3)]); 
                $filterStatus = __('pdf_filter_due_soon');
            } elseif ($status === 'unpaid') { 
                $query->where('is_paid', false); 
                $filterStatus = __('pdf_filter_unpaid');
            }
        }

        // Filter Bulan & Mapping Label Cetak (DI-FIX MENGGUNAKAN INT CASTING)
        $filterPeriode = __('pdf_filter_all_period');
        if ($request->filled('month')) { 
            $query->whereMonth('transaction_date', $request->month); 
            $filterPeriode = Carbon::create()->month((int) $request->month)->translatedFormat('F');
        }
        
        // Filter Tahun
        if ($request->filled('year')) { 
            $query->whereYear('transaction_date', $request->year); 
            if ($filterPeriode === __('pdf_filter_all_period')) {
                $filterPeriode = $request->year;
            } else {
                $filterPeriode .= ' ' . $request->year;
            }
        }

        $receivables = $query->latest()->get();
        $dateReport = Carbon::now()->format('d F Y (H:i)');

        $pdf = Pdf::loadView('receivables.pdf', compact('receivables', 'dateReport', 'filterStatus', 'filterPeriode'))->setPaper('a4', 'portrait');
        return $pdf->download(__('pdf_filename_prefix') . Carbon::now()->format('Ymd_His') . '.pdf');
    }

    /**
     * Export data piutang ke file Excel (Sesuai Filter Aktif).
     */
    public function exportExcel(Request $request)
    {
        $query = Receivable::with('customer');

        if ($request->filled('search')) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $status = $request->status;
            $today = Carbon::today();
            if ($status === 'paid') { $query->where('is_paid', true); }
            elseif ($status === 'overdue') { $query->where('is_paid', false)->where('due_date', '<', $today); }
            elseif ($status === 'due_soon') { $query->where('is_paid', false)->whereBetween('due_date', [$today, Carbon::today()->addDays(3)]); }
            elseif ($status === 'unpaid') { $query->where('is_paid', false); }
        }

        if ($request->filled('month')) { $query->whereMonth('transaction_date', $request->month); }
        if ($request->filled('year')) { $query->whereYear('transaction_date', $request->year); }

        $receivables = $query->latest()->get();
        $fileName = __('excel_receivable_filename_prefix') . Carbon::now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new \App\Exports\ReceivablesExport($receivables), $fileName);
    }

    /**
     * Menampilkan halaman statistik piutang dengan grafik interaktif.
     */
    public function statistics(Request $request): View
    {
        $filterYear = $request->input('year');
        $filterMonth = $request->input('month');

        // --- SUMMARY CARDS ---
        $baseQuery = Receivable::query();
        if ($filterYear) { $baseQuery->whereYear('transaction_date', $filterYear); }
        if ($filterMonth) { $baseQuery->whereMonth('transaction_date', $filterMonth); }

        $totalAmount = (clone $baseQuery)->sum('amount');
        $totalCount = (clone $baseQuery)->count();
        $paidAmount = (clone $baseQuery)->where('is_paid', true)->sum('amount');
        $paidCount = (clone $baseQuery)->where('is_paid', true)->count();
        $unpaidAmount = (clone $baseQuery)->where('is_paid', false)->sum('amount');
        $unpaidCount = (clone $baseQuery)->where('is_paid', false)->count();

        $today = Carbon::today();
        $overdueQuery = Receivable::where('is_paid', false)->where('due_date', '<', $today);
        if ($filterYear) { $overdueQuery->whereYear('transaction_date', $filterYear); }
        if ($filterMonth) { $overdueQuery->whereMonth('transaction_date', $filterMonth); }
        $overdueAmount = $overdueQuery->sum('amount');
        $overdueCount = $overdueQuery->count();

        // --- CHART 1: Tren Piutang Bulanan (12 bulan terakhir, atau bulan-bulan di tahun filter) ---
        $trendMonths = [];
        $trendNewAmounts = [];
        $trendPaidAmounts = [];

        if ($filterYear) {
            $startMonth = $filterMonth ? (int)$filterMonth : 1;
            $endMonth = $filterMonth ? (int)$filterMonth : 12;
            for ($m = $startMonth; $m <= $endMonth; $m++) {
                $monthStart = Carbon::create($filterYear, $m, 1)->startOfMonth();
                $monthEnd = Carbon::create($filterYear, $m, 1)->endOfMonth();
                $trendMonths[] = $monthStart->translatedFormat('M Y');
                $trendNewAmounts[] = Receivable::whereBetween('transaction_date', [$monthStart, $monthEnd])->sum('amount');
                $trendPaidAmounts[] = Receivable::where('is_paid', true)->whereBetween('transaction_date', [$monthStart, $monthEnd])->sum('amount');
            }
        } else {
            for ($i = 11; $i >= 0; $i--) {
                $monthStart = Carbon::today()->subMonths($i)->startOfMonth();
                $monthEnd = Carbon::today()->subMonths($i)->endOfMonth();
                $trendMonths[] = $monthStart->translatedFormat('M Y');
                $trendNewAmounts[] = Receivable::whereBetween('transaction_date', [$monthStart, $monthEnd])->sum('amount');
                $trendPaidAmounts[] = Receivable::where('is_paid', true)->whereBetween('transaction_date', [$monthStart, $monthEnd])->sum('amount');
            }
        }

        // --- CHART 2: Komposisi Status ---
        $threeDaysFromNow = Carbon::today()->addDays(3);
        $statusBaseQuery = Receivable::query();
        if ($filterYear) { $statusBaseQuery->whereYear('transaction_date', $filterYear); }
        if ($filterMonth) { $statusBaseQuery->whereMonth('transaction_date', $filterMonth); }

        $statusPaid = (clone $statusBaseQuery)->where('is_paid', true)->count();
        $statusOverdue = (clone $statusBaseQuery)->where('is_paid', false)->where('due_date', '<', $today)->count();
        $statusDueSoon = (clone $statusBaseQuery)->where('is_paid', false)->whereBetween('due_date', [$today, $threeDaysFromNow])->count();
        $statusUnpaid = (clone $statusBaseQuery)->where('is_paid', false)->where('due_date', '>', $threeDaysFromNow)->count();

        // --- CHART 3: Top 5 Pelanggan ---
        $topQuery = Receivable::with('customer')->where('is_paid', false);
        if ($filterYear) { $topQuery->whereYear('transaction_date', $filterYear); }
        if ($filterMonth) { $topQuery->whereMonth('transaction_date', $filterMonth); }
        $topData = $topQuery->get()->groupBy('customer_id')->map(function ($rows) {
            return [
                'name' => $rows->first()->customer->name ?? 'N/A',
                'total' => $rows->sum('amount'),
            ];
        })->sortByDesc('total')->take(5);
        $topNames = $topData->pluck('name')->values()->toArray();
        $topTotals = $topData->pluck('total')->values()->toArray();

        // --- CHART 4: Lunas vs Belum Lunas (amount) ---
        // Sudah ada: $paidAmount, $unpaidAmount

        // --- CHART 5: Realisasi Pelunasan Bulanan ---
        $paidMonths = [];
        $paidMonthlyAmounts = [];
        if ($filterYear) {
            $startM = $filterMonth ? (int)$filterMonth : 1;
            $endM = $filterMonth ? (int)$filterMonth : 12;
            for ($m = $startM; $m <= $endM; $m++) {
                $mStart = Carbon::create($filterYear, $m, 1)->startOfMonth();
                $mEnd = Carbon::create($filterYear, $m, 1)->endOfMonth();
                $paidMonths[] = $mStart->translatedFormat('M');
                $paidMonthlyAmounts[] = Receivable::where('is_paid', true)->whereBetween('transaction_date', [$mStart, $mEnd])->sum('amount');
            }
        } else {
            for ($i = 11; $i >= 0; $i--) {
                $mStart = Carbon::today()->subMonths($i)->startOfMonth();
                $mEnd = Carbon::today()->subMonths($i)->endOfMonth();
                $paidMonths[] = $mStart->translatedFormat('M');
                $paidMonthlyAmounts[] = Receivable::where('is_paid', true)->whereBetween('transaction_date', [$mStart, $mEnd])->sum('amount');
            }
        }

        // --- CHART 6: Tren Overdue ---
        $overdueMonths = [];
        $overdueMonthlyAmounts = [];
        if ($filterYear) {
            $startO = $filterMonth ? (int)$filterMonth : 1;
            $endO = $filterMonth ? (int)$filterMonth : 12;
            for ($m = $startO; $m <= $endO; $m++) {
                $oStart = Carbon::create($filterYear, $m, 1)->startOfMonth();
                $oEnd = Carbon::create($filterYear, $m, 1)->endOfMonth();
                $overdueMonths[] = $oStart->translatedFormat('M');
                $overdueMonthlyAmounts[] = Receivable::where('is_paid', false)->where('due_date', '<', $today)->whereBetween('transaction_date', [$oStart, $oEnd])->sum('amount');
            }
        } else {
            for ($i = 11; $i >= 0; $i--) {
                $oStart = Carbon::today()->subMonths($i)->startOfMonth();
                $oEnd = Carbon::today()->subMonths($i)->endOfMonth();
                $overdueMonths[] = $oStart->translatedFormat('M');
                $overdueMonthlyAmounts[] = Receivable::where('is_paid', false)->where('due_date', '<', $today)->whereBetween('transaction_date', [$oStart, $oEnd])->sum('amount');
            }
        }

        // --- Tahun yang tersedia untuk filter ---
        $availableYears = Receivable::selectRaw('YEAR(transaction_date) as year')
            ->distinct()->orderByDesc('year')->pluck('year')->toArray();

        return view('receivables.statistics', compact(
            'filterYear', 'filterMonth', 'availableYears',
            'totalAmount', 'totalCount', 'paidAmount', 'paidCount',
            'unpaidAmount', 'unpaidCount', 'overdueAmount', 'overdueCount',
            'trendMonths', 'trendNewAmounts', 'trendPaidAmounts',
            'statusPaid', 'statusOverdue', 'statusDueSoon', 'statusUnpaid',
            'topNames', 'topTotals',
            'paidMonths', 'paidMonthlyAmounts',
            'overdueMonths', 'overdueMonthlyAmounts'
        ));
    }
}