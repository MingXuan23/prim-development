<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Booking extends Model
{
    protected $primaryKey = 'bookingid';
    protected $fillable = [
        'checkin',
        'checkout',
        'status',
        'totalprice',
        'customerid',
        'roomid',
        'transactionid',
        'review_star',
        'review_comment',
        'discount_received',
        'increase_received',
        'booked_rooms',
        'review_images',
        'deposit_amount',
        'transaction_balance_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class ,'customerid' ,'id');
    }
    public function room()
    {
        return $this->belongsTo(Room::class ,'roomid');
    }
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
    
}
