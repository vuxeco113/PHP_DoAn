<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Compensation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompensationController extends Controller
{
    public function store(Request $request) // Bây giờ đây là Illuminate\Http\Request
    {
        try {
            $validated = $request->validate([
                'contract_id' => 'required|integer|exists:contracts,id',
                'items' => 'required|array|min:1',
                'items.*.info' => 'required|string',
                'items.*.cost' => 'required|numeric|min:0',
                'violation_terms.*' => 'nullable|string',
                'violation_details.*' => 'nullable|string',
            ]);

            // Xử lý items
            $processedItems = [];
            $totalAmount = 0;


            foreach ($validated['items'] as $index => $item) {
                $cost = floatval($item['cost']);
                $processedItems[] = [
                    'stt' => $index + 1,
                    'info' => $item['info'],
                    'cost' => $cost
                ];
                $totalAmount += $cost;
            }

            // Xử lý violations
            $processedViolations = [];
            $violationTerms = $request->input('violation_terms', []);
            $violationDetails = $request->input('violation_details', []);

            foreach ($violationTerms as $index => $term) {
                if (!empty($term)) {
                    $processedViolations[] = [
                        'term' => $term,
                        'detail' => $violationDetails[$index] ?? ''
                    ];
                }
            }
            $compensation = Compensation::create([
                'contract_id' => $validated['contract_id'],
                'date' => now(),
                'items' => $processedItems, // Laravel tự động convert sang JSON
                'violation_terms' => $processedViolations, // Laravel tự động convert sang JSON
                'total_amount' => $totalAmount,
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Tạo yêu cầu thành công',
                'data' => [
                    'id' => $compensation->id,
                    'date' => $compensation->date,
                    'items' => $compensation->items,
                    'violation_terms' => $compensation->violation_terms,
                    'total_amount' => $compensation->total_amount,
                    
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ], 500);
        }
    }
    public function getAllCompensationsByContractId($contractId)
    {
        try {
            $compensations = Compensation::where('contract_id', $contractId)->get();
            return response()->json([
                'success' => true,
                'message' => 'Lấy danh sách bồi thường thành công',
                'data' => $compensations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ], 500);
        }
    }
}
