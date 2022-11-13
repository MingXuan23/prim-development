<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\User;

class PickUpOrder extends Model
{
    use SoftDeletes;

    protected $table = "pickup_order";
    
    protected $fillable = ['pickup_date', 'total_price', 'note', 'status', 'user_id', 'organization_id', 'transaction_id'];
    public $timestamps = true;

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
