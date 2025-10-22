<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Bill extends Model
{
    //
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'bills';
    protected $fillable = [
        'khachThueId',// user khách
        'ownerId',// user chủ
        'roomId',// phòng
        'amenitiesPrice',//tự điền
        'date',// ngày tạo hóa đơn
        'paidAt',// ngày thanh toán
        'priceDien',//tự điền
        'priceRoom',//lấy từ hợp đồng
        'priceWater',// tự điền
        'soNguoi',// từ hợp phòng
        'sodienCu',//lấy từ phòng
        'sodienMoi',//tự điền
        'status',//lựa chọn pending/paid
        'sumPrice',// tính tổng tiền = priceRoom + (priceWater*soNguoi )+ (priceDien*(sodienMoi - sodienCu))
        'thangNam'// tháng năm hiện tại 
    ];



    // Relationship
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ownerId');
    }

    /**
     * Quan hệ với khách thuê
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'khachThueId');
    }

    /**
     * Quan hệ với phòng
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'roomId');
    }
}
