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
    public function homePage(){
        $roomsId = DB::table('rooms')
        ->where([
            'deleted_at' => null,
            'status' => 'Available',
        ]) 
        ->orderBy('roomid')
        ->pluck('roomid');
        
        // array to keep the room's data with its first room image
        $roomsWithImage = [];
        foreach($roomsId as $roomId){
            $firstRow = DB::table('rooms')
            ->join('homestay_images','homestay_images.room_id','rooms.roomid')
            ->where([
                'rooms.deleted_at' => null,
                'rooms.roomid' => $roomId
            ])
            ->orderBy('roomid')
            ->first();
            array_push($roomsWithImage, $firstRow);
        }
        return view('homestay.home')->with(['rooms' => $roomsWithImage]);
    }
    public function showRoom($roomId){
        $room = DB::table('rooms')
        ->where([
            'roomid' => $roomId,
        ])
        ->first();
        $roomImages= DB::table('rooms')
        ->join('homestay_images','homestay_images.room_id','rooms.roomid')
        ->where([
            'homestay_images.deleted_at' => null,
            'rooms.roomid' => $roomId
        ])
        ->pluck('homestay_images.image_path');

        return view('homestay.room')->with(['room' => $room , 'roomImages' => $roomImages]);
    }
    public function searchRoom(Request $request){
        $rooms = Room::search($request->searchRoom)->paginate(20);
        $roomImage = [];
        foreach($rooms as $room){
            $roomId = $room->roomid;
            $firstRow = DB::table('rooms')
            ->join('homestay_images','homestay_images.room_id','rooms.roomid')
            ->where([
                'rooms.deleted_at' => null,
                'rooms.roomid' => $roomId
            ])
            ->orderBy('roomid')
            ->pluck('image_path')
            ->first();

            array_push($roomImage, $firstRow);
        }
        return view('homestay.searchResults')->with(['rooms' => $rooms,'roomImage' => $roomImage]);
    }
    public function fetchUnavailableDates(Request $request)
    {   
        $roomId = $request->roomId;
        // Fetch all bookings for the specified room with status 'Booked' or 'Paid'
        $bookings = Booking::where('roomid', $roomId)
                           ->whereIn('status', ['Booked', 'Paid'])
                           ->get();
    
        $disabledDates = [];
    
        foreach ($bookings as $booking) {
            // Convert check-in and check-out dates to DateTime objects
            $checkIn = new DateTime($booking->checkin);
            $checkOut = new DateTime($booking->checkout);
    
            // Generate a range of dates between check-in and check-out (inclusive)
            $interval = new DateInterval('P1D'); // 1 day interval
            $period = new DatePeriod($checkIn, $interval, $checkOut);
    
            // Add each date in the range to the disabledDates array
            foreach ($period as $date) {
                $disabledDates[] = $date->format('d/m/Y');
            }
        }
    
        // Remove duplicates from the array (if any)
        $disabledDates = array_unique($disabledDates);
        return response()->json(['disabledDates' => $disabledDates]);
    }
    public function promotionPage(){
        $orgtype = 'Homestay / Hotel';
        $userId = Auth::id();
        $organization = DB::table('organizations as o')
            ->leftJoin('organization_user as ou', 'o.id', 'ou.organization_id')
            ->leftJoin('type_organizations as to', 'o.type_org', 'to.id')
            ->select("o.*")
            ->distinct()
            ->where('ou.user_id', $userId)
            ->where('to.nama', $orgtype)
            ->where('o.deleted_at', null)
            ->get();
        return view('homestay.promotion',compact('organization'));
    }    
    public function getPromotionData(Request $request){
        $promotions = DB::table('promotions')
        ->join('rooms','rooms.roomid','promotions.homestay_id')
        ->where([
            'rooms.deleted_at' => null,
            'promotions.deleted_at' => null,
            'rooms.homestayid' => $request->homestayid
        ])
        ->orderBy('rooms.roomname')
        ->get();
        return response()->json(['promotions' => $promotions]);
    }
    // public function promotionPage()
    // {
    //     $userId = Auth::id();
    //     $data = Promotion::join('organizations', 'promotions.homestayid', '=', 'organizations.id')
    //     ->join('organization_user','organizations.id','organization_user.organization_id')
    // ->where('organization_user.user_id',$userId)
    // ->select('organizations.nama', 'promotions.promotionid','promotions.promotionname','promotions.datefrom', 'promotions.dateto','promotions.discount')
    // ->get();

    //     return view('homestay.listpromotion', compact('data'));
    // }
    public function setpromotion($orgId){
        // get all homestays managed by this user
        $homestays =  DB::table('rooms')
        ->join('organizations','organizations.id','rooms.homestayid')
        ->where([
            'rooms.deleted_at' => null,
            'organizations.deleted_at' => null,
        ])
        ->join('organization_user','organization_user.organization_id','organizations.id')
        ->where([
            'organization_user.user_id' => Auth::user()->id,
            'organization_user.organization_id' => $orgId,
        ])
        ->orderBy('rooms.roomid')
        ->get();
        return view('homestay.setpromotion')->with(["homestays"=>$homestays, "orgId"=>$orgId]);
    }
    // public function setpromotion()
    // {
    //     $orgtype = 'Homestay / Hotel';
    //     $userId = Auth::id();
    //     $data = DB::table('organizations as o')
    //         ->leftJoin('organization_user as ou', 'o.id', 'ou.organization_id')
    //         ->leftJoin('type_organizations as to', 'o.type_org', 'to.id')
    //         ->select("o.*")
    //         ->distinct()
    //         ->where('ou.user_id', $userId)
    //         ->where('to.nama', $orgtype)
    //         ->where('o.deleted_at', null)
    //         ->get();
    //     return view('homestay.setpromotion', compact('data'));
    // }

    public function insertpromotion(Request $request)
    {
        $userId = Auth::id();
        $request->validate([
            'homestay_id' => 'required',
            'promotion_name' => 'required',
            'promotion_start' => 'required',
            'promotion_end' => 'required',
            'promotion_type'=>'required',
            'promotion_percentage' => 'required|min:1|max:100',
        ]);
        $startDate = Carbon::createFromFormat('d/m/Y', $request->promotion_start);
        $endDate = Carbon::createFromFormat('d/m/Y', $request->promotion_end);
        $discount = $increase = 0;
        if($request->promotion_type == "increase"){
            $discount = 0;
            $increase = $request->promotion_percentage;
        }else{
            $increase = 0;
            $discount = $request->promotion_percentage;
        }
        if($request->homestay_id != "all"){
            DB::table('promotions')
            ->insert([
                'promotionname' => $request->promotion_name,
                'datefrom' => $startDate,
                'dateto' => $endDate,
                'promotion_type'=> $request->promotion_type,
                'increase' => $increase,
                'discount' => $discount,   
                'created_at' => Carbon::now(),
                'homestay_id' => $request->homestay_id,
            ]);            
        }else{
            // insert for all homestay
            $orgId = $request->org_id;
            $homestayIds = DB::table('rooms')
            ->where([
                'deleted_at'=> null,
                'homestayid' => $orgId,
            ])
            ->pluck('roomid');

            foreach($homestayIds as $homestayId){
                DB::table('promotions')
                ->insert([
                    'promotionname' => $request->promotion_name,
                    'datefrom' => $startDate,
                    'dateto' => $endDate,
                    'promotion_type'=> $request->promotion_type,
                    'increase' => $increase,
                    'discount' => $discount,   
                    'created_at' => Carbon::now(),
                    'homestay_id' => $homestayId,
                ]);  
            }
        }
        return redirect()->route('homestay.promotionPage')->with('success','Promosi telah berjaya ditambah');
    }
    // get unavailable promotion dates for one homestay
    public function fetchUnavailablePromotionDates(Request $request){
        $homestayId = $request->roomId;    
        if($homestayId == "all"){
            $promotions = Promotion::where([
                'deleted_at'=> null,
            ])
            ->get();
        }else{
            $promotions = Promotion::where([
                'homestay_id' => $homestayId,
                'deleted_at' => null,  
            ]) // Add your additional condition here
            ->get(); 
        }

        $disabledDates = [];

        foreach ($promotions as $promotion) {
            $begin = new DateTime($promotion->datefrom);
            $end = new DateTime($promotion->dateto);
            $interval = new DateInterval('P1D');
            $daterange = new DatePeriod($begin, $interval, $end);

            foreach ($daterange as $date) {
                $disabledDates[] = $date->format('d/m/Y');
            }        
            // add the last date as well
            $disabledDates[] = $end->format('d/m/Y');
        }
        return response()->json(['disabledDates' => $disabledDates]);
    }
//     public function disabledatepromo($homestayid)
// {
    
//     $promotions = Promotion::where('homestayid', $homestayid) // Add your additional condition here
//                    ->get();

//     $disabledDates = [];

//     foreach ($promotions as $promotion) {
//         $begin = new DateTime($promotion->datefrom);
//         $end = new DateTime($promotion->dateto);
//         $interval = new DateInterval('P1D');
//         $daterange = new DatePeriod($begin, $interval, $end);

//         foreach ($daterange as $date) {
//             $disabledDates[] = $date->format('Y-m-d');
//         }
//     }

//     return response()->json(['disabledDates' => $disabledDates]);
// }

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
        'state' => 'required',
        'district' => 'required',
        'area' => 'required',
        'postcode' => 'required',
        'checkInAfter' => 'required',
        'checkOutBefore' => 'required',
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
    $room->state = $request->state;
    $room->district = $request->district;
    $room->area = $request->area;
    $room->postcode = $request->postcode;
    $room->check_in_after = Carbon::parse($request->checkInAfter)->format('H:i');
    $room->check_out_before = Carbon::parse($request->checkOutBefore)->format('H:i');
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

        // get states
        $states = Jajahan::negeri();

        return view('homestay.tambahbilik',compact('data','states'));
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
       // get states
       $states = Jajahan::negeri();
        return view('homestay.editBilik')->with(['room'=>$room , 'images'=>$roomImages,'states'=>$states]);
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
            'state' => $request->state,
            'district' => $request->district,
            'area' => $request->area,
            'postcode' => $request->postcode,
            'check_in_after' => $request->checkInAfter,
            'check_out_before' => $request->checkOutBefore,
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
    public function bookRoom(Request $request){
        $roomId = $request->roomId;
        $userId = Auth::user()->id;
        $checkInDate = Carbon::createFromFormat('d/m/Y', $request->checkIn);
        $checkOutDate = Carbon::createFromFormat('d/m/Y', $request->checkOut);

        // check whether room is booked by others 
        $availability = Booking::where('roomid', $roomId)
        ->where(function ($query) use ($request) {
        $query->where('checkin', '<', $request->checkOut)
            ->where('checkout', '>', $request->checkIn);
         })
        ->get();

        if($availability->isEmpty()){
            DB::table('bookings')
            ->insert([
                'checkin' => $checkInDate,
                'checkout' => $checkOutDate,
                'status' => "Booked",
                'totalprice' => $request->amount,
                'customerid' => $userId,
                'roomId' => $roomId,
                'created_at' => Carbon::now(),
            ]);
            return back()->with('success','Tempahan telah berjaya dibuat');
        }else{
            return back()->with('error','Homestay/bilik telah ditempah oleh pengguna lain pada masa itu.');
        }
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
