<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bill;
use App\Models\Room;

class BillController extends Controller
{
    //

    public function getRoomsWithoutBillThisMonth($buildingId)
    {
        $thangNamHienTai = now()->format('m/Y');

        $roomIdsDaCoHoaDon = Bill::where('thangNam', $thangNamHienTai)
            ->pluck('roomId');

        $roomsChuaCoHoaDon = Room::whereNotIn('id', $roomIdsDaCoHoaDon)
            ->where('buildingId', $buildingId)
            ->where('status', 'rented')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Danh sách phòng chưa có hóa đơn tháng ' . $thangNamHienTai,
            'data' => $roomsChuaCoHoaDon,
        ]);
    }

    public function getPendingBillCurrentMonth($buildingId)
    {
        $thangNamHienTai = now()->format('m/Y');

        // Lấy danh sách roomId chưa thanh toán trong tháng hiện tại
        $pendingBills = Bill::with(['room', 'tenant', 'owner'])
            ->where('status', 'pending')
            ->where('thangNam', $thangNamHienTai)
            ->whereHas('room', function ($query) use ($buildingId) {
                $query->where('buildingId', $buildingId);
            })
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Danh sách hóa đơn chưa thanh toán của tòa nhà ' . $buildingId,
            'data' => $pendingBills,
        ]);
    }
    public function getPaidBillCurrentMonth($buildingId)
    {
        $thangNamHienTai = now()->format('m/Y');

        // Lấy danh sách roomId chưa thanh toán trong tháng hiện tại
        $pendingBills = Bill::with(['room', 'tenant', 'owner'])
            ->where('status', 'paid')
            ->where('thangNam', $thangNamHienTai)
            ->whereHas('room', function ($query) use ($buildingId) {
                $query->where('buildingId', $buildingId);
            })
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Danh sách hóa đơn đã thanh toán của tòa nhà ' . $buildingId,
            'data' => $pendingBills,
        ]);
    }
    public function store(Request $request)
    {
        // 🔍 1. Validate dữ liệu
        $validatedData = $request->validate([
            'khachThueId' => 'required|integer|exists:users,id',
            'ownerId' => 'required|integer|exists:users,id',
            'roomId' => 'required|integer|exists:rooms,id',
            'priceDien' => 'required|integer|min:0',
            'priceRoom' => 'required|integer|min:0',
            'priceWater' => 'required|integer|min:0',
            'sumPrice' => 'required|numeric|min:0',
            'soNguoi' => 'required|integer|min:1',
            'sodienCu' => 'required|integer|min:0',
            'sodienMoi' => 'required|integer|min:0',
            'status' => 'nullable|string|in:pending,paid',
            'amenitiesPrice' => 'nullable|numeric|min:0',
            'thangNam' => [
                'nullable',
                'regex:/^(0[1-9]|1[0-2])\/\d{4}$/', // định dạng MM/YYYY
            ],
        ]);

        // 🔧 2. Nếu không truyền `thangNam`, mặc định là tháng hiện tại
        $validatedData['thangNam'] = $validatedData['thangNam'] ?? now()->format('m/Y');

        // 🔧 3. Thêm ngày tạo hóa đơn
        $validatedData['date'] = now();
        $validatedData['paidAt'] = $validatedData['paidAt'] ?? null; // chưa thanh toán
        $validatedData['status'] = $validatedData['status'] ?? 'pending';

        //  4. Tính tổng tiền (ví dụ: tiền phòng + nước + điện)
        // $validatedData['sumPrice'] =
        //     $validatedData['priceRoom'] +
        //     $validatedData['priceWater'] +
        //     $validatedData['priceDien'];

        // 💾 5. Lưu vào DB
        $bill = Bill::create($validatedData);

        // ✅ 6. Trả phản hồi
        return response()->json([
            'success' => true,
            'message' => 'Tạo hóa đơn thành công',
            'data' => $bill,
        ]);
    }

    public function update_status(Request $request, $id)
    {
        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'status' => 'nullable|string|in:paid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xác thực dữ liệu',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $room = Bill::find($id);
            if (!$room) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy hóa đơn với ID: ' . $id
                ], 404);
            }

            $room->status = $request->status;
            $room->save();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật hóa đơn phòng thành công',
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

    public function getAllBillsByTantentId($tenantId)
    {
        $bills = Bill::with(['room', 'owner', 'tenant'])
            ->where('khachThueId', $tenantId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Danh sách hóa đơn của khách thuê có ID: ' . $tenantId,
            'data' => $bills,
        ]);
    }

    public function getBillDetail($id)
    {
        try {
            // Lấy hóa đơn kèm theo thông tin phòng, chủ, khách thuê
            $bill = Bill::with(['room', 'owner', 'tenant'])->find($id);

            if (!$bill) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy hóa đơn có ID: ' . $id,
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Lấy chi tiết hóa đơn thành công',
                'data' => $bill,
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy chi tiết hóa đơn: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage(),
            ], 500);
        }
    }

     public function updateStatus($id, $status)
    {
        $bill = Bill::find($id);

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
