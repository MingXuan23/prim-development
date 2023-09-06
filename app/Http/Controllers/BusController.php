<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Model\Organization;
use App\Models\Bus;
use App\Models\Bus_Booking;
use Hash;
use Session;
use PDF;

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
        
        return redirect('/bus.manage')->with('success', 'Bus has been updated');
    }

}
