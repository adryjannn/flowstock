<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    protected $fillable = ['key', 'value'];

    public static function getValue(string $key, $default = null)
    {
        return self::where('key', $key)->pluck('value')->first() ?? $default;
    }
}
