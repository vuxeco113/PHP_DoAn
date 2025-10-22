<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BuildingController;
use App\Http\Controllers\API\RoomController;
use App\Http\Controllers\API\ContractController;
use App\Http\Controllers\API\RequestController;
use App\Http\Controllers\API\CompensationController;
use App\Http\Controllers\API\BillController;
use App\Http\Controllers\API\PhieuSuaController;
// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
Route::post("register", [AuthController::class, "register"]);
Route::post("login", [AuthController::class, "login"]);

Route::get('/buildings', [BuildingController::class, 'index']);
Route::get('/buildings/by-manager', [BuildingController::class, 'buildings_ByID']);
Route::post('/buildings/create', [BuildingController::class, 'store']);
// Route::middleware('auth:sanctum')->group(function () {
//     Route::resource("buildings", BuildingController::class);
// }); 

Route::prefix('buildings')->group(function () {
    Route::get('/index', [BuildingController::class, 'index']);
   
});


Route::prefix('rooms')->group(function () {
    // Routes cụ thể đặt TRƯỚC
    Route::get('/building/{buildingId}', [RoomController::class, 'getRoomsByBuildingId']);// lấy tất cả phòng theo ID building
    Route::get('/KiemTraTrangThaiPhong/{buildingId}', [RoomController::class, 'KiemTraTrangThaiPhong']);
    Route::post('/updateStatus/{roomId}', [RoomController::class, 'update_status']);
    Route::post('/updateOwnerId/{roomId}', [RoomController::class, 'update_ownerId']);
    Route::post('/update_sodiencu/{roomId}', [RoomController::class, 'update_sodiencu']);
    // Routes dynamic đặt SAU
    Route::get('/{id}', [RoomController::class, 'show']);// lấy thông tin phòng theo ID
    Route::get('/', [RoomController::class, 'index']); // Nếu có
    Route::post('/create', [RoomController::class, 'store']); // Nếu có
    route::get('/owner/{ownerId}', [RoomController::class, 'getRoomByUserId']); // Lấy phòng theo ownerId    1111
});


Route::prefix('requests')->group(function () {
    Route::get('/room/{roomId}', [RequestController::class, 'getByRoom']);
    Route::get('/room/thue_phong/{roomId}', [RequestController::class, 'getByRoom_ThuePhong']);
    Route::post('/create', [RequestController::class, 'store']);
    Route::get('/getAllRequestByTetantId/{tenantId}', [RequestController::class, 'getAllRequestByTetantId']);//111111
    Route::post('/update/{id}/{status}', [RequestController::class, 'updateStatus']);//11111
  
});

Route::prefix('contracts')->group(function () {
    Route::post('/create', [ContractController::class, 'store']);
    Route::post('update/{id}', [ContractController::class, 'update_status']);
    Route::get('getContactById/{id}', [ContractController::class, 'getByRoom']);
    Route::get('/user/{id}/has-active-contract', [ContractController::class, 'checkActiveContract']);
    Route::get('/index', [ContractController::class, 'index']);
    Route::get('/getConTractByTenant_id/{userId}', [ContractController::class, 'getConTractByTenant_id']);   //11111
});

Route::prefix('compensations')->group(function () {
    Route::post('/create', [CompensationController::class, 'store']);
    Route::get('/getAllCompensationsByContractId/{contractId}', [CompensationController::class, 'getAllCompensationsByContractId']);
});

Route::prefix('bills')->group(function () {
    Route::get('/rooms-without-bill-this-month/{buildingId}', [BillController::class, 'getRoomsWithoutBillThisMonth']);
    Route::get('/getPendingBillCurrentMonth/{buildingId}', [BillController::class, 'getPendingBillCurrentMonth']);
    Route::get('/getPaidBillCurrentMonth/{buildingId}', [BillController::class, 'getPaidBillCurrentMonth']);
    Route::post('/create', [BillController::class, 'store']);
    Route::post('/update_status/{roomId}', [BillController::class, 'update_status']);
    Route::get('/getAllBillsByTantentId/{tenantId}', [BillController::class, 'getAllBillsByTantentId']);//22222
    Route::get('/getBillDetail/{id}', [BillController::class, 'getBillDetail']);
    Route::post('/updateStatus/{id}/{status}', [BillController::class, 'updateStatus']);
});

Route::prefix('phieu-sua')->group(function () {
    Route::post('/create', [PhieuSuaController::class, 'store']);
    Route::get('/getAllPhieuSuaByTenantId/{tenantId}', [PhieuSuaController::class, 'getAllPhieuSuaByTenantId']);
    Route::post('/update/{id}', [PhieuSuaController::class, 'update']);////33333
    Route::get('/getPhieuSuaById/{tenantId}', [PhieuSuaController::class, 'getPhieuSuaById']);///3333
    Route::get('/getAllPhieuSuaByRoomId/{roomId}', [PhieuSuaController::class, 'getAllPhieuSuaByRoomId']);//3333
    Route::post('/updateStatus/{id}/{status}', [PhieuSuaController::class, 'updateStatus']);
});


Route::post('/momo-payment', [App\Http\Controllers\API\CheckoutController::class, 'momo_payment']);