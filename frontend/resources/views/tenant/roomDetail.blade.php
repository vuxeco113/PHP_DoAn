@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ url('/tenant/buildings') }}"
                    class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                    <svg class="w-3 h-3 mr-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z" />
                    </svg>
                    Danh sách tòa nhà
                </a>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-3 h-3 mx-1 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 9 4-4-4-4" />
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Chi tiết phòng</span>
                </div>
            </li>
        </ol>
    </nav>

    @if(isset($room['data']) && $room['success'])
    @php
    $roomData = $room['data'];
    @endphp

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Header với hình ảnh -->
        <div class="relative">
            @if(!empty($roomData['imageUrls']))
            <img src="{{ $roomData['imageUrls'][0] }}" alt="{{ $roomData['title'] }}" class="w-full h-64 object-cover">
            @else
            <div class="w-full h-64 bg-gray-200 flex items-center justify-center">
                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            @endif

            <!-- Status badge -->
            <div class="absolute top-4 right-4">
                <span
                    class="px-3 py-1 rounded-full text-sm font-semibold 
                                {{ $roomData['status'] === 'available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $roomData['status'] === 'available' ? 'Còn trống' : 'Đã thuê' }}
                </span>
            </div>
        </div>

        <!-- Thông tin chi tiết -->
        <div class="p-6">
            <!-- Tiêu đề và giá -->
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">
                        {{ $roomData['title'] }}
                    </h1>
                    <p class="text-gray-600">{{ $roomData['description'] }}</p>
                </div>
                <div class="text-right">
                    <p class="text-3xl font-bold text-[#E03C31]">
                        {{ number_format($roomData['price']) }} đ
                    </p>
                    <p class="text-gray-500">/ tháng</p>
                </div>
            </div>

            <!-- Thông số cơ bản -->
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <svg class="w-6 h-6 text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5" />
                    </svg>
                    <p class="text-sm text-gray-600">Diện tích</p>
                    <p class="font-semibold">{{ $roomData['area'] }} m²</p>
                </div>

                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <svg class="w-6 h-6 text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                    </svg>
                    <p class="text-sm text-gray-600">Sức chứa</p>
                    <p class="font-semibold">{{ $roomData['capacity'] }} người</p>
                </div>

                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <svg class="w-6 h-6 text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <p class="text-sm text-gray-600">Số điện</p>
                    <p class="font-semibold">{{ $roomData['sodien'] }} kWh</p>
                </div>

                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <svg class="w-6 h-6 text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <p class="text-sm text-gray-600">Trạng thái</p>
                    <p
                        class="font-semibold {{ $roomData['status'] === 'available' ? 'text-green-600' : 'text-red-600' }}">
                        {{ $roomData['status'] === 'available' ? 'Còn trống' : 'Đã thuê' }}
                    </p>
                </div>
            </div>

            <!-- Tiện nghi -->
            @if(!empty($roomData['amenities']))
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-3 text-gray-900">Tiện nghi</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($roomData['amenities'] as $amenity)
                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                        {{ $amenity }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Thông tin bổ sung -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-900">Thông tin bổ sung</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Ngày tạo:</span>
                        <span class="font-medium">
                            {{ \Carbon\Carbon::parse($roomData['created_at'])->format('d/m/Y H:i') }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Cập nhật lần cuối:</span>
                        <span class="font-medium">
                            {{ \Carbon\Carbon::parse($roomData['updated_at'])->format('d/m/Y H:i') }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tọa độ:</span>
                        <span class="font-medium text-sm">
                            {{ $roomData['latitude'] }}, {{ $roomData['longitude'] }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Nút hành động -->
            <div class="flex gap-4 pt-6 border-t">
                <a href="{{ url()->previous() }}"
                    class="flex-1 border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg font-semibold transition text-center">
                    Quay lại
                </a>

                @if($roomData['status'] === 'available' && $kq === false)
                <a href="{{ url('/tenant/requests/create/' . $roomData['id']) }}"
                    class="flex-1 bg-[#E03C31] hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold transition">
                    Thuê phòng
                </a>
                @else
                <button
                    class="flex-1 bg-gray-400 text-white px-4 py-2 rounded-lg font-semibold cursor-not-allowed"
                    disabled>
                    Không thể thuê
                </button>
                @endif
            </div>
        </div>
    </div>

    @else
    <!-- Error state -->
    <div class="text-center py-12">
        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <h3 class="text-lg font-semibold text-gray-900 mb-2">Không tìm thấy thông tin phòng</h3>
        <p class="text-gray-600 mb-6">Phòng bạn đang tìm kiếm không tồn tại hoặc đã bị xóa.</p>
        <a href="{{ url('/tenant/buildings') }}"
            class="bg-[#E03C31] hover:bg-red-700 text-white px-6 py-2 rounded-lg font-semibold transition inline-block">
            Quay lại danh sách
        </a>
    </div>
    @endif
</div>
@endsection