<?php

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

Route::get('/', function () {
    $url = env('BACKEND_URL') . '/buildings';
    $response = Http::get(url($url));
    $buildings = [];
    if ($response->successful()) {
        $buildings = $response->json()['data'];
    }
    if (session('user'))
        return redirect('/dashboard');
    return view('home', compact('buildings'));
});


Route::get('/login', fn() => view('auth.login'));
Route::get('/register', fn() => view('auth.register'));

Route::post('/login', function (Request $request) {
    $url = env('BACKEND_URL') . '/login';
    $response = Http::withHeaders([
        'Accept' => 'application/json',
    ])->post($url, $request->all());
    if ($response->successful() && $response->json('status') == 1) {
        $data = $response->json('data');
        session([
            'user' => [
                'name' => $data['name'],
                'email' => $data['email'],
                'role' => $data['role'],
                'token' => $data['token'],
                'id' => $data['id'],
            ]
        ]);
        return redirect('/dashboard');
    } else {
        return back()->withErrors(['login' => 'Đăng nhập thất bại']);
    }
});


Route::post('/register', function (Request $request) {
    $url = env('BACKEND_URL') . '/register';
    $response = Http::withHeaders([
        'Accept' => 'application/json',
    ])->post($url, $request->all());
    if ($response->successful() && $response->json('status') == 1) {
        $data = $response->json('data'); // Lấy token, name, email
        // Lưu vào session
        session([
            'user' => [
                'name' => $data['name'],
                'email' => $data['email'],
                'role' => $data['role'],
                'token' => $data['token'],
                'id' => $data['id'],
            ]
        ]);
        return redirect('/dashboard');
    } else {
        return back()->withErrors(['register' => 'Đăng ký thất bại']);
    }
});

Route::get('/dashboard', function () {
    $user = session('user');
    if (!$user) {
        return redirect('/login');
    }
    $buildings = [];

    if ($user['role'] == 'owner') {
        $managerId = $user['id'];
        $response = Http::get(url('http://127.0.0.1:8000/api/buildings/by-manager'), [
            'managerId' => $managerId
        ]);
        if ($response->successful()) {
            $buildings = $response->json()['data'];
        }


        return view('dashboard.landlord', compact('user', 'buildings'));
    } else {
        $managerId = $user['id'];
        $response = Http::get(url('http://127.0.0.1:8000/api/buildings'));
        if ($response->successful()) {
            $buildings = $response->json()['data'];
        }
        return view('dashboard.tenant', compact('user', 'buildings'));
    }
});


Route::post('/logout', function (Request $request) {
    Http::post(env('BACKEND_URL') . '/logout');
    $request->session()->flush();
    return redirect('/');
});


// Route::get('/buildings/create', fn() => view('buildings.create'));
Route::get('/buildings/create', function () {
    $user = session('user');
    if (!$user) {
        return redirect('/login');
    }
    return view('owner.buildings.create', compact('user'));
});


Route::get('/buildings/{id}/rooms', function ($id) {
    // Gọi API backend để lấy dữ liệu rooms theo building
    $response = Http::get("http://127.0.0.1:8000/api/rooms/building/{$id}");

    if ($response->failed()) {
        abort(404, 'Không thể lấy dữ liệu phòng');
    }

    $data = $response->json();

    // Lấy đúng mảng rooms bên trong JSON
    $rooms = $data['data']['rooms'] ?? [];
    $building = $data['data']['building'] ?? null;
    // Truyền thêm thông tin requests cho từng room
    foreach ($rooms as &$room) {
        $room['pending_requests_count'] = count(array_filter($room['requests'] ?? [], function ($request) {
            return $request['status'] === 'pending';
        }));
        $room['total_requests_count'] = count($room['requests'] ?? []);
    }

    // Trả về view hiển thị danh sách rooms
    return view('owner.buildings.room', compact('rooms', 'building'));
});
Route::get('/rooms/create', function (Request $request) {
    $user = session('user');
    if (!$user) {
        return redirect('/login');
    }
    $buildingId = $request->query('buildingId');
    // dd($user);
    return view('owner.rooms.create', compact('buildingId', 'user'));
})->name('rooms.create');

Route::post('/rooms/create', function (Request $request) {
    try {
        // Chuẩn bị multipart data
        $multipart = [];
        // Thêm các field text
        $textFields = [
            'title',
            'description',
            'price',
            'area',
            'capacity',
            'buildingId',
            'status',
            'sodien',
            'ownerId',
            'latitude',
            'longitude'
        ];
        foreach ($textFields as $field) {
            if ($request->has($field) && $request->$field !== null) {
                $multipart[] = [
                    'name' => $field,
                    'contents' => $request->$field
                ];
            }
        }

        // Thêm amenities
        if ($request->has('amenities')) {
            foreach ($request->amenities as $index => $amenity) {
                if (!empty($amenity)) {
                    $multipart[] = [
                        'name' => "amenities[{$index}]",
                        'contents' => $amenity
                    ];
                }
            }
        }

        // Thêm files
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                if ($file->isValid()) {
                    $multipart[] = [
                        'name' => "images[{$index}]",
                        'contents' => fopen($file->getRealPath(), 'r'),
                        'filename' => $file->getClientOriginalName()
                    ];
                }
            }
        }
        // Gọi API sử dụng Laravel HTTP Client
        $response = Http::asMultipart()
            ->withHeaders(['Accept' => 'application/json'])
            ->post('http://127.0.0.1:8000/api/rooms/create', $multipart);

        $result = $response->json();

        if ($response->successful() && $result['success']) {
            return back()->with('success', $result['message']);
        } else {
            $errorMessage = $result['message'] ?? 'Có lỗi xảy ra';
            if (isset($result['errors'])) {
                $errorMessage .= ': ' . implode(', ', Arr::flatten($result['errors']));
            }
            return back()->with('error', $errorMessage);
        }
    } catch (\Exception $e) {
        return back()->with('error', 'Lỗi kết nối: ' . $e->getMessage());
    }
})->name('rooms.create.store');



Route::prefix('tenant')->group(function () {
    Route::get('/buildings/{id}/rooms', function ($id) {
        // Gọi API backend để lấy dữ liệu rooms theo building
        $response = Http::get("http://127.0.0.1:8000/api/rooms/building/{$id}");

        if ($response->failed()) {
            abort(404, 'Không thể lấy dữ liệu phòng');
        }
        $data = $response->json();
        // Lấy đúng mảng rooms bên trong JSON
        $rooms = $data['data']['rooms'] ?? [];
        $building = $data['data']['building'] ?? null;
        // Truyền thêm thông tin requests cho từng room
        foreach ($rooms as &$room) {
            $room['pending_requests_count'] = count(array_filter($room['requests'] ?? [], function ($request) {
                return $request['status'] === 'pending';
            }));
            $room['total_requests_count'] = count($room['requests'] ?? []);
        }
        // Trả về view hiển thị danh sách rooms
        return view('tenant.room', compact('rooms', 'building'));
    });
    Route::get('/rooms/detail/{id}', function ($id) {

        $user = session('user');
        if (!$user) {
            return redirect('/login');
        }
        $response = Http::get("http://127.0.0.1:8000/api/rooms/{$id}");
        $response1 = Http::get("http://127.0.0.1:8000/api/contracts/user/{$user['id']}/has-active-contract");
        $room = $response->json();
        $kq = $response1->json()['data'];
        //dd($kq);
        return view('tenant.roomDetail', compact('room', 'user', 'kq'));
    });
    Route::get('/requests/create/{id}', function ($id) {
        $user = session('user');
        if (!$user)
            return redirect('/login');

        $roomId = $id;
        return view('tenant.requests.create', compact('roomId', 'user'));
    });
    Route::post('/requests/create', function (Request $request) {
        $user = session('user');
        if (!$user)
            return redirect('/login');

        $url = env('BACKEND_URL') . '/requests/create';

        // Gửi dữ liệu qua API
        $payload = [
            'user_khach_id' => $user['id'],
            'room_id' => $request->room_id,
            'loai_request' => $request->loai_request,
            'name' => $request->name,
            'sdt' => $request->sdt,
            'mo_ta' => $request->mo_ta,
        ];

        $response = Http::post($url, $payload);

        if ($response->successful() && $response->json('success')) {
            return redirect('/dashboard')->with('success', 'Gửi yêu cầu thành công!');
        } else {
            return back()->with('error', $response->json('message') ?? 'Gửi thất bại');
        }
    })->name('tenant.requests.create');
    Route::get('/myRoom', function () {
        $user = session('user');
        if (!$user)
            return redirect('/login');

        $url = env('BACKEND_URL') . '/rooms/owner/' . $user['id'];
        $url1 = env('BACKEND_URL') . '/contracts/getConTractByTenant_id/' . $user['id'];
        $url2 = env('BACKEND_URL') . '/requests/getAllRequestByTetantId/' . $user['id'];
        $response = Http::get($url);
        $room = $response->json()['data'] ?? null;
        $response1 = Http::get($url1);
        $contracts = $response1->json()['data'];
        $response2 = Http::get($url2);
        $requests = $response2->json()['data'];

        //dd($contracts);
        return view('tenant.myRoom', compact('room', 'user', 'contracts', 'requests'));
    })->name('tenant.myRoom');
    Route::post('/requests/create_SuaChua', function (Request $request) {
        $user = session('user');
        if (!$user)
            return redirect('/login');

        $url = env('BACKEND_URL') . '/requests/create';

        // Gửi dữ liệu qua API
        $payload = [
            'user_khach_id' => $user['id'],
            'room_id' => $request->room_id,
            'loai_request' => $request->loai_request,
            'name' => $request->name,
            'sdt' => $request->sdt,
            'mo_ta' => $request->mo_ta,
        ];
        // dd($payload);
        $response = Http::post($url, $payload);

        if ($response->successful() && $response->json('success')) {
            return redirect('/tenant/myRoom')->with('success', 'Gửi yêu cầu sửa chữa thành công!');
        } else {
            return back()->with('error', $response->json('message') ?? 'Gửi thất bại');
        }
    })->name('tenant.requests.create_SuaChua');
    Route::post('/requests/create_TraPhong', function (Request $request) {
        $user = session('user');
        if (!$user)
            return redirect('/login');

        $url = env('BACKEND_URL') . '/requests/create';

        // Gửi dữ liệu qua API
        $payload = [
            'user_khach_id' => $user['id'],
            'room_id' => $request->room_id,
            'loai_request' => $request->loai_request,
            'name' => $request->name,
            'sdt' => $request->sdt,
            'mo_ta' => $request->mo_ta,
        ];
        // dd($payload);
        $response = Http::post($url, $payload);

        if ($response->successful() && $response->json('success')) {
            return redirect('/tenant/myRoom')->with('success', 'Gửi yêu cầu sửa chữa thành công!');
        } else {
            return back()->with('error', $response->json('message') ?? 'Gửi thất bại');
        }
    })->name('tenant.requests.create_TraPhong');
    Route::get('/thanhtoan', function () {
        $user = session('user');
        if (!$user) {
            return redirect('/login');
        }

        $url = env('BACKEND_URL') . '/bills/getAllBillsByTantentId/' . $user['id'];
        $response = Http::get($url);
        $invoices = $response->json()['data'];


        $url = env('BACKEND_URL') . '/phieu-sua/getAllPhieuSuaByTenantId/' . $user['id'];
        $response1 = Http::get($url);
        $repairs = $response1->json()['data'];


        return View('tenant.thanhtoan', compact('user', 'invoices', 'repairs'));
    })->name('tenant.thanhtoan');
    Route::get('bill/Detail/{id}', function (Request $request, $id) {
        $user = session('user');
        if (!$user) {
            return redirect('/login');
        }

        if ($request->has('resultCode') && $request->resultCode == 0) {
            // Gọi API backend để update status
            $inUrl = "http://127.0.0.1:8000/api/bills/updateStatus/{$id}/paid";
            Http::post($inUrl);
        }



        $url = env('BACKEND_URL') . '/bills/getBillDetail/' . $id;
        $response = Http::get($url);
        $bill = $response->json()['data'];
        return View('tenant.bills.thanhtoan', compact('user', 'bill'));
    })->name('tenant.bill.thanhtoan');
    Route::post('bill/checkout/{id}', function ($id, Request $request) {
        $user = session('user');
        if (!$user) {
            return redirect('/login');
        }
        $backendUrl = env('BACKEND_URL') . '/bills/getBillDetail/' . $id;
        $response = Http::get($backendUrl);
        if (!$response->successful()) {
            return back()->with('error', 'Không thể lấy thông tin hóa đơn!');
        }
        $bill = $response->json()['data'];
        $backUrl = 'http://127.0.0.1:8001/tenant/bill/Detail/' . $bill['id'];
        $checkoutUrl = env('BACKEND_URL') . '/momo-payment';
        $momoResponse = Http::post($checkoutUrl, [
            'amount' => $bill['sumPrice'],
            'bill_id' => $bill['id'],
            'backUrl' => $backUrl,

        ]);
        if (!$momoResponse->successful()) {
            return back()->with('error', 'Lỗi tạo yêu cầu thanh toán MoMo!');
        }
        $json = $momoResponse->json();
        if (!empty($json['payUrl'])) {
            return redirect()->away($json['payUrl']);
        }
        return back()->with('error', 'Không nhận được đường dẫn thanh toán!');
    })->name('tenant.bill.checkout');
    Route::get('phieu-sua/Detail/{id}', function (Request $request, $id) {
        $user = session('user');
        if (!$user) {
            return redirect('/login');
        }
        if ($request->has('resultCode') && $request->resultCode == 0) {
            // Gọi API backend để update status
            $inUrl = "http://127.0.0.1:8000/api/phieu-sua/updateStatus/{$id}/completed";
            Http::post($inUrl);
        }

        $url = env('BACKEND_URL') . '/phieu-sua/getPhieuSuaById/' . $id;
        $response = Http::get($url);
        $phieuSua = $response->json()['data'];
        return View('tenant.phieu-sua.detail', compact('user', 'phieuSua'));
    })->name('tenant.phieu-sua.detail');







    Route::post('phieu-sua/checkout/{id}', function ($id, Request $request) {
        $user = session('user');
        if (!$user) {
            return redirect('/login');
        }
        $backendUrl = env('BACKEND_URL') . '/phieu-sua/getPhieuSuaById/' . $id;
        // dd( $backendUrl);
        $response = Http::get($backendUrl);
        if (!$response->successful()) {
            return back()->with('error', 'Không thể lấy thông tin hóa đơn!');
        }
        $bill = $response->json()['data'];
        $backUrl = 'http://127.0.0.1:8001/tenant/phieu-sua/Detail/' . $bill['id'];
        $checkoutUrl = env('BACKEND_URL') . '/momo-payment';
        $momoResponse = Http::post($checkoutUrl, [
            'amount' => $bill['tongTien'],
            'bill_id' => $bill['id'],
            'backUrl' => $backUrl,

        ]);
        if (!$momoResponse->successful()) {
            return back()->with('error', 'Lỗi tạo yêu cầu thanh toán MoMo!');
        }
        $json = $momoResponse->json();
        if (!empty($json['payUrl'])) {
            return redirect()->away($json['payUrl']);
        }
        return back()->with('error', 'Không nhận được đường dẫn thanh toán!');
    })->name('tenant.phieu-sua.checkout');
});


Route::prefix('owner')->group(function () {
    Route::get('/contracts/{id}/create', function ($id) {
        $user = session('user');
        if (!$user) {
            return redirect('/login');
        }
        // dd($user);  
        $roomId = $id;
        $response = Http::get("http://127.0.0.1:8000/api/rooms/{$id}");
        $room = $response->json()['data'];

        $response1 = Http::get("http://127.0.0.1:8000/api/requests/room/{$id}");
        $requests = $response1->json()['data'];

        return view('owner.contracts.create', compact('user', 'roomId', 'room', 'requests'));
    });
    Route::post('/contracts/create1', function (Request $request) {
        $user = session('user');
        if (!$user) {
            return redirect('/login');
        }

        $url = env('BACKEND_URL') . '/contracts/create';
        $url1 = 'http://127.0.0.1:8000/api/rooms/updateStatus/' . $request->room_id;
        $url2 = 'http://127.0.0.1:8000/api/rooms/updateOwnerId/' . $request->room_id;
        // Gửi dữ liệu qua API
        $payload1 = [
            'status' => 'pending',
        ];
        $payload2 = [
            'ownerId' => $request->tenant_id,
        ];
        $payload = [
            'owner_id' => $user['id'], // chủ trọ đang đăng nhập
            'tenant_id' => $request->tenant_id,
            'room_id' => $request->room_id,
            'deposit_amount' => (float) $request->deposit_amount,
            'rent_amount' => (float) $request->rent_amount,
            'start_date' => $request->start_date . ' 00:00:00',
            'end_date' => $request->end_date . ' 00:00:00',
            'terms_and_conditions' => $request->terms_and_conditions,
            'status' => $request->status ?? 'pending',
            'payment_history_ids' => [],
        ];
        $response = Http::post($url, $payload);
        if ($request->status === 'active') {
            $payload1 = [
                'status' => 'rented',
            ];
        }
        $response1 = Http::post($url1, $payload1);
        $response2 = Http::post($url2, $payload2);
        $success1 = $response->successful()  && data_get($response->json(), 'success') === true;
        $success2 = $response1->successful() && data_get($response1->json(), 'success') === true;
        $success3 = $response2->successful() && data_get($response2->json(), 'success') === true;

        if ($success1 && $success2 && $success3) {
            return back()->with('success', 'Tạo hợp đồng thành công!');
        }

        // ✅ Trường hợp thất bại: lấy lỗi cụ thể để debug
        $msg1 = data_get($response->json(), 'message', 'Lỗi tạo hợp đồng');
        $msg2 = data_get($response1->json(), 'message', 'Lỗi cập nhật trạng thái phòng');
        $msg3 = data_get($response2->json(), 'message', 'Lỗi cập nhật ownerId phòng');

        return back()->with('error', "Tạo hợp đồng thất bại: $msg1 | $msg2 | $msg3");
    })->name('owner.contracts.create1');
    Route::get('/rooms/detail/{id}', function ($id) {

        $user = session('user');
        if (!$user) {
            return redirect('/login');
        }
        $response = Http::get("http://127.0.0.1:8000/api/rooms/{$id}");
        $room = $response->json();
        return view('owner.rooms.roomDetail', compact('room', 'user'));
    });

    Route::get('/compensations/create',  function (Request $request) {

        $user = session('user');
        if (!$user) {
            return redirect('/login');
        }
        $roomId = $request->query('room_id');


        $response = Http::get("http://127.0.0.1:8000/api/contracts/getContactById/" . $request->query('room_id'));
        $contract = json_decode(json_encode($response->json()['data']));
        //    / dd($contract);

        $violationTemplates = [
            'Vi phạm điều khoản về bảo trì và sửa chữa tài sản',
            'Vi phạm điều khoản về thanh toán tiền thuê',
            'Vi phạm điều khoản về sử dụng tài sản đúng mục đích',
            'Vi phạm điều khoản về bảo quản tài sản',
            'Vi phạm điều khoản về thời hạn hợp đồng',
            'Vi phạm điều khoản về chấm dứt hợp đồng trước hạn',
            'Vi phạm điều khoản về an ninh trật tự',
            'Vi phạm điều khoản về vệ sinh môi trường',
            'Gây hư hại tài sản không thuộc phạm vi sử dụng thông thường',
            'Tự ý thay đổi kết cấu công trình không được sự đồng ý'
        ];
        return view('owner.compensations.create', compact('contract', 'violationTemplates', 'roomId'));
    })->name('owner.compensations.create');

    Route::post('/compensations',  function (Request $request) {

        $user = session('user');
        if (!$user) {
            return redirect('/login');
        }
        // dd($user);

        $url = env('BACKEND_URL') . '/compensations/create';


        $payload = [
            'contract_id' => $request->input('contract_id'), // chủ trọ đang đăng nhập
            'items' => $request->input('items'),
            'violation_terms' => $request->input('violation_terms'),
            'date' => $request->input('payment_date'),
        ];


        $url1 = 'http://127.0.0.1:8000/api/rooms/updateStatus/' . $request->query('room_id');
        // Gửi dữ liệu qua API
        $payload1 = [
            'status' => 'available',
        ];

        $url2 = 'http://127.0.0.1:8000/api/contracts/update/' . $request->input('contract_id');
        // Gửi dữ liệu qua API
        $payload2 = [
            'status' => 'expired',
        ];


        $response = Http::post($url, $payload);
        $response1 = Http::post($url1, $payload1);
        $response2 = Http::post($url2, $payload2);

        if ($response2->successful()  && $response1->successful()  && $response->successful()  && $response->json('success')) {
            return redirect()->to("/dashboard")
                ->with('success', 'Thanh lý hợp đồng thành công!');
        } else {
            return back()->with('error', $response->json('message') ?? 'Thanh lý hợp đồng thất bại');
        }
    })->name('owner.compensations.store');

    Route::get('/thanhtoan', function () {
        $user = session('user');
        if (!$user) {
            return redirect('/login');
        }
        $buildings = [];
        $managerId = $user['id'];
        $response = Http::get(url('http://127.0.0.1:8000/api/buildings/by-manager'), [
            'managerId' => $managerId
        ]);
        if ($response->successful()) {
            $buildings = $response->json()['data'];
        }

        return view('owner.bills.index', compact('user', 'buildings'));
    })->name('owner.thanhtoan.index');

    Route::get('/thanhtoan/building/{id}', function ($id) {
        $user = session('user');
        if (!$user) {
            return redirect('/login');
        }

        $response = Http::get("http://127.0.0.1:8000/api/bills/rooms-without-bill-this-month/{$id}");
        $unpaidRooms = $response->json()['data'];

        $response = Http::get("http://127.0.0.1:8000/api/bills/getPendingBillCurrentMonth/{$id}");
        $pendingInvoices = $response->json()['data'];

        $response = Http::get("http://127.0.0.1:8000/api/bills/getPaidBillCurrentMonth/{$id}");
        $paidInvoices = $response->json()['data'];

        return view('owner.bills.building', compact(
            'unpaidRooms',
            'pendingInvoices',
            'paidInvoices'
        ));
    })->name('owner.thanhtoan.building');
    Route::post('owner.thanhtoan.create', function (Request $request) {
        $user = session('user');
        if (!$user) {
            return redirect('/login');
        }
        $url = env('BACKEND_URL') . '/bills/create';
        $url1 = env('BACKEND_URL') . '/rooms/update_sodiencu/' . $request->input('roomId');
        $payload1 = [
            'sodien' => (int)$request->input('sodienMoi'),
        ];




        $payload = [
            'khachThueId' => (int) $request->input('khachThueId'), // chủ trọ đang đăng nhập
            'ownerId' => $user['id'],
            'roomId' => (int)$request->input('roomId'),
            'priceDien' => (int) $request->input('priceDien'),
            'priceRoom' => (int) $request->input('priceRoom'),
            'priceWater' => (int)$request->input('priceWater'),
            'sumPrice' => (int)$request->input('sumPrice'),
            'soNguoi' => (int) $request->input('soNguoi'),
            'sodienCu' => (int)$request->input('sodienCu'),
            'sodienMoi' => (int) $request->input('sodienMoi'),
            'status' => $request->input('status'),
            'amenitiesPrice' => (int)$request->input('amenitiesPrice'),
            'thangNam' => $request->input('thangNam'),

        ];
        //  dd($payload);

        $response = Http::post($url, $payload);
        $response1 = Http::post($url1, $payload1);
        if ($response->successful()  && $response1->successful()  && $response->json('success')) {
            return back()->with('success', 'Tạo hóa đơn thành công thành công!');
        } else {
            return back()->with('error', $response->json('message') ?? 'Tạo hóa đơn thất bại');
        }
    })->name('owner.thanhtoan.create');
    Route::post('owner.thanhtoan.update', function (Request $request) {
        $user = session('user');
        if (!$user) {
            return redirect('/login');
        }

        $url1 = env('BACKEND_URL') . '/bills/update_status/' . $request->input('status');
        $payload1 = [
            'status' => 'paid',
        ];


        $response1 = Http::post($url1, $payload1);
        // dd($url1);
        if ($response1->successful()  && $response1->json('success')) {
            return back()->with('success', 'Cập nhật hóa đơn thành công thành công!');
        } else {
            return back()->with('error', $response1->json('message') ?? 'Cập nhật hóa đơn thất bại');
        }
    })->name('owner.thanhtoan.update');

    Route::get('/contracts/index', function () {
        $user = session('user');
        if (!$user) {
            return redirect('/login');
        }
        $response = http::get("http://127.0.0.1:8000/api/contracts/index");
        $contracts2 = $response->json()['data']['data'];
        $response = http::get("http://127.0.0.1:8000/api/buildings/index");
        $buildings2 = $response->json()['data'];

        return view('owner.contracts.index', compact('contracts2', 'buildings2'));
    })->name('owner.contracts.index');

    Route::get('/phieu-sua/create/{requestId}/{roomId}/{tenantId}', function ($requestId, $roomId, $tenantId) {
        $user = session('user');
        if (!$user) {
            return redirect('/login');
        }
        $requestId1 = $requestId;
        $roomId1 = $roomId;
        $tenantId1 = $tenantId;

        return view('owner.phieu-sua.create', compact('user', 'requestId1', 'roomId1', 'tenantId1'));
    })->name('owner.phieu-sua.create');
    Route::post('/phieu-sua/create', function (Request $request) {
        $user = session('user');
        if (!$user) {
            return redirect('/login');
        }
        $url1 = env('BACKEND_URL') . '/phieu-sua/create';
        $payload1 = [
            'faultSource' => $request->input('faultSource'),
            'items' => $request->input('items'),
            'requestId' => (int)$request->input('requestId'),
            'roomId' => (int)$request->input('roomId'),
            'tenantId' => (int)$request->input('tenantId'),
            'tongTien' => $request->input('tongTien'),
            'status' => $request->input('status'),
            'ngaySua' => $request->input('ngaySua'),
        ];
        $response1 = Http::post($url1, $payload1);
        if ($response1->successful()  && $response1->json('success')) {
            return redirect()->to("/dashboard")
                ->with('success', 'Tạo phiếu sửa chữa thành công!');
        } else {
            return back()->with('error', $response1->json('message') ?? 'Tạo phiếu sửa chữa thất bại');
        }
    })->name('owner.phieu-sua.store');

    Route::get('/phieu-sua/{roomId}', function ($roomId) {
        $user = session('user');
        if (!$user) {
            return redirect('/login');
        }

        // $url = env('BACKEND_URL') . '/bills/getAllBillsByTantentId/' . $user['id'];
        // $response = Http::get($url);
        // $invoices = $response->json()['data'];


        $url = env('BACKEND_URL') . '/phieu-sua/getAllPhieuSuaByRoomId/' . $roomId;
        $response1 = Http::get($url);
        $repairs = $response1->json()['data'];


        return View('owner.phieu-sua.indexByRoom', compact('user', 'repairs'));
    })->name('owner.phieu-sua.indexByRoom');
    Route::get('/phieu-sua-detail/{id}', function ($id) {
        $user = session('user');
        if (!$user) {
            return redirect('/login');
        }

        $url = env('BACKEND_URL') . '/phieu-sua/getPhieuSuaById/' . $id;
        $response1 = Http::get($url);
        $phieuSua = $response1->json()['data'];
        // dd($phieuSua);
        return View('owner.phieu-sua.update', compact('user', 'phieuSua'));
    })->name('owner.phieu-sua.detail');
    Route::post('/phieu-sua/update', function (Request $request) {
        $user = session('user');
        if (!$user) {
            return redirect('/login');
        }
        $url1 = env('BACKEND_URL') . '/phieu-sua/update/' . $request->input('id');
        $payload1 = [
            'faultSource' => $request->input('faultSource'),
            'items' => $request->input('items'),
            'tongTien' => $request->input('tongTien'),
            'status' => $request->input('status'),
            'ngaySua' => $request->input('ngaySua'),
        ];
        $response1 = Http::post($url1, $payload1);
        //dd($url1);
        if ($response1->successful()  && $response1->json('success')) {
            return redirect()->to("/owner/phieu-sua/" . $request->input('roomId'))
                ->with('success', 'Cập nhật phiếu sửa chữa thành công!');
        } else {
            return back()->with('error', $response1->json('message') ?? 'Cập nhật phiếu sửa chữa thất bại');
        }
    })->name('owner.phieu-sua.update');
});
