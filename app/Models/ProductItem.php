<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductItem extends Model
{
    use SoftDeletes;

    protected $table = "product_item";
    protected $fillable = ['name', 'desc', 'quantity', 'price', 'image', 'status', 'product_type_id', 'organization_id'];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function product_order()
    {
        return $this->hasMany(ProductOrder::class, 'product_item');
    }

    public function product_type()
    {
        return $this->hasOne(ProductType::class);
    }

    public function koop_order()
    {
        return $this->belongsToMany(KoopOrder::class, 'product_order')
        ->withPivot('quantity', 'status', 'deleted_at')
        ->whereNull('deleted_at')
        ->withTimestamps();
    }
}
