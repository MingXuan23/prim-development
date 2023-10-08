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
        'homestayid',
        'address',
        'homestay_image_id',
        'created_at',
        'updated_at',
    ];

    public $timestamps = false;

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function booking()
    {
        return $this->hasMany(Booking::class);
    }
}
