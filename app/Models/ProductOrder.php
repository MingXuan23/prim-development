<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductOrder extends Model
{
    use SoftDeletes;

    protected $table = "product_order";
    protected $fillable = ['quantity', 'status', 'product_item_id', 'koop_order_id'];

    public function product_item()
    {
        return $this->belongsTo(ProductItem::class);
    }

    public function koop_order()
    {
        return $this->belongsTo(KoopOrder::class);
    }
}
