<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory; // Wajib ditambahkan agar factory bisa berjalan

    protected $fillable = [
        'name',
        'phone',
        'address'
    ];

    /**
     * Relasi: Satu pelanggan bisa memiliki banyak piutang.
     */
    public function receivables(): HasMany
    {
        return $this->hasMany(Receivable::class);
    }
}