<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PgngOrder extends Model
{
    use SoftDeletes;

    protected $table = "pgng_orders";
    
    protected $fillable = ['order_type', 'pickup_date', 'delivery_date', 'total_price', 'address', 'state', 'postcode', 'city', 'note', 'expired_at', 'status', 'user_id', 'organization_id', 'transaction_id'];
    
    public $timestamps = true;
}
