<?php
// app/Http/Controllers/ContractController.php

namespace App\Http\Controllers\API;

use App\Models\Contract;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContractController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 10);
            $contracts = Contract::with(['owner', 'tenant', 'room','room.building'])
                ->latest()
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $contracts,
                'message' => 'Lấy danh sách hợp đồng thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách hợp đồng: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created contract.
     */
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            // Kiểm tra xem phòng đã có hợp đồng đang hoạt động chưa
            $existingActiveContract = Contract::where('room_id', $request->room_id)
                ->where(function ($query) use ($request) {
                    $query->where('status', 'active')
                        ->orWhere('status', 'pending');
                })
                ->where(function ($query) use ($request) {
                    $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                        ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                        ->orWhere(function ($q) use ($request) {
                            $q->where('start_date', '<=', $request->start_date)
                                ->where('end_date', '>=', $request->end_date);
                        });
                })
                ->first();

            if ($existingActiveContract) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phòng này đã có hợp đồng đang hoạt động trong khoảng thời gian này'
                ], 422);
            }

            // Tạo hợp đồng mới
            $contract = Contract::create([
                'owner_id' => $request->owner_id,
                'tenant_id' => $request->tenant_id,
                'room_id' => $request->room_id,
                'deposit_amount' => $request->deposit_amount,
                'rent_amount' => $request->rent_amount,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'terms_and_conditions' => $request->terms_and_conditions,
                'status' => $request->status ?? 'pending',
                'payment_history_ids' => $request->payment_history_ids ?? [],
            ]);

            // Load relationships
            $contract->load(['owner', 'tenant', 'room']);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $contract,
                'message' => 'Tạo hợp đồng thành công'
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tạo hợp đồng: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified contract.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $contract = Contract::with(['owner', 'tenant', 'room'])->find($id);

            if (!$contract) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy hợp đồng'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $contract,
                'message' => 'Lấy thông tin hợp đồng thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy thông tin hợp đồng: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified contract.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $contract = Contract::find($id);

            if (!$contract) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy hợp đồng'
                ], 404);
            }

            // Kiểm tra xem có thay đổi room_id hoặc thời gian không
            if ($request->has('room_id') || $request->has('start_date') || $request->has('end_date')) {
                $roomId = $request->room_id ?? $contract->room_id;
                $startDate = $request->start_date ?? $contract->start_date;
                $endDate = $request->end_date ?? $contract->end_date;

                $existingActiveContract = Contract::where('room_id', $roomId)
                    ->where('id', '!=', $id)
                    ->where(function ($query) {
                        $query->where('status', 'active')
                            ->orWhere('status', 'pending');
                    })
                    ->where(function ($query) use ($startDate, $endDate) {
                        $query->whereBetween('start_date', [$startDate, $endDate])
                            ->orWhereBetween('end_date', [$startDate, $endDate])
                            ->orWhere(function ($q) use ($startDate, $endDate) {
                                $q->where('start_date', '<=', $startDate)
                                    ->where('end_date', '>=', $endDate);
                            });
                    })
                    ->first();

                if ($existingActiveContract) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Phòng này đã có hợp đồng đang hoạt động trong khoảng thời gian này'
                    ], 422);
                }
            }

            $contract->update($request->validated());
            $contract->load(['owner', 'tenant', 'room']);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $contract,
                'message' => 'Cập nhật hợp đồng thành công'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật hợp đồng: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified contract.
     */
    public function destroy(string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $contract = Contract::find($id);

            if (!$contract) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy hợp đồng'
                ], 404);
            }

            // Kiểm tra nếu hợp đồng đang active thì không cho xóa
            if ($contract->status === 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa hợp đồng đang hoạt động'
                ], 422);
            }

            $contract->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Xóa hợp đồng thành công'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa hợp đồng: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get contracts by owner.
     */
    public function getByOwner(Request $request, string $ownerId): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 10);
            $contracts = Contract::with(['owner', 'tenant', 'room'])
                ->where('owner_id', $ownerId)
                ->latest()
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $contracts,
                'message' => 'Lấy danh sách hợp đồng theo chủ trọ thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách hợp đồng: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get contracts by tenant.
     */
    public function getByTenant(Request $request, string $tenantId): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 10);
            $contracts = Contract::with(['owner', 'tenant', 'room'])
                ->where('tenant_id', $tenantId)
                ->latest()
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $contracts,
                'message' => 'Lấy danh sách hợp đồng theo người thuê thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách hợp đồng: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update contract status.
     */
    public function update_status(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:active,expired,terminated,pending'
        ]);

        DB::beginTransaction();
        try {
            $contract = Contract::find($id);

            if (!$contract) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy hợp đồng'
                ], 404);
            }

            $contract->update(['status' => $request->status]);
            $contract->load(['owner', 'tenant', 'room']);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $contract,
                'message' => 'Cập nhật trạng thái hợp đồng thành công'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật trạng thái: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:active,expired,terminated,pending'
        ]);

        DB::beginTransaction();
        try {
            $contract = Contract::find($id);

            if (!$contract) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy hợp đồng'
                ], 404);
            }

            $contract->update(['status' => $request->status]);
            $contract->load(['owner', 'tenant', 'room']);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $contract,
                'message' => 'Cập nhật trạng thái hợp đồng thành công'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật trạng thái: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getByRoom(Request $request, $roomId): JsonResponse
    {
        try {
            $contract = Contract::with(['owner', 'tenant', 'room'])
                ->where('room_id', $roomId)
                ->whereIn('status', ['active', 'pending'])
                ->latest()
                ->first(); // lấy hợp đồng mới nhất trong 2 loại trạng thái này

            if (!$contract) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy hợp đồng đang hoạt động hoặc chờ duyệt cho phòng này'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $contract,
                'message' => 'Lấy hợp đồng theo phòng thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy hợp đồng: ' . $e->getMessage()
            ], 500);
        }
    }


    public function checkActiveContract($userId)
    {
        $hasActive = Contract::where('tenant_id', $userId)
            ->where('status', ['active','active'])
          //  ->whereDate('end_date', '>=', now())
            ->exists();

        return response()->json([
            'success' => true,
            'data' => $hasActive,  // true hoặc false
        ]);
    }
    public function getConTractByTenant_id($userId)
    {
        try {
            // Kiểm tra tenantId có tồn tại không
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'tenantId là bắt buộc'
                ], 400);
            }

            // Lấy danh sách requests của tenant
            $requests = Contract::where('tenant_id', $userId)
                //->where('status', 'active')
                // ->orderBy('thoi_gian', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Lấy danh sách danh sách hợp đồng thành công',
                'data' => $requests
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ], 500);
        }
    }
}
