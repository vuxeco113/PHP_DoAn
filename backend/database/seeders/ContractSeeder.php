<?php
// database/seeders/ContractSeeder.php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\User;
use App\Models\Room;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ContractSeeder extends Seeder
{
    public function run()
    {
        // Lấy một số user và room để tạo dữ liệu mẫu
        $owners = User::where('role', 'owner')->take(2)->get();
        $tenants = User::where('role', 'tenant')->take(3)->get();
        $rooms = Room::take(3)->get();

        if ($owners->isEmpty() || $tenants->isEmpty() || $rooms->isEmpty()) {
            $this->command->info('Không đủ dữ liệu user hoặc room để tạo contracts!');
            return;
        }

        $contracts = [
            [
                'id' => '5OYCpBMIpNWkzPbbxRIA',
                'owner_id' => 'aVgJBfGm0IRcQVTmXEb1QJBftT73',
                'tenant_id' => 'nYo93OOxTrZtoiV8a59AwuQ51E53',
                'room_id' => '6837548b-8e15-40e5-b627-418dabc3d93b',
                'deposit_amount' => 5000000,
                'rent_amount' => 5000000,
                'start_date' => '2025-06-15 14:58:53',
                'end_date' => '2025-12-15 14:58:53',
                'terms_and_conditions' => '**ĐIỀU KHOẢN VÀ ĐIỀU KIỆN HỢP ĐỒNG THUÊ TRỌ** **Bên A (Bên Cho Thuê):** **Bên B (Bên Thuê):** Hai bên thống nhất ký kết hợp đồng thuê trọ với các điều khoản sau: **I. TRÁCH NHIỆM CỦA BÊN A (BÊN CHO THUÊ):** 1. Bàn giao phòng trọ cho Bên B đúng theo hiện trạng đã thỏa thuận, đảm bảo các trang thiết bị cơ bản (nếu có) hoạt động bình thường. 2. Đảm bảo quyền sử dụng riêng biệt, trọn vẹn phần diện tích thuê của Bên B. 3. Cung cấp đầy đủ, kịp thời các dịch vụ đã cam kết (điện, nước, internet - nếu có) và thu phí theo đúng quy định/thỏa thuận. 4. Thực hiện sửa chữa các hư hỏng thuộc về cấu trúc của căn nhà hoặc các thiết bị do Bên A lắp đặt (trừ trường hợp hư hỏng do lỗi của Bên B). 5. Thông báo trước cho Bên B một khoảng thời gian hợp lý (ví dụ: 07 ngày) nếu có kế hoạch sửa chữa lớn hoặc các thay đổi ảnh hưởng đến việc sử dụng phòng của Bên B. **II. TRÁCH NHIỆM CỦA BÊN B (BÊN THUÊ):** 1. Thanh toán tiền thuê phòng và các chi phí dịch vụ khác (nếu có) đầy đủ và đúng hạn theo thỏa thuận. 2. Sử dụng phòng trọ đúng mục đích thuê (để ở), giữ gìn vệ sinh chung và tài sản trong phòng. Không tự ý sửa chữa, thay đổi kết cấu phòng khi chưa có sự đồng ý của Bên A. 3. Chịu trách nhiệm đối với những hư hỏng tài sản trong phòng do lỗi của mình gây ra. 4. Chấp hành các quy định về an ninh trật tự, phòng cháy chữa cháy của khu vực và nội quy nhà trọ (nếu có). Không tàng trữ, sử dụng các chất cấm, chất cháy nổ. 5. Không cho người khác thuê lại hoặc chuyển nhượng hợp đồng thuê khi chưa có sự đồng ý bằng văn bản của Bên A. Bàn giao lại phòng và các trang thiết bị (nếu có) cho Bên A khi hết hạn hợp đồng hoặc chấm dứt hợp đồng trước thời hạn theo đúng hiện trạng ban đầu (có tính hao mòn tự nhiên). **III. ĐIỀU KHOẢN CHUNG:** 1. Mọi sửa đổi, bổ sung điều khoản của hợp đồng này phải được hai bên thỏa thuận bằng văn bản. 2. Hợp đồng này được lập thành 02 bản, mỗi bên giữ 01 bản và có giá trị pháp lý như nhau. 3. Hợp đồng có hiệu lực kể từ ngày ký.',
                'status' => 'expired',
                'payment_history_ids' => [],
            ],
        ];

        // Tạo contract từ dữ liệu mẫu
        foreach ($contracts as $contractData) {
            Contract::create($contractData);
        }

        // Tạo thêm các contracts mẫu
        for ($i = 0; $i < 5; $i++) {
            Contract::create([
                'id' => Str::random(20),
                'owner_id' => $owners->random()->id,
                'tenant_id' => $tenants->random()->id,
                'room_id' => $rooms->random()->id,
                'deposit_amount' => rand(1000000, 10000000),
                'rent_amount' => rand(2000000, 8000000),
                'start_date' => now()->addDays(rand(-30, 30)),
                'end_date' => now()->addDays(rand(60, 365)),
                'terms_and_conditions' => $this->generateSampleTerms(),
                'status' => ['active', 'expired', 'pending'][rand(0, 2)],
                'payment_history_ids' => [],
            ]);
        }

        $this->command->info('Contracts seeded successfully!');
    }

    private function generateSampleTerms(): string
    {
        return 'Hợp đồng thuê phòng trọ với các điều khoản cơ bản về quyền và nghĩa vụ của hai bên. Bên A chịu trách nhiệm bảo trì cơ sở vật chất, Bên B chịu trách nhiệm sử dụng đúng mục đích và thanh toán đầy đủ.';
    }
}