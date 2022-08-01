<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\User;

class KoopOrder extends Model
{
    use SoftDeletes;

    protected $table = "koop_order";

    protected $fillable = ['pickup_date', 'method_status', 'total_price', 'note', 'status', 'address', 'city', 'postcode', 'state', 'user_id', 'organization_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function product_order()
    {
        return $this->hasMany(ProductOrder::class);
    }

    public function product_item()
    {
        return $this->belongsToMany(ProductItem::class, 'product_order')
        ->withPivot('quantity', 'status', 'deleted_at')
        ->whereNull('deleted_at')
        ->withTimestamps();
    }
}
