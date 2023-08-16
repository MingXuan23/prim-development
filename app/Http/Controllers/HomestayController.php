<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use View;
use App\Models\Homestay;
use App\Models\Promotion;
use App\Models\Room;
use DateTime;
use DateInterval;
use DatePeriod;

class HomestayController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $data = Homestay::where('ownerid', $userId)->get();

        return view('homestay.listhomestay', compact('data'));
    }

    public function createhomestay()
    {
        return view('homestay.createhomestay');
    }

    public function inserthomestay(Request $request)
    {
        
        $userId = Auth::id();
        $request->validate([
            'name' => 'required|unique:homestays,name',
            'location' =>'required',
            'pno' =>'required|numeric',
            'stat' =>'required'
            ]);

            $homestay = new Homestay();
            $homestay->name = $request->name;
            $homestay->location = $request->location;
            $homestay->pno = $request->pno;
            $homestay->status = $request->stat;
            $homestay->ownerid = $userId;
            $result = $homestay->save();

            if($result)
        {
            return back()->with('success', 'Homestay Berjaya Ditambah');
        }
        else
        {
            return back()->withInput()->with('error', 'Homestay Telahpun Didaftarkan');

        }
    }

    public function disabledatepromo($homestayid)
{
    
    $promotions = Promotion::where('homestayid', $homestayid) // Add your additional condition here
                   ->get();

    $disabledDates = [];

    foreach ($promotions as $promotion) {
        $begin = new DateTime($promotion->datefrom);
        $end = new DateTime($promotion->dateto);
        $interval = new DateInterval('P1D');
        $daterange = new DatePeriod($begin, $interval, $end);

        foreach ($daterange as $date) {
            $disabledDates[] = $date->format('Y-m-d');
        }
    }

    return response()->json(['disabledDates' => $disabledDates]);
}

public function addpromo(Request $request, $homestayid)
{
    $request->validate([
        'promotionname' => 'required',
        'datefrom' => 'required',
        'dateto' => 'required',
        'discount' => 'required'
    ]);

    $promotionname = $request->input('promotionname');
    $datefrom = $request->input('datefrom');
    $dateto = $request->input('dateto');
    $discount = $request->input('discount');

    $promotion = new Promotion();
    $promotion->homestayid = $homestayid;
    $promotion->promotionname = $promotionname;
    $promotion->datefrom = $datefrom;
    $promotion->dateto = $dateto;
    $promotion->discount = $discount;
    $result = $promotion->save();

    if($result)
    {
        return back()->with('success', 'Promosi Berjaya Ditambah');
    }
    else
    {
        return back()->withInput()->with('error', 'Promosi Telahpun Didaftarkan');

    }
}

public function addroom(Request $request, $id)
{
    $request->validate([
        'roomname' => 'required',
        'roompax' => 'required',
        'details' => 'required',
        'roomprice' => 'required'
    ]);

    // Retrieve the roomname and price from the request
    $roomname = $request->input('roomname');
    $roompax = $request->input('roompax');
    $details = $request->input('details');
    $roomprice = $request->input('roomprice');
    $status = "Available";

    // Perform the logic to add the room using the provided data
    // You can use the $id, $roomname, and $price variables as needed

    // Example logic to add the room
    $room = new Room();
    $room->homestayid = $id;
    $room->roomname = $roomname;
    $room->roompax = $roompax;
    $room->details = $details;
    $room->price = $roomprice;
    $room->status = $status;
    $result = $room->save();

    if($result)
    {
        return back()->with('success', 'Bilik Berjaya Ditambah');
    }
    else
    {
        return back()->withInput()->with('error', 'Bilik Telahpun Didaftarkan');

    }

    
}

public function edithomestay(Request $request, $id)
    {
        $foreignkey = Auth::id();
        $homestay = Homestay::find($id);

        if ($homestay) {
            $homestay->name = $request->name;
            $homestay->location = $request->location;
            $homestay->pno = $request->pno;
            $homestay->status = $request->stat;
            $homestay->ownerid = $foreignkey;

            $result = $homestay->save();

            if ($result) {
                return back()->with('success', 'Bilik Berjaya Disunting');
            } else {
                return back()->withInput()->with('error', 'Bilik Gagal Disunting');
            }
        } else {
            return back()->with('fail', 'Homestay not found!');
        }
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
