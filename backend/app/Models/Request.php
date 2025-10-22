<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;
class Request extends Model
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'user_khach_id',
        'room_id',
        'loai_request',
        'name',
        'sdt',
        'mo_ta',
        'status',
        'thoi_gian'
    ];

    protected $casts = [
        'thoi_gian' => 'datetime',
    ];

    /**
     * Relationship với Room
     */
    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }
    /**
     * Relationship với User (nếu có bảng users)
     */
    public function user()
    {
        // Nếu bạn có bảng users, thêm relationship này
        return $this->belongsTo(User::class, 'user_khach_id');
    }

    /**
     * Scope để lấy requests theo trạng thái
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Kiểm tra request có đang pending không
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Kiểm tra request đã được approved chưa
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    /**
     * Kiểm tra request đã bị rejected chưa
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    /**
     * Đánh dấu request là approved
     */
    public function markAsApproved()
    {
        $this->update(['status' => 'approved']);
    }

    /**
     * Đánh dấu request là rejected
     */
    public function markAsRejected()
    {
        $this->update(['status' => 'rejected']);
    }

    /**
     * Format thời gian
     */
    public function getFormattedTimeAttribute()
    {
        return $this->thoi_gian->format('d/m/Y H:i');
    }
}
