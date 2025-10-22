@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6 text-center">Tạo yêu cầu thuê phòng</h1>

    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('tenant.requests.create') }}" class="space-y-5">
        @csrf

        <input type="hidden" name="room_id" value="{{ $roomId }}">

        <div>
            <label class="block text-gray-700 font-semibold mb-2">Họ tên</label>
            <input type="text" name="name" class="w-full border-gray-300 rounded p-2" required>
        </div>

        <div>
            <label class="block text-gray-700 font-semibold mb-2">Số điện thoại</label>
            <input type="text" name="sdt" class="w-full border-gray-300 rounded p-2" required>
        </div>

        <div>
            <label class="block text-gray-700 font-semibold mb-2">Loại yêu cầu</label>
            <select name="loai_request" class="w-full border-gray-300 rounded p-2">
                <option value="thue_phong">Thuê phòng</option>
                <option value="bao_tri">Bảo trì</option>
            </select>
        </div>

        <div>
            <label class="block text-gray-700 font-semibold mb-2">Mô tả chi tiết</label>
            <textarea name="mo_ta" rows="4" class="w-full border-gray-300 rounded p-2"></textarea>
        </div>

        <button type="submit"
            class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">
            Gửi yêu cầu
        </button>
    </form>
</div>
@endsection
