<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReceivableController;

// =========================================================================
// 1. RUTE DASHBOARD UTAMA
// =========================================================================
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// =========================================================================
// 2. RUTE MANAJEMEN DATA PIUTANG
// =========================================================================
// Halaman Utama Data Piutang (Tabel, Pencarian, & Filter)
Route::get('/receivables', [ReceivableController::class, 'index'])->name('receivables.index');

// Form Tambah & Proses Simpan Transaksi Piutang Baru
Route::get('/receivables/create', [ReceivableController::class, 'create'])->name('receivables.create');
Route::post('/receivables', [ReceivableController::class, 'store'])->name('receivables.store');

// Aksi Cepat: Mengubah status piutang menjadi lunas
Route::patch('/receivables/{id}/mark-as-paid', [ReceivableController::class, 'markAsPaid'])->name('receivables.mark-as-paid');

// =========================================================================
// 3. RUTE MULTI-BAHASA (LANGUAGE SWITCHER)
// =========================================================================
/**
 * Rute Ganti Bahasa
 * Mengubah bahasa aplikasi menjadi Indonesia (id) atau Inggris (en)
 */
Route::get('/switch-language/{locale}', function (string $locale) {
    if (! in_array($locale, ['id', 'en'])) {
        abort(400);
    }

    if (Auth::check()) {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->locale = $locale;
        $user->save();
    }

    session(['locale' => $locale]);

    return redirect()->back();
})->name('language.switch');