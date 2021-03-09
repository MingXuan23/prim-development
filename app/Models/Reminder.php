<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    protected $table = "donation_reminder";
    
    public function donation()
    {
        return $this->belongsToMany(Organization::class, 'user_donation_reminder', 'reminder_id', 'donation_id');
    }
}
