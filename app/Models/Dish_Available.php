<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dish_Available extends Model
{
    protected $table = 'dish_available';
    protected $primaryKey = 'id';
    protected $fillable = [
        'date',
        'time',
        'delivery_address',
        'dish_id',
        'latitude',
        'longitude'
    ];
}
