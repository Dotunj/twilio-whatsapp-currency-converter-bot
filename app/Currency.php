<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    public static function findByCurrencyCode(array $items)
    {
        return static::whereIn('code', $items)->get();
    }
}
