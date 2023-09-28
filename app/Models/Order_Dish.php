<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order_Dish extends Model
{
    protected $table = 'order_dish';
    protected $primaryKey = 'id';
    protected $fillable = [
        'quantity',
        'order_id',
        'dish_id'
    ];
}
