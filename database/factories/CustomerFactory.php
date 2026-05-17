<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Menggunakan faker berbahasa Indonesia
        $faker = \Faker\Factory::create('id_ID');

        return [
            'name' => $faker->name(),
            'phone' => $faker->phoneNumber(),
            'address' => $faker->address(),
        ];
    }
}