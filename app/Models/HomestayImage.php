<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomestayImage extends Model
{
    protected $fillable = [
        'image_path',
        'room_id',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class,'room_id','roomid');//
    }
}
