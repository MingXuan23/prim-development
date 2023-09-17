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
use App\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotifyPassengerGrabStudent;
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

    public function grabbayartempahan()
    {
        $userId = Auth::id();
        $list = DB::table('grab_notifys')
        ->join('destination_offers', 'grab_notifys.id_destination_offer', '=', 'destination_offers.id')
        ->join('users', 'users.id', '=', 'grab_notifys.id_user')
        ->join('grab_students', 'grab_students.id', '=', 'destination_offers.id_grab_student')
        ->where('grab_notifys.status', '=', 'ALREADY NOTIFY FOR BOOK')
        ->where('destination_offers.status', '!=', 'OCCUPIED')
        ->where('users.id', $userId)
        ->select('destination_offers.id','destination_offers.pick_up_point', 'destination_offers.destination_name', 'grab_students.car_name', 'grab_students.car_brand', 'grab_students.car_registration_num','grab_notifys.time_notify')
        ->get();
        return view("grab.bayartempahan", compact('list'));
    }

    public function grabsendnotify(Request $request)
    {
        $uniqueDestinations = Destination_Offer::where('destination_offers.status', '=', 'NOT CONFIRM')
        ->distinct()
        ->pluck('destination_name');

        $selectedDestination = $request->input('availabledestination');
        $selectedData = null;
    
        if ($selectedDestination) 
        {
            $selectedData = DB::table('destination_offers')
            ->join('grab_students', 'grab_students.id', '=', 'destination_offers.id_grab_student')
            ->join('grab_notifys', 'grab_notifys.id_destination_offer', '=', 'destination_offers.id')
            ->join('users', 'users.id', '=', 'grab_notifys.id_user')
            ->where('destination_offers.destination_name', $selectedDestination)
            ->where('destination_offers.status', '=', 'NOT CONFIRM')
            ->where('grab_notifys.status', '!=', 'PAID')
            ->select( 'grab_notifys.id','users.name','destination_offers.pick_up_point', 'destination_offers.destination_name', 'grab_students.car_name', 'grab_students.car_brand', 'grab_students.car_registration_num', 'grab_notifys.status','grab_notifys.time_notify')
            ->get();
        }
    
        return view('grab.notifypassenger', compact('uniqueDestinations', 'selectedDestination', 'selectedData'));
    }

    public function updatenotifygrab($id)
    {
        $updategrab = NotifyGrab::findOrFail($id);
        $updategrab->update([
            'status' => "ALREADY NOTIFY FOR BOOK",
        ]);
        
        // Fetch user data as stdClass
        $userData = DB::table('grab_notifys')
            ->join('users', 'grab_notifys.id_user', '=', 'users.id')
            ->where('grab_notifys.id', $id)
            ->first(); // Use first() to get a single user object
        
        if ($userData) {
            // Typecast the stdClass to an array
            $userDataArray = (array) $userData;
        
            // Create a User instance from the array
            $user = new User($userDataArray);

            $notify = NotifyGrab::where('id', '=', $id)->first();
            
            Mail::to($user->email)->send(new NotifyPassengerGrabStudent($notify, $user));
        } else {
            // Handle the case where no user was found for the given $id
        }

        $grab_notify = Organization::join('grab_students', 'organizations.id', '=', 'grab_students.id_organizations')
        ->join('destination_offers','grab_students.id','=','destination_offers.id_grab_student')
        ->join('grab_notifys','destination_offers.id','=','grab_notifys.id_destination_offer')
        ->where('grab_notifys.id',$notify->id) 
        ->select('destination_offers.pick_up_point','destination_offers.destination_name','destination_offers.available_time', 'grab_students.car_brand', 'grab_students.car_name', 'grab_students.car_registration_num', 'grab_students.number_of_seat')
        ->get();

        return view('grab.notifyemail', compact('grab_notify','notify', 'user'));
    

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

    public function selecttempahangrab($id)
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
        return view("grab.bayartempahan", compact('data','datadestinationid','userId'));
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
