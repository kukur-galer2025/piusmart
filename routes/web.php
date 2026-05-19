<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReceivableController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SettingController; // Import Controller Pengaturan Baru

// =========================================================================
// RUTE AUTENTIKASI (LOGIN & LOGOUT)
// =========================================================================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// =========================================================================
// RUTE SISTEM (DIGEMBOK: HANYA BISA DIAKSES JIKA SUDAH LOGIN)
// =========================================================================
Route::middleware('auth')->group(function () {
    
    // 1. Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // 2. Piutang
    Route::get('/receivables/export/pdf', [ReceivableController::class, 'exportPdf'])->name('receivables.export.pdf');
    Route::get('/receivables/export/excel', [ReceivableController::class, 'exportExcel'])->name('receivables.export.excel');
    
    Route::get('/receivables', [ReceivableController::class, 'index'])->name('receivables.index');
    Route::get('/receivables/create', [ReceivableController::class, 'create'])->name('receivables.create');
    Route::post('/receivables', [ReceivableController::class, 'store'])->name('receivables.store');
    Route::get('/receivables/{id}/edit', [ReceivableController::class, 'edit'])->name('receivables.edit');
    Route::put('/receivables/{id}', [ReceivableController::class, 'update'])->name('receivables.update');
    Route::delete('/receivables/{id}', [ReceivableController::class, 'destroy'])->name('receivables.destroy');
    
    // Fitur Mengubah Status Pelunasan (Tandai Lunas & Batal Lunas via SweetAlert)
    Route::patch('/receivables/{id}/mark-as-paid', [ReceivableController::class, 'markAsPaid'])->name('receivables.mark-as-paid');
    Route::patch('/receivables/{id}/mark-as-unpaid', [ReceivableController::class, 'markAsUnpaid'])->name('receivables.mark-as-unpaid');

    // 3. Pelanggan
    Route::get('/customers/export/pdf', [CustomerController::class, 'exportPdf'])->name('customers.export.pdf');
    Route::get('/customers/export/excel', [CustomerController::class, 'exportExcel'])->name('customers.export.excel');
    Route::resource('customers', CustomerController::class)->except(['show']);

    // 4. Notifikasi
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::patch('/notifications/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');

    // 5. Pengaturan Umum Sistem (Settings)
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::patch('/settings/notification', [SettingController::class, 'updateNotificationTime'])->name('settings.notification.update');
    
    // 🟢 RUTE PENGAMAN: Mencegah error MethodNotAllowedHttpException jika admin ketik manual / refresh (F5) setelah simpan
    Route::get('/settings/notification', function () {
        return redirect()->route('settings.index');
    });
});

// =========================================================================
// RUTE MULTI-BAHASA (LANGUAGE SWITCHER)
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