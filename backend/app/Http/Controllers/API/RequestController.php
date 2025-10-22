<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Request as RequestModel; // Đổi tên model
use Illuminate\Http\Request; // Laravel's HTTP Request
use Illuminate\Support\Facades\Validator;

class RequestController extends Controller
{
    public function getByRoom($roomId)
    {
        try {
            // Kiểm tra roomId có tồn tại không
            if (!$roomId) {
                return response()->json([
                    'success' => false,
                    'message' => 'roomId là bắt buộc'
                ], 400);
            }

            // Lấy danh sách rental requests - sử dụng RequestModel
            $requests = RequestModel::where('room_id', $roomId)
                ->where('loai_request', 'thue_phong')
                ->orderBy('thoi_gian', 'desc')
                ->get();

            // Format response data - chỉ thông tin request
            $formattedRequests = $requests->map(function ($request) {
                return [
                    'id' => $request->id,
                    'user_khach_id' => $request->user_khach_id,
                    'room_id' => $request->room_id,
                    'loai_request' => $request->loai_request,
                    'name' => $request->name,
                    'sdt' => $request->sdt,
                    'mo_ta' => $request->mo_ta,
                    'status' => $request->status,
                    'thoi_gian' => $request->thoi_gian->toISOString(),
                    'formatted_time' => $request->thoi_gian->format('d/m/Y H:i'),
                    'created_at' => $request->created_at->toISOString(),
                    'updated_at' => $request->updated_at->toISOString(),
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Lấy danh sách yêu cầu thành công',
                'data' => $formattedRequests,
                'total' => $requests->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ], 500);
        }
    }
    public function getByRoom_ThuePhong($roomId)
    {
        try {
            // Kiểm tra roomId có tồn tại không
            if (!$roomId) {
                return response()->json([
                    'success' => false,
                    'message' => 'roomId là bắt buộc'
                ], 400);
            }

            // Lấy danh sách rental requests - sử dụng RequestModel
            $requests = RequestModel::where('room_id', $roomId)
                ->orderBy('thoi_gian', 'desc')
                ->get();

            // Format response data - chỉ thông tin request
            $formattedRequests = $requests->map(function ($request) {
                return [
                    'id' => $request->id,
                    'user_khach_id' => $request->user_khach_id,
                    'room_id' => $request->room_id,
                    'loai_request' => $request->loai_request,
                    'name' => $request->name,
                    'sdt' => $request->sdt,
                    'mo_ta' => $request->mo_ta,
                    'status' => $request->status,
                    'thoi_gian' => $request->thoi_gian->toISOString(),
                    'formatted_time' => $request->thoi_gian->format('d/m/Y H:i'),
                    'created_at' => $request->created_at->toISOString(),
                    'updated_at' => $request->updated_at->toISOString(),
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Lấy danh sách yêu cầu thành công',
                'data' => $formattedRequests,
                'total' => $requests->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request) // Bây giờ đây là Illuminate\Http\Request
    {
        try {
            // Validate input - $request->all() sẽ trả về array như expected
            $validator = Validator::make($request->all(), [
                'user_khach_id' => 'required|integer',
                'room_id' => 'required|integer',
                'loai_request' => 'required|string',
                'name' => 'required|string|max:255',
                'sdt' => 'required|string|max:20',
                'mo_ta' => 'nullable|string',
                'status' => 'nullable|string|in:pending,approved,rejected'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validatedData = $validator->validated();
            $validatedData['thoi_gian'] = now();

            if (!isset($validatedData['status'])) {
                $validatedData['status'] = 'pending';
            }

            // Sử dụng RequestModel để tạo mới
            $newRequest = RequestModel::create($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Tạo yêu cầu thành công',
                'data' => [
                    'id' => $newRequest->id,
                    'user_khach_id' => $newRequest->user_khach_id,
                    'room_id' => $newRequest->room_id,
                    'loai_request' => $newRequest->loai_request,
                    'name' => $newRequest->name,
                    'sdt' => $newRequest->sdt,
                    'mo_ta' => $newRequest->mo_ta,
                    'status' => $newRequest->status,
                    'thoi_gian' => $newRequest->thoi_gian->toISOString(),
                    'formatted_time' => $newRequest->thoi_gian->format('d/m/Y H:i'),
                    'created_at' => $newRequest->created_at->toISOString(),
                    'updated_at' => $newRequest->updated_at->toISOString(),
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAllRequestByTetantId($tenantId)
    {
        try {
            // Kiểm tra tenantId có tồn tại không
            if (!$tenantId) {
                return response()->json([
                    'success' => false,
                    'message' => 'tenantId là bắt buộc'
                ], 400);
            }

            // Lấy danh sách requests của tenant
            $requests = RequestModel::where('user_khach_id', $tenantId)
                ->orderBy('thoi_gian', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Lấy danh sách yêu cầu thành công',
                'data' => $requests
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ], 500);
        }
    }
    public function updateStatus($id, $status)
    {
        $bill = RequestModel::find($id);
        if (!$bill) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy hóa đơn'], 404);
        }
        $bill->status = $status;
        $bill->save();
        return response()->json([
            'success' => true,
            'message' => "Đã cập nhật trạng thái hóa đơn #$id thành '$status'",
            'data' => $bill
        ]);
    }
}
