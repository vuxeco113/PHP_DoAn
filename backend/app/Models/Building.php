<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;
class Building extends Model
{
    use HasApiTokens, HasFactory, Notifiable;
  //  protected $keyType = 'string';
    protected $fillable = [
        'buildingName',
        'address',
        'imageUrls',
        'latitude',
        'longitude',
        'totalRooms',
        'managerId',
    ];
    protected $casts = [
        'imageUrls' => 'array',
    ];
    public function Users()
    {
        return $this->hasOne(User::class,'managerId');
    }
     public function rooms()
    {
        return $this->hasMany(Room::class, 'buildingId');
    }

    public function getImageUrlsAttribute($value)
    {
        if (!$value)
            return [];

        // Nếu value là string (JSON), decode thành array
        $paths = is_string($value) ? json_decode($value, true) : $value;

        if (!is_array($paths))
            return [];

        // Chuyển các path thành full URLs
        return array_map(function ($path) {
            // Nếu path đã là full URL thì giữ nguyên
            if (filter_var($path, FILTER_VALIDATE_URL)) {
                return $path;
            }
            // Chuyển relative path thành full URL
            return Storage::url($path);
        }, $paths);
    }

}
