<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'producer_id',
        'note',
        'status',
        'total_value',
        'pdf_file',
        'xls_file',
//        'confirmation_token',
    ];

    public function producer()
    {
        return $this->belongsTo(Producer::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function orderState()
    {
        return $this->belongsTo(OrderState::class, 'status'); // Relacja do OrderState przez pole 'status'
    }
}
