<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
class DonViSuaChua extends Model
{
    //
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'donvisuachuas';

    // Các cột có thể gán hàng loạt (mass assignment)
    protected $fillable = [
        'ten',
        'dia_chi',
    ];
}
