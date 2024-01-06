<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order_Available_Dish extends Model
{
    //
    protected $table = 'order_available_dish';
    protected $primaryKey = 'id';
    protected $fillable = [
        'quantity',
        'totalprice',
        'delivery_status',
        'delivery_proof_pic',
        'order_desc',
        'order_available_id',
        'order_cart_id'
    ];
}
