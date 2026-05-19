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
        $fileName = 'Laporan_Piutang_Piusmart_' . Carbon::now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new \App\Exports\ReceivablesExport($receivables), $fileName);
    }
}