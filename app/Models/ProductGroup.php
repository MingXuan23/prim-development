<?php

namespace App\Models;

use App\Queue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductGroup extends Model
{
    use SoftDeletes;
    protected $table = "product_group";
    protected $fillable = ['name', 'duration', 'organization_id'];
    public $timestamps = true;

    public function product_item()
    {
        return $this->hasMany(ProductItem::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function queues()
    {
        return $this->hasMany(Queue::class);
    }
}
