<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PhieuSua;
use Illuminate\Support\Facades\Validator;

class PhieuSuaController extends Controller
{
    //
    public function store(Request $request)
    {
        // Validate dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'faultSource' => 'required|string|max:255',
            'items' => 'required|array',
            'items.*.cost' => 'required|numeric',
            'items.*.info' => 'required|string',
            'ngaySua' => 'required|date',
            'requestId' => 'required|exists:requests,id',
            'roomId' => 'required|exists:rooms,id',
            'tenantId' => 'required|exists:users,id',
            'tongTien' => 'required|numeric',
            'status' => 'sometimes|string|in:pending,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $data['items'] = json_encode($data['items']);

        $phieuSua = PhieuSua::create($data);

        return response()->json([
            'message' => 'Phiếu sửa đã được tạo thành công',
            'data' => $phieuSua
        ], 201);
    }
    public function getAllPhieuSuaByTenantId($tenantId)
    {
        $phieuSuas = PhieuSua::with(['room', 'tenant', 'request'])
            ->where('tenantId', $tenantId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách phiếu sửa chữa thành công',
            'data' => $phieuSuas
        ]);
    }
    public function update(Request $request, $id)
    {

        $validated = $request->validate([
            'faultSource' => 'required|string',
            'items' => 'required|array',
            'ngaySua' => 'required|date',
            'tongTien' => 'required|numeric|min:0',
            'status' => 'required|string|in:pending,completed,cancelled',
        ]);
        $phieu = PhieuSua::find($id);

        if (!$phieu) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy phiếu sửa có ID: ' . $id,
            ], 404);
        }
        $phieu->faultSource = $validated['faultSource'];
        $phieu->items = json_encode($validated['items']);
        $phieu->ngaySua = $validated['ngaySua'];
        $phieu->tongTien = $validated['tongTien'];
        $phieu->status = $validated['status'];
        $phieu->save();
        return response()->json([
            'success' => true,
            'message' => 'Cập nhật phiếu sửa thành công!',
            'data' => $phieu,
        ]);
    }
    public function getPhieuSuaById($id)
    {
        $phieuSua = PhieuSua::findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Lấy phiếu sửa chữa thành công',
            'data' => $phieuSua
        ]);
    }
    public function getAllPhieuSuaByRoomId($roomId)
    {
        $phieuSuas = PhieuSua::with(['room', 'tenant', 'request'])
            ->where('roomId', $roomId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách phiếu sửa chữa theo phòng thành công',
            'data' => $phieuSuas
        ]);
    }
    public function updateStatus($id, $status)
    {
        $bill = PhieuSua::find($id);

        if (!$bill) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy phiếu sửa'], 404);
        }

        $bill->status = $status;
        $bill->save();

        return response()->json([
            'success' => true,
            'message' => "Đã cập nhật trạng thái phiếu sửa #$id thành '$status'",
            'data' => $bill
        ]);
    }
}
