@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-100 via-white to-pink-100">
    <div class="bg-white shadow-xl rounded-2xl p-8 w-full max-w-md">
        <h2 class="text-3xl font-bold text-center text-indigo-600 mb-6">Đăng ký tài khoản</h2>

        <form method="POST" action="/register" class="space-y-5">
            @csrf

            {{-- Tên --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Họ và tên</label>
                <input type="text" name="name" placeholder="Nhập họ tên của bạn" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-400 focus:outline-none transition" />
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" placeholder="Nhập email" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-400 focus:outline-none transition" />
            </div>

            {{-- Mật khẩu --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu</label>
                <input type="password" name="password" placeholder="Nhập mật khẩu" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-400 focus:outline-none transition" />
            </div>

            {{-- Xác nhận mật khẩu --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Xác nhận mật khẩu</label>
                <input type="password" name="confirm_password" placeholder="Nhập lại mật khẩu" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-400 focus:outline-none transition" />
            </div>

            {{-- Vai trò --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Vai trò</label>
                <select name="role"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-400 focus:outline-none transition">
                    <option value="owner">Chủ trọ</option>
                    <option value="tenant">Khách thuê</option>
                </select>
            </div>

            {{-- Nút Đăng ký --}}
            <div class="pt-3">
                <button type="submit"
                    class="w-full bg-indigo-600 text-white font-semibold py-2 rounded-lg hover:bg-indigo-700 transition-all duration-200 shadow-md">
                    Đăng ký
                </button>
            </div>

            {{-- Lỗi --}}
            @error('register')
                <p class="text-red-500 text-center text-sm mt-2">{{ $message }}</p>
            @enderror

            {{-- Link đăng nhập --}}
            <p class="text-center text-sm text-gray-600 mt-4">
                Đã có tài khoản?
                <a href="/login" class="text-indigo-600 hover:underline">Đăng nhập</a>
            </p>
        </form>
    </div>
</div>
@endsection
