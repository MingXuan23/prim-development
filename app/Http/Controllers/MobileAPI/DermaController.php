<?php

namespace App\Http\Controllers\MobileAPI;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class DermaController extends Controller
{
    public function login(Request $request)
    {  
        Auth::logout();
       $credentials = $request->only('email', 'password');
       $phone = $request->get('email');
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
           $newToken = $user->id . $randomString;
       
           // Update the user's device_token with the new token
           $user->remember_token = $newToken;
           $user->save();
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

        return response()->json(['data'=>$groupedDonations]);
        //dd($groupedDonations);
    }

    public function returnDermaView(Request $request){
        try{
            $token = $request->token;
            $donation_id = $request->donation_id;
            $tanpaNama = $request->desc == "Derma Tanpa Nama";

            $user = User::where('remember_token',$token)->first();
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

            if($tanpaNama){
                return view('paydonate.anonymous.index', compact('donation','referral_code','message'));

            }else{
                $specialSrabRequest=0;
                if($donation->id==161){
                    $specialSrabRequest=1;
                }
                return view('paydonate.pay', compact('donation', 'user','specialSrabRequest','referral_code','message'));

            }

            }catch(Exception $e){
                return view('errors.404');
            }
        

    }
}
