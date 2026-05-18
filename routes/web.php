<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReceivableController;
use App\Http\Controllers\CustomerController;

// =========================================================================
// 1. RUTE DASHBOARD UTAMA
// =========================================================================
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// =========================================================================
// 2. RUTE MANAJEMEN DATA PIUTANG
// =========================================================================
Route::get('/receivables', [ReceivableController::class, 'index'])->name('receivables.index');
Route::get('/receivables/create', [ReceivableController::class, 'create'])->name('receivables.create');
Route::post('/receivables', [ReceivableController::class, 'store'])->name('receivables.store');
Route::get('/receivables/{id}/edit', [ReceivableController::class, 'edit'])->name('receivables.edit');
Route::put('/receivables/{id}', [ReceivableController::class, 'update'])->name('receivables.update');
Route::delete('/receivables/{id}', [ReceivableController::class, 'destroy'])->name('receivables.destroy');
Route::patch('/receivables/{id}/mark-as-paid', [ReceivableController::class, 'markAsPaid'])->name('receivables.mark-as-paid');

// =========================================================================
// 3. RUTE MANAJEMEN DATA PELANGGAN (BARU)
// =========================================================================
Route::resource('customers', CustomerController::class)->except(['show']);

// =========================================================================
// 4. RUTE MULTI-BAHASA (LANGUAGE SWITCHER)
// =========================================================================
Route::get('/switch-language/{locale}', function (string $locale) {
    if (! in_array($locale, ['id', 'en'])) {
        abort(400);
    }
    if (Auth::check()) {
        $user = Auth::user();
        $user->locale = $locale;
        $user->save();
    }
    session(['locale' => $locale]);
    return redirect()->back();
})->name('language.switch');