<?php

namespace App\Http\Controllers\Cooperative\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminOpeningHoursCooperativeController extends Controller
{
    public function indexOpening()
    {
        $userID = Auth::id();
        $koperasi = DB::table('organizations as o')
                    ->join('organization_user as ou', 'o.id', '=', 'ou.organization_id')
                    ->where('ou.user_id', $userID)
                    ->where('ou.role_id', 1239)
                    ->first();

        $hour = DB::table('organization_hours as o')
                ->join('organization_user as ou','o.organization_id','=','ou.organization_id')
                ->select('o.*')
                ->where('ou.user_id', $userID)
                ->get();


 
        return view('koperasi-admin.opening' , compact('koperasi'), compact('hour'))
        ->with('hour',$hour);
    }

    public function storeOpening(Request $request)
    {
        $userID = Auth::id();
        $org = DB::table('organizations as o')
                ->join('organization_user as os', 'o.id', 'os.organization_id')
                ->where('os.user_id', $userID)
                ->select('o.id')
                ->first();


        // $hour = DB::table('organization_hours')
        if($request->day == 1)
        {
            $hour = DB::table('organization_hours')
            ->where('day','=',1)
            ->where('organization_id','=',$org->id,)
            ->update(['open_hour'=>$request->open,
            'close_hour'=>$request->close,
            'status' => $request->status,]);


            
        }
        else if($request->day == 2)
        {
            $hour = DB::table('organization_hours')
            ->where('day','=',2)
            ->where('organization_id','=',$org->id,)
            ->update(['open_hour'=>$request->open,
            'close_hour'=>$request->close,
            'status' => $request->status,]);
        }
        else if($request->day==3)
        {
            $hour = DB::table('organization_hours')
            ->where('day','=',3)
            ->where('organization_id','=',$org->id,)
            ->update(['open_hour'=>$request->open,
            'close_hour'=>$request->close,
            'status' => $request->status,]);
        }
        else if($request->day==4)
        {
            $hour = DB::table('organization_hours')
            ->where('day','=',4)
            ->where('organization_id','=',$org->id,)
            ->update(['open_hour'=>$request->open,
            'close_hour'=>$request->close,
            'status' => $request->status,]);
        }
        else if($request->day==5)
        {
            $hour = DB::table('organization_hours')
            ->where('day','=',5)
            ->where('organization_id','=',$org->id,)
            ->update(['open_hour'=>$request->open,
            'close_hour'=>$request->close,
            'status' => $request->status,]);
        }
        else if($request->day==6)
        {
            $hour = DB::table('organization_hours')
            ->where('day','=',6)
            ->where('organization_id','=',$org->id,)
            ->update(['open_hour'=>$request->open,
            'close_hour'=>$request->close,
            'status' => $request->status,]);
        }
        else if($request->day==0)
        {
            $hour = DB::table('organization_hours')
            ->where('day','=',0)
            ->where('organization_id','=',$org->id,)
            ->update(['open_hour'=>$request->open,
            'close_hour'=>$request->close,
            'status' => $request->status,]);
        }
                // ->where('day')
                // ->update([
                //     'open_hour'=>$request->open,
                //  ]);
        return redirect('koperasi/openingHours');
    }
}
