<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Models\Transaction;
use App\Models\Organization;
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
        $organizations = OrganizationController::getOrganizationByUserId();
        
        return view("index", compact('organizations'));
    }

    public function getDashboardItem(Request $requests)
    {
        $organizationId = $requests->id;

        // get total donors by day,week,month
        $donorsDays = $this->getTotalDonorByDay($organizationId);
        $donorsWeeks = $this->getTotalDonorByWeek($organizationId);
        $donorsMonths = $this->getTotalDonorByMonth($organizationId);
        
        // get total donation by day,week,month
        $donationDays = $this->getTotalDonationByDay($organizationId);
        $donationWeeks = $this->getTotalDonationByWeek($organizationId);
        $donationMonths = $this->getTotalDonationByMonth($organizationId);

        $dashboard = [
            "donor_day" => $donorsDays,
            "donor_week" => $donorsWeeks,
            "donor_month" => $donorsMonths,
            "donation_day" => $donationDays,
            "donation_week" => $donationWeeks,
            "donation_month" => $donationMonths
        ];

        // $org = Transaction::with(["donation.organization"])->whereHas('donation', function (Builder $query) use ($organizationId) {
        //     $query->whereHas("organization", function ($qTool) use ($organizationId) {
        //         $qTool->where("id", $organizationId);
        //     });
        // })->get();
        // $org = Organization::where("id", $organizationId)->with(["donations.transactions"])->get();
        return response()->json([
            'success' => true,
            'message' => "Succesfully",
            'data' => $dashboard,
        ], 200);
        // $dashboard = json_encode($dashboard);

        // return $dashboard;
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

    public function getTotalDonorByDay($organizationId)
    {
        $donors = Transaction::getTransactionByOrganizationIdAndStatus($organizationId)
                    ->whereRaw('date(transactions.datetime_created) = curdate()')
                    ->select(DB::raw('count(transactions.id) as donor'))
                    ->first();

        return $donors;
    }

    public function getTotalDonorByWeek($organizationId)
    {
        $donors = Transaction::getTransactionByOrganizationIdAndStatus($organizationId)
                    ->whereRaw('YEARWEEK(transactions.datetime_created, 1) = YEARWEEK(CURDATE(), 1)')
                    ->select(DB::raw('count(transactions.id) as donor'))
                    ->first();

        return $donors;
    }

    public function getTotalDonorByMonth($organizationId)
    {
        $donors = Transaction::getTransactionByOrganizationIdAndStatus($organizationId)
                    ->whereRaw('year(transactions.datetime_created) = year(curdate())')
                    ->whereRaw('month(transactions.datetime_created) = month(curdate())')
                    ->select(DB::raw('count(transactions.id) as donor'))
                    ->first();

        return $donors;
    }

    public function getTotalDonationByDay($organizationId)
    {
        $totalDonation = Transaction::getTransactionByOrganizationIdAndStatus($organizationId)
                        ->whereRaw('date(transactions.datetime_created) = curdate()')
                        ->first();

        return $totalDonation;
    }

    public function getTotalDonationByWeek($organizationId)
    {
        $totalDonation = Transaction::getTransactionByOrganizationIdAndStatus($organizationId)
                        ->whereRaw('YEARWEEK(transactions.datetime_created, 1) = YEARWEEK(CURDATE(), 1)')
                        ->select(DB::raw('sum(transactions.amount) as donation_amount'))
                        ->first();

        return $totalDonation;
    }

    public function getTotalDonationByMonth($organizationId)
    {
        $totalDonation = Transaction::getTransactionByOrganizationIdAndStatus($organizationId)
                        ->whereRaw('year(transactions.datetime_created) = year(curdate())')
                        ->whereRaw('month(transactions.datetime_created) = month(curdate())')
                        ->select(DB::raw('sum(transactions.amount) as donation_amount'))
                        ->first();

        return $totalDonation;
    }
}
