<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrganizationRequest;
use App\Models\Organization;
use App\Models\TypeOrganization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;
use App\Http\Jajahan\Jajahan;
use App\Models\OrganizationHours;
use App\Models\Donation;
use Illuminate\Support\Facades\Validator;
use App\Models\OrganizationRole;
use App\Models\Promotion;
use App\Models\Room;
use View;

use DateTime;
use DateInterval;
use DatePeriod;

class HomestayController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $data = Promotion::join('organizations', 'promotions.homestayid', '=', 'organizations.id')
        ->join('organization_user','organizations.id','organization_user.organization_id')
    ->where('organization_user.user_id',$userId)
    ->select('organizations.nama', 'promotions.promotionid','promotions.promotionname','promotions.datefrom', 'promotions.dateto','promotions.discount')
    ->get();

        return view('homestay.listpromotion', compact('data'));
    }

    public function setpromotion()
    {
        $orgtype = 'Homestay / Hotel';
        $userId = Auth::id();
        $data = DB::table('organizations as o')
            ->leftJoin('organization_user as ou', 'o.id', 'ou.organization_id')
            ->leftJoin('type_organizations as to', 'o.type_org', 'to.id')
            ->select("o.*")
            ->distinct()
            ->where('ou.user_id', $userId)
            ->where('to.nama', $orgtype)
            ->where('o.deleted_at', null)
            ->get();
        return view('homestay.setpromotion', compact('data'));
    }

    public function insertpromotion(Request $request)
    {
        
        $userId = Auth::id();
        $request->validate([
            'homestayid' => 'required',
            'promotionname' => 'required',
            'datefrom' => 'required',
            'dateto' => 'required',
            'discount' => 'required'
        ]);

        
            $promotion = new Promotion();
            $promotion->homestayid = $request->homestayid;
            $promotion->promotionname = $request->promotionname;
            $promotion->datefrom = $request->datefrom;
            $promotion->dateto = $request->dateto;
            $promotion->discount = $request->discount;
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

public function editpromo(Request $request,$promotionid)
{
    $request->validate([
        'promotionname' => 'required',
        'datefrom' => 'required',
        'dateto' => 'required',
        'discount' => 'required|numeric|min:1|max:100'
    ]);

    $promotionname = $request->input('promotionname');
    $datefrom = $request->input('datefrom');
    $dateto = $request->input('dateto');
    $discount = $request->input('discount');

    $userId = Auth::id();
    $promotion = Promotion::where('promotionid',$promotionid)
        ->first();

        if ($promotion) {
            $promotion->promotionname = $request->promotionname;
            $promotion->datefrom = $request->datefrom;
            $promotion->dateto = $request->dateto;
            $promotion->discount = $request->discount;

            $result = $promotion->save();

            if($result)
            {
                return back()->with('success', 'Promosi Berjaya Disunting');
            }
            else
            {
                return back()->withInput()->with('error', 'Promosi Gagal Disunting');
    
            }
        } else {
            return back()->with('fail', 'Promotions not found!');
        }
    }

    public function urusbilik()
    {
        $orgtype = 'Homestay / Hotel';
        $userId = Auth::id();
        $data = DB::table('organizations as o')
            ->leftJoin('organization_user as ou', 'o.id', 'ou.organization_id')
            ->leftJoin('type_organizations as to', 'o.type_org', 'to.id')
            ->select("o.*")
            ->distinct()
            ->where('ou.user_id', $userId)
            ->where('to.nama', $orgtype)
            ->where('o.deleted_at', null)
            ->get();
        return view('homestay.urusbilik', compact('data'));
    }

    public function gettabledata()
    {
        if (request()->ajax()) {
            $homestayid = $request->homestayid;

            $hasOrganization = $request->hasOrganization;

            $userId = Auth::id();

            if ($homestayid != '' && !is_null($hasOrganization)) {

                    $data = DB::table('rooms')
                    ->join('organizations', 'organizations.id', '=', 'rooms.homestayid')
                    ->join('organization_user as ou', 'o.id', 'ou.organization_id')
                    ->select('organizations.id as id', 'rooms.homestayid', 'rooms.roomname', 'rooms.roompax', 'rooms.details', 'rooms.price', 'rooms.status')
                    ->distinct()
                    ->where('ou.user_id', $userId)
                    ->where('organizations.id', $homestayid);
            }
            elseif ($hasOrganization == "true") {
                $data = DB::table('rooms')
                    ->join('organizations', 'organizations.id', '=', 'rooms.homestayid')
                    ->join('organization_user as ou', 'o.id', 'ou.organization_id')
                    ->select('organizations.id as id', 'rooms.homestayid', 'rooms.roomname', 'rooms.roompax', 'rooms.details', 'rooms.price', 'rooms.status')
                    ->distinct()
                    ->where('ou.user_id', $userId);
            }
            

            // dd($data->oid);
            $table = Datatables::of($data);

            $table->addColumn('status', function ($row) {
                if ($row->status == '1') {
                    $btn = '<div class="d-flex justify-content-center">';
                    $btn = $btn . '<span class="badge badge-success">Aktif</span></div>';

                    return $btn;
                } else {
                    $btn = '<div class="d-flex justify-content-center">';
                    $btn = $btn . '<span class="badge badge-danger"> Tidak Aktif </span></div>';

                    return $btn;
                }
            });
            return $table->make(true);
        }
        // return Donation::geturl();
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
