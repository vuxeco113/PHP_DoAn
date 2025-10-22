@extends('layouts.owner')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-semibold">
                Danh sách phòng - {{ $building['buildingName'] ?? 'Không rõ tên tòa nhà' }}
            </h2>
            <p class="text-gray-500 text-sm mt-1">{{ $building['address'] ?? '' }}</p>
        </div>

        <!-- Thống kê tổng quan -->
        @php
        $totalPendingRequests = array_sum(array_column($rooms, 'pending_requests_count'));
        $totalAllRequests = array_sum(array_column($rooms, 'total_requests_count'));
        @endphp

        <div class="flex items-center gap-4">
            @if($totalPendingRequests > 0)
            <div class="bg-orange-50 border border-orange-200 rounded-lg px-4 py-2">
                <div class="flex items-center gap-2">
                    <div class="relative">
                        <svg class="w-5 h-5 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                        </svg>
                        <span
                            class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold">
                            {{ $totalPendingRequests }}
                        </span>
                    </div>
                    <span class="text-sm text-orange-700 font-medium">
                        {{ $totalPendingRequests }} request chờ xử lý
                    </span>
                </div>
            </div>
            @endif

            <!-- Nút tạo phòng mới -->
            <a href="{{ route('rooms.create', ['buildingId' => $building['id']]) }}"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition font-medium">
                + Tạo phòng mới
            </a>
        </div>
    </div>

    <!-- Danh sách phòng -->
    @if(empty($rooms))
    <div class="text-center py-12">
        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
        </svg>
        <p class="text-gray-500 text-lg">Không có phòng nào trong tòa nhà này.</p>
    </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($rooms as $room)
        <div
            class="border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow bg-white relative">

            <!-- Chuông thông báo -->
            <div class="absolute top-4 right-4">
                <button
                    onclick="showRoomRequests({{ $room['id'] }}, <?php echo htmlspecialchars(json_encode($room['requests']), ENT_QUOTES, 'UTF-8', true); ?>, `<?php echo htmlspecialchars($room['title'], ENT_QUOTES, 'UTF-8'); ?>`)"
                    class="relative bg-white rounded-full p-2 shadow hover:shadow-lg transition-all cursor-pointer group border border-gray-200">
                    <svg class="w-5 h-5 {{ $room['pending_requests_count'] > 0 ? 'text-orange-500' : 'text-gray-400' }}"
                        fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                    </svg>

                    @if($room['pending_requests_count'] > 0)
                    <span
                        class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold">
                        {{ $room['pending_requests_count'] }}
                    </span>
                    @endif

                    <!-- Tooltip -->
                    <div class="absolute bottom-full right-0 mb-2 hidden group-hover:block z-10">
                        <div class="bg-gray-900 text-white text-xs rounded py-1 px-2 whitespace-nowrap">
                            @if($room['pending_requests_count'] > 0)
                            {{ $room['pending_requests_count'] }} request chờ xử lý
                            @else
                            Không có request nào
                            @endif
                        </div>
                        <div class="absolute top-full right-2 -mt-1 border-4 border-transparent border-t-gray-900">
                        </div>
                    </div>
                </button>
            </div>

            <!-- Hình ảnh -->
            <img src="{{ $room['imageUrls'][0] ?? '/placeholder.svg' }}" class="w-full h-40 object-cover rounded mb-3"
                alt="{{ $room['title'] }}">

            <!-- Thông tin phòng -->
            <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $room['title'] }}</h3>
            <p class="text-gray-500 text-sm mb-2 line-clamp-2">{{ $room['description'] }}</p>

            <div class="flex items-center justify-between mb-2">
                <p class="font-bold text-[#E03C31] text-lg">
                    {{ number_format($room['price']) }} đ / tháng
                </p>
                <span
                    class="text-xs px-2 py-1 rounded-full 
                                                                            {{ $room['status'] === 'available' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ $room['status'] === 'available' ? 'Còn trống' : 'Đã thuê' }}
                </span>
            </div>

            <!-- Thông số phòng -->
            <div class="flex items-center gap-4 text-sm text-gray-600 mb-3">
                <div class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5" />
                    </svg>
                    <span>{{ $room['area'] }} m²</span>
                </div>
                <div class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                    </svg>
                    <span>{{ $room['capacity'] }} người</span>
                </div>
                @if($room['status'] ==='rented')
                <a href="{{ route('owner.phieu-sua.indexByRoom', ['roomId' => $room['id']]) }}"
                    class="ml-auto bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-md transition font-medium text-sm whitespace-nowrap">
                    Sửa chữa
                </a>
                @endif
                @if($room['status'] ==='rented')
                <a href="{{ route('owner.compensations.create', ['room_id' => $room['id']]) }}"
                    class="ml-auto bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-md transition font-medium text-sm whitespace-nowrap">
                    Trả phòng
                </a>
                @endif
            </div>

            <!-- Thống kê requests -->
            <div class="border-t pt-3">
                <div class="flex justify-between items-center text-xs text-gray-500">
                    <span>Requests:
                        <span
                            class="font-semibold {{ $room['pending_requests_count'] > 0 ? 'text-orange-600' : 'text-gray-600' }}">
                            {{ $room['total_requests_count'] }}
                        </span>
                    </span>
                    @if($room['pending_requests_count'] > 0)
                    <span class="text-orange-600 font-semibold">
                        {{ $room['pending_requests_count'] }} chờ xử lý
                    </span>
                    @endif
                </div>
            </div>

        </div>
        @endforeach
    </div>
    @endif
</div>

<!-- Modal hiển thị danh sách requests -->
<div id="requestsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl mx-4 max-h-[80vh] overflow-hidden">
        <div class="flex justify-between items-center p-6 border-b">
            <div>
                <h3 class="text-lg font-semibold" id="modalTitle">Danh sách Requests</h3>
                <p class="text-sm text-gray-500" id="modalSubtitle"></p>
            </div>
            <button onclick="closeModal()"
                class="text-gray-500 hover:text-gray-700 p-2 rounded-full hover:bg-gray-100 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="p-6 overflow-y-auto max-h-[60vh]" id="modalContent">
            <!-- Nội dung requests sẽ được điền bằng JavaScript -->
        </div>
    </div>
</div>

<script>
    // Hiển thị modal với danh sách requests của phòng
    function showRoomRequests(roomId, requests, roomTitle) {
        if (!requests || requests.length === 0) {
            alert('Phòng "' + roomTitle + '" không có request nào');
            return;
        }

        // Cập nhật tiêu đề modal
        document.getElementById('modalTitle').textContent = `Requests - ${roomTitle}`;
        document.getElementById('modalSubtitle').textContent = `Tổng số: ${requests.length} request`;

        // Phân loại requests theo status
        const pendingRequests = requests.filter(req => req.status === 'pending');
        const approvedRequests = requests.filter(req => req.status === 'approved');
        const rejectedRequests = requests.filter(req => req.status === 'rejected');

        // Tạo nội dung modal
        let modalContent = `
                        <div class="mb-4 flex gap-4 text-sm">
                            <div class="flex items-center gap-1">
                                <span class="w-2 h-2 bg-orange-500 rounded-full"></span>
                                Chờ xử lý: ${pendingRequests.length}
                            </div>
                            <div class="flex items-center gap-1">
                                <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                Đã duyệt: ${approvedRequests.length}
                            </div>
                            <div class="flex items-center gap-1">
                                <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                                Đã từ chối: ${rejectedRequests.length}
                            </div>
                        </div>

                        <div class="space-y-4">
                    `;

        // Hiển thị requests theo thứ tự: pending -> approved -> rejected
        const sortedRequests = [...pendingRequests, ...approvedRequests, ...rejectedRequests];

        sortedRequests.forEach(request => {
            modalContent += `
                            <div class="border rounded-lg p-4 ${getRequestBorderClass(request.status)}">
                                <div class="flex justify-between items-start mb-3">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-lg capitalize text-gray-900">
                                            ${request.loai_request.replace(/_/g, ' ')}
                                        </h4>
                                        <div class="flex items-center gap-4 mt-1 text-sm text-gray-600">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                                ${request.name}
                                            </span>
                                            <span class="flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                                </svg>
                                                ${request.sdt}
                                            </span>
                                        </div>
                                    </div>
                                    <span class="px-3 py-1 rounded-full text-sm font-medium ${getStatusBadgeClass(request.status)}">
                                        ${getStatusText(request.status)}
                                    </span>
                                </div>

                                <div class="bg-gray-50 rounded p-3 mb-3">
                                    <p class="text-sm text-gray-700">${request.mo_ta}</p>
                                </div>

                                <div class="flex justify-between items-center text-xs text-gray-500">
                                    <span>Ngày tạo: ${new Date(request.created_at).toLocaleString('vi-VN')}</span>
                                    <span>ID: ${request.id}...</span>
                                </div>
                                <div class="flex justify-end gap-3 mt-2">
                                    <div class="flex justify-end gap-3 mt-2">
                                        <a href="#" 
                                            class="px-4 py-2 bg-blue-500 text-white text-sm rounded hover:bg-blue-600 transition">
                                            Xóa
                                        </a>
                                        ${getActionButton(roomId, request.loai_request,request.id,request.user_khach_id,)}
                                    </div>
                                </div>
                            </div>
                        `;
        });

        modalContent += `</div>`;

        document.getElementById('modalContent').innerHTML = modalContent;
        document.getElementById('requestsModal').classList.remove('hidden');
    }

    // Đóng modal
    function closeModal() {
        document.getElementById('requestsModal').classList.add('hidden');
    }

    function getActionButton(roomId, loai_request, requestId, tenantId) {
        let url = '#';
        let color = 'bg-yellow-500 hover:bg-yellow-600';
        let label = 'Xử lý';

        // Điều hướng theo loại request
        if (loai_request === 'thue_phong') {
            url = `/owner/contracts/${roomId}/create`;
            color = 'bg-yellow-500 hover:bg-yellow-600';
            label = 'Xử lý thuê phòng';
        } else if (loai_request === 'sua_chua') {
            url = `/owner/phieu-sua/create/${requestId}/${roomId}/${tenantId}`;
            color = 'bg-blue-500 hover:bg-blue-600';
            label = 'Xử lý sửa chữa';
        } else if (loai_request === 'tra_phong') {
            url = `/owner/compensations/create?room_id=${roomId}}`;
            color = 'bg-red-500 hover:bg-red-600';
            label = 'Xử lý trả phòng';
        } else {
            return '';
        }

        return `
        <a href="${url}" 
           class="px-4 py-2 ${color} text-white text-sm rounded transition">
           ${label}
        </a>
    `;
    }

    // Helper functions
    function getStatusBadgeClass(status) {
        switch (status) {
            case 'pending':
                return 'bg-yellow-100 text-yellow-800 border border-yellow-200';
            case 'approved':
                return 'bg-green-100 text-green-800 border border-green-200';
            case 'rejected':
                return 'bg-red-100 text-red-800 border border-red-200';
            default:
                return 'bg-gray-100 text-gray-800 border border-gray-200';
        }
    }

    function getRequestBorderClass(status) {
        switch (status) {
            case 'pending':
                return 'border-yellow-300 bg-yellow-50';
            case 'approved':
                return 'border-green-300 bg-green-50';
            case 'rejected':
                return 'border-red-300 bg-red-50';
            default:
                return 'border-gray-300 bg-gray-50';
        }
    }

    function getStatusText(status) {
        switch (status) {
            case 'pending':
                return 'Chờ xử lý';
            case 'approved':
                return 'Đã duyệt';
            case 'rejected':
                return 'Đã từ chối';
            default:
                return status;
        }
    }

    // Đóng modal khi click outside
    document.getElementById('requestsModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    // Đóng modal bằng phím ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });
</script>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endsection