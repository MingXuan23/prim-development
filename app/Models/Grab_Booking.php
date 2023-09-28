<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grab_Booking extends Model
{
    protected $table = 'grab_bookings';
    protected $primaryKey = 'id';
    protected $fillable = ['id_destination_offer','id_user','book_date'];
    public $timestamps = false;

    public function grab()
    {
        return $this->belongsTo(Destination_Offer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
