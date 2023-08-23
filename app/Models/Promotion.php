<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
