<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    /**
     * Nama tabel yang terkait dengan model (Opsional, karena sesuai konvensi plural Laravel).
     *
     * @var string
     */
    protected $table = 'settings';

    /**
     * Kolom yang diizinkan untuk diisi secara massal (Mass Assignment Guard).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'value',
    ];
}