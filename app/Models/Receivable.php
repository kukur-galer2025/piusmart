<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receivable extends Model
{
    use HasFactory; // Wajib ditambahkan agar factory bisa berjalan

    protected $fillable = [
        'customer_id',
        'item_name',
        'amount',
        'transaction_date',
        'due_date',
        'is_paid',
        'notes'
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'due_date'       => 'date',
        'is_paid'        => 'boolean',
    ];

    /**
     * Relasi: Piutang ini milik satu pelanggan.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Accessor untuk menentukan status pembayaran otomatis berdasarkan tanggal.
     */
    protected function status(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                if ($attributes['is_paid']) {
                    return 'Lunas';
                }

                $dueDate = Carbon::parse($attributes['due_date'])->startOfDay();
                $today   = Carbon::today();

                if ($today->gt($dueDate)) {
                    return 'Terlambat';
                }

                $daysDiff = $today->diffInDays($dueDate, false);
                
                if ($daysDiff <= 3 && $daysDiff >= 0) {
                    return 'Akan Jatuh Tempo';
                }

                return 'Belum Lunas';
            }
        );
    }
}