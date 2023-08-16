<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Homestay;

class Promotion extends Model
{
    protected $primaryKey = 'promotionid';
    protected $fillable = [
        'promotionname',
        'datefrom',
        'dateto',
        'discount',
        'homestayid'
    ];

    public $timestamps = false;

    public function homestay()
    {
        return $this->belongsTo(Homestay::class, 'homestayid');
    }
}
