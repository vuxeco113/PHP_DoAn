@extends('layouts.owner')

@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-2xl font-semibold mb-4">Cập nhật phiếu sửa</h2>

    <form id="phieuSuaForm" method="POST" action="{{ Route('owner.phieu-sua.update',  $phieuSua['id']) }}">
        @csrf
        @method('POST')

        <input type="hidden" name="roomId" value="{{ $phieuSua['roomId'] }}">
        <input type="hidden" name="tenantId" value="{{ $phieuSua['tenantId'] }}">
        <input type="hidden" name="requestId" value="{{ $phieuSua['requestId'] }}">
        <input type="hidden" name="id" value="{{ $phieuSua['id'] }}">

        {{-- Nguồn hỏng --}}
        <div class="mb-4">
            <label for="faultSource" class="block text-sm font-medium text-gray-700">Nguồn hỏng</label>
            <select id="faultSource" name="faultSource"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                required>
                <option value="khach_thue" {{ $phieuSua['faultSource'] == 'khach_thue' ? 'selected' : '' }}>Khách thuê</option>
                <option value="chu_tro" {{ $phieuSua['faultSource'] == 'chu_tro' ? 'selected' : '' }}>Chủ trọ</option>
            </select>
            @error('faultSource')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Ngày sửa --}}
        <div class="mb-4">
            <label for="ngaySua" class="block text-sm font-medium text-gray-700">Ngày sửa</label>
            <input type="date" id="ngaySua" name="ngaySua"
                value="{{ old('ngaySua', \Illuminate\Support\Str::substr($phieuSua['ngaySua'], 0, 10)) }}"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                required>
            @error('ngaySua')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Tổng tiền --}}
        <div class="mb-4">
            <label for="tongTien" class="block text-sm font-medium text-gray-700">Tổng tiền</label>
            <input type="number" id="tongTien" name="tongTien" value="{{ old('tongTien', $phieuSua['tongTien']) }}"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
            @error('tongTien')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Trạng thái --}}
        <div class="mb-4">
            <label for="status" class="block text-sm font-medium text-gray-700">Trạng thái</label>
            <select id="status" name="status"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <option value="pending" {{ $phieuSua['status'] == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                <option value="completed" {{ $phieuSua['status'] == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                <option value="cancelled" {{ $phieuSua['status'] == 'cancelled' ? 'selected' : '' }}>Hủy bỏ</option>
            </select>
            @error('status')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Hạng mục sửa --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Hạng mục sửa</label>
            <div id="itemsContainer">
                @php
                $items = is_array($phieuSua['items']) ? $phieuSua->items : json_decode($phieuSua['items'], true);
                @endphp

                @foreach($items as $index => $item)
                <div class="flex items-center mb-2">
                    <input type="number" name="items[{{ $index }}][cost]" value="{{ $item['cost'] ?? '' }}" placeholder="Chi phí" class="mr-2 p-2 border border-gray-300 rounded" required>
                    <input type="text" name="items[{{ $index }}][info]" value="{{ $item['info'] ?? '' }}" placeholder="Thông tin hạng mục" class="mr-2 p-2 border border-gray-300 rounded" required>
                    <button type="button" class="removeItemBtn text-red-500">Xóa</button>
                </div>
                @endforeach
            </div>
            <button type="button" id="addItemBtn" class="text-blue-500">+ Thêm hạng mục</button>
        </div>

        {{-- Nút cập nhật --}}
        <div class="mb-4">
            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">Cập nhật phiếu sửa</button>
        </div>
    </form>
</div>

{{-- JS xử lý thêm/xóa item và tính tổng tiền --}}
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

        itemsContainer.addEventListener('input', e => {
            if (e.target.name && e.target.name.endsWith('[cost]')) updateTotal();
        });

        itemsContainer.addEventListener('click', e => {
            if (e.target.classList.contains('removeItemBtn')) {
                e.target.closest('div.flex').remove();
                updateTotal();
            }
        });
    });
</script>
@endsection