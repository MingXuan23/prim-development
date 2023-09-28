<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Destination_Offer extends Model
{
    protected $table = 'destination_offers';
    protected $primaryKey = 'id';
    protected $fillable = ['destination_name', 'pick_up_point', 'status',  'price_destination', 'id_grab_student', 'available_time'];
    public $timestamps = false;

    public function grab()
    {
        return $this->belongsTo(Grab_Student::class);
    }

    public function grabbook()
    {
        return $this->hasMany(Grab_Booking::class);
    }
}
