<!-- resources/views/partials/footer.blade.php -->
<footer class="bg-gray-900 text-white pt-12 pb-8">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Main Footer Content -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
            <!-- Company Info -->
            <div class="lg:col-span-2">
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-2xl font-bold text-[#E03C31]">Quản Lý trọ</span>
                    <span class="text-xs text-gray-400">PHP Mã nguồn mở</span>
                </div>
                <p class="text-gray-300 mb-4 leading-relaxed">
                    Nền tảng quản lý cho thuê trọ hàng đầu Việt Nam. Cung cấp giải pháp toàn diện 
                    cho chủ trọ và người thuê với công nghệ hiện đại, dễ sử dụng.
                </p>
                <div class="flex gap-4">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <i class="fab fa-facebook-f text-lg"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <i class="fab fa-youtube text-lg"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <i class="fab fa-tiktok text-lg"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <i class="fab fa-github text-lg"></i>
                    </a>
                </div>
            </div>

            <!-- For Landlords -->
            <div>
                <h3 class="text-lg font-semibold mb-4 text-white">Dành cho Chủ trọ</h3>
                <ul class="space-y-2">
                    <li>
                        <a href="#" class="text-gray-300 hover:text-white transition-colors text-sm">
                            Quản lý phòng trọ
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-gray-300 hover:text-white transition-colors text-sm">
                            Theo dõi thu chi
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-gray-300 hover:text-white transition-colors text-sm">
                            Quản lý hợp đồng
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-gray-300 hover:text-white transition-colors text-sm">
                            Báo cáo tự động
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-gray-300 hover:text-white transition-colors text-sm">
                            Đăng tin cho thuê
                        </a>
                    </li>
                </ul>
            </div>

            <!-- For Tenants -->
            <div>
                <h3 class="text-lg font-semibold mb-4 text-white">Dành cho Người thuê</h3>
                <ul class="space-y-2">
                    <li>
                        <a href="#" class="text-gray-300 hover:text-white transition-colors text-sm">
                            Tìm phòng trọ
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-gray-300 hover:text-white transition-colors text-sm">
                            Thanh toán online
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-gray-300 hover:text-white transition-colors text-sm">
                            Báo cáo sự cố
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-gray-300 hover:text-white transition-colors text-sm">
                            Hỗ trợ 24/7
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-gray-300 hover:text-white transition-colors text-sm">
                            Đánh giá chủ trọ
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Features Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 py-8 border-t border-gray-700">
            <div class="text-center">
                <div class="bg-[#E03C31] rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-shield-alt text-white"></i>
                </div>
                <h4 class="font-semibold text-sm mb-1">Bảo mật</h4>
                <p class="text-gray-400 text-xs">Dữ liệu được mã hóa an toàn</p>
            </div>
            <div class="text-center">
                <div class="bg-[#E03C31] rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-headset text-white"></i>
                </div>
                <h4 class="font-semibold text-sm mb-1">Hỗ trợ 24/7</h4>
                <p class="text-gray-400 text-xs">Đội ngũ hỗ trợ nhiệt tình</p>
            </div>
            <div class="text-center">
                <div class="bg-[#E03C31] rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-mobile-alt text-white"></i>
                </div>
                <h4 class="font-semibold text-sm mb-1">Đa nền tảng</h4>
                <p class="text-gray-400 text-xs">Web & Mobile App</p>
            </div>
            <div class="text-center">
                <div class="bg-[#E03C31] rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-wallet text-white"></i>
                </div>
                <h4 class="font-semibold text-sm mb-1">Tiết kiệm</h4>
                <p class="text-gray-400 text-xs">Chi phí tối ưu nhất</p>
            </div>
        </div>

        <!-- Contact Info -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 py-8 border-t border-gray-700">
            <div class="flex items-center justify-center md:justify-start">
                <div class="bg-gray-800 rounded-lg p-3 mr-3">
                    <i class="fas fa-phone text-[#E03C31]"></i>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Hotline</p>
                    <p class="font-semibold">1900 1234</p>
                </div>
            </div>
            <div class="flex items-center justify-center md:justify-start">
                <div class="bg-gray-800 rounded-lg p-3 mr-3">
                    <i class="fas fa-envelope text-[#E03C31]"></i>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Email</p>
                    <p class="font-semibold">support@quantro.vn</p>
                </div>
            </div>
            <div class="flex items-center justify-center md:justify-start">
                <div class="bg-gray-800 rounded-lg p-3 mr-3">
                    <i class="fas fa-map-marker-alt text-[#E03C31]"></i>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Địa chỉ</p>
                    <p class="font-semibold">Hà Nội, Việt Nam</p>
                </div>
            </div>
        </div>

        <!-- Bottom Footer -->
        <div class="border-t border-gray-700 pt-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-gray-400 text-sm mb-4 md:mb-0">
                    © 2024 Quản Lý trọ. Bản quyền thuộc về PHO Open Source.
                </div>
                <div class="flex flex-wrap justify-center gap-6">
                    <a href="#" class="text-gray-400 hover:text-white text-sm transition-colors">
                        Điều khoản sử dụng
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white text-sm transition-colors">
                        Chính sách bảo mật
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white text-sm transition-colors">
                        FAQs
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white text-sm transition-colors">
                        Liên hệ
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>

<style>
    .footer-link {
        position: relative;
        transition: all 0.3s ease;
    }
    
    .footer-link:hover {
        transform: translateX(5px);
    }
    
    .feature-icon {
        transition: all 0.3s ease;
    }
    
    .feature-icon:hover {
        transform: scale(1.1);
    }
</style>