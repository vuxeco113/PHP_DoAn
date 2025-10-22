<?php

namespace App\Models;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
class Compensation extends Model
{
    //
    use HasApiTokens, HasFactory, Notifiable;
    
    protected $table = 'compensations';
   protected $fillable = [
        'contract_id',
        'date',
        'items',
        'violation_terms',
        'total_amount'
    ];

    protected $casts = [
        'date' => 'datetime',
        'items' => 'array', // Tự động convert JSON <-> Array
        'violation_terms' => 'array', // Tự động convert JSON <-> Array
        'total_amount' => 'decimal:2'
    ];

    // Relationship
    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }

    // Accessor - Lấy tổng số items
    public function getTotalItemsAttribute()
    {
        return count($this->items);
    }

    // Accessor - Lấy tổng số vi phạm
    public function getTotalViolationsAttribute()
    {
        return count($this->violation_terms);
    }
}