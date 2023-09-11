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

public function addroom(Request $request)
{
    $userId = Auth::id();
    $request->validate([
        'homestayid' => 'required',
        'roomname' => 'required',
        'roompax' => 'required',
        'details' => 'required',
        'price' => 'required'
    ]);

    $status = 'Available';

    $room = new Room();
    $room->homestayid = $request->homestayid;
    $room->roomname = $request->roomname;
    $room->roompax = $request->roompax;
    $room->details = $request->details;
    $room->price = $request->price;
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
                ->where('organizations.id', $homestayid) // Filter by the selected homestay
                ->select('organizations.id', 'rooms.roomid', 'rooms.roomname', 'rooms.details', 'rooms.roompax', 'rooms.price', 'rooms.status')
                ->get();
              
                return response()->json($rooms);
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

    }

    public function tempahananda()
    {
        $userId = Auth::id();
        $data = Organization::join('rooms', 'organizations.id', '=', 'rooms.homestayid')
                ->join('bookings','rooms.roomid','=','bookings.roomid')
                ->where('bookings.customerid', $userId) // Filter by the selected homestay
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

    // public function test(Request $request)
    // {
    //     $getstudentfees = ($request->student_fees_id) ? $request->student_fees_id : "";
    //     $getparentfees  = ($request->parent_fees_id) ? $request->parent_fees_id : "";
    //     $user=null;
    //     $prefixCode="";
    //     if ($request->desc == 'Donation') {
    //         $user = User::find(Auth::id());
    //         $organization = $this->organization->getOrganizationByDonationId($request->d_id);

    //         if(isset($request->email))
    //         {
    //             $fpx_buyerEmail = $request->email;
    //             $telno = $request->telno;
    //             $fpx_buyerName = $request->name;
    //         }
    //         else
    //         {
    //             $fpx_buyerEmail =  NULL;
    //             $telno = NULL;
    //             $fpx_buyerName = "Penderma Tanpa Nama";
    //         }
            
    //         $fpx_sellerExOrderNo = $request->desc . "_" . $request->d_code . "_" . date('YmdHis') . "_" . $organization->id;

    //         $fpx_sellerOrderNo  = "PRIM" . str_pad($request->d_id, 3, "0", STR_PAD_LEFT)  . "_" . date('YmdHis') . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

    //         $fpx_sellerExId     = config('app.env') == 'production' ? "EX00011125" : "EX00012323";

    //         $fpx_sellerId       = config('app.env') == 'production' ? $organization->seller_id : "SE00013841";

    //         // $fpx_buyerIban      = $request->name . "/" . $telno . "/" . $request->email;
    //     } 
    //     else if ($request->desc == 'School_Fees')
    //     {
    //         $user = User::find(isset($request->user_id) ? $request->user_id : Auth::id());
    //         $organization = Organization::find($request->o_id);
            
    //         $fpx_buyerEmail      = $user->email;
    //         $telno               = $user->telno;
    //         $fpx_buyerName       = User::where('id', '=', Auth::id())->pluck('name')->first();
    //         $fpx_sellerExOrderNo = $request->desc . "_" . date('YmdHis');
    //         $fpx_sellerOrderNo  = "YSPRIM" . date('YmdHis') . rand(10000, 99999);

    //         $fpx_sellerExId     = config('app.env') == 'production' ? "EX00011125" : "EX00012323";
    //         $fpx_sellerId       = config('app.env') == 'production' ? $organization->seller_id : "SE00013841";
    //     }
    //     else if($request->desc == 'Food_Order')
    //     {
    //         $user = User::find($request->user_id);
    //         $organization = Organization::find($request->o_id);
    //         $fpx_buyerEmail      = $user->email;
    //         $telno               = $user->telno;
    //         $fpx_buyerName       = User::where('id', '=', Auth::id())->pluck('name')->first();
    //         $fpx_sellerExOrderNo = $request->desc . "_" . date('YmdHis');
    //         $fpx_sellerOrderNo  = "FOPRIM" . date('YmdHis') . rand(10000, 99999);

    //         $fpx_sellerExId     = config('app.env') == 'production' ? "EX00011125" : "EX00012323";
    //         $fpx_sellerId       = config('app.env') == 'production' ? $organization->seller_id : "SE00013841";
    //     }
    //     else if($request->desc == 'Merchant')
    //     {
    //         $gng_order_id = $request->order_id;

    //         DB::table('pgng_orders')->where('id', $gng_order_id)->update([
    //             'updated_at' => Carbon::now(),
    //             'status' => 'Pending'
    //         ]);

    //         $gng_order = DB::table('pgng_orders')
    //         ->where('id', $gng_order_id)
    //         ->select('user_id', 'organization_id')
    //         ->first();

    //         $ficts_seller_id = "SE00054277";

    //         $user = User::find($gng_order->user_id);
    //         $organization = Organization::find($gng_order->organization_id);
    //         $fpx_buyerEmail      = $user->email;
    //         $telno               = $user->telno;
    //         $fpx_buyerName       = User::where('id', '=', Auth::id())->pluck('name')->first();
    //         $fpx_sellerExOrderNo = $request->desc . "_" . date('YmdHis');
    //         $fpx_sellerOrderNo  = "MUPRIM" . date('YmdHis') . rand(10000, 99999);

    //         $fpx_sellerExId     = config('app.env') == 'production' ? "EX00011125" : "EX00012323";
    //         $fpx_sellerId       = config('app.env') == 'production' ? $organization->seller_id: "SE00013841";
    //     }
    //     else if($request->desc == 'Koperasi')
    //     {
    //         $cart = PgngOrder::find($request->cartId);

    //         $request->amount = $cart->total_price;
    //         $ficts_seller_id = "SE00054277";

    //         $user = User::find($cart->user_id);
    //         $organization = Organization::find($cart->organization_id);
    //         $fpx_buyerEmail      = $user->email;
    //         $telno               = $user->telno;
    //         $fpx_buyerName       = User::where('id', '=', Auth::id())->pluck('name')->first();
    //         $fpx_sellerExOrderNo = $request->desc . "_" . date('YmdHis');
    //         $fpx_sellerOrderNo  = "KOPPRIM" . date('YmdHis') . rand(10000, 99999);

    //         $fpx_sellerExId     = config('app.env') == 'production' ? "EX00011125" : "EX00012323";
    //         $fpx_sellerId       = config('app.env') == 'production' ? $organization->seller_id : "SE00013841";
    //     }
    //     else if($request->desc == 'Homestay')
    //     {
    //         $homestay = Booking::find($request->bookingid);
    //         $user = User::find($homestay->customerid);
    //         $room = Room::find($homestay->roomid);
            
    //         $request->amount = $homestay->totalprice;
    //         $bookingId = $request->bookingid;

    //         DB::table('bookings')->where('bookingid', $bookingId)->update([
    //             'updated_at' => Carbon::now(),
    //             'status' => 'Paid'
    //         ]);

    //         $organization = Organization::find($room->homestayid);
    //         $fpx_buyerEmail      = $user->email;
    //         $telno               = $user->telno;
    //         $fpx_buyerName       = User::where('id', '=', Auth::id())->pluck('name')->first();
    //         $fpx_sellerExOrderNo = $request->desc . "_" . date('YmdHis');
    //         $fpx_sellerOrderNo  = "HOPRIM" . date('YmdHis') . rand(10000, 99999);

    //         $fpx_sellerExId     = config('app.env') == 'production' ? "EX00011125" : "EX00012323";
    //         $fpx_sellerId       = config('app.env') == 'production' ? $organization->seller_id : "SE00013841";
    //     }

    //     $fpx_msgType        = "AR";
    //     $fpx_msgToken       = "01";
    //     $fpx_sellerTxnTime  = date('YmdHis');
    //     $fpx_sellerBankCode = "01";
    //     $fpx_txnCurrency    = "MYR";
    //     $fpx_buyerIban      = "";
    //     $fpx_txnAmount      = $request->amount;
    //     $fpx_checkSum       = "";
    //     $fpx_buyerBankId    = $request->bankid;
    //     $fpx_buyerBankBranch = "";
    //     $fpx_buyerAccNo     = "";
    //     $fpx_buyerId        = "";
    //     $fpx_makerName      = "";
    //     $fpx_productDesc    = $request->desc;
    //     $fpx_version        = "6.0";

    //     /* Generating signing String */
    //     $data = $fpx_buyerAccNo . "|" . $fpx_buyerBankBranch . "|" . $fpx_buyerBankId . "|" . $fpx_buyerEmail . "|" . $fpx_buyerIban . "|" . $fpx_buyerId . "|" . $fpx_buyerName . "|" . $fpx_makerName . "|" . $fpx_msgToken . "|" . $fpx_msgType . "|" . $fpx_productDesc . "|" . $fpx_sellerBankCode . "|" . $fpx_sellerExId . "|" . $fpx_sellerExOrderNo . "|" . $fpx_sellerId . "|" . $fpx_sellerOrderNo . "|" . $fpx_sellerTxnTime . "|" . $fpx_txnAmount . "|" . $fpx_txnCurrency . "|" . $fpx_version;

    //     /* Reading key */
    //     // dd(getenv('FPX_KEY'));
    //    // $priv_key = getenv('FPX_KEY');
    //     // $priv_key = file_get_contents('C:\\pki-keys\\DevExchange\\EX00012323.key');
    //     //$pkeyid = openssl_get_privatekey($priv_key, null);
    //     //openssl_sign($data, $binary_signature, $pkeyid, OPENSSL_ALGO_SHA1);
    //     //$fpx_checkSum = strtoupper(bin2hex($binary_signature));

    //     $transaction = new Transaction();
    //     $transaction->nama          = $fpx_sellerExOrderNo;
    //     $transaction->description   = $fpx_sellerOrderNo;
    //     $transaction->transac_no    = NULL;
    //     // convert time to Y-m-d H:i:s format
    //     $date_time = date_create_from_format('YmdHis', $fpx_sellerTxnTime);
    //     $transaction->datetime_created = $date_time->format('Y-m-d H:i:s');
    //     $transaction->amount        = $fpx_txnAmount;
    //     $transaction->status        = 'Pending';
    //     $transaction->email         = $fpx_buyerEmail;
    //     $transaction->telno         = $telno;
    //     $transaction->user_id       = $user ? $user->id : null;
    //     $transaction->username      = strtoupper($fpx_buyerName);
    //     $transaction->fpx_checksum  = $fpx_checkSum;
    //     $transaction->buyerBankId      = $request->bankid;

    //     $list_student_fees_id   = $getstudentfees;
    //     $list_parent_fees_id    = $getparentfees;

    //     $id = explode("_", $fpx_sellerOrderNo);
    //     $id = (int) str_replace("PRIM", "", $id[0]);

    //     if ($transaction->save()) {

    //         // ******* save bridge transaction *********
    //         // type = S for school fees and D for donation

    //         if (substr($fpx_sellerExOrderNo, 0, 1) == 'S') {

    //             // ********* student fee id

    //             if ($list_student_fees_id) {
    //                 for ($i = 0; $i < count($list_student_fees_id); $i++) {
    //                     $array[] = array(
    //                         'student_fees_id' => $list_student_fees_id[$i],
    //                         'payment_type_id' => 1,
    //                         'transactions_id' => $transaction->id,
    //                     );
    //                 }

    //                 DB::table('fees_transactions_new')->insert($array);
    //             }
    //             if ($list_parent_fees_id) {

    //                 for ($i = 0; $i < count($list_parent_fees_id); $i++) {
    //                     $result = DB::table('fees_new_organization_user')
    //                         ->where('id', $list_parent_fees_id[$i])
    //                         ->update([
    //                             'transaction_id' => $transaction->id
    //                         ]);
    //                 }
    //             }
    //         } 
    //         else if (substr($fpx_sellerExOrderNo, 0, 1) == 'F')
    //         {
    //             $result = DB::table('orders')
    //             ->where('id', $request->order_id)
    //             ->update([
    //                 'transaction_id' => $transaction->id
    //             ]);
    //         }
    //         else if (substr($fpx_sellerExOrderNo, 0, 1) == 'M')
    //         {
    //             $result = DB::table('pgng_orders')
    //             ->where('id', $gng_order_id)
    //             ->update([
    //                 'transaction_id' => $transaction->id
    //             ]);
    //         }
    //         else if (substr($fpx_sellerExOrderNo, 0, 1) == 'K')
    //         {
    //             $daySelect = (int)$request->week_status;     
    //             $pickUp = Carbon::now()->next($daySelect)->toDateString();
    //             $result = DB::table('pgng_orders')
    //             ->where('id', $request->cartId)
    //             ->update([
    //                 'pickup_date' => $pickUp,
    //                 'note' => $request->note,
    //                 'transaction_id' => $transaction->id
    //             ]);
               
    //         }
    //         else if (substr($fpx_sellerExOrderNo, 0, 1) == 'H')
    //         {
    //             $result = DB::table('bookings')
    //             ->where('bookingid', $bookingId)
    //             ->update([
    //                 'transactionid' => $transaction->id
    //             ]);
               
    //         }
    //         else {
    //             $transaction->donation()->attach($id, ['payment_type_id' => 1]);
    //         }
    //     }
    //     else
    //     {
    //         return view('errors.500');
    //     }

    //     return view('fpx.index', compact(
    //         'fpx_msgType',
    //         'fpx_msgToken',
    //         'fpx_sellerExId',
    //         'fpx_sellerExOrderNo',
    //         'fpx_sellerTxnTime',
    //         'fpx_sellerOrderNo',
    //         'fpx_sellerId',
    //         'fpx_sellerBankCode',
    //         'fpx_txnCurrency',
    //         'fpx_txnAmount',
    //         'fpx_buyerEmail',
    //         'fpx_checkSum',
    //         'fpx_buyerName',
    //         'fpx_buyerBankId',
    //         'fpx_buyerBankBranch',
    //         'fpx_buyerAccNo',
    //         'fpx_buyerId',
    //         'fpx_makerName',
    //         'fpx_buyerIban',
    //         'fpx_productDesc',
    //         'fpx_version',
    //         'telno',
    //         'data',
    //         'getstudentfees',
    //         'getparentfees'
    //     ));
    // }


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
