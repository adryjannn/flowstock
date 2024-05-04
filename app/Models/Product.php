<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_number', 'name', 'description', 'stock_available', 'producer_id', 'wholesale_price'
    ];

    public function producer()
    {
        return $this->belongsTo(Producer::class);
    }
}
