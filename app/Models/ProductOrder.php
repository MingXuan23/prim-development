<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductOrder extends Model
{
    use SoftDeletes;

    protected $table = "product_order";
    protected $fillable = ['quantity', 'selling_quantity', 'product_item_id', 'pgng_order_id'];
    public $timestamps = true;

    public function product_item()
    {
        return $this->belongsTo(ProductItem::class);
    }

    public function pickup_order()
    {
        return $this->belongsTo(PickUpOrder::class);
    }

    public function queues()
    {
        return $this->belongsToMany(Queue::class);
    }
}
