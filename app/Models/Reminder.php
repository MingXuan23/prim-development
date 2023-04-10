<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Illuminate\Support\Facades\Auth;

class Reminder extends Model
{
    protected $fillable = ['date','time','day','recurrence'];

    public function donation()
    {
        return $this->belongsTo(Donation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getReminderByDonationId($donationId)
    {
        $reminders = Reminder::with(["donation"])->whereHas('donation', function ($query) use ($donationId) {
            $query->where("id", $donationId);
        })->get();

        return $reminders;
    }

    public function getAllReminder()
    {
        $userId = Auth::id();
        $reminders = Reminder::with('donation')->where('user_id', $userId)->get();

        return $reminders;
    }
}
