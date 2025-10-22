<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
class PhieuSua extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    // Tên bảng trong database
    protected $table = 'phieu_sua';

    // Các cột có thể gán giá trị hàng loạt
    protected $fillable = [
        'faultSource',
        'items',
        'ngaySua',
        'requestId',
        'roomId',
        'tenantId',
        'tongTien',
        'status',
    ];

    // Tự động chuyển kiểu dữ liệu cho các cột
    protected $casts = [
        'items' => 'array',      // Laravel tự decode JSON sang mảng PHP
        'ngaySua' => 'date',
        'tongTien' => 'float',
    ];

    // -----------------------
    // 🔗 Định nghĩa quan hệ
    // -----------------------

    // Phiếu sửa thuộc về 1 request
    public function request()
    {
        return $this->belongsTo(Request::class, 'requestId');
    }

    // Phiếu sửa thuộc về 1 phòng
    public function room()
    {
        return $this->belongsTo(Room::class, 'roomId');
    }

    // Phiếu sửa thuộc về 1 người thuê
    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenantId');
    }
}
