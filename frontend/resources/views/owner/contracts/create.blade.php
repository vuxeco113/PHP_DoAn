{{-- resources/views/contracts/create.blade.php --}}
@extends('layouts.owner')

@section('title', 'Tạo Hợp Đồng Thuê Phòng')

@php
// Default terms and conditions
$defaultTerms = "**ĐIỀU KHOẢN VÀ ĐIỀU KIỆN HỢP ĐỒNG THUÊ TRỌ**

**Bên A (Bên Cho Thuê):** [Tên chủ trọ]
**Bên B (Bên Thuê):** [Tên người thuê]

Hai bên thống nhất ký kết hợp đồng thuê trọ với các điều khoản sau:

**I. TRÁCH NHIỆM CỦA BÊN A (BÊN CHO THUÊ):**
1. Bàn giao phòng trọ cho Bên B đúng theo hiện trạng đã thỏa thuận, đảm bảo các trang thiết bị cơ bản (nếu có) hoạt động bình thường.
2. Đảm bảo quyền sử dụng riêng biệt, trọn vẹn phần diện tích thuê của Bên B.
3. Cung cấp đầy đủ, kịp thời các dịch vụ đã cam kết (điện, nước, internet - nếu có) và thu phí theo đúng quy định/thỏa thuận.
4. Thực hiện sửa chữa các hư hỏng thuộc về cấu trúc của căn nhà hoặc các thiết bị do Bên A lắp đặt (trừ trường hợp hư hỏng do lỗi của Bên B).

**II. TRÁCH NHIỆM CỦA BÊN B (BÊN THUÊ):**
1. Thanh toán tiền thuê phòng và các chi phí dịch vụ khác (nếu có) đầy đủ và đúng hạn theo thỏa thuận.
2. Sử dụng phòng trọ đúng mục đích thuê (để ở), giữ gìn vệ sinh chung và tài sản trong phòng.
3. Không tự ý sửa chữa, thay đổi kết cấu phòng khi chưa có sự đồng ý của Bên A.
4. Chịu trách nhiệm đối với những hư hỏng tài sản trong phòng do lỗi của mình gây ra.

**III. ĐIỀU KHOẢN CHUNG:**
1. Mọi sửa đổi, bổ sung điều khoản của hợp đồng này phải được hai bên thỏa thuận bằng văn bản.
2. Hợp đồng này được lập thành 02 bản, mỗi bên giữ 01 bản và có giá trị pháp lý như nhau.
3. Hợp đồng có hiệu lực kể từ ngày ký.";
@endphp
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Tạo Hợp Đồng Thuê Phòng</h4>
                </div>
                <div class="card-body">
                    @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>❌ Thất bại!</strong> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <form action="{{ route('owner.contracts.create1') }}" method="POST">
                        @csrf

                        <!-- Thông tin phòng -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-info">Thông tin phòng</h5>
                                <div class="border p-3 rounded">
                                    <p><strong>Tên phòng:</strong> {{ $room['title'] }}</p>
                                    <p><strong>Mô tả:</strong> {{ $room['description'] }}</p>
                                    <p><strong>Giá thuê:</strong> {{ number_format($room['price'], 0, ',', '.') }} VNĐ</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Thông tin hợp đồng -->
                            <div class="col-md-6">
                                <h5 class="text-info mb-3">Thông tin hợp đồng</h5>

                                <!-- Owner ID (hidden) -->
                                <input type="hidden" name="owner_id" value="{{ auth()->id() }}">

                                <!-- Room ID (hidden) -->
                                <input type="hidden" name="room_id" value="{{ $room['id'] }}">

                                <!-- Chọn người thuê -->
                                <div class="mb-3">
                                    <label for="tenant_id" class="form-label">Chọn người thuê <span class="text-danger">*</span></label>
                                    <select name="tenant_id" id="tenant_id" class="form-select @error('tenant_id') is-invalid @enderror" required>
                                        <option value="">-- Chọn người thuê --</option>
                                        @foreach($requests as $request)
                                        <option value="{{ $request['user_khach_id'] }}"
                                            {{ old('tenant_id') == $request['user_khach_id'] ? 'selected' : '' }}>
                                            {{ $request['name'] }} - {{ $request['sdt'] }}
                                            @if($request['mo_ta'])
                                            ({{ Str::limit($request['mo_ta'], 30) }})
                                            @endif
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('tenant_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if(empty($requests))
                                    <div class="text-warning mt-1">
                                        Không có yêu cầu thuê phòng nào cho phòng này.
                                    </div>
                                    @endif
                                </div>

                                <!-- Số tiền đặt cọc -->
                                <div class="mb-3">
                                    <label for="deposit_amount" class="form-label">Số tiền đặt cọc (VNĐ) <span class="text-danger">*</span></label>
                                    <input type="number" name="deposit_amount" id="deposit_amount"
                                        class="form-control @error('deposit_amount') is-invalid @enderror"
                                        value="{{ old('deposit_amount', $room['price']) }}"
                                        min="0" required>
                                    @error('deposit_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Số tiền thuê -->
                                <div class="mb-3">
                                    <label for="rent_amount" class="form-label">Số tiền thuê hàng tháng (VNĐ) <span class="text-danger">*</span></label>
                                    <input type="number" name="rent_amount" id="rent_amount"
                                        class="form-control @error('rent_amount') is-invalid @enderror"
                                        value="{{ old('rent_amount', $room['price']) }}"
                                        min="0" required>
                                    @error('rent_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Ngày bắt đầu -->
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" id="start_date"
                                        class="form-control @error('start_date') is-invalid @enderror"
                                        value="{{ old('start_date', date('Y-m-d')) }}"
                                        min="{{ date('Y-m-d') }}" required>
                                    @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Ngày kết thúc -->
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                                    <input type="date" name="end_date" id="end_date"
                                        class="form-control @error('end_date') is-invalid @enderror"
                                        value="{{ old('end_date', date('Y-m-d', strtotime('+6 months'))) }}"
                                        min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                                    @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Trạng thái -->
                                <div class="mb-3">
                                    <label for="status" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Đang chờ</option>
                                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                                    </select>
                                    @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Điều khoản và điều kiện -->
                            <div class="col-md-6">
                                <h5 class="text-info mb-3">Điều khoản và điều kiện</h5>

                                <div class="mb-3">
                                    <label for="terms_and_conditions" class="form-label">Nội dung hợp đồng <span class="text-danger">*</span></label>
                                    <textarea name="terms_and_conditions" id="terms_and_conditions"
                                        class="form-control @error('terms_and_conditions') is-invalid @enderror"
                                        rows="15" required>{{ old('terms_and_conditions', $defaultTerms) }}</textarea>
                                    @error('terms_and_conditions')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Payment History IDs (hidden) -->
                                <input type="hidden" name="payment_history_ids" value="[]">
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ url()->previous() }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Quay lại
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Tạo hợp đồng
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection