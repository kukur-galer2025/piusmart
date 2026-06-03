<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Receivable;
use App\Models\ReceivablePayment;
use Illuminate\Http\RedirectResponse;

class ReceivablePaymentController extends Controller
{
    /**
     * Menyimpan data pembayaran baru.
     */
    public function store(Request $request, int $receivableId): RedirectResponse
    {
        $receivable = Receivable::findOrFail($receivableId);

        $validated = $request->validate([
            'amount'       => 'required|numeric|min:1',
            'payment_date' => 'required|date',
            'notes'        => 'nullable|string',
        ], [
            'amount.required'       => 'Nominal pembayaran wajib diisi.',
            'amount.numeric'        => 'Nominal pembayaran harus berupa angka.',
            'amount.min'            => 'Nominal pembayaran minimal Rp 1.',
            'payment_date.required' => 'Tanggal pembayaran wajib diisi.',
            'payment_date.date'     => 'Format tanggal tidak valid.',
        ]);

        // Simpan pembayaran
        $receivable->payments()->create([
            'amount'       => $validated['amount'],
            'payment_date' => $validated['payment_date'],
            'notes'        => $validated['notes'],
        ]);

        // Cek apakah sudah lunas (menggunakan accessor remainingBalance)
        if ($receivable->remaining_balance <= 0) {
            $receivable->update(['is_paid' => true]);
        }

        return redirect()->route('receivables.show', $receivableId)
                         ->with('success', 'Pembayaran berhasil ditambahkan!');
    }

    /**
     * Menghapus data pembayaran.
     */
    public function destroy(int $id): RedirectResponse
    {
        $payment = ReceivablePayment::findOrFail($id);
        $receivable = $payment->receivable;

        $payment->delete();

        // Cek ulang apakah sisa tagihan jadi > 0 (batal lunas)
        // Kita perlu me-refresh model receivable untuk mendapatkan nilai sum yang terbaru
        $receivable->refresh();
        if ($receivable->remaining_balance > 0 && $receivable->is_paid) {
            $receivable->update(['is_paid' => false]);
        }

        return redirect()->route('receivables.show', $receivable->id)
                         ->with('success', 'Riwayat pembayaran berhasil dihapus!');
    }
}
