<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Destination_Offer;
use App\Models\Grab_Student;
use App\Models\Grab_Booking;
use App\Models\Organization;
use Hash;
use Session;
use PDF;

class GrabStudentController extends Controller
{

    public function setcar()
    {
        $orgtype = 'Grab Student';
        $userId = Auth::id();
        $data = DB::table('organizations as o')
        ->leftJoin('organization_user as ou', 'o.id', 'ou.organization_id')
        ->leftJoin('type_organizations as to', 'o.type_org', 'to.id')
        ->select("o.id")
        ->distinct()
        ->where('ou.user_id', $userId)
        ->where('to.nama', $orgtype)
        ->where('o.deleted_at', null)
        ->get();
        return view('grab.insertgrab', compact('data'));
    }

    public function checkcar()
    {
        $userId = Auth::id();
        $data = DB::table('organization_user')
        ->join('grab_students', 'grab_students.id_organizations', 'organization_user.organization_id')
        ->select("grab_students.id","grab_students.car_brand","grab_students.car_name","grab_students.car_registration_num","grab_students.number_of_seat","grab_students.available_time","grab_students.status")
        ->distinct()
        ->where('organization_user.user_id', $userId)
        ->get();

        return view("grab.managegrab", compact('data'));
    }

    public function setdestination()
    {
        $data =  DB::table('grab_students')
        ->where('status', '=', 'AVAILABLE')->get();
        $list = DB::table('grab_students')
        ->join('destination_offers', 'grab_students.id', '=', 'destination_offers.id_grab_student')
        ->select('grab_students.car_brand','grab_students.car_name', 'grab_students.car_registration_num', 'grab_students.available_time', 'grab_students.status', 'destination_offers.destination_name', 'destination_offers.pick_up_point', 'destination_offers.price_destination')
        ->get();
        return view("grab.insertdestination", compact('list','data'));
    }

    public function updatecar(Request $request, $id)
    {
        $updategrab = Grab_Student::findOrFail($id);
        $updategrab->update($request->all());
        $updategrab->update([
            'available_time' => $request->input('time'),
            'status' => $request->input('status'),
        ]);
        
        return back()->with('success', 'Row updated successfully');
    }


    public function insertcar(Request $request)
    {
        $request->validate([
            'carbrand'=>'required',
            'carname'=>'required',
            'carregisternumber'=>'required|max:8',
            'totalseat'=>'required',
            'time'=>'required',
            'status'=>'required',
            'organizationid'=>'required'
        ]);

        $userId = Auth::id();
        $grabstudent = new Grab_Student();
        $grabstudent->car_brand = $request->carbrand;
        $grabstudent->car_name = $request->carname;
        $grabstudent->car_registration_num = $request->carregisternumber;
        $grabstudent->number_of_seat = $request->totalseat;
        $grabstudent->available_time =  $request->time;
        $grabstudent->status = $request->status;
        $grabstudent->id_organizations = $request->organizationid;
        $res = $grabstudent->save();
    
        if($res){
            return back()->with('success','Your car has been registered successfully');
        }else{
            return back()->with('fail','Something went wrong');
        }
    }

    public function insertdestination(Request $request)
    {
        $request->validate([
            'destination'=>'required',
            'pickup'=>'required',
            'price'=>'required',
            'grabcar'=>'required',
        ]);
        
        $destinationoffer = new Destination_Offer();
        $destinationoffer->destination_name = $request->destination;
        $destinationoffer->pick_up_point = $request->pickup;
        $destinationoffer->price_destination = $request->price;
        $destinationoffer->id_grab_student = $request->grabcar;
        $res = $destinationoffer->save();
    
        if($res){
            return back()->with('success','Your car destination has been registered successfully');
        }else{
            return back()->with('fail','Something went wrong');
        }
    }

}
