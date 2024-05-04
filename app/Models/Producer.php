<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producer extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'full_name',
        'phone',
        'email',
        'delivery_time',
        'time_in_stock',
        'currency',
        'logistic_minimum',
        'logistic_minimum_alert',
        'order_time',


    ];
}
