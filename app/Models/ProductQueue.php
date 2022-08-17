<?php

namespace App;

use App\Models\ProductItem;
use Illuminate\Database\Eloquent\Model;

class ProductQueue extends Model
{
    protected $table = "product_queue";
    protected $fillable = ['product_item_id', 'start_slot_time', 'end_slot_time', 'quantity_available', 'status'];
    public $timestamps = true;

    public function product_item()
    {
        return $this->belongsTo(ProductItem::class);
    }
}
