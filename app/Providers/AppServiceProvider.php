<?php

namespace App\Providers;

use App\Models\Receivable;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // View Composer: Otomatis menyuntikkan data ke layouts.app setiap kali dipanggil
        View::composer('layouts.app', function ($view) {
            $today = Carbon::today();
            $threeDaysFromNow = Carbon::today()->addDays(3);

            // Ambil maksimal 5 pengingat piutang paling mendesak (terlambat atau H-3 jatuh tempo)
            $globalNotifications = Receivable::with('customer')
                ->where('is_paid', false)
                ->where('due_date', '<=', $threeDaysFromNow)
                ->orderBy('due_date', 'asc')
                ->take(5)
                ->get()
                ->map(function ($receivable) use ($today) {
                    $dueDate = Carbon::parse($receivable->due_date)->startOfDay();
                    $days = $today->diffInDays($dueDate);

                    // Jika tanggal jatuh tempo sudah terlewat (Overdue)
                    if ($today->gt($dueDate)) {
                        return [
                            'type'    => 'overdue',
                            'message' => __('warning_overdue', ['name' => $receivable->customer->name, 'days' => $days]),
                        ];
                    }

                    // Jika mendekati tanggal jatuh tempo (Due Soon)
                    return [
                        'type'    => 'due_soon',
                        'message' => __('warning_due_soon', ['name' => $receivable->customer->name, 'days' => $days]),
                    ];
                });

            // Kirim variabel ke dalam master layout
            $view->with('globalNotifications', $globalNotifications);
        });
    }
}