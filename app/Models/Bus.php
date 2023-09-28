<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bus extends Model
{
    protected $table = 'buses';
    protected $primaryKey = 'id';
    protected $fillable = ['total_seat','booked_seat','available_seat','minimum_seat', 'bus_registration_number', 'status', 'trip_number', 'trip_description', 'bus_depart_from', 'bus_destination', 'departure_time', 'price_per_seat', 'estimate_arrive_time', 'departure_date','id_organizations'];
    public $timestamps = false;

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function busbooking()
    {
        return $this->hasMany(Bus_Booking::class);
    }
}
