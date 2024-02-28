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
        return $this->belongsTo(Organization::class ,'homestayid' ,'id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class ,'roomid');
    }
    public function homestayImage(){
        return $this->hasMany(HomestayImage::class, 'room_id', 'roomid');// 2nd parameter: foreign key for Room in HomestayImage,3rd parameter: primary key for Room
    }
    public function disabledDates(){
        return $this->hasMany(HomestayDisabledDate::class, 'homestay_id' , 'roomid');
    }
    // for seach function
    public function scopeSearch($query, $term){
        return $query->where('deleted_at', null)
        ->where(function ($query) use ($term) {
            $query->where('state', 'like', '%' . $term . '%')
                ->orWhere('district', 'like', '%' . $term . '%')
                ->orWhere('area', 'like', '%' . $term . '%')
                ->orWhere('roomname', 'like', '%' . $term . '%');
        });
    }

    // for filter out a list of homestay for autocomplete
    public function scopeFilterByHomestay($query , $term){
        return $query->where('deleted_at', null)
        ->where(function ($query) use ($term) {
                $query->where('roomname', 'like', '%' . $term . '%');
        });
    }
}
