<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'id';
    protected $fillable = [
        'delivery_status',
        'user_id',
        'organ_id',
        'dish_available_id',
        'transaction_id',
        'order_description'
    ];

    //protected $fillable = ['delivery_status', 'dish_available_id', 'user_id', 'order_description'];
}
