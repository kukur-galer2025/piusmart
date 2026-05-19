<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CustomersExport;

class CustomerController extends Controller
{
    public function index(Request $request): View|string
    {
        $query = Customer::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
        }

        $customers = $query->latest()->paginate(10)->withQueryString();

        // 🟢 JIKA REQUEST DARI AJAX, KEMBALIKAN TABEL SAJA
        if ($request->ajax()) {
            return view('customers.partials.table', compact('customers'))->render();
        }

        return view('customers.index', compact('customers'));
    }

    public function create(): View
    {
        return view('customers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        // Validasi Super Ketat (Mendukung format kurung, spasi, dan strip)
        $validated = $request->validate([
            'name'    => [
                'required', 
                'string', 
                'min:3', 
                'max:255', 
                'regex:/^[a-zA-Z\s.,\'`\-]+$/'
            ],
            'phone'   => [
                'nullable', 
                'string', 
                'min:9', 
                'max:20', // Batas dinaikkan menjadi 20 karakter untuk menampung format simbol
                'unique:customers,phone', 
                'regex:/^(\+62|62|0|\(\+62\))[0-9\s\-]+$/' // Regex baru mendukung (+62), spasi, dan strip
            ],
            'address' => 'nullable|string|min:5',
        ], [
            // Custom Message Error Bahasa Indonesia
            'name.required' => 'Nama pelanggan wajib diisi.',
            'name.min'      => 'Nama pelanggan terlalu pendek, minimal 3 karakter.',
            'name.regex'    => 'Nama hanya boleh berisi huruf, spasi, titik, koma, atau tanda hubung.',
            'phone.unique'  => 'Nomor HP ini sudah terdaftar untuk pelanggan lain.',
            'phone.min'     => 'Nomor HP tidak valid, minimal berukuran 9 digit.',
            'phone.max'     => 'Nomor HP terlalu panjang, maksimal berukuran 20 karakter.',
            'phone.regex'   => 'Format nomor HP salah. Harus diawali dengan 0, 62, +62, atau (+62) diikuti angka, spasi, atau tanda hubung.',
            'address.min'   => 'Jika alamat diisi, masukkan alamat lengkap minimal 5 karakter.',
        ]);

        Customer::create($validated);
        return redirect()->route('customers.index')->with('success', 'Pelanggan baru berhasil ditambahkan!');
    }

    public function edit(Customer $customer): View
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        // Validasi Super Ketat untuk Ubah Data
        $validated = $request->validate([
            'name'    => [
                'required', 
                'string', 
                'min:3', 
                'max:255', 
                'regex:/^[a-zA-Z\s.,\'`\-]+$/'
            ],
            'phone'   => [
                'nullable', 
                'string', 
                'min:9', 
                'max:20', 
                'unique:customers,phone,' . $customer->id, // Mengabaikan keunikan untuk ID milik sendiri
                'regex:/^(\+62|62|0|\(\+62\))[0-9\s\-]+$/'
            ],
            'address' => 'nullable|string|min:5',
        ], [
            'name.required' => 'Nama pelanggan wajib diisi.',
            'name.min'      => 'Nama pelanggan terlalu pendek, minimal 3 karakter.',
            'name.regex'    => 'Nama hanya boleh berisi huruf, spasi, titik, koma, atau tanda hubung.',
            'phone.unique'  => 'Nomor HP ini sudah digunakan oleh pelanggan lain.',
            'phone.min'     => 'Nomor HP tidak valid, minimal berukuran 9 digit.',
            'phone.max'     => 'Nomor HP terlalu panjang, maksimal berukuran 20 karakter.',
            'phone.regex'   => 'Format nomor HP salah. Harus diawali dengan 0, 62, +62, atau (+62) diikuti angka, spasi, atau tanda hubung.',
            'address.min'   => 'Jika alamat diisi, masukkan alamat lengkap minimal 5 karakter.',
        ]);

        $customer->update($validated);
        return redirect()->route('customers.index')->with('success', 'Data pelanggan berhasil diperbarui!');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        try {
            $customer->delete();
            return redirect()->back()->with('success', 'Data pelanggan berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal! Pelanggan ini tidak bisa dihapus karena masih memiliki data transaksi piutang.');
        }
    }

    /**
     * Export data pelanggan ke PDF (Memperhitungkan filter pencarian)
     */
    public function exportPdf(Request $request)
    {
        $query = Customer::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
        }

        $customers = $query->latest()->get();
        $dateReport = Carbon::now()->format('d F Y (H:i)');

        $pdf = Pdf::loadView('customers.pdf', compact('customers', 'dateReport'))->setPaper('a4', 'portrait');
        
        return $pdf->download(__('pdf_customer_filename_prefix') . Carbon::now()->format('Ymd_His') . '.pdf');
    }

    /**
     * Export data pelanggan ke Excel (Memperhitungkan filter pencarian)
     */
    public function exportExcel(Request $request)
    {
        $query = Customer::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
        }

        $customers = $query->latest()->get();
        $fileName = 'Data_Pelanggan_Piusmart_' . Carbon::now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new CustomersExport($customers), $fileName);
    }
}