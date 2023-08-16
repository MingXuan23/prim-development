<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Homestay;

class Room extends Model
{
    protected $primaryKey = 'roomid';

    protected $fillable = [
        'roomname',
        'roompax',
        'details',
        'price',
        'status',
        'homestayid'

    ];

    public $timestamps = false;

    public function homestay()
    {
        return $this->belongsTo(Homestay::class, 'homestayid');
    }
}
