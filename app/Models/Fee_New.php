<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fee_New extends Model
{
    //
    public $table = "fees_new";

    protected $fillable = ['name', 'desc', 'category', 'quantity', 'price', 'totalAmount', 'start_date', 'end_date', 'status', 'target', 'organization_id'];


    protected $casts = [
        'target' => 'array'
    ];

    public $timestamps = false;
}
