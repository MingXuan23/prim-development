<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    protected $fillable = ['delivery_status', 'dish_available_id', 'user_id', 'order_description'];
}
