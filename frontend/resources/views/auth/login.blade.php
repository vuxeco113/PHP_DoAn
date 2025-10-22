@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-100 via-white to-indigo-100">
    <div class="bg-white shadow-2xl rounded-2xl p-8 w-full max-w-md">
        <h2 class="text-3xl font-bold text-center text-indigo-600 mb-6">Đăng nhập</h2>

        <form method="POST" action="/login" class="space-y-5">
            @csrf

            {{-- Email --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" placeholder="Nhập email của bạn" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-400 focus:outline-none transition" />
            </div>

            {{-- Mật khẩu --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu</label>
                <input type="password" name="password" placeholder="Nhập mật khẩu" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-400 focus:outline-none transition" />
            </div>

            {{-- Ghi nhớ đăng nhập --}}
            <div class="flex items-center justify-between text-sm">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="remember" class="rounded text-indigo-600 focus:ring-indigo-500">
                    <span class="ml-2 text-gray-700">Ghi nhớ đăng nhập</span>
                </label>
                <a href="#" class="text-indigo-600 hover:underline">Quên mật khẩu?</a>
            </div>

            {{-- Nút đăng nhập --}}
            <div class="pt-3">
                <button type="submit"
                    class="w-full bg-indigo-600 text-white font-semibold py-2 rounded-lg hover:bg-indigo-700 transition-all duration-200 shadow-md">
                    Đăng nhập
                </button>
            </div>

            {{-- Hiển thị lỗi --}}
            @error('login')
                <p class="text-red-500 text-center text-sm mt-2">{{ $message }}</p>
            @enderror

            {{-- Link đăng ký --}}
            <p class="text-center text-sm text-gray-600 mt-4">
                Chưa có tài khoản?
                <a href="/register" class="text-indigo-600 hover:underline">Đăng ký ngay</a>
            </p>
        </form>
    </div>
</div>
@endsection
