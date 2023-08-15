<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Homestay extends Model
{
    protected $primaryKey = 'homestayid';
    protected $fillable = [
        'name',
        'location',
        'pno',
        'status',
        'ownerid'
    ];

    public $timestamps = false;
}
