<?php

namespace App\Http\Controllers\Cooperative\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\PgngOrder;
use App\Models\OrganizationHours;
use App\Models\Organization;
use Illuminate\Support\Carbon;
use App\Exports\ExportKoperasiOverview;
use Maatwebsite\Excel\Facades\Excel;

class AdminOrderCooperativeController extends Controller
{
    public function indexConfirm(Request $request)
    {
        $role_id = DB::table('roles')->where('name','Koop Admin')->first()->id;
        $userID = Auth::id();

        $koperasiList = DB::table('organizations as o')
        ->join('organization_user as ou', 'o.id', '=', 'ou.organization_id')
        ->where('ou.user_id', $userID)
        ->where('ou.role_id', $role_id)
        ->get();
        //d($request);
        $koperasi = $koperasiList->first();
        $koopId=$request->session()->get('koopId');
        if($koopId!=null)
            $koperasi=$koperasiList->where('organization_id',$koopId)->first();

        return view('koperasi-admin.confirm',compact('koperasi'),compact('koperasiList'));
        // return view('koperasi-admin.confirm');
 
    }

    
    public function fetchConfirmTable(Request $request){
        $order = DB::table('pgng_orders as pg')
        ->join('users as u','pg.user_id','=','u.id')
        ->join('organization_user as ou','pg.organization_id','=','ou.organization_id')
        ->join('transactions as t','t.id','pg.transaction_id')
        ->where('ou.organization_id', $request->koopId)
        ->where(function ($query) use ($request) {
            $query->where('u.name', 'LIKE', '%' . $request->key . '%')
                ->orWhere('pg.id', $request->key)
                ->orWhere('pg.note', 'LIKE', '%' . $request->key . '%')
                ->orWhere('pg.created_at', 'LIKE', '%' . $request->key . '%');
        })
        ->whereIn('pg.status', [2,3,4])
        ->where('t.status','Success')
        ->groupBy('pg.id')
        ->orderBy('pg.pickup_date')
        ->select('pg.*','u.*','u.id as customerID','ou.*','pg.id as id','pg.status as status','pg.created_at as orderTime')
        ->get();
        return response()->json(['order'=>$order]);
    }

    public function exportKoperasiOverview($id){

        return Excel::download(new ExportKoperasiOverview($id), 'JualanBarang' . '.xlsx');
    }
    public function viewPgngList($id,$customerID){
       
        $userID = $customerID;
        // Get Information about the order

        $list_detail = DB::table('pgng_orders as ko')
                        ->join('organizations as o', 'ko.organization_id', '=', 'o.id')
                        ->join('transactions as t','t.id','ko.transaction_id')
                        ->where('ko.id', $id)
                        ->where('ko.status', '>' , 0)
                        ->where('ko.user_id', $userID)
                        ->select('ko.updated_at', 'ko.pickup_date', 'ko.total_price', 'ko.note', 'ko.status',
                                'o.id','o.nama', 'o.parent_org', 'o.telno', 'o.email', 'o.address', 'o.postcode', 'o.state','t.transac_no as fpxId')
                        ->first();
        
        $date = Carbon::createFromDate($list_detail->pickup_date); // create date based on pickup date

        $day = $this->getDayIntegerByDayName($date->format('l')); // get day in integer based on day name

        // get open and close hour org
        $allOpenDays = OrganizationHours::where([
            ['organization_id', $list_detail->id],
            ['day', $day],
        ])->first();

        // get parent name
        $parent_org = Organization::where('id', $list_detail->parent_org)->select('nama')->first();

        $sekolah_name = $parent_org->nama;

        // get all product based on order
        $item = DB::table('product_order as po')
                ->join('product_item as pi', 'po.product_item_id', '=', 'pi.id')
                ->join('pgng_orders as pg','po.pgng_order_id','=','pg.id')
                ->where('po.pgng_order_id', $id)
                ->where('pg.status', '>', 0)
                ->select('pi.name', 'pi.price', 'po.quantity')
                ->get();
                // dd($item);

        $totalPrice = array();
        
        foreach($item as $row)
        {
            $key = strval($row->name); // key based on item name
            $totalPrice[$key] = doubleval($row->price * $row->quantity); // calculate total for each item in cart
        }
        $previousUrl = url()->previous();
        $previousUrl = str_replace('/', '-', $previousUrl);
        //dd($previousUrl);
        return view('koperasi.list', compact('list_detail', 'allOpenDays', 'sekolah_name', 'item', 'totalPrice','previousUrl'));
    }

    public function returnFromList($previousUrl,$koopId){
        $previousUrl = str_replace('-', '/', $previousUrl);
        return redirect($previousUrl)->with('koopId', $koopId);
    }

    public function storeConfirm(Request $request,Int $id)
    {
        $userID = Auth::id();
        $order = DB::table('pgng_orders')
                    ->where('id',$id);
        $koopId=$order->first()->organization_id;
        $order->update([
            'status' => 3 ,
            'confirm_picked_up_time'=>Now(),
            'confirm_by'=>$userID,
        ]);
         return redirect()->back()->with('koopId',$koopId);
    }

    public function adminHistory(Request $request){
        $role_id = DB::table('roles')->where('name','Koop Admin')->first()->id;
        $userID = Auth::id();

        $koperasiList = DB::table('organizations as o')
        ->join('organization_user as ou', 'o.id', '=', 'ou.organization_id')
        ->where('ou.user_id', $userID)
        ->where('ou.role_id', $role_id)
        ->get();

        $koperasi=$koperasiList->first();
        $koopId=$request->session()->get('koopId');
        if($koopId!=null)
            $koperasi=$koperasiList->where('organization_id',$koopId)->first();
        //dd($koopId);
        //dd($koperasi->organization_id);
        $order = DB::table('pgng_orders as ko')
                ->join('organizations as o', 'ko.organization_id', '=', 'o.id')
                ->join('transactions as t','t.id','ko.transaction_id')
                ->join('users as u','u.id','ko.confirm_by')
                ->whereIn('ko.status', [3, 100, 200])
                ->where('t.status','Success')
                ->where('o.id', $koperasi->organization_id)
                ->select('ko.*', 'o.nama as koop_name', 'o.telno as koop_telno','u.name as confirmPerson','u.id as customerId')
                ->orderBy('ko.status', 'desc')
                ->orderBy('ko.pickup_date', 'asc')
                ->orderBy('ko.updated_at', 'desc')
                ->get();
        //dd($order);

        //$order = $query->paginate(5);
        //dd($order);
        return view('koperasi.history', compact('order','koperasiList','koperasi'));
    }

    public function fetchAdminHistory(Request $request){

        $koopId=$request->koopId;
        $order = DB::table('pgng_orders as ko')
        ->join('organizations as o', 'ko.organization_id', '=', 'o.id')
        ->join('users as u','u.id','ko.confirm_by')
        ->join('transactions as t','t.id','ko.transaction_id')
        ->whereIn('ko.status', [3, 100, 200])
        ->where('t.status','Success')
        ->where('o.id', $koopId)
        ->select('ko.*', 'o.nama as koop_name', 'o.telno as koop_telno','u.name as confirmPerson','ko.user_id as customerID','ko.confirm_picked_up_time as pickup_date')
        ->orderBy('ko.status', 'desc')
        ->orderBy('ko.pickup_date', 'asc')
        ->orderBy('ko.updated_at', 'desc')
        ->get();

        return response()->json(['order'=>$order]);
    }

    public function notConfirm(Request $request,Int $id)
    {   
        $userID = Auth::id();
        $order = DB::table('pgng_orders')
                    ->where('id',$id);
        $koopId=$order->first()->organization_id;
        $order->update([
            'status' => 4
        ]);
        return redirect()->back()->with('koopId',$koopId);
    }
    public function getDayIntegerByDayName($date)
    {
        $day = null;
        if($date == "Monday") { $day = 1; }
        else if($date == "Tuesday") { $day = 2; }
        else if($date == "Wednesday") { $day = 3; }
        else if($date == "Thursday") { $day = 4; }
        else if($date == "Friday") { $day = 5; }
        else if($date == "Saturday") { $day = 6; }
        else if($date == "Sunday") { $day = 0; }
        return $day;
    }

}
