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
use App\Models\Booking;
use App\Models\Room;
use View;
use Carbon\Carbon;
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
            'discount' => 'required|numeric|min:1|max:100'
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

public function addroom(Request $request)
{
    $userId = Auth::id();
    $request->validate([
        'homestayid' => 'required',
        'roomname' => 'required',
        'roompax' => 'required',
        'details' => 'required',
        'price'=> 'numeric|min:1',
        'address' => 'required',
    ]);
    
    $status = 'Available';

    $room = new Room();
    $room->homestayid = $request->homestayid;
    $room->roomname = $request->roomname;
    $room->roompax = $request->roompax;
    $room->details = $request->details;
    $room->price = $request->price;
    $room->status = $status;
    $room->address = $request->address;
    $room->created_at = Carbon::now();
    $result = $room->save();
    
    $latestRoom = DB::table('rooms')
    ->latest('roomid')
    ->first();
    $latestRoomID = (int)$latestRoom->roomid;
    
    $images = $request->file('images');
    if($images){
        $i = 0;
        foreach ($images as $image){
            $imagePath = "homestay-image/".$latestRoomID."(".$i.").".$image->getClientOriginalExtension();
            $image->move(public_path('homestay-image'),$imagePath);
            DB::table('homestay_images')
            ->insert([
                'image_path'=>$imagePath,
                'created_at' => Carbon::now(),
                'room_id'=> $latestRoomID,
            ]);
            $i++;
        }
    }


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

            $rooms = Organization::join('rooms', 'organizations.id', '=', 'rooms.homestayid')
                ->join('organization_user','organizations.id', '=', 'organization_user.organization_id')
                ->where('organization_user.user_id',$userId)
                ->select('organizations.id', 'rooms.roomid','rooms.roomname','rooms.details', 'rooms.roompax','rooms.price','rooms.status')
                ->get();
        return view('homestay.urusbilik', compact('data','rooms'));
    }

    public function gettabledata(Request $request)
    {
        $homestayid = $request->homestayid;
        $userId = Auth::id();

        $rooms = Organization::join('rooms', 'organizations.id', '=', 'rooms.homestayid')
                ->join('organization_user', 'organizations.id', '=', 'organization_user.organization_id')
                ->where('organization_user.user_id', $userId)
                ->where([
                    'organizations.id'=> $homestayid,
                    'rooms.deleted_at'=> null,
                ]) // Filter by the selected homestay
                ->select('organizations.id', 'rooms.roomid', 'rooms.roomname', 'rooms.roompax', 'rooms.price', 'rooms.status')
                ->get();
        $allRoomImages = DB::table('rooms')
        ->join('homestay_images','homestay_images.room_id','rooms.roomid')
        ->orderBy('rooms.roomid')
        ->select('rooms.roomid', 'rooms.roomname', 'homestay_images.image_path')
        ->get();

        $currentRoomId = $previousRoomId = 0;
        $roomsImage = [];
        foreach($allRoomImages as $roomImages){
            $currentRoomId = (int)$roomImages->roomid;
            if($currentRoomId == $previousRoomId){
                $previousRoomId = $currentRoomId;
                continue;
            }else{
                array_push($roomsImage , $roomImages);
                $previousRoomId = $currentRoomId;
            }
        }
        return response()->json(['rooms'=>$rooms,'roomImages' => $roomsImage]);
    }

    public function tambahbilik()
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
        return view('homestay.tambahbilik',compact('data'));
    }

    // public function editroom(Request $request,$roomid)
    // {
    //     $request->validate([
    //         'roompax' => 'required',
    //         'details' => 'required',
    //         'price' => 'required|numeric|min:0'
    //     ]);

    //     $roompax = $request->input('roompax');
    //     $details = $request->input('details');
    //     $price = $request->input('price');

    //     $userId = Auth::id();
    //     $room = Room::where('roomid',$roomid)
    //         ->first();

    //         if ($room) {
    //             $room->roompax = $request->roompax;
    //             $room->details = $request->details;
    //             $room->price = $request->price;

    //             $result = $room->save();

    //             if($result)
    //             {
    //                 return back()->with('success', 'Bilik Berjaya Disunting');
    //             }
    //             else
    //             {
    //                 return back()->withInput()->with('error', 'Bilik Gagal Disunting');
    
    //             }
    //         } else {
    //             return back()->with('fail', 'Bilik not found!');
    //         }
    // }
    public function editRoomPage($roomId){
        $room = DB::table('rooms')
        ->where('roomid',$roomId)
        ->first();

        $roomImages = DB::table('rooms')
        ->where('room_id',$roomId)
        ->join('homestay_images','homestay_images.room_id','rooms.roomid')
        ->select('homestay_images.id','homestay_images.image_path')
        ->get();

        return view('homestay.editBilik')->with(['room'=>$room , 'images'=>$roomImages]);
    }
    public function updateRoom(Request $request){
        // for updating images
        if($request->file('image') != null){
            $imageCounter = $imageKeyCounter = 0;
            $newImages = $request->file('image');
            $imagesKey = array_keys($newImages);
            $imagesInDB = DB::table('homestay_images')
                ->where([
                    'room_id' => $request->roomid,
                ])
                ->orderBy('id')
                ->get();
        
            foreach($imagesInDB as $image){
                if(isset($imagesKey[$imageKeyCounter])){
                    $currentImageKey = $imagesKey[$imageKeyCounter];
                    if($imageCounter == $currentImageKey){
                        // delete the old image at this position
                        unlink(public_path($image->image_path));
                        // place the new one
                        $newImagePath = 'homestay-image/'.$request->roomid."(".$imageCounter.").".$newImages[$currentImageKey]->getClientOriginalExtension();
                        $newImages[$currentImageKey]->move(public_path('homestay-image'),$newImagePath);
        
                        // update database
                        DB::table('homestay_images')
                            ->where([
                                'image_path' => $image->image_path,
                            ])
                            ->update([
                                'image_path' => $newImagePath,
                                'updated_at' => Carbon::now(),
                            ]);
                        $imageKeyCounter++;
                    }
                }
                $imageCounter++;
            }            
        }
        // for updating other information
        $request->validate([
            'price'=> 'numeric|min:1',
        ]);
        $isAvailable = $request->isAvailable == "" ? "Not Available" : "Available";
        DB::table('rooms')
        ->where([
            'roomid' => $request->roomid,
        ])
        ->update([
            'roomname' => $request->roomname,
            'roompax' => $request->roompax,
            'price' => $request->price,
            'details' => $request->details,
            'status' => $isAvailable,
            'address' => $request->address,
            'updated_at' => Carbon::now(),
        ]);
    
        return redirect()->route('homestay.urusbilik')->with('success', 'Homestay/Bilik Berjaya Disunting');
    }
    public function deleteRoom(Request $request){
        $roomId = $request->roomId;
        DB::table('rooms')
        ->where([
            'roomid' => $roomId,
        ])
        ->update([
            'deleted_at' => Carbon::now(),
        ]);
        return response()->json(['success' => 'Homestay/Bilik Berjaya Dibuang']);
    }
    public function bookinglist()
    {
        $orgtype = 'Homestay / Hotel';
        $userId = Auth::id();
        $data = DB::table('organizations as o')
            ->join('organization_user as ou', 'o.id', 'ou.organization_id')
            ->join('type_organizations as to', 'o.type_org', 'to.id')
            ->select("o.*")
            ->selectRaw('(SELECT MIN(price) FROM rooms WHERE homestayid = o.id) AS cheapest')
            ->distinct()
            ->where('to.nama', $orgtype)
            ->where('o.deleted_at', null)
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('rooms')
                      ->whereRaw('rooms.homestayid = o.id');
            }) // Add WHERE EXISTS condition
            ->get();
            return view('homestay.bookinglist',compact('data'));
    }

    public function bookhomestay($homestayid)
    {


        $data = Organization::join('rooms', 'organizations.id', '=', 'rooms.homestayid')
                ->join('organization_user', 'organizations.id', '=', 'organization_user.organization_id')
                ->where('organizations.id', $homestayid) // Filter by the selected homestay
                ->select('organizations.id','organizations.nama', 'rooms.roomid', 'rooms.roomname', 'rooms.details', 'rooms.roompax', 'rooms.price', 'rooms.status')
                ->get();

                $nama = $data->isEmpty() ? '' : $data[0]->nama;

                return view('homestay.bookhomestay', compact('data', 'nama','homestayid'));
    }

    public function disabledateroom($roomid)
    {
        $bookings = Booking::where('roomid', $roomid)
                   ->where(function($query) {
                       $query->where('status', 'Booked')
                             ->orWhere('status', 'Paid');
                   })
                   ->get();

    $disabledDates = [];

    foreach ($bookings as $booking) {
        $begin = new DateTime($booking->checkin);
        $end = new DateTime($booking->checkout);
        $interval = new DateInterval('P1D');
        $daterange = new DatePeriod($begin, $interval, $end);

        foreach ($daterange as $date) {
            $disabledDates[] = $date->format('Y-m-d');
        }
    }

    return response()->json(['disabledDates' => $disabledDates]);
    }

    public function insertbooking(Request $request, $roomid, $price)
    {
        $request->validate([
            'checkin' => 'required',
            'checkout' => 'required',
        ]);

        $userId = Auth::id();
        $fkroom = $roomid;
        $status = "Booked";

        $homestay = Room::where('roomid', $fkroom)->first();

        if (!$homestay) {
            return back()->with('fail', 'Homestay not found!');
        }

        $homestayid = $homestay->homestayid;

        $checkinDate = Carbon::createFromFormat('Y-m-d', $request->checkin);
        $checkoutDate = Carbon::createFromFormat('Y-m-d', $request->checkout);
        $totalDays = $checkoutDate->diffInDays($checkinDate) + 1;
    
    // Retrieve the applicable promotion based on check-in and check-out dates
        $promotion = Promotion::where('datefrom', '<=', $request->checkin)
        ->where('dateto', '>=', $request->checkout)
        ->where('homestayid',$homestayid)
        ->first();
    
    // Calculate the total price with discount if a promotion is applicable
        if ($promotion !== null) {
            $discountedPrice = $price - ($price * $promotion->discount / 100);
            $totalPrice = $discountedPrice * $totalDays;
        } else {
            $totalPrice = $price * $totalDays;
        }

        $availability = Booking::where('roomid', $fkroom)
        ->where(function ($query) use ($request) {
        $query->where('checkin', '<', $request->checkout)
            ->where('checkout', '>', $request->checkin);
    })
    ->get();

    if ($availability->isEmpty()) {

            $booking = new Booking();
            $booking->checkin = $request->checkin;
            $booking->checkout = $request->checkout;
            $booking->status = $status;
            $booking->totalprice = $totalPrice;
            $booking->customerid = $userId;
            $booking->roomid = $fkroom;
    
            $result = $booking->save();
    
            if($result)
                {
                    return back()->withInput()->with('success', 'Bilik Berjaya Ditempah');
                }
                else
                {
                    return back()->withInput()->with('error', 'Tempahan Gagal Dibuat');
        
                }
        } else {
            return back()->withInput()->with('error', 'Bilik Pada Tarikh Tersebut Telah Penuh');
        }

        

    }

    public function tempahananda()
    {
        $userId = Auth::id();
        $status = 'Booked';
        $data = Organization::join('rooms', 'organizations.id', '=', 'rooms.homestayid')
                ->join('bookings','rooms.roomid','=','bookings.roomid')
                ->where('bookings.customerid', $userId)
                ->where('bookings.status', $status) // Filter by the selected homestay
                ->select('organizations.id','organizations.nama','organizations.address', 'rooms.roomid', 'rooms.roomname', 'rooms.details', 'rooms.roompax', 'rooms.price', 'rooms.status','bookings.bookingid','bookings.checkin','bookings.checkout','bookings.totalprice')
                ->get();

        return view('homestay.tempahananda',compact('data'));
    }

    public function homestayresit($bookingid)
    {
        $userId = Auth::id();
        $data = Organization::join('rooms', 'organizations.id', '=', 'rooms.homestayid')
                ->join('bookings','rooms.roomid','=','bookings.roomid')
                ->where('bookings.customerid', $userId)
                ->where('bookings.bookingid',$bookingid) // Filter by the selected homestay
                ->select('organizations.id','organizations.nama','organizations.address', 'rooms.roomid', 'rooms.roomname', 'rooms.details', 'rooms.roompax', 'rooms.price', 'rooms.status','bookings.bookingid','bookings.checkin','bookings.checkout','bookings.totalprice')
                ->get();

        $totalprice = 0; // Initialize the total price variable

        foreach ($data as $record) {
                // Add the totalprice for each record to the totalPrice variable
                $totalprice += $record->totalprice;
        }


        return view('homestay.homestayresit',compact('data','bookingid','totalprice'));
    }

    public function urustempahan()
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


        return view('homestay.urustempahan', compact('data'));
    }

    public function tunjukpelanggan(Request $request)
    {
        $homestayid = $request->input('homestayid');

        $data = Booking::join('rooms', 'bookings.roomid', '=', 'rooms.roomid')
        ->join('organizations', 'organizations.id', '=', 'rooms.homestayid')
        ->join('users', 'bookings.customerid', '=', 'users.id')
        ->where('organizations.id', $homestayid)
        ->select(
            'users.name',
            'users.telno',
            'bookings.checkin',
            'bookings.checkout',
            'bookings.bookingid',
            'bookings.status',
            'bookings.totalprice',
            'rooms.roomname'
    )
    ->get();

        return response()->json($data);
    }

    public function cancelpelanggan($bookingid)
    {
        $status = "Canceled";
        $booking = Booking::find($bookingid);
        $booking->status = $status;

        $result = $booking->save();

        if($result)
            {
                return back()->withInput()->with('success', 'Tempahan Berjaya Dibatalkan');
            }
            else
            {
                return back()->withInput()->with('error', 'Tempahan Gagal Dibatalkan');
    
            }
    }

    public function userhistory()
    {
        $userId = Auth::id();
        $data = Organization::join('rooms', 'organizations.id', '=', 'rooms.homestayid')
                ->join('bookings','rooms.roomid','=','bookings.roomid')
                ->where('bookings.customerid', $userId)
                ->select('organizations.id','organizations.nama','organizations.address', 'rooms.roomid', 'rooms.roomname', 'rooms.details', 'rooms.roompax', 'rooms.price', 'rooms.status','bookings.bookingid','bookings.checkin','bookings.checkout','bookings.totalprice','bookings.status')
                ->get();

        return view('homestay.userhistory',compact('data'));
    }

    public function tunjuksales()
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


        return view('homestay.tunjuksales', compact('data'));
    }

    public function homestaysales($id,$checkin,$checkout)
    {
        $checkinTimestamp = strtotime($checkin);
        $checkoutTimestamp = strtotime($checkout);
        
        $salesData = DB::table('bookings')
            ->join('rooms', 'bookings.roomid', '=', 'rooms.roomid')
            ->join('organizations', 'rooms.homestayid', '=', 'organizations.id')
            ->select(DB::raw('DATE(bookings.updated_at) as date'), DB::raw('SUM(bookings.totalprice) as total_sales'))
            ->where('bookings.status', 'Paid')
            ->where('organizations.id', $id)
            ->whereBetween(DB::raw('DATE(bookings.updated_at)'), [date('Y-m-d', $checkinTimestamp), date('Y-m-d', $checkoutTimestamp)])
            ->groupBy(DB::raw('DATE(bookings.updated_at)'))
            ->get();
        
        $dateLabels = [];
        $currentDate = $checkinTimestamp;
        while ($currentDate <= $checkoutTimestamp) {
            $dateLabels[] = date('Y-m-d', $currentDate);
            $currentDate += 86400;
        }
        

        $dailySales = [];
        

        foreach ($dateLabels as $dateLabel) {
            $found = false;
            foreach ($salesData as $entry) {
                if ($entry->date === $dateLabel) {
                    $dailySales[] = $entry->total_sales;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $dailySales[] = 0; // No sales for this date
            }
        }
        
        // Prepare the data for the chart
        $chartData = [
            'labels' => $dateLabels,
            'dataset' => $dailySales,
        ];
        
        return response()->json($chartData);
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
