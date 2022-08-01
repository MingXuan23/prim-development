<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    protected $table = "product_type";

    public function product_item()
    {
        return $this->hasMany(ProductItem::class);
    }

}
