<?php

namespace App\Notifications;

use App\User;

class DonationReminderNotification
{       
    public function __construct ($data) 
    {
        $firebaseToken = User::whereNotNull('device_token')->pluck('device_token')->all();
          
        $SERVER_API_KEY = 'AAAAZ2cxdAY:APA91bG1viidrnyfHhJR_cvcmDBhyTubyqXLvu-g_gN6_cq5pBj1DiFW6CriQZr7zQ2Bz-bX4UZNWFSS7HX4bbpiniZpupvmwlZ_4hCySVwPVIqhNKB6Ejh5O7npWkTHtIM6MSMt9qqO';
  
        $data = [
            "registration_ids" => $firebaseToken,
            "notification" => [
                "title" => "Peringatan Derma: " . $data->nama,
                "body"  => $data->nama,  
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

