<?php

namespace App\Models;

use App\Transaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Donation extends Model
{
    use SoftDeletes;

    protected $fillable = ['nama', 'description', 'date_created', 'date_started', 'date_end', 'status'];
    
    public $timestamps = false;

    protected $dates = ['deleted_at'];

    public function organization()
    {
        return $this->belongsToMany(Organization::class, 'donation_organization');
    }

    public function transaction()
    {
        return $this->belongsToMany(Transaction::class, 'donation_transaction');
    }

    public function donation()
    {
        return $this->belongsToMany(Reminder::class, 'user_donation_reminder', 'donation_id', 'reminder_id');
    }
}
