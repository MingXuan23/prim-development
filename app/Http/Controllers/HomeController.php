<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\OrganizationController;
use DB;

class HomeController extends Controller
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
        $donorsDays = $this->getTotalDonorByDay();
        $donorsWeeks = $this->getTotalDonorByWeek();
        $donorsMonths = $this->getTotalDonorByMonth();
        $organizations = OrganizationController::getOrganizationByUserId();
        
        return view("index", compact('organizations', 'donorsDays', 'donorsWeeks', 'donorsMonths'));
    }

    public function getTotalDonorDashboard(Request $requests)
    {
        $organizationId = $requests->id;
        $donorsDays = $this->getTotalDonorByDay($organizationId);
        $donorsWeeks = $this->getTotalDonorByWeek($organizationId);
        $donorsMonths = $this->getTotalDonorByMonth($organizationId);

        $donor = [
            "day" => $donorsDays,
            "week" => $donorsWeeks,
            "month" => $donorsMonths
        ];

        $donor = json_encode($donor);

        return $donor;
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

    public function getTotalDonorByDay()
    {
        $donors = DB::table('transactions')
                    ->join('donation_transaction', 'donation_transaction.transaction_id', '=', 'transactions.id')
                    ->join('donations', 'donation_transaction.donation_id', '=', 'donations.id')
                    ->where('donations.id', 1)
                    ->where('transactions.status', 'Success')
                    ->whereRaw('date(transactions.datetime_created) = curdate()')
                    ->select(DB::raw('count(transactions.id) as donor'))
                    ->get();

        return $donors;
    }

    public function getTotalDonorByWeek()
    {
        $donors = DB::table('transactions')
                    ->join('donation_transaction', 'donation_transaction.transaction_id', '=', 'transactions.id')
                    ->join('donations', 'donation_transaction.donation_id', '=', 'donations.id')
                    ->where('donations.id', 1)
                    ->where('transactions.status', 'Success')
                    ->whereRaw('YEARWEEK(transactions.datetime_created, 1) = YEARWEEK(CURDATE(), 1)')
                    ->select(DB::raw('count(transactions.id) as donor'))
                    ->get();

        return $donors;
    }

    public function getTotalDonorByMonth()
    {
        $donors = DB::table('transactions')
                    ->join('donation_transaction', 'donation_transaction.transaction_id', '=', 'transactions.id')
                    ->join('donations', 'donation_transaction.donation_id', '=', 'donations.id')
                    ->where('donations.id', 1)
                    ->where('transactions.status', 'Success')
                    ->whereRaw('year(transactions.datetime_created) = year(curdate())')
                    ->whereRaw('month(transactions.datetime_created) = month(curdate())')
                    ->select(DB::raw('count(transactions.id) as donor'))
                    ->get();

        return $donors;
    }
}
