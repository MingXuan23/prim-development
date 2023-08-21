<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\customer;
use App\room;
use App\payment;

class booking extends Model
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

    public $timestamps = false;

    public function users()
    {
        return $this->belongsTo(User::class);
    }
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
