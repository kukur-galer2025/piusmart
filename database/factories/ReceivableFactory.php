<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Customer;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Receivable>
 */
class ReceivableFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Membuat tanggal transaksi acak (antara 2 bulan lalu sampai hari ini)
        $transactionDate = fake()->dateTimeBetween('-2 months', 'now');
        
        // Tanggal jatuh tempo acak (antara 1 bulan lalu sampai 1 bulan ke depan)
        // Ini memastikan kita punya contoh status: Terlambat, Jatuh Tempo, dan Belum Lunas
        $dueDate = fake()->dateTimeBetween('-1 months', '+1 months');
        
        // 30% peluang sudah lunas
        $isPaid = fake()->boolean(30);

        return [
            'customer_id' => Customer::factory(), // Akan membuat customer otomatis jika tidak di-supply
            'amount' => fake()->randomElement([500000, 1000000, 1500000, 2000000, 5000000, 10000000]),
            'transaction_date' => $transactionDate,
            'due_date' => $dueDate,
            'is_paid' => $isPaid,
            'notes' => fake()->boolean(40) ? fake()->sentence() : null, // 40% peluang ada catatan
        ];
    }
}