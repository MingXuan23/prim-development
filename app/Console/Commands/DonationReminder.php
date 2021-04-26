<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Notifications\DonationReminderNotification;
use DB;

class DonationReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'donation:reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check notification reminder for user donation';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $data = DB::table('users')
                    ->join('user_donation_reminder', 'users.id', '=', 'user_donation_reminder.user_id')
                    ->join('donations', 'user_donation_reminder.donation_id', '=', 'donations.id')
                    ->join('donation_reminder', 'user_donation_reminder.reminder_id', '=', 'donation_reminder.id')
                    ->select('users.email', 'users.name', 'users.device_token', 'donations.nama', 'donation_reminder.date', 'donation_reminder.day', 'donation_reminder.recurrence', 'donation_reminder.time')
                    ->get();

        // dd($data);
            
        foreach ($data as $reminder) {
            $recurrence = $reminder->recurrence;
            $time = $reminder->time;
            $day = $reminder->day;
            $date = $reminder->date;

            //get current time
            $timeNow = now()->format('H:i');
                
            //get current day
            $dayNow = now()->dayOfWeekIso;

            //get current date
            $dateNow = now()->format('d');

            if ($recurrence == "daily") {
                if ($timeNow == $time) {
                    new DonationReminderNotification($reminder);
                } else {
                    print_r("Not time: daily \n");
                }
            } elseif ($recurrence == "weekly") {
                if (($timeNow == $time) && ($dayNow == $day)) {
                    new DonationReminderNotification($reminder);
                } else {
                    print_r("Not time: weekly \n");
                }
            } elseif ($recurrence == "monthly") {
                if (($timeNow == $time) && ($dateNow == $date)) {
                    new DonationReminderNotification($reminder);
                } else {
                    print_r("NNot time: monthly\n");
                }
            }
        }
    }
}
