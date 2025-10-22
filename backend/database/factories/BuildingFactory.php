<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Building>
 */
class BuildingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'buildingName' => $this->faker->company . ' Residence',
            'address' => $this->faker->address,
            'imageUrls' => [
                $this->faker->imageUrl(640, 480, 'building', true),
                $this->faker->imageUrl(640, 480, 'apartment', true)
            ],
            'latitude' => $this->faker->latitude(10, 21),   
            'longitude' => $this->faker->longitude(103, 109),
            'totalRooms' => $this->faker->numberBetween(5, 50),
            'managerId' => 1, 
        ];
    }
}
