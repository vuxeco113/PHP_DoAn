<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DonViSuaChua>
 */
class DonViSuaChuaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
         return [
            'ten' => $this->faker->company(),   // Tên công ty sửa chữa
            'dia_chi' => $this->faker->address(), // Địa chỉ ngẫu nhiên
        ];
    }
}
