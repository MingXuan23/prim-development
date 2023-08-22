<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Booking extends Model
{
    protected $primaryKey = 'bookingid';
    protected $fillable = [
        'checkin',
        'checkout',
        'status',
        'totalprice',
        'customerid',
        'roomid'
    ];

    public function users()
    {
        return $this->belongsTo(User::class);
    }
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
