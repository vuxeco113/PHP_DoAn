<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Building;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RoomController extends Controller
{
    public function getByBuilding(Request $request, $buildingId = null)
    {
        $buildingId = $buildingId ?: $request->input('buildingId');

        if (!$buildingId) {
            return response()->json([
                'success' => false,
                'message' => 'buildingId là bắt buộc'
            ], 400);
        }

        // Kiểm tra building có tồn tại không
        $building = Building::find($buildingId);
        if (!$building) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy tòa nhà với ID: ' . $buildingId
            ], 404);
        }

        try {
            // $rooms = Room::where('buildingId', $buildingId)
            //     ->orderBy('created_at', 'desc')
            //     ->get();
            $rooms = Room::where('buildingId', $buildingId)
                ->withCount([
                    'requests as pending_requests_count' => function ($query) {
                        $query->where('status', 'pending');
                    },
                    'requests as total_requests_count'
                ])
                ->with([
                    'requests' => function ($query) {
                        $query->orderBy('created_at', 'desc');
                        //  ->take(5); // Lấy 5 requests mới nhất mỗi room
                    }
                ])
                ->orderBy('created_at', 'desc')
                ->get();

            // Nếu không có rooms, vẫn trả về success với mảng rỗng
            $formattedRooms = $rooms->map(function ($room) {
                return [
                    'id' => $room->id,
                    'title' => $room->title,
                    'description' => $room->description,
                    'price' => $room->price,
                    'area' => $room->area,
                    'capacity' => $room->capacity,
                    'amenities' => $room->amenities ?? [],
                    'imageUrls' => $room->imageUrls ?? [],
                    'status' => $room->status,
                    'latitude' => $room->latitude,
                    'longitude' => $room->longitude,
                    'sodien' => $room->sodien,
                    // 'currentTenantId' => $room->currentTenantId,
                    'ownerId' => $room->ownerId,
                    // 'rentStartDate' => $room->rentStartDate,
                    'buildingId' => $room->buildingId,
                    'createdAt' => $room->created_at,
                    'updatedAt' => $room->updated_at,
                    'requests' => $room->requests->map(function ($request) {
                        return [
                            'id' => $request->id,
                            'user_khach_id' => $request->user_khach_id,
                            'room_id' => $request->room_id,
                            'loai_request' => $request->loai_request,
                            'name' => $request->name,
                            'sdt' => $request->sdt,
                            'mo_ta' => $request->mo_ta,
                            'status' => $request->status,
                            'created_at' => $request->created_at,
                            'updated_at' => $request->updated_at,
                        ];
                    })
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Lấy danh sách phòng thành công',
                'data' => [
                    'building' => [
                        'id' => $building->id,
                        'buildingName' => $building->buildingName,
                        'address' => $building->address,
                        'totalRooms' => $building->totalRooms,
                    ],
                    'rooms' => $formattedRooms,
                    'total' => $rooms->count()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching rooms by building: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getRoomsByBuildingId($buildingId)
    {
        return $this->getByBuilding(new Request(), $buildingId);
    }

    public function show($id)
    {
        try {
            // SỬA: 'building' thay vì 'buildings'
            $room = Room::with('building')->find($id);

            if (!$room) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy phòng với ID: ' . $id
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $room
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching room: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'area' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string',
            'images' => 'required|array|min:1',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'buildingId' => 'required|exists:buildings,id',
            'sodien' => 'nullable|integer|min:0',
            'ownerId' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ], [
            'buildingId.exists' => 'Tòa nhà không tồn tại',
            'images.required' => 'Vui lòng chọn ít nhất 1 ảnh',
            'images.*.image' => 'File phải là định dạng ảnh',
            'images.*.max' => 'Kích thước ảnh tối đa là 5MB',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xác thực dữ liệu',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $imagePaths = [];

            // DEBUG: Kiểm tra request
            Log::info('=== BUILDING CREATION START ===');
            Log::info('Request has files: ' . $request->hasFile('images'));
            Log::info('Request all files: ', $request->allFiles());

            // Xử lý upload ảnh
            if ($request->hasFile('images')) {
                $images = $request->file('images');

                Log::info('Images type: ' . gettype($images));
                Log::info('Is array: ' . (is_array($images) ? 'yes' : 'no'));

                // Nếu chỉ có 1 ảnh (không phải array), chuyển thành array
                if (!is_array($images)) {
                    $images = [$images];
                    Log::info('Converted single file to array');
                }

                Log::info('Total images to process: ' . count($images));

                foreach ($images as $index => $image) {
                    Log::info("Processing image {$index}: " . $image->getClientOriginalName());
                    if ($image->isValid()) {
                        $fileName = Str::slug($request->title) . '_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                        $savePath = storage_path('app/public/rooms');
                        if (!file_exists($savePath)) {
                            mkdir($savePath, 0777, true);
                        }
                        $image->move($savePath, $fileName);
                        $publicUrl = 'http://127.0.0.1:8000/storage/rooms/' . $fileName;

                        $imagePaths[] = $publicUrl;
                    } else {
                        Log::error("❌ Invalid image file: " . $image->getClientOriginalName());
                    }
                }
            } else {
                Log::warning('No images found in request');
            }

            Log::info('Final image paths: ' . json_encode($imagePaths));
            if (empty($imagePaths)) {
                Log::error('No images were saved');
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể lưu ảnh. Vui lòng thử lại.'
                ], 500);
            }
            $room = Room::create([
                'title' => $request->title,
                'description' => $request->description,
                'price' => $request->price,
                'area' => $request->area,
                'capacity' => $request->capacity,
                'amenities' => $request->amenities ?? [],
                'imageUrls' => $imagePaths,
                'status' => $request->status ?? 'available',
                'latitude' => $request->latitude ?? 0,
                'longitude' => $request->longitude ?? 0,
                'sodien' => $request->sodien ?? 0,
                'ownerId' => $request->ownerId,
                'buildingId' => $request->buildingId,
            ]);

            // Load relationship để trả về đầy đủ thông tin
            $room->load('building');

            Log::info('Room created successfully: ' . $room->id);

            return response()->json([
                'success' => true,
                'message' => 'Tạo phòng thành công',
                'data' => $room
            ], 201);
        } catch (\Exception $e) {
            Log::error('Room creation error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ], 500);
        }
    }
    public function update_status(Request $request, $id)
    {
        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:available,rented,pending'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xác thực dữ liệu',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $room = Room::find($id);
            if (!$room) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy phòng với ID: ' . $id
                ], 404);
            }

            $room->status = $request->status;
            $room->save();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật trạng thái phòng thành công',
                'data' => $room
            ]);
        } catch (\Exception $e) {
            Log::error('Room status update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ], 500);
        }
    }
    public function update_ownerId(Request $request, $id)
    {
        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'ownerId' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xác thực dữ liệu',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $room = Room::find($id);
            if (!$room) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy phòng với ID: ' . $id
                ], 404);
            }

            $room->ownerId = $request->ownerId;
            $room->save();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật ownerId phòng thành công',
                'data' => $room
            ]);
        } catch (\Exception $e) {
            Log::error('Room ownerId update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ], 500);
        }
    }


    public function update_sodiencu(Request $request, $id)
    {
        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'sodien' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xác thực dữ liệu',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $room = Room::find($id);
            if (!$room) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy phòng với ID: ' . $id
                ], 404);
            }

            $room->sodien = $request->sodien;
            $room->save();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật số điện phòng thành công',
                'data' => $room
            ]);
        } catch (\Exception $e) {
            Log::error('Room sodien update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ], 500);
        }
    }
    public function getRoomByUserId($ownerId)
    {
        try {
            // Lấy 1 phòng đầu tiên của chủ sở hữu
            $room = Room::where('ownerId', $ownerId)
            ->where('status', 'rented')
            ->first();

            if (!$room) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy phòng cho ownerId: ' . $ownerId
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $room
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching room by ownerId: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ], 500);
        }
    }
}
