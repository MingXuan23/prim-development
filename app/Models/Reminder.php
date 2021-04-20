<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    protected $fillable = ['date','time','day','recurrence'];

    public function donation()
    {
        return $this->belongsTo(Donation::class);
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
        $reminders = Reminder::with('donation')->get();

        return $reminders;
    }
}
