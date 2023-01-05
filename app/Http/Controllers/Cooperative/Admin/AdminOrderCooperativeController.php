<?php

namespace App\Http\Controllers\Cooperative\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminOrderCooperativeController extends Controller
{
    public function indexConfirm()
    {
        $userID = Auth::id();
        $koperasi = DB::table('organizations as o')
                    ->join('organization_user as ou', 'o.id', '=', 'ou.organization_id')
                    ->where('ou.user_id', $userID)
                    ->where('ou.role_id', 1239)
                    ->first();

        $customer = DB::table('pgng_orders as o')
                    ->join('users as ou','o.user_id','=','ou.id')
                    ->join('organization_user as op','o.organization_id','=','op.organization_id')
                    ->where('op.user_id', $userID)
                    ->select('o.*','ou.*','op.*','o.id as id','o.status as status')
                    ->where('o.status', 2)
                    ->get();

        return view('koperasi-admin.confirm',compact('koperasi'),compact('customer'))->with('customer',$customer);
        // return view('koperasi-admin.confirm');
 
    }

    public function storeConfirm(Request $request,Int $id)
    {
        $userID = Auth::id();
        $customer = DB::table('pgng_orders')
                    ->where('id',$id)
                    ->update([
                        'status' => 3 ,
                    ]);
         return redirect('koperasi/Confirm');
    }

    public function notConfirm(Request $request,Int $id)
    {
        $userID = Auth::id();

        $customer = DB::table('pgng_orders')
                    ->where('id',$id)
                    ->update([
                        'status' => 4 ,
                    ]);
         return redirect('koperasi/Confirm');
    }
}
