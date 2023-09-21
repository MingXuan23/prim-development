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
use App\Mail\ResitBayaranGrab;
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
        ->select('grab_notifys.id','destination_offers.pick_up_point', 'destination_offers.destination_name', 'grab_students.car_name', 'grab_students.car_brand', 'grab_students.car_registration_num','grab_notifys.time_notify')
        ->get();
        return view("grab.bayartempahan", compact('list'));
    }

    public function grabsendnotify(Request $request)
    {
        $uniqueDestinations = Destination_Offer::select('id','destination_name','pick_up_point','available_time')
        ->where('status', '=', 'NOT CONFIRM')
        ->distinct()
        ->get();

        $selectedDestination = $request->input('availabledestination');
        $selectedData = null;
    
        if ($selectedDestination) 
        {
            $selectedData = DB::table('destination_offers')
            ->join('grab_students', 'grab_students.id', '=', 'destination_offers.id_grab_student')
            ->join('grab_notifys', 'grab_notifys.id_destination_offer', '=', 'destination_offers.id')
            ->join('users', 'users.id', '=', 'grab_notifys.id_user')
            ->where('destination_offers.id', $selectedDestination)
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

        return back()->with('success', 'Notify Email telah dihantar kepada penumpang');

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
        $updategrab->update([
            'status' => $request->input('status'),
            'available_time' =>  $request->input('updatetime'),
        ]);
        return back()->with('success', 'Data Destinasi telah berjaya Update');
    }

    public function bookgrab(Request $request)
    {
        $uniquePickupPoints = Destination_Offer::distinct()->pluck('pick_up_point');
        $uniqueDestinations = Destination_Offer::distinct()->pluck('destination_name');

        $selectedPickupPoint = $request->input('pick_up_point');
        $selectedDestination = $request->input('availabledestination');
        $matchedData = null;

        if ($request->has('button1')) 
        {
                // Fetch data matching both selections
                 if ($selectedPickupPoint && $selectedDestination) 
                 {
                    $matchedData = Destination_Offer::when($selectedPickupPoint, function ($query) use ($selectedPickupPoint) {
                    $query->where('pick_up_point', $selectedPickupPoint);
                 })
                ->when($selectedDestination, function ($query) use ($selectedDestination) 
                {
                    $query->where('destination_name', $selectedDestination);
                })
                ->where('grab_students.status', '=', 'AVAILABLE')
                ->where('destination_offers.status', '!=', 'NOT AVAILABLE')
                ->where('destination_offers.status', '!=', 'OCCUPIED')
                ->where('destination_offers.destination_name', '=', $selectedDestination)
                ->where('destination_offers.pick_up_point', '=', $selectedPickupPoint)
                ->join('grab_students', 'destination_offers.id_grab_student', '=', 'grab_students.id')
                ->select(
                    'destination_offers.id',
                    'grab_students.car_brand',
                    'grab_students.car_name',
                    'destination_offers.status',
                    'grab_students.number_of_seat',
                    'destination_offers.available_time',
                    'destination_offers.destination_name',
                    'destination_offers.pick_up_point',
                    'destination_offers.price_destination'
                )
                ->get();
                }
        }
        elseif ($request->has('button2')) 
        {
                $matchedData = Destination_Offer::where('grab_students.status', '=', 'AVAILABLE')
                ->where('destination_offers.status', '!=', 'NOT AVAILABLE')
                ->where('destination_offers.status', '!=', 'OCCUPIED')
                ->join('grab_students', 'destination_offers.id_grab_student', '=', 'grab_students.id')
                ->select(
                    'destination_offers.id',
                    'grab_students.car_brand',
                    'grab_students.car_name',
                    'destination_offers.status',
                    'grab_students.number_of_seat',
                    'destination_offers.available_time',
                    'destination_offers.destination_name',
                    'destination_offers.pick_up_point',
                    'destination_offers.price_destination'
                )
                ->get();
        }
        return view('grab.bookgrab', compact('uniquePickupPoints', 'uniqueDestinations', 'selectedPickupPoint', 'selectedDestination', 'matchedData'));
    }

    public function passengerpilihtempahan($id)
    {
        $userId = Auth::id();
        $data =  Destination_Offer::join('grab_students', 'grab_students.id', '=', 'destination_offers.id_grab_student')
        ->join('grab_notifys','grab_notifys.id_destination_offer','=','destination_offers.id')
        ->where('grab_notifys.id', $id)
        ->select( 'grab_notifys.id as notifyid','destination_offers.id as desid','grab_students.id as grabid','grab_students.car_brand','grab_students.car_name', 'grab_students.number_of_seat', 'destination_offers.available_time', 'destination_offers.status', 'destination_offers.destination_name', 'destination_offers.pick_up_point', 'destination_offers.price_destination')
        ->get();
        return view("grab.passengerbayartempahan", compact('data','userId'));
    }

    public function selectbookgrab($id)
    {
        $userId = Auth::id();
        $data =  Destination_Offer::join('grab_students', 'grab_students.id', '=', 'destination_offers.id_grab_student')
        ->where('destination_offers.id', $id)
        ->select( 'destination_offers.id as desid','grab_students.id as grabid','grab_students.car_brand','grab_students.car_name', 'grab_students.number_of_seat', 'destination_offers.available_time', 'destination_offers.status', 'destination_offers.destination_name', 'destination_offers.pick_up_point', 'destination_offers.price_destination')
        ->get();
        return view("grab.viewbookgrab", compact('data','userId'));
    }

    public function grabcheckpassenger()
    {
        $data =  Grab_Booking::join('destination_offers', 'destination_offers.id', '=', 'grab_bookings.id_destination_offer')
        ->join('users','users.id','=','grab_bookings.id_user')
        ->join('grab_students','grab_students.id','=','destination_offers.id_grab_student')
        ->select( 'users.name','grab_students.car_brand','grab_students.car_name','destination_offers.pick_up_point','destination_offers.available_time','destination_offers.price_destination','destination_offers.destination_name','grab_bookings.book_date','grab_bookings.status')
        ->orderBy('grab_bookings.id','desc')
        ->get();
        return view("grab.checkpassenger", compact('data'));
    }

    public function checksales()
    {
        $orgtype = 'Grab Student';
        $userId = Auth::id();

        $org = DB::table('organizations as o')
            ->leftJoin('organization_user as ou', 'o.id', 'ou.organization_id')
            ->leftJoin('type_organizations as to', 'o.type_org', 'to.id')
            ->select("o.*")
            ->distinct()
            ->where('ou.user_id', $userId)
            ->where('to.nama', $orgtype)
            ->where('o.deleted_at', null)
            ->get();

        return view('grab.tunjuksales', compact('org'));
    }

    public function grabsales(Request $request)
    {
        $org = $request->input('org');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
    
        // Fetch sales data from the database
        $salesData = DB::table('grab_bookings')
        ->join('destination_offers', 'grab_bookings.id_destination_offer', '=', 'destination_offers.id')
        ->join('grab_students', 'destination_offers.id_grab_student', '=', 'grab_students.id')
        ->select('grab_bookings.book_date', DB::raw('SUM(destination_offers.price_destination) as total_sales'))
        ->whereBetween('grab_bookings.book_date', [$startDate, $endDate])
        ->where('grab_students.id_organizations', $org)
        ->groupBy('grab_bookings.book_date')
        ->get();
    
        // Render the graph using a charting library like Chart.js
        // You can pass $salesData to your view and use JavaScript to render the graph.
    
        return view('grab.tunjuksales', compact('salesData','org'));
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
    
        return redirect()->route('bayar.grab');
    }

    public function makepaymentgrab()
    {
        $data = Grab_Booking::join('destination_offers', 'destination_offers.id', '=', 'grab_bookings.id_destination_offer')
        ->join('users', 'users.id', '=', 'grab_bookings.id_user')
        ->join('grab_students', 'grab_students.id', '=', 'destination_offers.id_grab_student')
        ->select('grab_bookings.id as bookid', 'grab_students.car_brand', 'grab_students.car_name', 'destination_offers.pick_up_point', 'destination_offers.destination_name', 'grab_students.number_of_seat', 'destination_offers.available_time', 'destination_offers.price_destination')
        ->where('grab_bookings.id', '=', function ($query) {
            $query->selectRaw('MAX(id)')
                ->from('grab_bookings');
        })
        ->get();

       return view('grab.grabreceipt', compact('data'));
    }

    public function passengerbayartempahan(Request $request, $id)
    {
        $updategrab = Grab_Student::findOrFail($id);
        $updategrab->update([
            'status' => "BOOKED",
        ]);

        $request->validate([
            'iddestination'=>'required',
            'idpassenger'=>'required',
            'notify'=>'required'
        ]);

        $updatedestinationgrab = Destination_Offer::findOrFail($request->iddestination);
        $updatedestinationgrab->update([
            'status' => "OCCUPIED",
        ]);

        $updatenotify = NotifyGrab::findOrFail($request->notify);
        $updatenotify->update([
            'status' => "PAID",
        ]);

        $today = Carbon::now();
        $formattedDate = $today->format('Y-m-d');

        $destination = new Grab_Booking();
        $destination->id_destination_offer = $request->iddestination;
        $destination->id_user = $request->idpassenger;
        $destination->book_date = $formattedDate;
        $res = $destination->save();
    
        return redirect()->route('bayar.grab');
    }



    public function notifygrab(Request $request, $id)
    {
        $request->validate([
            'iddestination'=>'required',
            'idpassenger'=>'required'
        ]);

        $today = Carbon::now();
        $time = $today->format('H:i');

        $paymentStatus = "NOT NOTIFY TO PAY";

        $destination = new NotifyGrab();
        $destination->id_destination_offer = $request->iddestination;
        $destination->id_user = $request->idpassenger;
        $destination->status = $paymentStatus;
        $destination->time_notify = $time;
        $res = $destination->save();
    
        return redirect()->route('book.grab')->with('success', 'Notify Sent');
    }


}
