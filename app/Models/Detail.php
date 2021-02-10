<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Detail extends Model
{
    //
    protected $fillable = ['nama', 'price', 'quantity', 'totalamount', 'category_id'];

    public $timestamps = false;
}
