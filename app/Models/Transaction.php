<?php

namespace App\Models;

use App\Models\Donation;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    //
    public function donation()
    {
        return $this->belongsToMany(Donation::class, 'donation_transaction', 'transaction_id', 'donation_id');
    }
    public $timestamps = false;

    protected $dates = ['datetime_created'];

}
