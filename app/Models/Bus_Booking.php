<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bus_Booking extends Model
{
    protected $table = 'bus_bookings';
    protected $primaryKey = 'id';
    protected $fillable = ['id_bus','id_passenger'];
    public $timestamps = false;

    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
