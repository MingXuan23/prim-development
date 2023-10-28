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
use PDF;
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
        // get ratings for those rooms
        foreach($roomsWithImage as $key => $room){
            $ratings = DB::table('bookings')
            ->where([
                'roomid' => $room->roomid,
            ])
            ->where('review_star','!=', null)
            ->pluck('review_star');

            $overallRating = 0;
            if(count($ratings) > 0 ){
                $overallRating = number_format($ratings->sum() / $ratings->count(),2);
            }
            
            $room->overallRating = $overallRating;
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
            
        $customerReviews = DB::table('bookings')
        ->where([
            'status' => 'Completed',
            'roomid'=>$roomId,
        ])
        ->where('review_star' , '!=' , null)
        ->join('users' , 'users.id' , 'bookings.customerid')
        ->orderBy('bookings.updated_at', 'desc')
        ->select('bookings.bookingid', 'bookings.updated_at' ,'bookings.review_star' , 'bookings.review_comment' ,'users.name')
        ->paginate(6);
        
        $customerReviewsRating = 0;

        foreach($customerReviews as $review){
            $customerStar = (int)$review->review_star;
            $customerReviewsRating += $customerStar;
        }

        $customerReviewsRating = $customerReviewsRating != 0 ? number_format($customerReviewsRating / $customerReviews->count(),2) : 0;

        return view('homestay.room')->with(['room' => $room , 'roomImages' => $roomImages ,'customerReviews' => $customerReviews, 'customerReviewsRating' => $customerReviewsRating]);
    }
    public function getMoreReviews(Request $request){
        if($request->ajax()){
            $roomId = $request->roomId;
            $customerReviews = DB::table('bookings')
            ->where([
                'status' => 'Completed',
                'roomid'=>$roomId,
            ])
            ->where('review_star' , '!=' , null)
            ->join('users' , 'users.id' , 'bookings.customerid')
            ->orderBy('bookings.updated_at', 'desc')
            ->select('bookings.bookingid', 'bookings.updated_at' ,'bookings.review_star' , 'bookings.review_comment' ,'users.name')
            ->paginate(6);
            return view('homestay.review_data',compact('customerReviews'))->render();
        }
    }
    public function calculateTotalPrice(Request $request){
        $checkInDate = Carbon::createFromFormat('d/m/Y', $request->checkInDate);
        $checkOutDate = Carbon::createFromFormat('d/m/Y', $request->checkOutDate);


        $roomId = $request->roomId;

        $roomPrice = DB::table('rooms')
        ->where([
            'roomid' => $roomId
        ])
        ->pluck('price')
        ->first();

        $roomPrice = (float)$roomPrice;
        $numberOfNights = $checkInDate->diffInDays($checkOutDate);        
        
        $checkOutDate = $checkOutDate->format('Y-m-d');
        $checkInDate = $checkInDate->format('Y-m-d');
        
        $promotions = DB::table('promotions')
        ->where('datefrom', '<', $checkOutDate)
        ->where('dateto', '>=', $checkInDate)
        ->where('homestay_id',$roomId)
        ->orderBy('datefrom')
        ->get();
        // Initialize the total price
        $totalPrice = $roomPrice * $numberOfNights;
        $initialPrice = $totalPrice;
        $discountTotal = $increaseTotal =  0;
        $discountDate = $increaseDate = [];


        if(count($promotions) > 0){
            $currentDate = Carbon::createFromFormat('Y-m-d', $checkInDate);
            $stopDate = Carbon::createFromFormat('Y-m-d', $checkOutDate);
            $test = [];
            while($currentDate->lessThan($stopDate)){
                array_push($test,$currentDate->format('Y-m-d'));
                foreach ($promotions as $promotion) {
                    $startDate = Carbon::createFromFormat('Y-m-d', $promotion->datefrom);
                    $endDate = Carbon::createFromFormat('Y-m-d', $promotion->dateto);
                    if ($currentDate->greaterThanOrEqualTo($startDate) && $currentDate->lessThanOrEqualTo($endDate)) {
                        if ($promotion->promotion_type === 'discount') {
                            // Calculate the discount amount
                            $discountAmount = $roomPrice * ($promotion->discount / 100);
                            $discountTotal += $discountAmount;
                            array_push($discountDate,$currentDate->format('d'));
                            $totalPrice -= $discountAmount;
                        } elseif ($promotion->promotion_type === 'increase') {
                            // Calculate the price increase
                            $priceIncrease = $roomPrice * ($promotion->increase / 100);
                            $increaseTotal += $priceIncrease;
                            array_push($increaseDate,$currentDate->format('d'));
                            $totalPrice += $priceIncrease;
                        }
                    }
                } 
                $currentDate->addDay();
            }
            return response()->json(['totalPrice' => number_format($totalPrice,2),'numberOfNights'=>$numberOfNights, 'roomPrice'=> $roomPrice,'initialPrice'=>number_format($initialPrice,2) , 'discountTotal'=>number_format($discountTotal,2) , 'increaseTotal'=>number_format($increaseTotal,2), 'discountDate' => $discountDate, 'increaseDate'=>$increaseDate]);
        }else{
            return response()->json(['totalPrice' =>number_format($totalPrice,2),'numberOfNights'=>$numberOfNights, 'roomPrice'=> $roomPrice,'initialPrice'=>number_format($initialPrice,2)]);
        }
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
        $bookings = Booking::where('roomid', $roomId)
                           ->whereIn('status', ['Booked', 'Completed'])
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
    public function fetchDiscountIncreaseDates(Request $request){
        $homestayId = $request->homestayId;
        $promotions = DB::table('promotions')
        ->where([
            'deleted_at' => null,
            'homestay_id' => $homestayId
        ])
        ->orderBy('datefrom')
        ->get();
        $discountDates = $increaseDates = [];
        foreach($promotions as $promotion){
            $promotionStart = new DateTime($promotion->datefrom);
            $promotionEnd = new DateTime($promotion->dateto);
            $interval = new DateInterval('P1D'); // 1 day interval
            $period = new DatePeriod($promotionStart, $interval, $promotionEnd);
            if($promotion->promotion_type == "discount"){
                foreach($period as $date){
                    $discountDates[] =  [
                      'date' =>  $date->format('d/m/Y'),
                      'percentage' => $promotion->discount,
                    ];
                }
                // add the last date as well
                $discountDates[] = [
                    'date' => $promotionEnd->format('d/m/Y'),
                    'percentage' => $promotion->discount,
                ];
            }else if($promotion->promotion_type == "increase"){
                foreach($period as $date){
                    $increaseDates[] =  [
                        'date' =>  $date->format('d/m/Y'),
                        'percentage' => $promotion->increase,
                    ];
                }
                $increaseDates[] = [
                    'date' =>  $promotionEnd->format('d/m/Y'),
                    'percentage' => $promotion->increase,
                ];
            }
        }
        return response()->json(['increaseDates' => $increaseDates , 'discountDates' => $discountDates]);
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
            ]) 
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
    
    $status = $request->isAvailable == "" ? "Not Available" : "Available";

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
public function editPromotionPage($promotionId){
    $promotion = DB::table('promotions')
    ->join('rooms','rooms.roomid','promotions.homestay_id')
    ->where([
        'promotions.promotionid' => $promotionId,
        'promotions.deleted_at' => null,
        'rooms.deleted_at' => null,
    ])
    ->first();
    return view('homestay.editPromotion',compact('promotion'));
}
// fetch disabled dates for promotion except the current editing one
public function fetchUnavailableEditPromotionDates(Request $request){
    $promotionId = $request->promotionId;    
    $homestayId = $request->homestayId;
    $promotions = Promotion::where('deleted_at',null)
    ->where('homestay_id',$homestayId)
    ->where('promotionid','!=', $promotionId)
    ->get(); 

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
    $disabledDates = array_unique($disabledDates);
    return response()->json(['disabledDates' => $disabledDates]);
}
public function updatePromotion(Request $request){
    $userId = Auth::id();
    $request->validate([
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
    DB::table('promotions')        
    ->where([
        'deleted_at' => null,
        'promotionid' => $request->promotion_id,
    ])
    ->update([
        'promotionname' => $request->promotion_name,
        'datefrom' => $startDate,
        'dateto' => $endDate,
        'promotion_type'=> $request->promotion_type,
        'increase' => $increase,
        'discount' => $discount,   
        'updated_at' => Carbon::now(),
    ]);
          
    return redirect()->route('homestay.promotionPage')->with('success','Promosi telah berjaya disunting');
}
public function deletePromotion(Request $request){
    DB::table('promotions')
    ->where([
        'promotionid' => $request->promotionId,
    ])
    ->update([
        'deleted_at' => Carbon::now(),
    ]);
    return response()->json(['success' => 'Promotion Berjaya Dibuang']);
}
// public function editpromo(Request $request,$promotionid)
// {
//     $request->validate([
//         'promotionname' => 'required',
//         'datefrom' => 'required',
//         'dateto' => 'required',
//         'discount' => 'required|numeric|min:1|max:100'
//     ]);

//     $promotionname = $request->input('promotionname');
//     $datefrom = $request->input('datefrom');
//     $dateto = $request->input('dateto');
//     $discount = $request->input('discount');

//     $userId = Auth::id();
//     $promotion = Promotion::where('promotionid',$promotionid)
//         ->first();

//         if ($promotion) {
//             $promotion->promotionname = $request->promotionname;
//             $promotion->datefrom = $request->datefrom;
//             $promotion->dateto = $request->dateto;
//             $promotion->discount = $request->discount;

//             $result = $promotion->save();

//             if($result)
//             {
//                 return back()->with('success', 'Promosi Berjaya Disunting');
//             }
//             else
//             {
//                 return back()->withInput()->with('error', 'Promosi Gagal Disunting');
    
//             }
//         } else {
//             return back()->with('fail', 'Promotions not found!');
//         }
//     }

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
            $floatTotalPrice = (float) str_replace(',', '', $request->amount);
            $bookingId  = DB::table('bookings')
            ->insertGetId([
                'checkin' => $checkInDate,
                'checkout' => $checkOutDate,
                'status' => "Checkout",
                'totalprice' => $floatTotalPrice,
                'customerid' => $userId,
                'roomId' => $roomId,
                'created_at' => Carbon::now(),
            ]);

            
            $homestay = DB::table('rooms')
            ->where([
                'roomid' => $request->roomId,
                'deleted_at' => null,
            ])
            ->first();

            $homestayImage = DB::table('homestay_images')
            ->where([
                'room_id' => $request->roomId,
                'deleted_at' => null,
            ]) 
            ->pluck('image_path')
            ->first();

            $checkoutDetails = [
                'checkInDate' => $request->checkIn,
                'checkOutDate' => $request->checkOut,
                'totalPrice' => $request->amount,
                'discountAmount' => $request->discountAmount,
                'discountDates' => $request->discountDates,
                'increaseAmount' => $request->increaseAmount,
                'increaseDates' => $request->increaseDates,
                'nightCount' => $request->nightCount,
                'homestay'=>$homestay,  
                'homestayImage'=> $homestayImage, 
                'bookingId' => $bookingId,             
            ];
            // dd($checkoutDetails);
            // redirect to checkout page
            return view('homestay.bookingCheckout')->with(['checkoutDetails' => $checkoutDetails]);
        }else{
            return back()->with('error','Homestay/bilik telah ditempah oleh pengguna lain pada masa itu.');
        }
    }
    // public function insertbooking(Request $request, $roomid, $price)
    // {
    //     $request->validate([
    //         'checkin' => 'required',
    //         'checkout' => 'required',
    //     ]);

    //     $userId = Auth::id();
    //     $fkroom = $roomid;
    //     $status = "Booked";

    //     $homestay = Room::where('roomid', $fkroom)->first();

    //     if (!$homestay) {
    //         return back()->with('fail', 'Homestay not found!');
    //     }

    //     $homestayid = $homestay->homestayid;

    //     $checkinDate = Carbon::createFromFormat('Y-m-d', $request->checkin);
    //     $checkoutDate = Carbon::createFromFormat('Y-m-d', $request->checkout);
    //     $totalDays = $checkoutDate->diffInDays($checkinDate) + 1;
    
    // // Retrieve the applicable promotion based on check-in and check-out dates
    //     $promotion = Promotion::where('datefrom', '<=', $request->checkin)
    //     ->where('dateto', '>=', $request->checkout)
    //     ->where('homestayid',$homestayid)
    //     ->first();
    
    // // Calculate the total price with discount if a promotion is applicable
    //     if ($promotion !== null) {
    //         $discountedPrice = $price - ($price * $promotion->discount / 100);
    //         $totalPrice = $discountedPrice * $totalDays;
    //     } else {
    //         $totalPrice = $price * $totalDays;
    //     }

    //     $availability = Booking::where('roomid', $fkroom)
    //     ->where(function ($query) use ($request) {
    //     $query->where('checkin', '<', $request->checkout)
    //         ->where('checkout', '>', $request->checkin);
    //      })
    // ->get();

    // if ($availability->isEmpty()) {

    //         $booking = new Booking();
    //         $booking->checkin = $request->checkin;
    //         $booking->checkout = $request->checkout;
    //         $booking->status = $status;
    //         $booking->totalprice = $totalPrice;
    //         $booking->customerid = $userId;
    //         $booking->roomid = $fkroom;
    
    //         $result = $booking->save();
    
    //         if($result)
    //             {
    //                 return back()->withInput()->with('success', 'Bilik Berjaya Ditempah');
    //             }
    //             else
    //             {
    //                 return back()->withInput()->with('error', 'Tempahan Gagal Dibuat');
        
    //             }
    //     } else {
    //         return back()->withInput()->with('error', 'Bilik Pada Tarikh Tersebut Telah Penuh');
    //     }

        

    // }

    public function tempahananda()
    {
        $userId = Auth::id();
        // data for checkout tab
        $checkoutBookings = DB::table('bookings')
        ->where([
            'bookings.customerid' => $userId,
            'bookings.status' => 'Booked',
        ])
        ->join('rooms','rooms.roomid','bookings.roomid')
        ->join('organizations','organizations.id','rooms.homestayid')
        ->orderBy('bookings.updated_at','desc')
        ->get();
        $checkoutImages = [];
        foreach($checkoutBookings as $checkoutBooking){
            $image = DB::table('homestay_images')
            ->where([
                'room_id' => $checkoutBooking->roomid,
                'deleted_at' => null,
            ])
            ->pluck('image_path')
            ->first();
            array_push($checkoutImages,$image);
        }
        // data for completed tab
        $completedBookings = DB::table('bookings')
        ->where([
            'bookings.customerid' => $userId,
            'bookings.status' => 'Completed',
        ])
        ->join('rooms','rooms.roomid','bookings.roomid')
        ->join('organizations','organizations.id','rooms.homestayid')
        ->orderBy('bookings.updated_at','desc')
        ->get();
        $completedImages = [];
        foreach($completedBookings as $completedBooking){
            $image = DB::table('homestay_images')
            ->where([
                'room_id' => $completedBooking->roomid,
                'deleted_at' => null,
            ])
            ->pluck('image_path')
            ->first();
            array_push($completedImages,$image);
        }   
        // data for cancelled tab
        $cancelledBookings = DB::table('bookings')
        ->where([
            'bookings.customerid' => $userId,
            'bookings.status' => 'Cancelled',
        ])
        ->join('rooms','rooms.roomid','bookings.roomid')
        ->join('organizations','organizations.id','rooms.homestayid')
        ->orderBy('bookings.updated_at','desc')
        ->get();
        $cancelledImages = [];
        foreach($cancelledBookings as $cancelledBooking){
            $image = DB::table('homestay_images')
            ->where([
                'room_id' => $cancelledBooking->roomid,
                'deleted_at' => null,
            ])
            ->pluck('image_path')
            ->first();
            array_push($cancelledImages,$image);
        }   
        return view('homestay.tempahananda')
        ->with(['checkoutBookings'=> $checkoutBookings, 'checkoutImages'=> $checkoutImages,'completedBookings'=> $completedBookings , 'completedImages'=> $completedImages,'cancelledBookings'=> $cancelledBookings,'cancelledImages'=> $cancelledImages,]);
    }
    public function addReview(Request $request){
       $bookingId = $request->booking_id;
       DB::table('bookings')
       ->where([
        'bookingid' => $bookingId
       ])
       ->update([
        'updated_at' => Carbon::now(),
        'review_star'=> $request->rating,
        'review_comment'=> $request->review_comment,
       ]);

       return redirect()->route('homestay.tempahananda')->with(['success' => 'Berjaya Memberikan Nilaian, Terima Kasih']);
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

    public function bookingDetails($bookingId){
        $organization = DB::table('bookings')
        ->join('rooms', 'rooms.roomid' , 'bookings.roomid')
        ->join('organizations','organizations.id' ,'rooms.homestayid')
        ->where([
            'bookings.bookingid' => $bookingId,
        ])
        ->select('organizations.nama','organizations.email' ,'organizations.telno','organizations.organization_picture','organizations.address','organizations.postcode', 'organizations.city','organizations.state')
        ->first();

        $transaction = DB::table('bookings')
        ->join('transactions' ,'transactions.id' ,'bookings.transactionid')
        ->where([
            'bookings.bookingid' => $bookingId,
        ])
        ->first();

        $user = DB::table('bookings')
        ->join('users' ,'users.id' ,'bookings.customerid')
        ->where('bookings.bookingid', $bookingId)
        ->first();    
        
        $homestay = DB::table('bookings')
        ->join('rooms' ,'rooms.roomid' ,'bookings.roomid')
        ->where([
            'bookings.bookingid' => $bookingId,
        ])
        ->first();

        $homestayImage = DB::table('homestay_images')
        ->where([
            'room_id' => $homestay->roomid,
        ])
        ->pluck('image_path')
        ->first();

        $checkInDate = Carbon::createFromFormat('Y-m-d' ,$transaction->checkin);
        $checkOutDate =  Carbon::CreateFromFormat('Y-m-d' ,$transaction->checkout);

        $numberOfNights = $checkInDate->diffInDays($checkOutDate);
        return view('homestay.bookingDetails')->with(['organization' => $organization , 'transaction' => $transaction , 'user' => $user ,'homestay'=>$homestay ,'homestayImage' => $homestayImage ,'numberOfNights' => $numberOfNights]);
    }
    public function generateBookingDetailsPdf($bookingId){
        $organization = DB::table('bookings')
        ->join('rooms', 'rooms.roomid' , 'bookings.roomid')
        ->join('organizations','organizations.id' ,'rooms.homestayid')
        ->where([
            'bookings.bookingid' => $bookingId,
        ])
        ->select('organizations.nama','organizations.email' ,'organizations.telno','organizations.organization_picture','organizations.address','organizations.postcode', 'organizations.city','organizations.state')
        ->first();

        $transaction = DB::table('bookings')
        ->join('transactions' ,'transactions.id' ,'bookings.transactionid')
        ->where([
            'bookings.bookingid' => $bookingId,
        ])
        ->first();

        $user = DB::table('bookings')
        ->join('users' ,'users.id' ,'bookings.customerid')
        ->where('bookings.bookingid', $bookingId)
        ->first();    
        
        $homestay = DB::table('bookings')
        ->join('rooms' ,'rooms.roomid' ,'bookings.roomid')
        ->where([
            'bookings.bookingid' => $bookingId,
        ])
        ->first();

        $homestayImage = DB::table('homestay_images')
        ->where([
            'room_id' => $homestay->roomid,
        ])
        ->pluck('image_path');

        $checkInDate = Carbon::createFromFormat('Y-m-d' ,$transaction->checkin);
        $checkOutDate =  Carbon::CreateFromFormat('Y-m-d' ,$transaction->checkout);

        $numberOfNights = $checkInDate->diffInDays($checkOutDate);
        $data = [
            'organization' => $organization,
            'transaction' => $transaction,
            'user' => $user,
            'homestay' => $homestay,
            'homestayImage' => $homestayImage,
            'numberOfNights' => $numberOfNights,
        ];
        //return content of view as a string with render()
        $html = view('homestay.bookingDetails' ,$data)->render();
        //generate pdf
        $pdf = PDF::loadHTML($html);
        return $pdf->download('bookingDetails.pdf');
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
    public function getBookingData(Request $request){
        $orgId = $request->homestayid;
        $bookings = DB::table('rooms as r')
        ->where([
            'r.homestayid' => $orgId,
            'r.deleted_at' => null
        ])
        ->join('bookings as b' , 'b.roomid' , 'r.roomid')
        ->where([
            'b.status' => 'Booked'
        ])
        ->join('users as u', 'u.id' ,'b.customerid')
        ->orderBy('b.bookingid','desc')
        ->get();

        return response()->json(['bookings' => $bookings]);
    }
    public function checkoutHomestay(Request $request){
        $bookingId = $request->bookingId;
        DB::table('bookings')
        ->where([
            'bookingid' => $bookingId,
        ])
        ->update([
            'updated_at' => Carbon::now(),
            'status' => 'Completed',
        ]);

        return response()->json(['success' =>'Checked Out Successfully']);
    }
    public function cancelBooking(Request $request){
        $bookingId = $request->bookingId;
        DB::table('bookings')
        ->where([
            'bookingid' => $bookingId,
        ])
        ->update([
            'updated_at' => Carbon::now(),
            'status' => 'Cancelled',
        ]);

        return response()->json(['success' =>'Cancel Booking Successfully']);
    }
    public function viewBookingHistory($orgId){
        $organization = DB::table('organizations')
        ->where([
            'id' => $orgId,
        ])
        ->first();
        return view('homestay.customersBookingHistory',compact('organization'));
    }
    public function getBookingHistoryData(Request $request){
        $orgId = $request->organizationId;
        $bookings = DB::table('rooms as r')
        ->where([
            'r.homestayid' => $orgId,
            'r.deleted_at' => null
        ])
        ->join('bookings as b' , 'b.roomid' , 'r.roomid')
        ->whereIn('b.status',['Cancelled','Completed'])
        ->join('users as u', 'u.id' ,'b.customerid')
        ->orderBy('b.updated_at','desc')
        ->get();
        return response()->json(['bookings' => $bookings]);
    }
    public function viewCustomersReview($orgId){
        $organization = DB::table('organizations')
        ->where([
            'id' => $orgId,
        ])
        ->first();
        $homestays = DB::table('rooms')
        ->where([
            'deleted_at' => null,
            'homestayid' => $orgId,
        ])
        ->orderBy('roomid')
        ->select('roomid','roomname')
        ->get();
        return view('homestay.customersReview',compact('organization','homestays'));
    }
    public function getCustomersReview(Request $request){
        // fetch reviews for all homestays
        $reviews = [];
        if($request->homestayId == "all"){
           $reviews =  DB::table('bookings')
            ->where('review_star','!=',null)
            ->join('rooms','rooms.roomid','bookings.roomid')
            ->where([
                'rooms.homestayid' => $request->organizationId,
            ])
            ->join('users' ,'users.id' ,'bookings.customerid')
            ->orderBy('bookings.updated_at' ,'desc')
            ->select('bookings.bookingid','bookings.updated_at' ,'bookings.review_star' ,'review_comment','users.id' ,'users.name' ,'rooms.roomname','rooms.roomid')
            ->get();
        }else{
            // fetch reviews for a homestay
            $reviews =  DB::table('bookings')
            ->where('review_star','!=',null)
            ->join('rooms','rooms.roomid','bookings.roomid')
            ->where([
                'bookings.roomid' => $request->homestayId,
                'rooms.homestayid' => $request->organizationId,
            ])
            ->join('users' ,'users.id' ,'bookings.customerid')
            ->orderBy('bookings.updated_at' ,'desc')
            ->select('bookings.bookingid','bookings.updated_at' ,'bookings.review_star' ,'review_comment','users.id' ,'users.name' ,'rooms.roomname','rooms.roomid')
            ->get();

        }
        return response()->json(['reviews' => $reviews]);
    }
    // public function tunjukpelanggan(Request $request)
    // {
    //     $homestayid = $request->input('homestayid');

    //     $data = Booking::join('rooms', 'bookings.roomid', '=', 'rooms.roomid')
    //     ->join('organizations', 'organizations.id', '=', 'rooms.homestayid')
    //     ->join('users', 'bookings.customerid', '=', 'users.id')
    //     ->where('organizations.id', $homestayid)
    //     ->select(
    //         'users.name',
    //         'users.telno',
    //         'bookings.checkin',
    //         'bookings.checkout',
    //         'bookings.bookingid',
    //         'bookings.status',
    //         'bookings.totalprice',
    //         'rooms.roomname'
    // )
    // ->get();

    //     return response()->json($data);
    // }

    // public function cancelpelanggan($bookingid)
    // {
    //     $status = "Canceled";
    //     $booking = Booking::find($bookingid);
    //     $booking->status = $status;

    //     $result = $booking->save();

    //     if($result)
    //         {
    //             return back()->withInput()->with('success', 'Tempahan Berjaya Dibatalkan');
    //         }
    //         else
    //         {
    //             return back()->withInput()->with('error', 'Tempahan Gagal Dibatalkan');
    
    //         }
    // }

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
