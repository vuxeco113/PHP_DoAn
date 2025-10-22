@extends('layouts.owner')

@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-2xl font-semibold mb-4">Tạo phiếu sửa</h2>

    <form id="phieuSuaForm" method="POST" action="{{ Route('owner.phieu-sua.store') }}">
        @csrf
        <input type="hidden" name="roomId" value="{{ old('roomId', $roomId1 ?? '') }}">
        <input type="hidden" name="tenantId" value="{{ old('tatenId', $tenantId1 ?? '') }}">
        <input type="hidden" name="requestId" value="{{ old('requestId', $requestId1 ?? '') }}">

        <div class="mb-4">
            <label for="faultSource" class="block text-sm font-medium text-gray-700">Nguồn hỏng</label>
            <select id="faultSource" name="faultSource"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                required>
                <option value="">-- Chọn nguồn hỏng --</option>
                <option value="khach_thue" {{ old('faultSource') == 'khach_thue' ? 'selected' : '' }}>Khách thuê</option>
                <option value="chu_tro" {{ old('faultSource') == 'chu_tro' ? 'selected' : '' }}>Chủ trọ</option>
            </select>
            @error('faultSource')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-4">
            <label for="ngaySua" class="block text-sm font-medium text-gray-700">Ngày sửa</label>
            <input type="date" id="ngaySua" name="ngaySua" value="{{ old('ngaySua') }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
            @error('ngaySua')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="tongTien" class="block text-sm font-medium text-gray-700">Tổng tiền</label>
            <input type="number" id="tongTien" name="tongTien" value="{{ old('tongTien') }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
            @error('tongTien')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="status" class="block text-sm font-medium text-gray-700">Trạng thái</label>
            <select id="status" name="status" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Hủy bỏ</option>
            </select>
            @error('status')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="items" class="block text-sm font-medium text-gray-700">Hạng mục sửa</label>
            <div id="itemsContainer">
                <div class="flex items-center mb-2">
                    <input type="number" name="items[0][cost]" placeholder="Chi phí" class="mr-2 p-2 border border-gray-300 rounded" required>
                    <input type="text" name="items[0][info]" placeholder="Thông tin hạng mục" class="mr-2 p-2 border border-gray-300 rounded" required>
                    <button type="button" class="removeItemBtn text-red-500">Xóa</button>
                </div>
            </div>
            <button type="button" id="addItemBtn" class="text-blue-500">Thêm hạng mục</button>
        </div>

        <div class="mb-4">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md">Tạo phiếu sửa</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const addItemBtn = document.getElementById('addItemBtn');
        const itemsContainer = document.getElementById('itemsContainer');
        const tongTienInput = document.getElementById('tongTien');

        function updateTotal() {
            let total = 0;
            itemsContainer.querySelectorAll('input[name$="[cost]"]').forEach(input => {
                const val = parseFloat(input.value);
                if (!isNaN(val)) total += val;
            });
            tongTienInput.value = total;
        }

        // Thêm hạng mục
        addItemBtn.addEventListener('click', function() {
            const index = itemsContainer.children.length;
            const itemDiv = document.createElement('div');
            itemDiv.classList.add('flex', 'items-center', 'mb-2');
            itemDiv.innerHTML = `
            <input type="number" name="items[${index}][cost]" placeholder="Chi phí" class="mr-2 p-2 border border-gray-300 rounded" required>
            <input type="text" name="items[${index}][info]" placeholder="Thông tin hạng mục" class="mr-2 p-2 border border-gray-300 rounded" required>
            <button type="button" class="removeItemBtn text-red-500">Xóa</button>
        `;
            itemsContainer.appendChild(itemDiv);
        });

        // Event delegation cho input cost và nút xóa
        itemsContainer.addEventListener('input', function(e) {
            if (e.target.name && e.target.name.endsWith('[cost]')) {
                updateTotal();
            }
        });

        itemsContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('removeItemBtn')) {
                e.target.closest('div.flex').remove();
                updateTotal();
            }
        });
    });
</script>
@endsection