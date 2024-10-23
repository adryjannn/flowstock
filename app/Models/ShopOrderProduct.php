<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ShopOrderProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_order',
        'id_shop_order',
        'product_code',
        'product_name',
        'product_price',
        'quantity',
    ];

    public function order()
    {
        return $this->belongsTo(ShopOrder::class, 'id_order');
    }

    protected $table = 'shop_orders_product';

}
