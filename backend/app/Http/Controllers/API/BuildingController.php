<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Building;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Log;

class BuildingController extends Controller
{
    //
    public function index()
    {
        // Lấy tất cả buildings
        $buildings = Building::all();

        // Chuyển imageUrls từ JSON string sang array nếu cần
        // $buildings->transform(function ($building) {
        //     $building->imageUrls = json_decode($building->imageUrls);
        //     return $building;
        // });

        return response()->json([
            'status' => 'success',
            'data' => $buildings
        ]);
    }
    public function buildings_ByID(Request $request)
    {
        // Lấy managerId từ request
        $managerId = $request->input('managerId');

        if (!$managerId) {
            return response()->json([
                'status' => 'error',
                'message' => 'managerId is required'
            ], 400);
        }

        // Lấy danh sách buildings có managerId tương ứng
        $buildings = Building::where('managerId', $managerId)->get();


        return response()->json([
            'status' => 'success',
            'data' => $buildings
        ]);
    }




    public function buildings_ByID2(Request $request)
    {
        // Lấy managerId từ request
        $managerId = $request->input('managerId');

        if (!$managerId) {
            return response()->json([
                'status' => 'error',
                'message' => 'managerId is required'
            ], 400);
        }

        // Lấy danh sách buildings có managerId tương ứng với rooms và request count
        $buildings = Building::where('managerId', $managerId)
            ->with([
                'rooms' => function ($query) {
                    $query->withCount([
                        'requests as pending_requests_count' => function ($query) {
                            $query->where('status', 'pending');
                        }
                    ])
                        ->with([
                            'requests' => function ($query) {
                                $query->orderBy('created_at', 'desc')
                                    ->take(3); // Lấy 3 requests mới nhất mỗi room
                            }
                        ]);
                }
            ])
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $buildings
        ]);
    }





    public function store(Request $request)
    {
        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'buildingName' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'images' => 'required', // Bỏ 'array' requirement
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'totalRooms' => 'required|integer|min:1',
            'managerId' => 'required|exists:users,id'
        ], [
            'images.required' => 'Vui lòng chọn ít nhất 1 ảnh',
            'images.*.image' => 'File phải là định dạng ảnh',
            'images.*.mimes' => 'Ảnh phải có định dạng: jpeg, png, jpg, gif, webp',
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
                        // Tạo tên file unique
                        $fileName = Str::slug($request->buildingName) . '_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

                        $savePath = storage_path('app/public/buildings');
                        if (!file_exists($savePath)) {
                            mkdir($savePath, 0777, true);
                        }

                        // Di chuyển file ảnh
                        $image->move($savePath, $fileName);

                        // ✅ Tạo URL cố định để truy cập từ frontend
                        $publicUrl = 'http://127.0.0.1:8000/storage/buildings/' . $fileName;

                        $imagePaths[] = $publicUrl;
                    } else {
                        Log::error("❌ Invalid image file: " . $image->getClientOriginalName());
                    }
                }
            } else {
                Log::warning('No images found in request');
            }

            Log::info('Final image paths: ' . json_encode($imagePaths));

            // Kiểm tra nếu không có ảnh nào được lưu
            if (empty($imagePaths)) {
                Log::error('No images were saved');
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể lưu ảnh. Vui lòng thử lại.'
                ], 500);
            }

            // Tạo building
            $building = Building::create([
                'buildingName' => $request->buildingName,
                'address' => $request->address,
                'imageUrls' => $imagePaths,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'totalRooms' => $request->totalRooms,
                'managerId' => $request->managerId
            ]);

            Log::info('✅ Building created successfully ID: ' . $building->id);
            Log::info('=== BUILDING CREATION END ===');

            return response()->json([
                'success' => true,
                'message' => 'Tạo tòa nhà thành công',
                'data' => $building
            ], 201);

        } catch (\Exception $e) {
            Log::error('❌ Building creation error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ], 500);
        }
    }
}
