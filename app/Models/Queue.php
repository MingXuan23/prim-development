<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    protected $table = "queues";
    protected $fillable = ['slot_time', 'status', 'slot_number', 'product_group_id'];
    public $timestamps = true;

    public function product_group()
    {
        return $this->belongsTo(ProductGroup::class);
    }

    public function product_order()
    {
        return $this->belongsToMany(ProductOrder::class);
    }
}
