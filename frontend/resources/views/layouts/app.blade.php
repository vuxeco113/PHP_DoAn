<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('vendor/bladewind/js/helpers.js') }}"></script>
    <link href="{{ asset('vendor/bladewind/css/bladewind-ui.min.css') }}" rel="stylesheet" />

    <!-----------------------------------------------------------
-- animate.min.css by Daniel Eden (https://animate.style)
-- is required for the animation of notifications and slide out panels
-- you can ignore this step if you already have this file in your project
--------------------------------------------------------------------------->

    <link href="{{ asset('vendor/bladewind/css/animate.min.css') }}" rel="stylesheet" />
    <script src="//unpkg.com/alpinejs" defer></script>
</head>

<body>
    <style>
        .navbar-modern {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
            padding: 15px 0;
        }

        .navbar-brand-modern {
            font-size: 1.5rem;
            font-weight: bold;
            color: white !important;
            text-decoration: none;
        }

        .navbar-brand-modern:hover {
            color: #f8f9fa !important;
            transform: scale(1.05);
            transition: all 0.3s ease;
        }

        .nav-link-modern {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            padding: 10px 20px !important;
            margin: 0 5px;
            border-radius: 25px;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .nav-link-modern:hover {
            background: rgba(255, 255, 255, 0.2);
            color: white !important;
            transform: translateY(-2px);
        }

        .nav-link-modern.active {
            background: rgba(255, 255, 255, 0.3);
            color: white !important;
        }

        .admin-badge {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            padding: 8px 16px;
            color: white;
        }

        .line-clamp-1 {
            overflow: hidden;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 1;
        }

        .line-clamp-2 {
            overflow: hidden;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
        }
    </style>
    @php
    $user = session('user');
    @endphp

    <div class="min-h-screen bg-background">
        <!-- Top Navigation -->
        <header class="border-b bg-white">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4">
                <!-- Logo -->
                <div class="flex items-center gap-2">
                    <div class="flex flex-col">
                        <span class="text-2xl font-bold text-[#E03C31]">Quản Lý trọ</span>
                        <span class="text-xs text-muted-foreground">PHP Mã nguồn mở</span>
                    </div>
                </div>

                <!-- Main Navigation -->
                <nav class="hidden items-center gap-6 lg:flex">
                    <a href="/dashboard" class="text-sm font-medium text-foreground ">
                        Danh Sách Trọ
                    </a>
                    <a href="{{ Route('tenant.myRoom') }}" class="text-sm font-medium text-foreground ">
                        Phòng của tôi
                    </a>
                    <a href="{{ Route("tenant.thanhtoan") }}" class="text-sm font-medium text-foreground">
                        Thanh toán
                    </a>
                    <span class="text-sm font-medium text-foreground opacity-50 cursor-not-allowed">
                        Danh bạ
                    </span>
                </nav>

                <!-- Right Actions -->
                <div class="flex items-center gap-4">
                    <button class="hidden text-sm font-medium text-foreground hover:text-primary lg:block">
                        Tải ứng dụng
                    </button>
                    <button class="hidden lg:block">
                        <i class="fas fa-heart h-5 w-5 text-foreground"></i>
                    </button>

                    @if($user)
                    <!-- Hiển thị khi đã đăng nhập -->
                    <div class="flex items-center gap-2">
                        <span class="text-sm">Xin chào, {{ $user['name'] }}</span>
                        <form method="POST" action="/logout">
                            @csrf
                            <button type="submit" class="text-sm font-medium hover:text-primary">
                                Đăng xuất
                            </button>
                        </form>
                    </div>
                    @else
                    <!-- Hiển thị khi chưa đăng nhập -->
                    <a href="/login"
                        class="hidden lg:inline-flex bg-transparent border border-gray-300 rounded-md px-4 py-2 text-sm font-medium text-foreground hover:text-primary">
                        Đăng nhập
                    </a>
                    <a href="/register"
                        class="hidden lg:inline-flex bg-transparent border border-gray-300 rounded-md px-4 py-2 text-sm font-medium text-foreground hover:text-primary">
                        Đăng ký
                    </a>
                    @endif

                    <span class="bg-[#E03C31] text-white hover:bg-[#C02F25] rounded-md px-4 py-2 cursor-pointer">
                        Đăng tin
                    </span>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main>
            @yield('content')
        </main>

        <!-- Footer -->
        @include('partials.footer')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    @yield('scripts')
</body>

</html>