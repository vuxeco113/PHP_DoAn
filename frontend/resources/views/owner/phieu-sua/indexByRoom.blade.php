@extends('layouts.app')

@section('content')
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
                            @if($repair['status'] !== 'completed')
                            <a href="{{ Route('owner.phieu-sua.detail', ['id' => $repair['id']]) }}"
                                class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                                Xử lý
                            </a>
                            @elseif($repair['status'] === 'completed')
                            <span class="bg-green-500 text-white px-3 py-1 rounded">Xem</span>
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