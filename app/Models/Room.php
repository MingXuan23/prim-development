<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Homestay;

class Room extends Model
{
    protected $primaryKey = 'roomid';

    protected $fillable = [
        'roomname',
        'roompax',
        'details',
        'price',
        'status',
        'homestayid',
        'address',
        'created_at',
        'updated_at',
    ];

    public $timestamps = false;

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function booking()
    {
        return $this->hasMany(Booking::class);
    }
    public function homestayImage(){
        return $this->hasMany(HomestayImage::class, 'room_id', 'roomid');// 2nd parameter: foreign key for Room in HomestayImage,3rd parameter: primary key for Room
    }
    // for seach function
    public function scopeSearch($query, $term){
        return $query->where('deleted_at', null)
        ->where(function ($query) use ($term) {
            $query->where('state', 'like', '%' . $term . '%')
                ->orWhere('district', 'like', '%' . $term . '%');
        });
    }
}
