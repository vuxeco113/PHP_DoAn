<?php

namespace Database\Factories;
use App\Models\Building;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $roomTypes = ['Phòng Standard', 'Phòng Deluxe', 'Phòng VIP', 'Phòng Studio', 'Phòng Family'];
        $amenitiesOptions = [
            ['Máy lạnh', 'TV', 'Tủ lạnh', 'WiFi'],
            ['Máy lạnh', 'TV', 'Tủ lạnh', 'WiFi', 'Máy giặt'],
            ['Máy lạnh', 'TV', 'Tủ lạnh', 'WiFi', 'Máy giặt', 'Bếp'],
            ['Máy lạnh', 'TV', 'Tủ lạnh', 'WiFi', 'Máy giặt', 'Bếp', 'Ban công'],
            ['Máy lạnh', 'TV', 'Tủ lạnh', 'WiFi', 'Máy giặt', 'Bếp', 'Ban công', 'Hồ bơi']
        ];

        return [
            'title' => $this->faker->randomElement($roomTypes),
            'description' => $this->faker->sentence(10),
            'price' => $this->faker->numberBetween(2000000, 10000000),
            'area' => $this->faker->numberBetween(20, 60),
            'capacity' => $this->faker->numberBetween(1, 4),
            'amenities' => $this->faker->randomElement($amenitiesOptions),
            'imageUrls' => [
                $this->faker->imageUrl(640, 480, 'room'),
                $this->faker->imageUrl(640, 480, 'apartment')
            ],
            'status' => 'available',
            'latitude' => 0,
            'longitude' => 0,
            'sodien' => $this->faker->numberBetween(0, 100),
            'ownerId' => null,
            // 'rentStartDate' => null,
            'buildingId' => Building::factory(),
        ];
    }
}
