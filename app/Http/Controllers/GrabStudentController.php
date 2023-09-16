<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Destination_Offer;
use App\Models\Grab_Student;
use App\Models\Grab_Booking;
use App\Models\Organization;
use App\Models\NotifyGrab;
use Hash;
use Session;
use PDF;
use Carbon\Carbon;

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
        ->select("grab_students.id","grab_students.car_brand","grab_students.car_name","grab_students.car_registration_num","grab_students.number_of_seat","grab_students.status")
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
        ->select('grab_students.car_brand','grab_students.car_name', 'grab_students.car_registration_num', 'destination_offers.available_time', 'grab_students.status as grab_status', 'destination_offers.destination_name', 'destination_offers.id', 'destination_offers.status as destination_status', 'destination_offers.pick_up_point', 'destination_offers.price_destination')
        ->get();
        return view("grab.insertdestination", compact('list','data'));
    }

    public function updatecar(Request $request, $id)
    {
        $updategrab = Grab_Student::findOrFail($id);
        $updategrab->update($request->all());
        $updategrab->update([
            'status' => $request->input('status'),
        ]);
        
        return back()->with('success', 'Row updated successfully');
    }

    public function updatedestination(Request $request, $id)
    {
        $updategrab = Destination_Offer::findOrFail($id);
        $updategrab->update($request->all());
        $updategrab->update([
            'status' => $request->input('status'),
        ]);
        
        return back()->with('success', 'Row updated successfully');
    }

    public function bookgrab(Request $request)
    {
    $uniquePickupPoints = Destination_Offer::distinct()->pluck('pick_up_point');
    $uniqueDestinations = Destination_Offer::distinct()->pluck('destination_name');

    $selectedPickupPoint = $request->input('pick_up_point');
    $selectedDestination = $request->input('availabledestination');

    // Fetch data matching both selections
    $matchedData = Destination_Offer::join('grab_students', 'grab_students.id', '=', 'destination_offers.id_grab_student')
    ->when($selectedPickupPoint, function ($query) use ($selectedPickupPoint) {
        $query->where('pick_up_point', $selectedPickupPoint);
    })
    ->when($selectedDestination, function ($query) use ($selectedDestination) {
        $query->where('destination_name', $selectedDestination);
    })
    ->where('grab_students.status', '=', 'AVAILABLE')
    ->orWhere('destination_offers.status', '=', 'TRIP CONFIRM')
    ->orWhere('destination_offers.status', '=', 'NOT CONFIRM')
    ->select( 'destination_offers.id','grab_students.car_brand','grab_students.car_name','destination_offers.status', 'grab_students.number_of_seat', 'destination_offers.available_time', 'destination_offers.destination_name','destination_offers.pick_up_point', 'destination_offers.price_destination')
    ->get();

    return view('grab.bookgrab', compact('uniquePickupPoints', 'uniqueDestinations', 'selectedPickupPoint', 'selectedDestination', 'matchedData'));


    }

    public function selectbookgrab($id)
    {
        $userId = Auth::id();
        $data =  Destination_Offer::join('grab_students', 'grab_students.id', '=', 'destination_offers.id_grab_student')
        ->where('destination_offers.id', $id)
        ->select( 'destination_offers.id','grab_students.id','grab_students.car_brand','grab_students.car_name', 'grab_students.number_of_seat', 'destination_offers.available_time', 'destination_offers.status', 'destination_offers.destination_name', 'destination_offers.pick_up_point', 'destination_offers.price_destination')
        ->get();
        $datadestinationid =  Destination_Offer::join('grab_students', 'grab_students.id', '=', 'destination_offers.id_grab_student')
        ->where('destination_offers.id', $id)
        ->select( 'destination_offers.id','grab_students.car_brand', 'grab_students.number_of_seat', 'destination_offers.available_time', 'destination_offers.destination_name', 'destination_offers.price_destination')
        ->get();
        return view("grab.viewbookgrab", compact('data','datadestinationid','userId'));
    }

    public function grabcheckpassenger()
    {
        $data =  Grab_Booking::join('destination_offers', 'destination_offers.id', '=', 'grab_bookings.id_destination_offer')
        ->join('users','users.id','=','grab_bookings.id_user')
        ->join('grab_students','grab_students.id','=','destination_offers.id_grab_student')
        ->select( 'users.name','grab_students.car_brand','grab_students.car_name','destination_offers.destination_name','grab_bookings.book_date')
        ->orderBy('grab_bookings.id','desc')
        ->get();
        return view("grab.checkpassenger", compact('data'));
    }


    public function insertcar(Request $request)
    {
        $request->validate([
            'carbrand'=>'required',
            'carname'=>'required',
            'carregisternumber'=>'required|max:8',
            'totalseat'=>'required',
            'status'=>'required',
            'organizationid'=>'required'
        ]);

        $userId = Auth::id();
        $grabstudent = new Grab_Student();
        $grabstudent->car_brand = $request->carbrand;
        $grabstudent->car_name = $request->carname;
        $grabstudent->car_registration_num = $request->carregisternumber;
        $grabstudent->number_of_seat = $request->totalseat;
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
            'time'=>'required',
            'status'=>'required',
        ]);
        
        $destinationoffer = new Destination_Offer();
        $destinationoffer->destination_name = $request->destination;
        $destinationoffer->status = $request->status;
        $destinationoffer->pick_up_point = $request->pickup;
        $destinationoffer->price_destination = $request->price;
        $destinationoffer->available_time = $request->time;
        $destinationoffer->id_grab_student = $request->grabcar;
        $res = $destinationoffer->save();
    
        if($res){
            return back()->with('success','Your car destination has been registered successfully');
        }else{
            return back()->with('fail','Something went wrong');
        }
    }

    public function paymentgrab(Request $request, $id)
    {
        $updategrab = Grab_Student::findOrFail($id);
        $updategrab->update([
            'status' => "BOOKED",
        ]);

        $request->validate([
            'iddestination'=>'required',
            'idpassenger'=>'required'
        ]);

        $updatedestinationgrab = Destination_Offer::findOrFail($request->iddestination);
        $updatedestinationgrab->update([
            'status' => "OCCUPIED",
        ]);

        $today = Carbon::now();
        $formattedDate = $today->format('Y-m-d');

        $destination = new Grab_Booking();
        $destination->id_destination_offer = $request->iddestination;
        $destination->id_user = $request->idpassenger;
        $destination->book_date = $formattedDate;
        $res = $destination->save();
    
        return redirect()->route('book.grab')->with('success', 'Payment Received');
    }

    public function notifygrab(Request $request, $id)
    {
        $request->validate([
            'iddestination'=>'required',
            'idpassenger'=>'required'
        ]);

        $today = Carbon::now();
        $time = $today->format('H:i');

        $paymentStatus = "NOT ABLE TO PAY";

        $destination = new NotifyGrab();
        $destination->id_destination_offer = $request->iddestination;
        $destination->id_user = $request->idpassenger;
        $destination->status = $paymentStatus;
        $destination->time_notify = $time;
        $res = $destination->save();
    
        return redirect()->route('book.grab')->with('success', 'Notify Sent');
    }


}
