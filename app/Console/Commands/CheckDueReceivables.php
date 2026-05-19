<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Receivable;
use App\Models\User;
use App\Notifications\BillingNotification;
use Carbon\Carbon;

class CheckDueReceivables extends Command
{
    // Ini perintah yang akan dipanggil oleh server nanti
    protected $signature = 'receivables:check-due';
    protected $description = 'Memeriksa piutang yang akan jatuh tempo atau terlambat secara otomatis';

    public function handle()
    {
        $today = Carbon::today();
        $threeDaysFromNow = Carbon::today()->addDays(3);

        // 1. Ambil semua piutang yang BELUM LUNAS
        $receivables = Receivable::where('is_paid', false)->with('customer')->get();
        
        // 2. Ambil user Admin yang akan menerima notifikasi
        $admin = User::where('role', 'admin')->first(); // Sesuaikan dengan logika role di aplikasimu
        
        if (!$admin) {
            $this->error('Admin tidak ditemukan.');
            return;
        }

        foreach ($receivables as $item) {
            $dueDate = Carbon::parse($item->due_date)->startOfDay();

            // Cek apakah sudah pernah dikirimi notifikasi serupa biar gak spam di DB
            $alreadyNotified = $admin->notifications()
                ->where('data->receivable_id', $item->id)
                ->where('read_at', null)
                ->exists();

            if (!$alreadyNotified) {
                // KONDISI A: Sudah Terlambat (Overdue)
                if ($today->gt($dueDate)) {
                    $admin->notify(new BillingNotification($item, 'overdue'));
                } 
                // KONDISI B: Akan Jatuh Tempo (Due Soon - H sampai H+3)
                elseif ($dueDate->between($today, $threeDaysFromNow)) {
                    $admin->notify(new BillingNotification($item, 'due_soon'));
                }
            }
        }

        $this->info('Pemeriksaan piutang selesai dan notifikasi berhasil diperbarui!');
    }
}