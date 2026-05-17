<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Customer;
use App\Models\Receivable;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat Akun Admin Utama
        User::create([
            'name' => 'Admin Piusmart',
            'email' => 'admin@piusmart.com',
            'password' => Hash::make('password123'), // Password default
            'role' => 'admin',
            'locale' => 'id', // Default bahasa Indonesia
        ]);

        // 2. Buat 15 Pelanggan, masing-masing punya 1 sampai 3 data piutang acak
        Customer::factory(15)->create()->each(function ($customer) {
            Receivable::factory(rand(1, 3))->create([
                'customer_id' => $customer->id
            ]);
        });
    }
}