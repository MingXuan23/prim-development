<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductType extends Model
{
    use SoftDeletes;
    protected $table = "product_type";
    protected $fillable = ['name', 'organization_id'];
    public $timestamps = true;

    public function product_item()
    {
        return $this->hasMany(ProductItem::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
