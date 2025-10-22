@extends('layouts.app')

@section('content')
<div class="w-full px-6">
    <x-bladewind::card title="Danh sách hóa đơn">
        <div class="overflow-x-auto">
            <table class="bw-table min-w-full">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Khách thuê</th>
                        <th>Phòng</th>
                        <th>Tháng/Năm</th>
                        <th>Tiền phòng</th>
                        <th>Điện</th>
                        <th>Nước</th>
                        <th>Tiện ích</th>
                        <th>Tổng cộng</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $invoice)
                    <tr>
                        <td>{{ $invoice['id'] }}</td>
                        <td>{{ $invoice['tenant']['name'] ?? '---' }}</td>
                        <td>
                            <div class="flex items-center space-x-2">
                                @if(!empty($invoice['room']['imageUrls'][0]))
                                <img src="{{ $invoice['room']['imageUrls'][0] }}" alt="room" class="w-10 h-10 rounded-md object-cover">
                                @endif
                                <div>
                                    <div class="font-semibold">{{ $invoice['room']['title'] ?? '' }}</div>
                                    <div class="text-xs text-gray-500">{{ $invoice['room']['area'] ?? '' }} m²</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $invoice['thangNam'] }}</td>
                        <td>{{ number_format($invoice['priceRoom']) }} đ</td>
                        <td>{{ number_format($invoice['priceDien']) }} đ</td>
                        <td>{{ number_format($invoice['priceWater']) }} đ</td>
                        <td>{{ number_format($invoice['amenitiesPrice']) }} đ</td>
                        <td class="font-bold text-blue-700">{{ number_format($invoice['sumPrice']) }} đ</td>
                        <td>
                            @if($invoice['status'] === 'pending')
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded">Chờ thanh toán</span>
                            @elseif($invoice['status'] === 'paid')
                            <span class="px-3 py-1 bg-yellow-100 text-green-800 rounded">Đã thanh toán</span>
                            @else
                            <span class="px-3 py-1 bg-yellow-100 text-red-800 rounded">Hủy</span>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($invoice['date'])->format('d/m/Y') }}</td>
                        <td>
                            @if($invoice['status'] === 'pending')
                            <a href="{{ Route('tenant.bill.thanhtoan',$invoice['id']) }}"
                                class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-3 py-1.5 rounded-md transition duration-150 ease-in-out">
                                <i class="fas fa-eye mr-1"></i> Thanh Toán
                            </a>
                            @else
                            <a href="{{ Route('tenant.bill.thanhtoan',$invoice['id']) }}"
                                class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-3 py-1.5 rounded-md transition duration-150 ease-in-out">
                                <i class="fas fa-eye mr-1"></i>Xem
                            </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-bladewind::card>
</div>


<div class="w-full px-6">
    <x-bladewind::card title="Danh sách phiếu sửa chữa" class="w-full max-w-none overflow-x-auto">
        <div class="w-full overflow-x-auto">
            <table class="bw-table min-w-max w-full whitespace-nowrap">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Người báo lỗi</th>
                        <th>Phòng</th>
                        <th>Chi tiết sửa</th>
                        <th>Ngày sửa</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th>Người yêu cầu</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($repairs as $repair)
                    @php
                    $items = json_decode($repair['items'], true);
                    @endphp
                    <tr>
                        <td>{{ $repair['id'] }}</td>
                        <td>
                            @if($repair['faultSource'] === 'chu_tro')
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded">Chủ trọ</span>
                            @elseif($repair['faultSource'] === 'khach_thue')
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded">Khách thuê</span>
                            @else
                            {{ $repair['faultSource'] }}
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center space-x-2">
                                @if(!empty($repair['room']['imageUrls'][0]))
                                <img src="{{ $repair['room']['imageUrls'][0] }}" class="w-10 h-10 rounded-md object-cover" alt="room">
                                @endif
                                <div>
                                    <div class="font-semibold">{{ $repair['room']['title'] ?? '---' }}</div>
                                    <div class="text-xs text-gray-500">{{ $repair['room']['area'] ?? '' }} m²</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <ul class="list-disc list-inside text-sm">
                                @foreach($items as $it)
                                <li>{{ $it['info'] }}: <span class="font-semibold">{{ number_format($it['cost']) }} đ</span></li>
                                @endforeach
                            </ul>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($repair['ngaySua'])->format('d/m/Y') }}</td>
                        <td class="font-bold text-blue-700">{{ number_format($repair['tongTien']) }} đ</td>
                        <td>
                            @if($repair['status'] === 'pending')
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded">Đang xử lý</span>
                            @elseif($repair['status'] === 'completed')
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded">Hoàn thành</span>
                            @else
                            <span class="px-3 py-1 bg-red-100 text-red-800 rounded">Khác</span>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($repair['created_at'])->format('d/m/Y H:i') }}</td>
                        <td>
                            <div>
                                <div class="font-semibold">{{ $repair['request']['name'] ?? '---' }}</div>
                                <div class="text-xs text-gray-500">{{ $repair['request']['sdt'] ?? '' }}</div>
                            </div>
                        </td>
                        <td>
                            @if($repair['status'] === 'pending')
                            <a href="{{ Route('tenant.phieu-sua.detail',$repair['id']) }}"
                                class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-3 py-1.5 rounded-md transition duration-150 ease-in-out">
                                <i class="fas fa-eye mr-1"></i> Xử lý
                            </a>
                            @else
                            <a href="{{ Route('tenant.phieu-sua.detail',$repair['id']) }}"
                                class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-3 py-1.5 rounded-md transition duration-150 ease-in-out">
                                <i class="fas fa-eye mr-1"></i>Xem
                            </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-bladewind::card>
</div>
@endsection