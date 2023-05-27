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
        $role_id = DB::table('roles')->where('name','Koop Admin')->first()->id;
        $userID = Auth::id();
        $koperasi = DB::table('organizations as o')
                    ->join('organization_user as ou', 'o.id', '=', 'ou.organization_id')
                    ->where('ou.user_id', $userID)
                    ->where('ou.role_id', $role_id)
                    ->first();

        $customer = DB::table('pgng_orders as pg')
                    ->join('users as u','pg.user_id','=','u.id')
                    ->join('organization_user as ou','pg.organization_id','=','ou.organization_id')
                    ->where('ou.user_id', $userID)
                    ->where('pg.status', 2)
                    ->groupBy('pg.id')
                    ->select('pg.*','u.*','ou.*','pg.id as id','pg.status as status')
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
