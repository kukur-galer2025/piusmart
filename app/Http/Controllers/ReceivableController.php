<?php

namespace App\Http\Controllers;

use App\Models\Receivable;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class ReceivableController extends Controller
{
    /**
     * Menampilkan daftar piutang dengan fitur pencarian dan filter status.
     */
    public function index(Request $request): View
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

        // Ambil data dengan pagination (10 data per halaman) dan pertahankan query string di URL
        $receivables = $query->latest()->paginate(10)->withQueryString();

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
     * Aksi Cepat: Mengubah status piutang menjadi lunas.
     */
    public function markAsPaid(int $id): RedirectResponse
    {
        $receivable = Receivable::findOrFail($id);
        $receivable->update(['is_paid' => true]);

        return redirect()->back()->with('success', 'Piutang berhasil dilunasi!');
    }
}