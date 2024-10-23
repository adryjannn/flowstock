<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderState extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'position', 'color'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

}
