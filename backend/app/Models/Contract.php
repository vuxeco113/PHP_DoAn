<?php
// app/Models/Contract.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Contract extends Model
{
     use HasApiTokens, HasFactory, Notifiable;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'owner_id',
        'tenant_id',
        'room_id',
        'deposit_amount',
        'rent_amount',
        'start_date',
        'end_date',
        'terms_and_conditions',
        'status',
        'payment_history_ids',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'deposit_amount' => 'integer',
        'rent_amount' => 'integer',
        'payment_history_ids' => 'array',
    ];

    /**
     * Quan hệ với chủ trọ
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Quan hệ với khách thuê
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    /**
     * Quan hệ với phòng
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    /**
     * Scope cho hợp đồng đang hoạt động
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }

    /**
     * Scope cho hợp đồng đã hết hạn
     */
    public function scopeExpired($query)
    {
        return $query->where('status', 'expired')
                    ->orWhere('end_date', '<', now());
    }

    /**
     * Kiểm tra hợp đồng có đang hoạt động không
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && 
               $this->start_date <= now() && 
               $this->end_date >= now();
    }

    /**
     * Kiểm tra hợp đồng đã hết hạn chưa
     */
    public function isExpired(): bool
    {
        return $this->status === 'expired' || $this->end_date < now();
    }

    /**
     * Tính số ngày còn lại
     */
    public function getDaysRemainingAttribute(): int
    {
        return now()->diffInDays($this->end_date, false);
    }

    /**
     * Tính tổng số tiền cần thanh toán (tiền thuê + đặt cọc)
     */
    public function getTotalAmountAttribute(): int
    {
        return $this->rent_amount + $this->deposit_amount;
    }
}