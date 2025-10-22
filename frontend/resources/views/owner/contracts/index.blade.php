@extends('layouts.owner')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Quản lý hợp đồng</h1>
        </div>

        <!-- Filters -->
        <div class="flex flex-wrap gap-4 mb-6">
            <!-- Chọn tòa nhà -->
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">Chọn tòa nhà</label>
                <select id="buildingFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Tất cả tòa nhà</option>
                    @foreach($buildings2 as $building)
                    <option value="{{ $building['id'] }}">{{ $building['buildingName'] }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Sắp xếp -->
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
                <select id="statusFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Tất cả trạng thái</option>
                    <option value="active">Đang hoạt động</option>
                    <option value="expired">Hết hạn</option>
                    <option value="pending">Chờ duyệt</option>
                </select>
            </div>

            <!-- Tìm kiếm -->
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tìm kiếm</label>
                <input type="text" id="searchInput" placeholder="Tên khách thuê, số phòng..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <!-- Nút tạo hợp đồng mới -->

        </div>

        <!-- Kết quả lọc -->
        <div class="mb-4">
            <span class="text-sm text-gray-600">Hiển thị <strong id="resultCount">{{ count($contracts2) }}</strong> hợp đồng</span>
        </div>

        <!-- Header danh sách -->
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Danh sách hợp đồng</h2>
        </div>

        <!-- Contracts List -->
        @if(count($contracts2) > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 px-4 font-medium text-gray-700">ID</th>
                        <th class="text-left py-3 px-4 font-medium text-gray-700">Khách thuê</th>
                        <th class="text-left py-3 px-4 font-medium text-gray-700">Phòng</th>
                        <th class="text-left py-3 px-4 font-medium text-gray-700">Tòa nhà</th>
                        <th class="text-left py-3 px-4 font-medium text-gray-700">Ngày bắt đầu</th>
                        <th class="text-left py-3 px-4 font-medium text-gray-700">Ngày kết thúc</th>
                        <th class="text-left py-3 px-4 font-medium text-gray-700">Tiền thuê</th>
                        <th class="text-left py-3 px-4 font-medium text-gray-700">Tiền cọc</th>
                        <th class="text-left py-3 px-4 font-medium text-gray-700">Trạng thái</th>
                        <th class="text-left py-3 px-4 font-medium text-gray-700">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contracts2 as $contract)
                    <tr class="contract-row border-b border-gray-100 hover:bg-gray-50 transition-colors"
                        data-id="{{ $contract['id'] }}"
                        data-room-id="{{ $contract['room']['id'] }}"
                        data-building="{{ $contract['room']['building']['id'] }}"
                        data-status="{{ $contract['status'] }}"
                        data-tenant="{{ strtolower($contract['tenant']['name']) }}"
                        data-room="{{ strtolower($contract['room']['id']) }}">
                        <td class="py-4 px-4 text-gray-900">#{{ $contract['id'] }}</td>
                        <td class="py-4 px-4 text-gray-900">{{ $contract['tenant']['name'] }}</td>
                        <td class="py-4 px-4 text-gray-900">{{ $contract['room']['title'] }}</td>
                        <td class="py-4 px-4 text-gray-600">{{ $contract['room']['building']['buildingName'] }}</td>
                        <td class="py-4 px-4 text-gray-600">{{ date('d/m/Y', strtotime($contract['start_date'])) }}</td>
                        <td class="py-4 px-4 text-gray-600">{{ date('d/m/Y', strtotime($contract['end_date'])) }}</td>
                        <td class="py-4 px-4 text-gray-900">{{ number_format($contract['rent_amount'], 0, ',', '.') }}đ</td>
                        <td class="py-4 px-4 text-gray-900">{{ number_format($contract['deposit_amount'], 0, ',', '.') }}đ</td>
                        <td class="py-4 px-4">
                            @if($contract['status'] === 'active')
                            <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Đang hoạt động
                            </span>
                            @elseif($contract['status'] === 'expired')
                            <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                Hết hạn
                            </span>
                            @elseif($contract['status'] === 'pending')
                            <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Chờ duyệt
                            </span>
                            @endif
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex items-center">
                                <select
                                    class="status-select bg-gray-100 border border-gray-300 rounded-lg px-3 py-2 text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                    data-contract-id="{{ $contract['id'] }}"
                                    data-room-id="{{ $contract['room']['id'] }}">

                                    <option value="active" {{ $contract['status'] === 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                                    <option value="expired" {{ $contract['status'] === 'expired' ? 'selected' : '' }}>Hết hạn</option>
                                    <option value="pending" {{ $contract['status'] === 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                                </select>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <!-- Empty State -->
        <div id="emptyState" class="text-center py-16" style="display: none;">
            <i class="fas fa-file-contract text-6xl text-gray-300 mb-4"></i>
            <p class="text-xl text-gray-500">Không tìm thấy hợp đồng nào</p>
            <p class="text-sm text-gray-400 mt-2">Thử thay đổi bộ lọc hoặc tìm kiếm</p>
        </div>
        @endif
    </div>
</div>

<style>
    .container {
        max-width: 1400px;
    }

    table {
        border-collapse: separate;
        border-spacing: 0;
    }

    select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
        background-position: right 0.5rem center;
        background-repeat: no-repeat;
        background-size: 1.5em 1.5em;
        padding-right: 2.5rem;
    }

    .contract-row {
        transition: all 0.3s ease;
    }

    .contract-row.hidden {
        display: none;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const buildingFilter = document.getElementById('buildingFilter');
        const statusFilter = document.getElementById('statusFilter');
        const searchInput = document.getElementById('searchInput');
        const contractRows = document.querySelectorAll('.contract-row');
        const resultCount = document.getElementById('resultCount');
        const emptyState = document.getElementById('emptyState');
        const tableContainer = document.querySelector('.overflow-x-auto');

        // Hàm lọc hợp đồng
        function filterContracts() {
            const buildingValue = (buildingFilter.value || '').toLowerCase().trim();
            const statusValue = (statusFilter.value || '').toLowerCase().trim();
            const searchValue = (searchInput.value || '').toLowerCase().trim();

            let visibleCount = 0;

            contractRows.forEach(row => {
                // Đảm bảo không lỗi khi dataset chưa có giá trị
                const building = (row.dataset.building || '').toLowerCase();
                const status = (row.dataset.status || '').toLowerCase();
                const tenant = (row.dataset.tenant || '');
                const room = (row.dataset.room || '');

                // Điều kiện lọc
                const matchBuilding = !buildingValue || building.includes(buildingValue);
                const matchStatus = !statusValue || status === statusValue;
                const matchSearch = !searchValue ||
                    tenant.includes(searchValue) ||
                    room.includes(searchValue);

                // Hiển thị hoặc ẩn hàng
                if (matchBuilding && matchStatus && matchSearch) {
                    row.classList.remove('hidden');
                    visibleCount++;
                } else {
                    row.classList.add('hidden');
                }
            });

            // Cập nhật số lượng kết quả
            resultCount.textContent = visibleCount;

            // Hiển thị hoặc ẩn empty state
            if (visibleCount === 0) {
                emptyState.style.display = 'block';
                tableContainer.style.display = 'none';
            } else {
                emptyState.style.display = 'none';
                tableContainer.style.display = 'block';
            }
        }

        // Gắn sự kiện cho các bộ lọc
        [buildingFilter, statusFilter].forEach(el =>
            el.addEventListener('change', filterContracts)
        );
        searchInput.addEventListener('input', filterContracts);

        // Gọi lần đầu để khởi tạo
        filterContracts();
    });
    document.addEventListener('DOMContentLoaded', function() {
        const statusSelects = document.querySelectorAll('.status-select');

        statusSelects.forEach(select => {
            select.addEventListener('change', function() {
                const newStatus = this.value;
                const contractId = this.dataset.contractId;
                const roomId = this.dataset.roomId;
                const newStatus_room = 'rented';

                if (newStatus === 'expired') {
                    // 👉 Chuyển hướng sang trang tạo biên bản bồi thường
                    window.location.href = '{{ Route("owner.compensations.create") }}?room_id=' + roomId;
                    return;
                }
                if (newStatus === 'pending') {
                    // 👉 Chuyển hướng sang trang tạo biên bản bồi thường
                    alert('Khồn thể chuyển trạng thái về chờ duyệt!');
                    return;
                }

                // Ngược lại: Cập nhật trạng thái qua API
                fetch(`http://127.0.0.1:8000/api/contracts/update/${contractId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            // 'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            status: newStatus
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {

                            // ✅ Gọi thêm API cập nhật khác, ví dụ: cập nhật trạng thái phòng
                            return fetch(`http://127.0.0.1:8000/api/rooms/updateStatus/${roomId}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    status: newStatus_room
                                })
                            });
                        } else {
                            throw new Error('Cập nhật hợp đồng thất bại');
                        }
                    })
                    .then(res => res.json())
                    .then(roomData => {
                        if (roomData.success) {
                            alert('Cập nhật hợp đồng & phòng thành công!');
                            location.reload();
                        } else {
                            alert('Cập nhật hợp đồng OK nhưng phòng lỗi!');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Lỗi khi cập nhật trạng thái!');
                    });

            });
        });
    });
</script>
@endsection