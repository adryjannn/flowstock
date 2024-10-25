<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_shop_order',
        'order_reference',
        'payment_type',
        'carrier',
        'order_state',
        'total_paid',
        'total_shipping'
    ];

    public function products()
    {
        return $this->hasMany(ShopOrderProduct::class, 'id_order');
    }

    public function orderStatus()
    {
        return $this->belongsTo(ShopOrderStatus::class, 'order_state', 'shop_order_status_id');
    }
}
