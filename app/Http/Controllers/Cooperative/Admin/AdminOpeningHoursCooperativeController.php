<?php

namespace App\Http\Controllers\Cooperative\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminOpeningHoursCooperativeController extends Controller
{
    public function indexOpening(Request $request)
    {
        $role_id = DB::table('roles')->where('name','Koop Admin')->first()->id;
        $userID = Auth::id();
        $koperasiList = DB::table('organizations as o')
                    ->join('organization_user as ou', 'o.id', '=', 'ou.organization_id')
                    ->where('ou.user_id', $userID)
                    ->where('ou.role_id', $role_id)
                    ->get();

        $koperasi=$koperasiList->first();

        
        $koopId=$request->session()->get('koopId');

        if($koopId!=null){
            $koperasi=$koperasiList->where('organization_id',$koopId)->first();
        }
        
        $hour =  $this->getOpeningHourByOrganizationId($koperasi->organization_id);
        
        return view('koperasi-admin.opening' , compact('koperasiList','koperasi'));
        
    }

    public function getOpeningHourByOrganizationId($koopId){
        $hour=DB::table('organization_hours as oh')
        ->join('organization_user as ou','oh.organization_id','=','ou.organization_id')
        ->join('organizations as o', 'o.id', '=', 'ou.organization_id')
        ->select('oh.*')
        ->distinct('o.id')
        ->where('o.type_org',10)
        ->where('o.id',$koopId)
        ->get();
        return $hour;
    }
    
    public function openingChangeKoperasi(Request $request){
        if (request()->ajax()){
            
            $role_id = DB::table('roles')->where('name','Koop Admin')->first()->id;
            $userID = Auth::id();
            $koperasi = DB::table('organizations as o')
                        ->join('organization_user as ou', 'o.id', '=', 'ou.organization_id')
                        ->where('ou.user_id', $userID)
                        ->where('ou.organization_id',$request->koopId)
                        ->where('ou.role_id', $role_id)
                        ->first();
            
            $hour =  $this->getOpeningHourByOrganizationId($koperasi->organization_id);
            
            return response()->json(['koperasi' => $koperasi,'hour'=>$hour]);
        }
    }
    
    public function storeOpening(Request $request)
    {
        $userID = Auth::id();

        $org = $request->koopId;
        $enable =$request->checkboxEnablePickUpTime=="on"?1:0;
        //dd($enable,$request->noteReq);
        
        $update =DB::table('organization_hours')
        ->where('organization_id','=',$org)
        ->update(['date_selection_enable'=>$enable,
        'note_requirement'=>$request->noteReq,
        ]);
        $hour = DB::table('organization_hours')
            ->where('day','=',$request->day)
            ->where('organization_id','=',$org)
            ->update(['open_hour'=>$request->open,
            'close_hour'=>$request->close,
            'status' => $request->status,]);

       
        return redirect('koperasi/openingHours')->with('koopId',$org);
        // if($request->day == 1)
        // {
        //     $hour = DB::table('organization_hours')
        //     ->where('day','=',1)
        //     ->where('organization_id','=',$org)
        //     ->update(['open_hour'=>$request->open,
        //     'close_hour'=>$request->close,
        //     'status' => $request->status,]);


            
        // }
        // else if($request->day == 2)
        // {
        //     $hour = DB::table('organization_hours')
        //     ->where('day','=',2)
        //     ->where('organization_id','=',$org)
        //     ->update(['open_hour'=>$request->open,
        //     'close_hour'=>$request->close,
        //     'status' => $request->status,]);
        // }
        // else if($request->day==3)
        // {
        //     $hour = DB::table('organization_hours')
        //     ->where('day','=',3)
        //     ->where('organization_id','=',$org)
        //     ->update(['open_hour'=>$request->open,
        //     'close_hour'=>$request->close,
        //     'status' => $request->status,]);
        // }
        // else if($request->day==4)
        // {
        //     $hour = DB::table('organization_hours')
        //     ->where('day','=',4)
        //     ->where('organization_id','=',$org)
        //     ->update(['open_hour'=>$request->open,
        //     'close_hour'=>$request->close,
        //     'status' => $request->status,]);
        // }
        // else if($request->day==5)
        // {
        //     $hour = DB::table('organization_hours')
        //     ->where('day','=',5)
        //     ->where('organization_id','=',$org->id,)
        //     ->update(['open_hour'=>$request->open,
        //     'close_hour'=>$request->close,
        //     'status' => $request->status,]);
        // }
        // else if($request->day==6)
        // {
        //     $hour = DB::table('organization_hours')
        //     ->where('day','=',6)
        //     ->where('organization_id','=',$org->id,)
        //     ->update(['open_hour'=>$request->open,
        //     'close_hour'=>$request->close,
        //     'status' => $request->status,]);
        // }
        // else if($request->day==0)
        // {
        //     $hour = DB::table('organization_hours')
        //     ->where('day','=',0)
        //     ->where('organization_id','=',$org->id,)
        //     ->update(['open_hour'=>$request->open,
        //     'close_hour'=>$request->close,
        //     'status' => $request->status,]);
        // }
                // ->where('day')
                // ->update([
                //     'open_hour'=>$request->open,
                //  ]);
        
    }
}
