<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Models\DonationStreak;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class PointController extends Controller
{
     public function index()
    {
        //DonationStreak::updateStreak(17151,42633);
        $user_id = Auth::id();

        $referral_code = $this->getReferralCode(true);

        // $point_month = DB::table('point_history')
        // ->where('referral_code_id',$referral_code->id)
        // ->whereBetween('created_at', [now()->startOfMonth()->format('Y-m-d'), now()->endOfMonth()->format('Y-m-d')])
        // ->where('status',1)
        // ->sum('points');


        $continueStreak = DB::table('point_history')
        ->where('referral_code_id', $referral_code->id)
        ->where(function($query) {
            $query->whereDate('created_at', today())
                ->orWhereDate('created_at', today()->subDay());
        })
        ->where('desc','LIKE','Transaksi Derma%')
        ->exists();

        if(!$continueStreak && $referral_code->donation_streak >0 ){
            DB::table('referral_code')
            ->where('id',$referral_code->id)
            ->update(['donation_streak'=>0]);
            $referral_code = $this->getReferralCode(true);
        }
        
        // $total_transaction = DB::table('point_history as ph')
        // ->leftJoin('transactions as t','t.id','ph.transaction_id')
        // ->where('ph.referral_code_id',$referral_code->id)
        // ->where('ph.status',1)
        // ->where('t.status',"Success")
        // ->sum('t.amount');

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
        
        $total_follower = DB::table('referral_code_member')->where('leader_referral_code_id',$referral_code->id)->where('status',1)->count();
        $today_follower_donation = DB::table('point_history as ph')
                                    ->join('referral_code_member as rcm','rcm.leader_referral_code_id','ph.referral_code_id')
                                    ->where('ph.status',1)
                                    ->whereBetween('ph.created_at', [Carbon::today(), Carbon::tomorrow()])
                                    ->where('rcm.leader_referral_code_id', $referral_code->id)
                                    ->where('rcm.status',1)
                                    ->whereNotNull('ph.member_id')
                                    ->select('ph.member_id')
                                    ->distinct()
                                    ->get()
                                    ->count();
                                    //->get();
        $exist_leader = DB::table('referral_code_member')->where('status',1)->where('member_user_id',$user_id)->exists();
        $donateToLeader =DB::table('point_history as ph')
                                    ->join('referral_code_member as rcm','rcm.leader_referral_code_id','ph.referral_code_id')
                                    ->where('ph.status',1)
                                    ->whereBetween('ph.created_at', [Carbon::today(), Carbon::tomorrow()])
                                    ->where('rcm.member_user_id', $user_id)
                                    ->whereNotNull('ph.member_id')
                                    ->select('ph.member_id')
                                    ->distinct()
                                    ->get()
                                    ->count();
       // dd($today_follower_donation);
                                    // 
                                    // ;
        $col = $exist_leader && $total_follower > 0?'col-md-3':'col-md-4';
        $donateToLeaderDiv = $exist_leader ?'  <div id="criteria4" class="'.$col.' col-sm-12">
                <input type="text" readonly value="Derma kepada Ketua: '.$donateToLeader.'" class="form-control bg-' . ($donateToLeader >= 1 ? 'success' : 'danger') . ' text-white">
            </div>':'';
        $followerDiv = ($total_follower > 0 || !$exist_leader) ?'<div id="criteria3" class="'.$col.' col-sm-12">
                <input type="text" readonly value="Derma Ahli Anda: '.$today_follower_donation .'/'.$total_follower.'" class="form-control bg-' . ($today_follower_donation >= $total_follower ? 'success' : 'danger') . ' text-white">
            </div>' :'';

        $progressToday = '<div class="form-row">
            <div id="criteria1" class="'.$col.' col-sm-12">
                <input type="text" readonly value="Derma anda: ' . $donationsToday->donation_leader_today . '" class="form-control bg-' . ($donationsToday->donation_leader_today > 0 ? 'success' : 'danger') . ' text-white">
            </div>
            <div id="criteria2" class="'.$col.' col-sm-12">
                <input type="text" readonly value="Derma guna kod anda: '.$donationsToday->donation_member_today.'" class="form-control bg-' . ($donationsToday->donation_member_today > 0 ? 'success' : 'danger') . ' text-white">
            </div>
           '.$followerDiv.$donateToLeaderDiv.'
        </div>';
        $streakData = DonationStreak::getStreakData();
       


        //dd($transaction_ids);
         return view('point.index',compact('referral_code','total_follower','progressToday','streakData'));
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


    public function getDonationStreakTable(){
        if (request()->ajax()) {
            $user_id = Auth::id();
            $donationStreak = $this->getReferralCode(true);
            //dd($referral_code);
            $data = DB::table('donation_streak as ds')
                    ->where('ds.status',1)
                    ->where('ds.user_id', $user_id)
                   
                    ->select('ds.startdate','ds.enddate','ds.prim_medal','ds.desc','ds.id')
                    ->orderBy('ds.startdate','desc')
                    ->get();
            foreach($data as $d){
                $d->status = ($d->enddate !=null && $d->prim_medal < 0)?'Gagal':'Berjaya';
                if($d->enddate == null){
                    $d->enddate = 'Sedang Proses';
                    $d->status = '-';
                }
                switch($d->prim_medal){
                    case -1:
                        $d->prim_medal = 'Belum Selesai'; break;
                    case 0:
                        $d->prim_medal = 'Telah Diguna'; break;
                   
                }
                $d->detail = '<a href="' . route('point.viewDonationStreak', ['id' => $d->id]) . '" class="btn btn-primary btn-sm">Butiran</a>';
                unset($d->id);
            }
            //dd($data);
            $table = Datatables::of($data);
            $table->rawColumns(['detail']); 
            return $table->make(true);
        }
    }
    
    public function viewDonationStreak($id){
        $donationStreak = DB::table('donation_streak')->where('id', $id)->first();
        if (!$donationStreak) {
           return view('erros.404');
        }

        // Generate the route for the DataTable
        $route = route('point.getDonationStreakTransactionTable', ['id' => $id]);

       
        // Return the view with the necessary data
        return view('point.streak_detail', compact('route'));
      
    }

    public function getDonationStreakTransactionTable($id){
        if(request()->ajax()){
           $data= DB::table('donation_streak_transaction')
            ->where('donation_streak_id',$id)
            ->select('created_at','day','quality_donation')
            ->get();

            foreach($data as $d){
                $d->quality_donation =  $d->quality_donation?'YA':'TIDAK';
            }

            return DataTables::of($data)->make(true);
        }
    }
    
    public function getReferralCodeMemberDatatable()
    {
        if (request()->ajax()) {
            $referral_code = $this->getReferralCode(true);
            
            $data = DB::table('referral_code_member as rcm')
                    ->join('users as mu', 'mu.id', '=', 'rcm.member_user_id') 
                    ->where('rcm.leader_referral_code_id', $referral_code->id)
                    ->select('mu.name as member_name', 'rcm.created_at', 'mu.id as member_user_id','rcm.level','rcm.id as rcm_id')
                    ->get(); // Execute the query to fetch data
            
            foreach ($data as $d) {
                $d->contribution = DB::table('point_history as ph')
                                    ->where([ 
                                        ['ph.status', 1],
                                        ['ph.member_id', $d->rcm_id]
                                    ])->sum('ph.points');

                $d->todayContribution = DB::table('point_history as ph')
                                    ->where([ 
                                        ['ph.status', 1],
                                        ['ph.member_id', $d->rcm_id]
                                    ])
                                    ->whereBetween('ph.created_at', [Carbon::today(), Carbon::tomorrow()])
                                    ->sum('ph.points');
                // switch($d->level){
                //     case '1':
                //         $d->level = 'Ahli Biasa'; break;
                //     case '2':
                //         $d->level = 'Ahli Aktif'; break;
                //     case '3':
                //         $d->level = 'Super Ahli'; break;
                //     default:
                //         $d->level = 'Ahli Tingkat '.$d->level; break;

                // }

            
                unset($d->member_user_id); // Remove member_user_id from the object
            }
            
            // Using DataTables to format the data
            $table = Datatables::of($data);
            //dd($table);
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
                case 'R':
                    return response()->json(['link'=>$hostname.'/register?referral_code='.$referral_code->code]);
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

    public static function validateReferralCode($code){
        $exist = DB::table('referral_code')->where('code',$code)->where('status',1)->exists();
        return $exist;
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
