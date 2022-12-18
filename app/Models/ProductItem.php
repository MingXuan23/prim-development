<?php

namespace App\Models;

use App\ProductQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductItem extends Model
{
    use SoftDeletes;

    protected $table = "product_item";
    protected $fillable = ['name', 'desc', 'type', 'quantity_available', 'price', 'image', 'status', 'product_group_id'];
    public $timestamps = true;

    public function product_order()
    {
        return $this->hasMany(ProductOrder::class, 'product_item');
    }

    public function product_group()
    {
        return $this->belongsTo(ProductGroup::class);
    }

    public function pickup_order()
    {
        return $this->belongsToMany(PickUpOrder::class, 'product_order')
        ->withPivot('quantity', 'status', 'deleted_at')
        ->whereNull('deleted_at')
        ->withTimestamps();
    }
}
