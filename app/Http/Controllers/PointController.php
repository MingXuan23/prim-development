<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class PointController extends Controller
{
     public function index()
    {
        $user_id = Auth::id();

        $referral_code = $this->getReferralCode();

        $point_month = DB::table('point_history')
        ->where('referral_code_id',$referral_code->id)
        ->whereBetween('created_at', [now()->startOfMonth()->format('Y-m-d'), now()->endOfMonth()->format('Y-m-d')])
        ->where('status',1)
        ->sum('points');
        
        $total_transaction = DB::table('point_history as ph')
        ->leftJoin('transactions as t','t.id','ph.transaction_id')
        ->where('ph.referral_code_id',$referral_code->id)
        ->where('ph.status',1)
        ->where('t.status',"Success")
        ->sum('t.amount');

         return view('point.index',compact('referral_code','point_month','total_transaction'));
    }

    public function getWheelList(){
        $referral_code = DB::table('referral_code as rc')
        ->leftJoin('point_history as ph','ph.referral_code_id','rc.id')
       // ->whereBetween('ph.created_at', [now()->startOfMonth()->format('Y-m-d'), now()->endOfMonth()->format('Y-m-d')])
        ->where('rc.status',1)
        ->where('ph.status',1)
        ->select('rc.code', 'rc.id', DB::raw('SUM(ph.points) as points_in_month'))
        ->groupBy('rc.code')
        ->get();

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
            $referral_code = $this->getReferralCode();
            $data = DB::table('point_history as ph')
                    ->leftJoin('transactions as t','t.id','ph.transaction_id')
                    ->where('ph.referral_code_id',$referral_code->id)

                    ->where('ph.status',1)
                    ->select('t.username','ph.points','ph.created_at as datetime','ph.desc')
                    ->orderBy('ph.created_at','desc');

            $table = Datatables::of($data);
            return $table->make(true);
        }
    }

    public function getReferralCode(){
        $user_id = Auth::id();

        $referral_code = DB::table('referral_code')->where('user_id',$user_id)->first();
        return $referral_code;
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
}
