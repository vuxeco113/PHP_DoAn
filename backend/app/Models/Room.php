<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;
class Room extends Model
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'title',
        'description',
        'price',
        'area',
        'capacity',
        'amenities',
        'imageUrls',
        'status',
        'latitude',
        'longitude',
        'sodien',
        'currentTenantId',
        'ownerId',
        'rentStartDate',
        'buildingId'
    ];

    protected $casts = [
        'amenities' => 'array',
        'imageUrls' => 'array',
        'price' => 'decimal:2',
        'area' => 'decimal:2',
        'rentStartDate' => 'date',
    ];
    public function building()
    {
        return $this->belongsTo(Building::class, 'buildingId', 'id');
    } 
    public function requests()
    {
        return $this->hasMany(Request::class, 'room_id');
    }

}
