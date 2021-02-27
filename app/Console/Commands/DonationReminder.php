<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

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
                ->join('user_donation_reminder','users.id','=','user_donation_reminder.user_id')
                ->join('donations','user_donation_reminder.donation_id','=','donations.id')
                ->join('donation_reminder','user_donation_reminder.reminder_id','=','donation_reminder.id')
                ->select('users.email','users.name','users.device_token','donations.nama','donation_reminder.date','donation_reminder.day','donation_reminder.time')
                ->get();

            $firebaseToken = User::whereNotNull('device_token')->pluck('device_token')->all();
          
            $SERVER_API_KEY = 'AAAAZ2cxdAY:APA91bG1viidrnyfHhJR_cvcmDBhyTubyqXLvu-g_gN6_cq5pBj1DiFW6CriQZr7zQ2Bz-bX4UZNWFSS7HX4bbpiniZpupvmwlZ_4hCySVwPVIqhNKB6Ejh5O7npWkTHtIM6MSMt9qqO';
      
            $data = [
                "registration_ids" => $firebaseToken,
                "notification" => [
                    "title" => $data->name,
                    "body" => $data->nama,  
                ]
            ];
            $dataString = json_encode($data);
        
            $headers = [
                'Authorization: key=' . $SERVER_API_KEY,
                'Content-Type: application/json',
            ];
        
            $ch = curl_init();
          
            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
                   
            $response = curl_exec($ch);
      
            dd($response);
    }
}
