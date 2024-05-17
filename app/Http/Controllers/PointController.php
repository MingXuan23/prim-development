<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class PointController extends Controller
{
     public function index()
    {
        $user_id = Auth::id();

        $referral_code = $this->getReferralCode(true);

        $point_month = DB::table('point_history')
        ->where('referral_code_id',$referral_code->id)
        ->whereBetween('created_at', [now()->startOfMonth()->format('Y-m-d'), now()->endOfMonth()->format('Y-m-d')])
        ->where('status',1)
        ->sum('points');

        $continueStreak = DB::table('point_history')
        ->where('referral_code_id', $referral_code->id)
        ->where(function($query) {
            $query->whereDate('created_at', today())
                ->orWhereDate('created_at', today()->subDay());
        })
        ->exists();

        if(!$continueStreak && $referral_code->donation_streak >0 ){
            DB::table('referral_code')
            ->where('id',$referral_code->id)
            ->update(['donation_streak'=>0]);
            $referral_code = $this->getReferralCode(true);
        }
        
        $total_transaction = DB::table('point_history as ph')
        ->leftJoin('transactions as t','t.id','ph.transaction_id')
        ->where('ph.referral_code_id',$referral_code->id)
        ->where('ph.status',1)
        ->where('t.status',"Success")
        ->sum('t.amount');

        //dd($referral_code);

        $donationsToday = DB::table('point_history as ph')
            ->join('donation_transaction as dt', 'dt.transaction_id', 'ph.transaction_id')
            ->join('transactions as t', 't.id', 'ph.transaction_id')
            ->where('ph.status', 1)
            ->whereDate('t.datetime_created', today())
            ->where('ph.referral_code_id', $referral_code->id)
            ->select(
                DB::raw('COUNT(CASE WHEN ph.fromSubline = 0 THEN ph.id END) AS donation_leader_today'),
                DB::raw('COUNT(CASE WHEN ph.fromSubline = 1 THEN ph.id END) AS donation_member_today'),
                DB::raw('COUNT(ph.id) AS total_donation_today')
            )
            ->first();

        $progressToday = '<div class="form-row">
            <div id="criteria1" class="col-md-4 col-sm-12">
                <input type="text" readonly value="Derma anda: ' . $donationsToday->donation_leader_today . '/1" class="form-control bg-' . ($donationsToday->donation_leader_today > 0 ? 'success' : 'danger') . ' text-white">
            </div>
            <div id="criteria2" class="col-md-4 col-sm-12">
                <input type="text" readonly value="Derma guna kod anda: '.$donationsToday->donation_member_today.'/1" class="form-control bg-' . ($donationsToday->donation_member_today > 0 ? 'success' : 'danger') . ' text-white">
            </div>
            <div id="criteria3" class="col-md-4 col-sm-12">
                <input type="text" readonly value="Total Derma hari ini: '.$donationsToday->total_donation_today.'/16" class="form-control bg-' . ($donationsToday->total_donation_today >= 16 ? 'success' : 'danger') . ' text-white">
            </div>
        </div>';

        $referral_code->streakToday = Carbon::parse($referral_code->updated_at)->toDateString() == today()->toDateString();

         return view('point.index',compact('referral_code','point_month','total_transaction','progressToday'));
    }

    public function getWheelList(){
        $referral_code = DB::table('referral_code as rc')
        ->leftJoin('point_history as ph','ph.referral_code_id','rc.id')
       // ->whereBetween('ph.created_at', [now()->startOfMonth()->format('Y-m-d'), now()->endOfMonth()->format('Y-m-d')])
        ->where('rc.status',1)
        ->where('ph.status',1)
        ->select('rc.code', 'rc.id', DB::raw('SUM(ph.points) as points_in_month'))
        ->groupBy('rc.code')
        //->orderByRaw('RAND()') 
        ->get();
        //dd($referral_code);
        return $referral_code;
    }

    public function spinningWheel(){
        if(!Auth::user()->hasRole('Superadmin') && Auth::id() != 17151){
            return view('errors.404');
        }
        
        $referral_code = $this->getWheelList();

        return view('point.wheelTest',compact('referral_code'));
    }

    public function getPointHistoryDatatable(){
        if (request()->ajax()) {
            $referral_code = $this->getReferralCode(true);
            //dd($referral_code);
            $data = DB::table('point_history as ph')
                    ->leftJoin('transactions as t','t.id','ph.transaction_id')
                    ->where('ph.referral_code_id',$referral_code->id)

                    ->where('ph.status',1)
                    ->select('t.username','ph.points','ph.created_at as datetime','ph.desc')
                    ->orderBy('ph.created_at','desc');
            //dd($data);
            $table = Datatables::of($data);
            return $table->make(true);
        }
    }


    public function getReferralCode($object =false){
        if(request()->ajax() && !$object){

            if(auth()->user()->hasRole('Guest')){
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            if(!DB::table('referral_code')->where('user_id',Auth::id())->exists()){
                $this->generateReferralCode();
             }
     

             $code =DB::table('referral_code')->where('user_id',Auth::id())->first();
             //dd($code);
             return response()->json(['referral_code'=>$code->code]);
        }else{
            $user_id = Auth::id();

            $referral_code = DB::table('referral_code')
                            ->leftJoin('users','users.id','referral_code.user_id')
                            ->where('referral_code.user_id',$user_id)
                            ->select('referral_code.*','users.name as username')
                            ->first();
            //dd($referral_code);
            return $referral_code;
        }
        
    }


    public function generateReferralCode(){
        $userId = Auth::id();

        if(DB::table('referral_code')->where('user_id',$userId)->exists()){
            return;
        }
        
        $namestr = substr(str_replace(' ', '', Auth::user()->name),0,5);

        $randomString = Str::random(3);
        $user_code = base_convert($userId, 10, 32);
        $user_code = Str::upper(str_pad($user_code, 4, '0', STR_PAD_LEFT));

       
        $code = $namestr.$randomString.$user_code;

        while(DB::table('referral_code')->where('code',$code)->exists()){
            $randomString = Str::random(3);
            $code = $namestr.$randomString.$user_code;
        }
        
        DB::table('referral_code')->insert([
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now(),
            'code'=> $code,
            'user_id'=>$userId,
            'status'=>1,
            'total_point'=>0
        ]);
    }

    public function shareReferralLink(Request $request){
        try{
            $user_id = Auth::id();

            $referral_code = DB::table('referral_code')->where('user_id',$user_id)->where('status',1)->first();
            $hostname = request()->getHost();
            //dd($request->page);
            switch ($request->page){
                case 'M' :
                    return response()->json(['link'=>$hostname.'/getngo/product?referral_code='.$referral_code->code]);
                default:
                    return response()->json(['link'=>'Invalid Link']);
            }
        }catch(Exception $e){
            return response()->json(['link'=>'Invalid Link']);
        }
       
    }

    public static function processReferralCode($inputReferralCode) {
        // Initialize variables
        $referral_code = "";
        $message = "";

        // Check if referral code is provided in request input
        if ($inputReferralCode !== null) {
            $referral_code = $inputReferralCode;
        } elseif (session()->has('referral_code') && !empty(session('referral_code'))) {
            // Retrieve referral code from session if not provided in request input
            $referral_code = session()->pull('referral_code');
        }
    
        // Check if referral code exists in the database
        $exists = DB::table('referral_code')
            ->where('code', $referral_code)
            ->exists();
    
        if (!$exists) {
            // If referral code doesn't exist, handle the error
            if($referral_code !="" && $referral_code !=null)
                $message = "Invalid Referral Code Used!!";

            $referral_code = "";
        } else {
            // Increment the total visit count for the referral code
            DB::table('referral_code')
                ->where('code', $referral_code)
                ->increment('total_visit');
        }
    
        // Store the referral code in the session
        session(['referral_code' => $referral_code]);
        //dd(session()->pull('referral_code'));
        // Return the processed referral code and any error message
        return ['referral_code' => $referral_code, 'message' => $message];
    }

    public function create()
    {
        
    }

    public function store(Request $request)
    {
        
    }

    public function show($id)
    {
        
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }

    public function pointPolicy(){
        return view('point.policy');
    }
}
