<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Room;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Request>
 */
class RequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
         return [
            'id' => $this->faker->uuid(),
            'user_khach_id' => User::factory(),
            'room_id' => Room::factory(),
            'loai_request' => 'thue_phong',
            'name' => $this->faker->name(),
            'sdt' => $this->faker->phoneNumber(),
            'mo_ta' => $this->faker->sentence(),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
            'thoi_gian' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
