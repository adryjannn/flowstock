<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopOrderStatus extends Model
{
    use HasFactory;

    protected $primaryKey = 'shop_order_status_id';
    public $incrementing = false;
    protected $keyType = 'unsignedBigInteger';

    protected $fillable = [
        'shop_order_status_id',
        'name',
        'color',
    ];


    public function orders()
    {
        return $this->hasMany(ShopOrder::class, 'order_state', 'shop_order_status_id');
    }

}
