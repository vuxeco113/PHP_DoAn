@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white shadow-lg rounded-lg p-6 mt-6">
    <h2 class="text-2xl font-bold mb-4 text-gray-700">Chi tiết Hóa đơn</h2>

    {{-- Thông tin chung --}}
    <div class="space-y-2 text-sm text-gray-700">
        <p><strong>Mã hóa đơn:</strong> {{ $bill['id'] }}</p>
        <p><strong>Khách thuê:</strong> {{ $bill['tenant']['name'] }}</p>
        <p><strong>Phòng:</strong> {{ $bill['room']['title'] }}</p>
        <p><strong>Ngày lập:</strong> {{ \Carbon\Carbon::parse($bill['date'])->format('d/m/Y H:i') }}</p>
        <p><strong>Trạng thái:</strong> 
            <span class="px-2 py-1 rounded text-white 
                @if($bill['status'] === 'pending') bg-yellow-500 
                @elseif($bill['status'] === 'paid') bg-green-600 
                @else bg-gray-400 @endif">
                {{ ucfirst($bill['status']) }}
            </span>
        </p>
    </div>

    <hr class="my-4">

    {{-- Chi tiết tính tiền --}}
    <h3 class="font-semibold text-lg mb-2">Chi tiết chi phí</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm border border-gray-200">
            <tbody>
                <tr>
                    <td class="border px-3 py-2">Tiền phòng</td>
                    <td class="border px-3 py-2 text-right">{{ number_format($bill['priceRoom']) }} đ</td>
                </tr>
                <tr>
                    <td class="border px-3 py-2">Tiền điện ({{ $bill['sodienMoi'] - $bill['sodienCu'] }} kWh x {{ number_format($bill['priceDien']) }}đ)</td>
                    <td class="border px-3 py-2 text-right">{{ number_format(($bill['sodienMoi'] - $bill['sodienCu']) * $bill['priceDien']) }} đ</td>
                </tr>
                <tr>
                    <td class="border px-3 py-2">Tiền nước</td>
                    <td class="border px-3 py-2 text-right">{{ number_format($bill['priceWater']) }} đ</td>
                </tr>
                <tr>
                    <td class="border px-3 py-2">Dịch vụ / Tiện ích</td>
                    <td class="border px-3 py-2 text-right">{{ number_format($bill['amenitiesPrice']) }} đ</td>
                </tr>
                <tr class="font-semibold bg-gray-100">
                    <td class="border px-3 py-2">Tổng cộng</td>
                    <td class="border px-3 py-2 text-right text-red-600">{{ number_format($bill['sumPrice']) }} đ</td>
                </tr>
            </tbody>
        </table>
    </div>

    <hr class="my-4">

    {{-- Ảnh phòng --}}
    <div>
        <h3 class="font-semibold text-lg mb-2">Phòng thuê</h3>
        <div class="flex items-center gap-3">
            @if(!empty($bill['room']['imageUrls']))
                <img src="{{ $bill['room']['imageUrls'][0] }}" 
                     alt="Ảnh phòng" class="w-32 h-32 object-cover rounded-lg border">
            @endif
            <div>
                <p><strong>Tên phòng:</strong> {{ $bill['room']['title'] }}</p>
                <p><strong>Sức chứa:</strong> {{ $bill['room']['capacity'] }} người</p>
                <p><strong>Tiện nghi:</strong> {{ implode(', ', $bill['room']['amenities']) }}</p>
            </div>
        </div>
    </div>

    {{-- Nút Thanh toán --}}
    @if($bill['status'] === 'pending')
        <div class="mt-6 flex justify-end">
            <form action="{{ Route('tenant.bill.checkout',['id' => $bill['id']]) }}" method="POST">
                @csrf
                <button type="submit" 
                    class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg font-semibold transition">
                    Thanh toán
                </button>
            </form>
        </div>
    @endif
</div>
@endsection
