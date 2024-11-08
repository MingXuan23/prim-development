<?php

namespace App\Http\Controllers\MobileAPI;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use App\Models\DonationStreak;

use App\Http\Controllers\PointController;
use Exception;
use Illuminate\Support\Carbon;

class DermaController extends Controller
{
    public function validateToken(Request $request)
    {  
        $token = $request->token;

        $user = DB::table('user_token')->where('application_id',1)->where('remember_token',$token)->exists();
        //$user = DB::table('users')->where('remember_token',$token)->exists();

        if($user){
            return response()->json(['result' => 'Validated'], 200);
        }
        else{
            return response()->json(['result' => 'Unauthorized'], 401);
        }
    }

    public function getUserByToken($token){
        $user_token =  DB::table('user_token')->where('application_id',1)->where('remember_token',$token)->select('user_id')->first();
        if($user_token ==null)
            return null;

        $user = User::where('id',$user_token->user_id)->first();
        return $user;

    }

    public function updateToken($user_id,$token,$device_token){
        $update =  DB::table('user_token')
        ->where('application_id',1)
        ->where('user_id',$user_id)
        ->update([
            'remember_token' => $token,
            'updated_at' => Carbon::now(),
            'expired_at' =>Carbon::now()->addDays(7),
            'device_token' =>$device_token
        ]);

        if($update)
        {
            return;
        }

        $exist = DB::table('user_token')
        ->where('application_id',1)
        ->where('user_id',$user_id)
       ->exists();

       if($exist)
       {
        return;
       }
          
       DB::table('user_token')->insert([
        'application_id'=>1,
        'user_id'=>$user_id,
        'remember_token'=> $token,
        'updated_at' => Carbon::now(),
        'expired_at' =>Carbon::now()->addDays(7),
        'device_token' =>$device_token
       ]);
    }

    public function pointPage(Request $request)
    {

        // Extract the Authorization header (Bearer token)
        $authorizationHeader = $request->header('Authorization');

        // Check if the Authorization header exists and starts with 'Bearer '
        if ($authorizationHeader && strpos($authorizationHeader, 'Bearer ') === 0) {
            // Extract the token
            $token = substr($authorizationHeader, 7);
            $user = $this->getUserByToken($token);
            if($user){
                Auth::logout();
                Auth::loginUsingId($user->id);

                return redirect('/point');
            } 

            return response()->json(['error' => 'Unauthorized'], 401);
        
        } else {
            // Return an error if the Authorization header is missing or invalid
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function login(Request $request)
    {  
        Auth::logout();
       $credentials = $request->only('email', 'password');
       $phone = $request->get('email');
       $device_token = $request->get('device_token')??null;
       //return response()->json(['user',$credentials],200);
       if(is_numeric($request->get('email'))){
           $user = User::where('icno', $phone)->first();
          
           if ($user) {
               //dd($user);
               //return ['icno' => $phone, 'password' => $request->get('password')];
               $credentials = ['icno'=>$phone, 'password' => $request->get('password')];
           }
           else{
               if(!$this->startsWith((string)$request->get('email'),"+60") && !$this->startsWith((string)$request->get('email'),"60")){
                   if(strlen((string)$request->get('email')) == 10)
                   {
                       $phone = str_pad($request->get('email'), 12, "+60", STR_PAD_LEFT);
                   } 
                   elseif(strlen((string)$request->get('email')) == 11)
                   {
                       $phone = str_pad($request->get('email'), 13, "+60", STR_PAD_LEFT);
                   }   
               } else if($this->startsWith((string)$request->get('email'),"60")){
                   if(strlen((string)$request->get('email')) == 11)
                   {
                       $phone = str_pad($request->get('email'), 12, "+", STR_PAD_LEFT);
                   } 
                   elseif(strlen((string)$request->get('email')) == 12)
                   {
                       $phone = str_pad($request->get('email'), 13, "+", STR_PAD_LEFT);
                   }   
               }
               $credentials = ['telno'=>$phone,'password'=>$request->get('password')];
           }
       }
       else if(strpos($request->get('email'), "@") !== false){
           $credentials = ['email'=>$phone,'password'=>$request->get('password')];
       }
       else{
           $credentials =['telno' => $phone, 'password'=>$request->get('password')];

       }


       if (Auth::attempt($credentials)) {
           $user = Auth::User();
           $randomString = Str::random(25);
           $newToken =  Str::random(10) .$user->id . $randomString;
            $this->updateToken($user->id ,$newToken,$device_token);
           // Update the user's device_token with the new token
           
            //dd($user);
           return response()->json([
               'token' => $newToken,
               'name' => $user->name,
               //'referral_code'=>$user->device_token
           ], 200);
       }
       return response()->json(['error' => 'Unauthorized'], 401);
        
    }

    public function getDerma(){
        $dermas = DB::table('donations as d')
        ->join('donation_type as dt', 'dt.id', '=', 'd.donation_type')
        ->select('dt.nama as donation_type_name', 'd.id as donation_id', 'd.nama as donation_name', 'd.url')
        ->where('d.status', 1)
        ->get();

    // Organize the results into a structure where donations are grouped by their type
        $groupedDonations = $dermas->groupBy('donation_type_name')->map(function ($group) {
            return [
                'donation_type' => $group->first()->donation_type_name,
                'donations' => $group->map(function ($item) {
                    return [
                        'donation_id' => $item->donation_id,
                        'donation_name' => $item->donation_name,
                        'donation_url' => $item->url
                    ];
                })->values()->all(),
            ];
        })->values()->all();

        $relogin = true;
        $token = request()->header('token');
        if(isset($token)){
           
            $relogin = $this->getUserByToken($token)==null;

        }
        return response()->json(['data'=>$groupedDonations,'relogin'=>$relogin]);
        //dd($groupedDonations);
    }

    public function returnDermaView(Request $request){
        try{
            $token = $request->token;
            $donation_id = $request->donation_id;
            $tanpaNama = $request->desc == "Derma Tanpa Nama";

            $user =$this->getUserByToken($token);
            if (!$user) {
                throw new Exception('User not found');
            }
            Auth::logout();
            Auth::loginUsingId($user->id);
        // dd(Auth::id());
            $donation = DB::table('donations')->where('id',$donation_id)->first();
        // dd($request->donation_id,$request->token,$user,$donation,$donation_id);

            if($donation->status == 0)
            {
                return view('errors.404');
            }
            //$referral_code = request()->input('referral_code');

            $referral_code = "";
            $message = "";
          
            if ($tanpaNama) {
                // Redirect to the anonymous donation route
                return redirect()->route('ANONdonate', ['link' => $donation->url]);
            } else {
    
                // Redirect to the non-anonymous donation route
                return redirect()->route('URLdonate', [
                    'link' => $donation->url,
        
                ]);
            }

            }catch(Exception $e){
                return view('errors.404');
            }
        

    }

    public function getDermaInfo(Request $request){
        try{
            $token = $request->token;
            //dd('here');
            $user =$this->getUserByToken($token);
            if (!$user) {
                throw new Exception('User not found');
            }
            $code =DB::table('referral_code')->where('user_id',$user->id)->first();
            DonationStreak::fetchLastTransactionStatus(3,$user_id);

            $data = DonationStreak::getStreakData($user->id);
            
           // dd($jsondata);
           // $data = json_decode($jsondata);
            $data['prim_medal_days'] = 40;
            $data['sedekah_subuh_days'] = 40;
            $data['prim_point'] = isset($code)?$code->total_point:0;

            $controller = new PointController();

           // dd('here');
            $codeOfUser = $controller->getReferralCode(false,$user->id);
            $donationsToday = DB::table('point_history as ph')
            ->join('donation_transaction as dt', 'dt.transaction_id', 'ph.transaction_id')
            ->join('transactions as t', 't.id', 'ph.transaction_id')
            ->where('ph.status', 1)
            ->whereDate('t.datetime_created', today())
            ->where('ph.referral_code_id', $codeOfUser->id)
            ->select(
                DB::raw('COUNT(CASE WHEN ph.fromSubline = 0 THEN ph.id END) AS donation_today'),
                // DB::raw('COUNT(CASE WHEN ph.fromSubline = 1 THEN ph.id END) AS donation_member_today'),
                // DB::raw('COUNT(ph.id) AS total_donation_today')
            )
            ->first();

            $code = $codeOfUser ==null?null:$codeOfUser->code;
            $data['donation_today'] = $code ==null?0: $donationsToday->donation_today;
            

            return response()->json(['data'=>$data, 'code'=>$code]);
        }catch(Exception $e){
           //dd($e);
            return response()->json(['error' => 'Unauthorized'], 401);
        }
       
    }
}
