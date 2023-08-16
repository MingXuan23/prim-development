<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Promotion;
use App\Models\Room;

class Homestay extends Model
{
    protected $primaryKey = 'homestayid';
    protected $fillable = [
        'name',
        'location',
        'pno',
        'status',
        'ownerid'
    ];

    public $timestamps = false;

    public function rooms()
    {
        return $this->hasMany(Room::class, 'homestayid');
    }

    public function promotion()
    {
        return $this->hasMany(Promotion::class, 'homestayid');
    }
}
