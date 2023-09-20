<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Model\Organization;
use App\Models\Bus;
use App\Models\Bus_Booking;
use App\Models\NotifyBus;
use App\Mail\NotifyPassengerBus;
use Illuminate\Support\Facades\Mail;
use App\User;
use Hash;
use Session;
use PDF;
use Carbon\Carbon;

class BusController extends Controller
{
    public function setbus()
    {
        $orgtype = 'Bas';
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
        return view('bus.insertbus', compact('data'));
    }

    public function managebus()
    {
        $userId = Auth::id();
        $data = DB::table('organization_user')
        ->join('buses', 'buses.id_organizations', 'organization_user.organization_id')
        ->select("buses.*")
        ->distinct()
        ->where('organization_user.user_id', $userId)
        ->get();
        return view('bus.managebus', compact('data'));
    }

    public function manageselectedbus($id)
    {
        $databus =  Bus::findOrFail($id);
        return view("bus.manageselectedbus", compact('databus'));
    }

    public function selectbookbus($id)
    {
        $userId = Auth::id();
        $data =  Bus::where('id', $id)
        ->get();
        return view("bus.viewbookbus", compact('data','userId'));
    }

    public function bussendnotify(Request $request)
    {
        $uniqueDestinations = Bus::where('status', '=', 'NOT CONFIRM')
        ->get();

        $selectedDestination = $request->input('availabledestination');
        $selectedData = null;
    
        if ($selectedDestination) 
        {
            $selectedData = DB::table('buses')
            ->join('bus_notifys', 'bus_notifys.id_bus', '=', 'buses.id')
            ->join('users', 'users.id', '=', 'bus_notifys.id_user')
            ->where('buses.id', $selectedDestination)
            ->where('buses.status', '=', 'NOT CONFIRM')
            ->where('bus_notifys.status', '!=', 'PAID')
            ->select( 'bus_notifys.id','users.name','buses.bus_depart_from', 'buses.bus_destination', 'buses.trip_number', 'buses.bus_registration_number', 'bus_notifys.status','bus_notifys.time_notify')
            ->get();
        }
    
        return view('bus.notifypassenger', compact('uniqueDestinations', 'selectedDestination', 'selectedData'));
    }

    public function busbayartempahan()
    {
        $userId = Auth::id();
        $list = DB::table('bus_notifys')
        ->join('buses', 'bus_notifys.id_bus', '=', 'buses.id')
        ->join('users', 'users.id', '=', 'bus_notifys.id_user')
        ->where('bus_notifys.status', '=', 'ALREADY NOTIFY FOR BOOK')
        ->where('buses.status', '!=', 'FULLY BOOK')
        ->where('users.id', $userId)
        ->select('bus_notifys.id','buses.bus_depart_from', 'buses.bus_destination', 'buses.price_per_seat', 'buses.trip_number', 'buses.departure_time', 'buses.departure_date','bus_notifys.time_notify')
        ->get();
        return view("bus.bayartempahan", compact('list'));
    }

    public function buspilihtempahan($id)
    {
        $userId = Auth::id();
        $data =  Bus::join('bus_notifys','bus_notifys.id_bus','=','buses.id')
        ->where('bus_notifys.id', $id)
        ->select( 'bus_notifys.id as notifyid','buses.id as busid','buses.bus_depart_from', 'buses.bus_destination', 'buses.departure_time', 'buses.departure_date', 'buses.bus_registration_number', 'buses.trip_number', 'buses.price_per_seat', 'buses.available_seat', 'buses.estimate_arrive_time', 'buses.booked_seat')
        ->get();
        return view("bus.passengerbayartempahan", compact('data','userId'));
    }

    public function bookbus(Request $request)
    {
        $uniquePickupPoints = Bus::distinct()->pluck('bus_depart_from');
        $uniqueDestinations = Bus::distinct()->pluck('bus_destination');
        $selectedPickupPoint = $request->input('departfrom');
        $selectedDestination = $request->input('destination');
        $matchedData = null;

        if($request->has('button1')) 
        {
            if ($selectedPickupPoint && $selectedDestination) 
            {
                // Fetch data matching both selections
                $matchedData = Bus::when($selectedPickupPoint, function ($query) use ($selectedPickupPoint) {
                $query->where('bus_depart_from', $selectedPickupPoint);
                })
                ->when($selectedDestination, function ($query) use ($selectedDestination) {
                $query->where('bus_destination', $selectedDestination);
                })
                ->where('status', '!=', 'NOT AVAILABLE')
                ->where('status', '!=', 'FULLY BOOK')
                ->where('bus_depart_from', '=', $selectedPickupPoint)
                ->where('bus_destination', '=', $selectedDestination)
                ->get();
            }
        }
        elseif($request->has('button2')) 
        {
                // Fetch data matching both selections
                $matchedData = Bus::where('status', '!=', 'NOT AVAILABLE')
                ->where('status', '!=', 'FULLY BOOK')
                ->get();
        }

        return view('bus.bookbus', compact('uniquePickupPoints', 'uniqueDestinations', 'selectedPickupPoint', 'selectedDestination', 'matchedData'));
    }

    public function updatenotifybus($id)
    {
        $updatebus = NotifyBus::findOrFail($id);
        $updatebus->update([
            'status' => "ALREADY NOTIFY FOR BOOK",
        ]);
        
        // Fetch user data as stdClass
        $userData = DB::table('bus_notifys')
            ->join('users', 'bus_notifys.id_user', '=', 'users.id')
            ->where('bus_notifys.id', $id)
            ->first(); // Use first() to get a single user object
        
        if ($userData) {
            // Typecast the stdClass to an array
            $userDataArray = (array) $userData;
        
            // Create a User instance from the array
            $user = new User($userDataArray);

            $notify = NotifyBus::where('id', '=', $id)->first();
            
            Mail::to($user->email)->send(new NotifyPassengerBus($notify, $user));
        } else {
            // Handle the case where no user was found for the given $id
        }

        return back()->with('success', 'Notify Email telah dihantar kepada penumpang');

    }

    public function insertbus(Request $request)
    {
        $request->validate([
            'totalseat'=>'required',
            'minimumseat'=>'required',
            'busregisternumber'=>'required|max:7',
            'bustripnumber'=>'required',
            'tripdesc'=>'required',
            'busdepart'=>'required',
            'busdestination'=>'required',
            'time'=>'required',
            'estimatetime'=>'required',
            'date'=>'required',
            'priceperseat'=>'required',
            'organizationid'=>'required',
            'status'=>'required'
        ]);

        $buscompany = new Bus();
        $buscompany->total_seat = $request->totalseat;
        $buscompany->booked_seat = '0';
        $buscompany->available_seat = $request->totalseat;
        $buscompany->minimum_seat = $request->minimumseat;
        $buscompany->bus_registration_number = $request->busregisternumber;
        $buscompany->status = $request->status;
        $buscompany->trip_number =  $request->bustripnumber;
        $buscompany->trip_description = $request->tripdesc;
        $buscompany->bus_depart_from = $request->busdepart;
        $buscompany->bus_destination = $request->busdestination;
        $buscompany->departure_time = $request->time;
        $buscompany->price_per_seat = $request->priceperseat;
        $buscompany->estimate_arrive_time =  $request->estimatetime;
        $buscompany->departure_date = $request->date;
        $buscompany->id_organizations = $request->organizationid;
        $res = $buscompany->save();
    
        if($res){
            return back()->with('success','Your bus has been registered successfully');
        }else{
            return back()->with('fail','Something went wrong');
        }
    }

    public function updatebus(Request $request, $id)
    {

        $updatebus = Bus::findOrFail($id);
        $updatebus->update($request->all());
        $updatebus->update([
            'trip_number' => $request->input('bustripnumber'),
            'minimum_seat' => $request->input('minimumseat'),
            'trip_description' => $request->input('tripdesc'),
            'bus_depart_from' => $request->input('busdepart'),
            'bus_destination' => $request->input('busdestination'),
            'departure_time' => $request->input('time'),
            'price_per_seat' => $request->input('priceperseat'),
            'estimate_arrive_time' => $request->input('estimatetime'),
            'departure_date' => $request->input('date'),
            'status' => $request->input('status'),
        ]);
        
        return redirect()->route('bus.manage')->with('success', 'Update bus was successful');
    }

    public function notifybus(Request $request, $id)
    {
        $request->validate([
            'idpassenger'=>'required'
        ]);

        $today = Carbon::now();
        $time = $today->format('H:i');

        $paymentStatus = "NOT NOTIFY TO PAY";

        $destination = new NotifyBus();
        $destination->id_bus = $id;
        $destination->id_user = $request->idpassenger;
        $destination->status = $paymentStatus;
        $destination->time_notify = $time;
        $res = $destination->save();
    
        return redirect()->route('book.bus')->with('success', 'Notify Sent');
    }

    public function paymentbus(Request $request, $id)
    {
        $currentValue = $request->input('seat');
        $newValue = $currentValue - 1;

        $bookedValue = $request->input('bookedseat');
        $newBookedValue = $bookedValue + 1;

        $updatebus = Bus::findOrFail($id);
        $updatebus->update([
            'available_seat' =>  $newValue,
            'booked_seat' =>  $newBookedValue,
        ]);

        $request->validate([
            'idpassenger'=>'required'
        ]);

        $today = Carbon::now();
        $formattedDate = $today->format('Y-m-d');

        $destination = new Bus_Booking();
        $destination->id_bus =  $id;
        $destination->id_user = $request->idpassenger;
        $destination->book_date = $formattedDate;
        $res = $destination->save();
    
        return redirect()->route('bayar.bus');
    }

    public function passengerbusbayartempahan(Request $request, $id)
    {
        $currentValue = $request->input('seat');
        $newValue = $currentValue - 1;

        $bookedValue = $request->input('bookedseat');
        $newBookedValue = $bookedValue + 1;

        $updatebus = Bus::findOrFail($id);
        $updatebus->update([
            'available_seat' =>  $newValue,
            'booked_seat' =>  $newBookedValue,
        ]);

        $request->validate([
            'idpassenger'=>'required',
            'notify'=>'required'
        ]);

        $today = Carbon::now();
        $formattedDate = $today->format('Y-m-d');

        $destination = new Bus_Booking();
        $destination->id_bus =  $id;
        $destination->id_user = $request->idpassenger;
        $destination->book_date = $formattedDate;
        $res = $destination->save();

        $updatenotify = NotifyBus::findOrFail($request->notify);
        $updatenotify->update([
            'status' => "PAID",
        ]);

        return redirect()->route('bayar.bus');
    }

    public function makepaymentbus()
    {
        $data = Bus_Booking::join('buses', 'buses.id', '=', 'bus_bookings.id_bus')
        ->join('users', 'users.id', '=', 'bus_bookings.id_user')
        ->select('bus_bookings.id as bookid', 'buses.bus_registration_number', 'buses.booked_seat', 'buses.available_seat', 'buses.trip_number', 'buses.bus_depart_from', 'buses.bus_destination', 'buses.departure_time', 'buses.departure_date', 'buses.price_per_seat')
        ->where('bus_bookings.id', '=', function ($query) {
            $query->selectRaw('MAX(id)')
                ->from('bus_bookings');
        })
        ->get();

       return view('bus.busreceipt', compact('data'));
    }

  

}
