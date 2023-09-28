<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotifyGrab extends Model
{
    protected $table = 'grab_notifys';
    protected $primaryKey = 'id';
    protected $fillable = ['id_destination_offer','id_user','status','time_notify'];
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
