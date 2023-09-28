<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotifyBus extends Model
{
    protected $table = 'bus_notifys';
    protected $primaryKey = 'id';
    protected $fillable = ['id_bus','id_user','status','time_notify'];
    public $timestamps = false;

    public function grab()
    {
        return $this->belongsTo(Bus::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
