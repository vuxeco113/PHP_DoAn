<?php

namespace Database\Seeders;
use App\Models\DonViSuaChua;
use App\Models\Building;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Room;
use App\Models\Request; 
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'owner',
            'password' => '123456'
        ]);
        $user1 = User::factory()->create([
            'name' => 'Test User1',
            'email' => 'test1@example.com',
            'role' => 'tenant',
            'password' => '123456'
        ]);
        $building = Building::factory(5)->create([
            // 'buildingName' => 'Sunshine Apartments',
            // 'address' => '123 Main St, Cityville',
            'imageUrls' => [
                'http://127.0.0.1:8000/storage/buildings/SunshineApartments_1.jpg',
                'http://127.0.0.1:8000/storage/buildings/SunshineApartments_2.jpg'
            ],
            // 'latitude' => 0,
            // 'longitude' => 0,
            // 'totalRooms' => 20,
            'managerId' => 1
        ]);
        Room::factory(10)->create([
            'buildingId' => $building[0]->id,
       //     'title' => 'HaiCanVip2',
       //     'description' => 'Trọ cho người giàu',
        //    'price' => 6000000,
         //   'area' => 40,
         //   'capacity' => 3,
        //    'amenities' => ['day du'],
            'imageUrls' => [
                'http://127.0.0.1:8000/storage/rooms/compart_1760104446_68e90ffe97eb8.jpg',
            ],
            'sodien' => 0,
            'ownerId' => null,
    

        ]);

        $rooms = Room::where('buildingId', $building[0]->id)->get();
         $rentalRequests = [
            [
              
                'user_khach_id' => $user1->id,
                'room_id' => $rooms[0]->id,
                'loai_request' => 'thue_phong',
                'name' => 'Luong Liem Phong',
                'sdt' => '081936448',
                'mo_ta' => 'aaa',
                'status' => 'pending',
                'thoi_gian' => '2025-06-15T14:58:38.025570',
            ],
            [
            
                'user_khach_id' => $user1->id,
                'room_id' => $rooms[1]->id,
                'loai_request' => 'thue_phong',
                'name' => 'Nguyen Van A',
                'sdt' => '0912345678',
                'mo_ta' => 'Tôi muốn thuê phòng này vì vị trí thuận tiện',
                'status' => 'pending',
                'thoi_gian' => now()->subDays(2),
            ],
            [
               
                'user_khach_id' => $user1->id,
                'room_id' => $rooms[2]->id,
                'loai_request' => 'thue_phong',
                'name' => 'Tran Thi B',
                'sdt' => '0987654321',
                'mo_ta' => 'Phòng rất đẹp, hy vọng được duyệt',
                'status' => 'pending',
                'thoi_gian' => now()->subDays(5),
            ]
        ];

        foreach ($rentalRequests as $requestData) {
            Request::create($requestData);
        }

        DonViSuaChua::factory()->count(10)->create();
    }
}
