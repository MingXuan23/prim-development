<?php

namespace App\Http\Controllers;

use DB;
use App\User;
use App\Models\Transaction;
use App\Models\Organization;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\OrganizationController;

class HomeController extends AppBaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $organizations = OrganizationController::getOrganizationByUserId();
        
        return view("index", compact('organizations'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function form()
    {
        return view("form");
    }

    public function showNotification()
    {
        return view('notification.push-notification');
    }

    public function saveToken(Request $request)
    {
        $userId = Auth::id();

        auth()->user()->where('id', $userId)->update(['device_token'=>$request->token]);
        return response()->json(['token saved successfully.']);
    }
  
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function sendNotification(Request $request)
    {
        $firebaseToken = User::whereNotNull('device_token')->pluck('device_token')->all();
          
        $SERVER_API_KEY = 'AAAAZ2cxdAY:APA91bG1viidrnyfHhJR_cvcmDBhyTubyqXLvu-g_gN6_cq5pBj1DiFW6CriQZr7zQ2Bz-bX4UZNWFSS7HX4bbpiniZpupvmwlZ_4hCySVwPVIqhNKB6Ejh5O7npWkTHtIM6MSMt9qqO';
  
        $data = [
            "registration_ids" => $firebaseToken,
            "notification" => [
                "title" => $request->title,
                "body" => $request->body,
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

    public function pwaOffline()
    {
        return view('vendor.laravelpwa.offline');
    }
}
