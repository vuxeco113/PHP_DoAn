@extends('layouts.owner')


@section('content')

<div class="container py-4">
    <h1 class="mb-4"><i class="bi bi-receipt"></i> Quản lý Hóa đơn Tháng {{ date('m/Y') }}</h1>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <ul class="nav nav-tabs" id="invoiceTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="unpaid-rooms-tab" data-bs-toggle="tab" data-bs-target="#unpaid-rooms" type="button">
                <i class="bi bi-house-door"></i> Phòng chưa có hóa đơn
                <span class="badge bg-warning text-dark">{{ count($unpaidRooms) }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pending-invoices-tab" data-bs-toggle="tab" data-bs-target="#pending-invoices" type="button">
                <i class="bi bi-clock-history"></i> Hóa đơn chưa thanh toán
                <span class="badge bg-danger">{{ count($pendingInvoices) }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="paid-invoices-tab" data-bs-toggle="tab" data-bs-target="#paid-invoices" type="button">
                <i class="bi bi-check-circle"></i> Hóa đơn đã thanh toán
                <span class="badge bg-success">{{ count($paidInvoices) }}</span>
            </button>
        </li>
    </ul>

    <div class="tab-content" id="invoiceTabsContent">
        <!-- Tab 1: Phòng chưa có hóa đơn -->
        <div class="tab-pane fade show active" id="unpaid-rooms" role="tabpanel">
            <div class="row">
                @forelse($unpaidRooms as $room)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card invoice-card h-100">
                        @if(!empty($room['imageUrls']))
                        <img src="{{ $room['imageUrls'][0] }}" class="card-img-top room-img" alt="{{ $room['title'] }}">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $room['title'] }}</h5>
                            <p class="card-text text-muted small">{{ Str::limit($room['description'], 100) }}</p>
                            <div class="mb-2">
                                <span class="badge bg-info">{{ $room['area'] }}m²</span>
                                <span class="badge bg-secondary">Sức chứa: {{ $room['capacity'] }} người</span>
                            </div>
                            <p class="h5 text-primary mb-3">{{ number_format($room['price']) }} VNĐ</p>
                            <div class="mb-3">
                                <strong>Tiện nghi:</strong><br>
                                @foreach($room['amenities'] as $amenity)
                                <span class="badge bg-light text-dark">{{ $amenity }}</span>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#createInvoiceModal{{ $room['id'] }}">
                                <i class="bi bi-plus-circle"></i> Tạo hóa đơn
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Modal tạo hóa đơn -->
                <div class="modal fade" id="createInvoiceModal{{ $room['id'] }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Tạo hóa đơn - {{ $room['title'] }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ Route('owner.thanhtoan.create') }}" method="POST">
                                @csrf
                                <div class="modal-body">
                                    <input type="hidden" name="roomId" value="{{ $room['id'] }}">
                                    <input type="hidden" name="khachThueId" value="{{ $room['ownerId'] }}">
                                    <input type="hidden" name="ownerId" value="{{ auth()->id() }}"> {{-- ID chủ trọ --}}

                                    @php
                                    $currentMonthYear = now()->format('m/Y'); // Lấy tháng/năm hiện tại
                                    @endphp

                                    <div class="mb-3">
                                        <label class="text">Hóa đơn tháng</label>
                                        <input type="text" name="thangNam" class="form-control" value="{{ $currentMonthYear }}" readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label class="text">Số điện cũ</label>
                                        <input type="number" id="sodienCu{{ $room['id'] }}" name="sodienCu" class="form-control" value="{{ $room['sodien'] }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="text">Số điện mới</label>
                                        <input type="number" id="sodienMoi{{ $room['id'] }}" name="sodienMoi" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="text">Số người ở</label>
                                        <input type="number" id="soNguoi{{ $room['id'] }}" name="soNguoi" class="form-control" value="{{ $room['capacity'] }}" min="1" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="text">Giá Điện</label>
                                        <input type="number" id="priceDien{{ $room['id'] }}" name="priceDien" class="form-control" value="5000" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="text">Giá Nước</label>
                                        <input type="number" id="priceWater{{ $room['id'] }}" name="priceWater" class="form-control" value="50000" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="text">Giá Phòng</label>
                                        <input type="number" id="priceRoom{{ $room['id'] }}" name="priceRoom" class="form-control" value="{{ $room['price'] }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="text">Phí dịch vụ</label>
                                        <input type="number" id="amenitiesPrice{{ $room['id'] }}" name="amenitiesPrice" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="text">Tổng tiền</label>
                                        <input type="number" id="sumPrice{{ $room['id'] }}" name="sumPrice" class="form-control" readonly required>
                                    </div>


                                    <div class="mb-3">
                                        <label class="text">Trạng thái hóa đơn</label>
                                        <select name="status" class="form-select" required>
                                            <option value="pending" selected>Chưa thanh toán</option>
                                            <option value="paid">Đã thanh toán</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                    <button type="submit" class="btn btn-primary">Tạo hóa đơn</button>
                                </div>
                            </form>

                            {{-- Script tính tổng tiền tự động --}}
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const roomId = "{{ $room['id'] }}";
                                    const inputs = [
                                        `priceRoom${roomId}`,
                                        `priceWater${roomId}`,
                                        `priceDien${roomId}`,
                                        `soNguoi${roomId}`,
                                        `sodienCu${roomId}`,
                                        `sodienMoi${roomId}`,
                                        `amenitiesPrice${roomId}`
                                    ];

                                    function calcTotal() {
                                        const priceRoom = parseFloat(document.getElementById(`priceRoom${roomId}`).value) || 0;
                                        const priceWater = parseFloat(document.getElementById(`priceWater${roomId}`).value) || 0;
                                        const priceDien = parseFloat(document.getElementById(`priceDien${roomId}`).value) || 0;
                                        const soNguoi = parseFloat(document.getElementById(`soNguoi${roomId}`).value) || 0;
                                        const sodienCu = parseFloat(document.getElementById(`sodienCu${roomId}`).value) || 0;
                                        const sodienMoi = parseFloat(document.getElementById(`sodienMoi${roomId}`).value) || 0;
                                        const amenitiesPrice = parseFloat(document.getElementById(`amenitiesPrice${roomId}`).value) || 0;

                                        const tongTien = priceRoom + (priceWater * soNguoi) + (priceDien * (sodienMoi - sodienCu)) + amenitiesPrice;
                                        document.getElementById(`sumPrice${roomId}`).value = tongTien > 0 ? Math.round(tongTien) : 0;
                                    }

                                    inputs.forEach(inputId => {
                                        const el = document.getElementById(inputId);
                                        if (el) el.addEventListener('input', calcTotal);
                                    });
                                });
                            </script>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Tất cả các phòng đã có hóa đơn trong tháng này.
                    </div>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Tab 2: Hóa đơn chưa thanh toán -->
        <div class="tab-pane fade" id="pending-invoices" role="tabpanel">
            <div class="row">
                @forelse($pendingInvoices as $invoice)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card invoice-card h-100 border-warning">
                        <div class="card-header bg-warning">
                            <h6 class="mb-0">
                                <i class="bi bi-receipt"></i> Hóa đơn #{{ $invoice['id'] }}
                                <span class="badge bg-danger float-end">Chưa thanh toán</span>
                            </h6>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $invoice['room']['title'] }}</h5>
                            <p class="text-muted mb-2"><strong>Tháng:</strong> {{ $invoice['thangNam'] }}</p>
                            <p class="mb-1"><strong>Khách thuê:</strong> {{ $invoice['tenant']['name'] }}</p>
                            <p class="mb-1"><strong>Email:</strong> {{ $invoice['tenant']['email'] }}</p>
                            <hr>
                            <div class="mb-2">
                                <div class="d-flex justify-content-between">
                                    <span>Tiền phòng:</span>
                                    <strong>{{ number_format($invoice['priceRoom']) }} VNĐ</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Tiền điện ({{ $invoice['sodienCu'] }} → {{ $invoice['sodienMoi'] }}):</span>
                                    <strong>{{ number_format($invoice['priceDien']) }} VNĐ</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Tiền nước:</span>
                                    <strong>{{ number_format($invoice['priceWater']) }} VNĐ</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Tiền dịch vụ:</span>
                                    <strong>{{ number_format($invoice['amenitiesPrice']) }} VNĐ</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Số người:</span>
                                    <strong>{{ $invoice['soNguoi'] }}</strong>
                                </div>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <h5>Tổng cộng:</h5>
                                <h5 class="text-danger">{{ number_format($invoice['sumPrice']) }} VNĐ</h5>
                            </div>
                            <form action="{{ Route('owner.thanhtoan.update') }}" method="POST">
                                @csrf
                                @method('POST')
                                <input type="hidden" name="status" value="{{ $invoice['id'] }}">
                                <button type="submit" class="btn btn-primary w-100" onclick="return confirm('Xác nhận đã thanh toán?')">
                                    <i class="bi bi-check-circle"></i> Đánh dấu đã thanh toán
                                </button>
                            </form>
                        </div>
                        <div class="card-footer text-muted small">
                            Ngày tạo: {{ date('d/m/Y H:i', strtotime($invoice['created_at'])) }}
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle"></i> Không có hóa đơn chưa thanh toán.
                    </div>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Tab 3: Hóa đơn đã thanh toán -->
        <div class="tab-pane fade" id="paid-invoices" role="tabpanel">
            <div class="row">
                @forelse($paidInvoices as $invoice)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card invoice-card h-100 border-success">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0">
                                <i class="bi bi-receipt-cutoff"></i> Hóa đơn #{{ $invoice['id'] }}
                                <span class="badge bg-light text-success float-end">Đã thanh toán</span>
                            </h6>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $invoice['room']['title'] }}</h5>
                            <p class="text-muted mb-2"><strong>Tháng:</strong> {{ $invoice['thangNam'] }}</p>
                            <p class="mb-1"><strong>Khách thuê:</strong> {{ $invoice['tenant']['name'] }}</p>
                            <p class="mb-1"><strong>Email:</strong> {{ $invoice['tenant']['email'] }}</p>
                            <hr>
                            <div class="mb-2">
                                <div class="d-flex justify-content-between">
                                    <span>Tiền phòng:</span>
                                    <strong>{{ number_format($invoice['priceRoom']) }} VNĐ</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Tiền điện ({{ $invoice['sodienCu'] }} → {{ $invoice['sodienMoi'] }}):</span>
                                    <strong>{{ number_format($invoice['priceDien']) }} VNĐ</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Tiền nước:</span>
                                    <strong>{{ number_format($invoice['priceWater']) }} VNĐ</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Tiền dịch vụ:</span>
                                    <strong>{{ number_format($invoice['amenitiesPrice']) }} VNĐ</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Số người:</span>
                                    <strong>{{ $invoice['soNguoi'] }}</strong>
                                </div>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <h5>Tổng cộng:</h5>
                                <h5 class="text-success">{{ number_format($invoice['sumPrice']) }} VNĐ</h5>
                            </div>
                            @if($invoice['paidAt'])
                            <p class="text-muted small mt-2">
                                <i class="bi bi-calendar-check"></i> Thanh toán: {{ date('d/m/Y H:i', strtotime($invoice['paidAt'])) }}
                            </p>
                            @endif
                        </div>
                        <div class="card-footer text-muted small">
                            Ngày tạo: {{ date('d/m/Y H:i', strtotime($invoice['created_at'])) }}
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Chưa có hóa đơn nào được thanh toán.
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection