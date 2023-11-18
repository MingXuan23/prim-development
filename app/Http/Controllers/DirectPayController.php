<?php

namespace App\Http\Controllers;
use App\User;
use App\Models\Order;
use App\Models\Fee_New;
use App\Models\Student;
use App\Models\Booking;
use App\Models\Room;
use App\Models\Donation;
use App\Models\Promotion;
use App\Mail\OrderReceipt;
use App\Mail\OrderSReceipt;
use App\Mail\MerchantOrderReceipt;
use App\Mail\HomestayReceipt;
use App\Models\PgngOrder;
use App\Models\Transaction;
use App\Models\Organization;
use App\Models\Destination_Offer;
use App\Models\Grab_Student;
use App\Models\Grab_Booking;
use App\Models\Bus;
use App\Models\Bus_Booking;
use App\Models\NotifyBus;
use App\Models\NotifyGrab;
use Illuminate\Http\Request;
use App\Mail\DonationReceipt;
use App\Mail\ResitBayaranGrab;
use App\Mail\ResitBayaranBus;
use App\Models\Dev\DevTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use phpDocumentor\Reflection\Types\Null_;
use App\Http\Controllers\AppBaseController;
use League\CommonMark\Inline\Parser\EscapableParser;


class DirectPayController extends Controller
{
    // 
    private $donation;
    private $user;
    private $organization;
    private $transaction;

    public function __construct(Donation $donation, User $user, Organization $organization, Transaction $transaction)
    {
        $this->donation = $donation;
        $this->user = $user;
        $this->organization = $organization;
        $this->transaction = $transaction;
    }
    public function directpayIndex(Request $request)
    {
        $getstudentfees = ($request->student_fees_id) ? $request->student_fees_id : "";
        $getparentfees  = ($request->parent_fees_id) ? $request->parent_fees_id : "";
        $user=null;
        $prefixCode="";
        if ($request->desc == 'Donation') {
            $user = User::find(Auth::id());
            $organization = $this->organization->getOrganizationByDonationId($request->d_id);

            if(isset($request->email))
            {
                $fpx_buyerEmail = $request->email;
                $telno = $request->telno;
                $fpx_buyerName = $request->name;
            }
            else
            {
                $fpx_buyerEmail =  NULL;
                $telno = NULL;
                $fpx_buyerName = "Penderma Tanpa Nama";
            }
            
            $fpx_sellerExOrderNo = $request->desc . "_" . $request->d_code . "_" . date('YmdHis') . "_" . $organization->id;

            $fpx_sellerOrderNo  = "PRIM" . str_pad($request->d_id, 3, "0", STR_PAD_LEFT)  . "_" . date('YmdHis') . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

           // $fpx_sellerExId     = config('app.env') == 'production' ? "EX00011125" : "EX00012323";

            //$fpx_sellerId       = config('app.env') == 'production' ? $organization->seller_id : "SE00013841";

            // $fpx_buyerIban      = $request->name . "/" . $telno . "/" . $request->email;
        } 
        else if ($request->desc == 'School_Fees')
        {
            $user = User::find(isset($request->user_id) ? $request->user_id : Auth::id());
            $organization = Organization::find($request->o_id);
            
            $fpx_buyerEmail      = $user->email;
            $telno               = $user->telno;
            $fpx_buyerName       = User::where('id', '=', Auth::id())->pluck('name')->first();
            $fpx_sellerExOrderNo = $request->desc . "_" . date('YmdHis');
            $fpx_sellerOrderNo  = "YSPRIM" . date('YmdHis') . rand(10000, 99999);

            //$fpx_sellerExId     = config('app.env') == 'production' ? "EX00011125" : "EX00012323";
            //$fpx_sellerId       = config('app.env') == 'production' ? $organization->seller_id : "SE00013841";
        }
        else if($request->desc == 'Food_Order')
        {
            $user = User::find($request->user_id);
            $organization = Organization::find($request->o_id);
            $fpx_buyerEmail      = $user->email;
            $telno               = $user->telno;
            $fpx_buyerName       = User::where('id', '=', Auth::id())->pluck('name')->first();
            $fpx_sellerExOrderNo = $request->desc . "_" . date('YmdHis');
            $fpx_sellerOrderNo  = "FOPRIM" . date('YmdHis') . rand(10000, 99999);

           // $fpx_sellerExId     = config('app.env') == 'production' ? "EX00011125" : "EX00012323";
            //$fpx_sellerId       = config('app.env') == 'production' ? $organization->seller_id : "SE00013841";
        }
        else if($request->desc == 'Merchant')
        {
            $gng_order_id = $request->order_id;

            DB::table('pgng_orders')->where('id', $gng_order_id)->update([
                'updated_at' => Carbon::now(),
                'status' => 'Pending'
            ]);

            $gng_order = DB::table('pgng_orders')
            ->where('id', $gng_order_id)
            ->select('user_id', 'organization_id')
            ->first();

            $ficts_seller_id = "SE00054277";

            $user = User::find($gng_order->user_id);
            $organization = Organization::find($gng_order->organization_id);
            $fpx_buyerEmail      = $user->email;
            $telno               = $user->telno;
            $fpx_buyerName       = User::where('id', '=', Auth::id())->pluck('name')->first();
            $fpx_sellerExOrderNo = $request->desc . "_" . date('YmdHis');
            $fpx_sellerOrderNo  = "MUPRIM" . date('YmdHis') . rand(10000, 99999);

           // $fpx_sellerExId     = config('app.env') == 'production' ? "EX00011125" : "EX00012323";
            //$fpx_sellerId       = config('app.env') == 'production' ? $organization->seller_id: "SE00013841";
        }
        else if($request->desc == 'Koperasi')
        {
            $cart = PgngOrder::find($request->cartId);

            $request->amount = $cart->total_price;
            $ficts_seller_id = "SE00054277";

            $user = User::find($cart->user_id);
            $organization = Organization::find($cart->organization_id);
            $fpx_buyerEmail      = $user->email;
            $telno               = $user->telno;
            $fpx_buyerName       = User::where('id', '=', Auth::id())->pluck('name')->first();
            $fpx_sellerExOrderNo = $request->desc . "_" . date('YmdHis');
            $fpx_sellerOrderNo  = "KOPPRIM" . date('YmdHis') . rand(10000, 99999);

           // $fpx_sellerExId     = config('app.env') == 'production' ? "EX00011125" : "EX00012323";
           // $fpx_sellerId       = config('app.env') == 'production' ? $organization->seller_id : "SE00013841";
        }
        else if($request->desc == 'Homestay')
        {
            $homestay = Booking::find($request->bookingid);
            $user = User::find($homestay->customerid);
            $room = Room::find($homestay->roomid);
            
            $request->amount = $homestay->totalprice;
            $bookingId = $request->bookingid;

            DB::table('bookings')->where('bookingid', $bookingId)->update([
                'updated_at' => Carbon::now(),
                'status' => 'Paid'
            ]);

            $organization = Organization::find($room->homestayid);
            $fpx_buyerEmail      = $user->email;
            $telno               = $user->telno;
            $fpx_buyerName       = User::where('id', '=', Auth::id())->pluck('name')->first();
            $fpx_sellerExOrderNo = $request->desc . "_" . date('YmdHis');
            $fpx_sellerOrderNo  = "HOPRIM" . date('YmdHis') . rand(10000, 99999);

            //$fpx_sellerExId     = config('app.env') == 'production' ? "EX00011125" : "EX00012323";
            //$fpx_sellerId       = config('app.env') == 'production' ? $organization->seller_id : "SE00013841";
        }
        else if($request->desc == 'Grab Student')
        {
            $grab = Grab_Booking::find($request->bookingid);
            $user = User::find($grab->id_user);
            $destination = Destination_Offer::find($grab->id_destination_offer);
            $kereta = Grab_Student::find($destination->id_grab_student);
            
            $bookingId = $request->bookingid;

            $organization = Organization::find($kereta->id_organizations);
            $fpx_buyerEmail      = $user->email;
            $telno               = $user->telno;
            $fpx_buyerName       = User::where('id', '=', Auth::id())->pluck('name')->first();
            $fpx_sellerExOrderNo = $request->desc . "_" . date('YmdHis');
            $fpx_sellerOrderNo  = "GSPRIM" . date('YmdHis') . rand(10000, 99999);

            //$fpx_sellerExId     = config('app.env') == 'production' ? "EX00011125" : "EX00012323";
            //$fpx_sellerId       = config('app.env') == 'production' ? $organization->seller_id : "SE00013841";
        }
        else if($request->desc == 'Bus')
        {
            $bus = Bus_Booking::find($request->bookingid);
            $user = User::find($bus->id_user);
            $basorg = Bus::find($bus->id_bus);
        
            $bookingId = $request->bookingid;
          
            $organization = Organization::find($basorg->id_organizations);
            $fpx_buyerEmail      = $user->email;
            $telno               = $user->telno;
            $fpx_buyerName       = User::where('id', '=', Auth::id())->pluck('name')->first();
            $fpx_sellerExOrderNo = $request->desc . "_" . date('YmdHis');
            $fpx_sellerOrderNo  = "BUPRIM" . date('YmdHis') . rand(10000, 99999);

            //$fpx_sellerExId     = config('app.env') == 'production' ? "EX00011125" : "EX00012323";
            //$fpx_sellerId       = config('app.env') == 'production' ? $organization->seller_id : "SE00013841";
        }
        else if($request->desc == 'OrderS')
        {
            $orders = Order::find($request->orderid);
            $user = User::find($orders->user_id);
            
            $amount = $request->amount;
            $orderId = $request->orderId;

            DB::table('orders')->where('id', $orderId)->update([
                'updated_at' => Carbon::now(),
                'status' => 'Preparing'
            ]);

            $organization = Organization::find($orders->organ_id);
            $fpx_buyerEmail      = $user->email;
            $telno               = $user->telno;
            $fpx_buyerName       = User::where('id', '=', Auth::id())->pluck('name')->first();
            $fpx_sellerExOrderNo = $request->desc . "_" . date('YmdHis');
            $fpx_sellerOrderNo  = "OSPRIM" . date('YmdHis') . rand(10000, 99999);

           // $fpx_sellerExId     = config('app.env') == 'production' ? "EX00011125" : "EX00012323";
            //$fpx_sellerId       = config('app.env') == 'production' ? $organization->seller_id : "SE00013841";
        }
        

       
         $fpx_sellerTxnTime  = date('YmdHis');
         $fpx_txnAmount      = $request->amount;


        $transaction = new Transaction();
        $transaction->nama          = $fpx_sellerExOrderNo;
        $transaction->description   = $fpx_sellerOrderNo;
        $transaction->transac_no    = NULL;
        // convert time to Y-m-d H:i:s format
        $date_time = date_create_from_format('YmdHis', $fpx_sellerTxnTime);
        $transaction->datetime_created = $date_time->format('Y-m-d H:i:s');
        $transaction->amount        = $fpx_txnAmount;
        $transaction->status        = 'Pending';
        $transaction->email         = $fpx_buyerEmail;
        $transaction->telno         = $telno;
        $transaction->user_id       = $user ? $user->id : null;
        $transaction->username      = strtoupper($fpx_buyerName);
        //$transaction->fpx_checksum  = $fpx_checkSum;
        //$transaction->buyerBankId      = $request->bankid;

        $list_student_fees_id   = $getstudentfees;
        $list_parent_fees_id    = $getparentfees;

        $id = explode("_", $fpx_sellerOrderNo);
        $id = (int) str_replace("PRIM", "", $id[0]);

        if ($transaction->save()) {

            // ******* save bridge transaction *********
            // type = S for school fees and D for donation

            if (substr($fpx_sellerExOrderNo, 0, 1) == 'S') {

                // ********* student fee id

                if ($list_student_fees_id) {
                    for ($i = 0; $i < count($list_student_fees_id); $i++) {
                        $array[] = array(
                            'student_fees_id' => $list_student_fees_id[$i],
                            'payment_type_id' => 1,
                            'transactions_id' => $transaction->id,
                        );
                    }

                    DB::table('fees_transactions_new')->insert($array);
                }
                if ($list_parent_fees_id) {

                    for ($i = 0; $i < count($list_parent_fees_id); $i++) {
                        $result = DB::table('fees_new_organization_user')
                            ->where('id', $list_parent_fees_id[$i])
                            ->update([
                                'transaction_id' => $transaction->id
                            ]);
                    }
                }
            } 
            else if (substr($fpx_sellerExOrderNo, 0, 1) == 'F')
            {
                $result = DB::table('orders')
                ->where('id', $request->order_id)
                ->update([
                    'transaction_id' => $transaction->id
                ]);
            }
            else if (substr($fpx_sellerExOrderNo, 0, 1) == 'M')
            {
                $result = DB::table('pgng_orders')
                ->where('id', $gng_order_id)
                ->update([
                    'transaction_id' => $transaction->id
                ]);
            }
            else if (substr($fpx_sellerExOrderNo, 0, 1) == 'K')
            {
                $daySelect = (int)$request->week_status;
                if($daySelect ==-1){
                    $pickUp = Carbon::create(1, 1, 1)->toDateString();//mindate
                }     
                else{
                    $pickUp = Carbon::now()->next($daySelect)->toDateString();
                }
                
                $result = DB::table('pgng_orders')
                ->where('id', $request->cartId)
                ->update([
                    'pickup_date' => $pickUp,
                    'note' => $request->note,
                    'transaction_id' => $transaction->id
                ]);
               
            }
            else if (substr($fpx_sellerExOrderNo, 0, 1) == 'H')
            {
                $result = DB::table('bookings')
                ->where('bookingid', $bookingId)
                ->update([
                    'transactionid' => $transaction->id
                ]);
               
            }
            else if (substr($fpx_sellerExOrderNo, 0, 1) == 'O')
            {
                $result = DB::table('orders')
                ->where('id', $orderId)
                ->update([
                    'transaction_id' => $transaction->id
                ]);
               
            }
            else if (substr($fpx_sellerExOrderNo, 0, 1) == 'G')
            {
                $result = DB::table('grab_bookings')
                ->where('id', $bookingId)
                ->update([
                    'transactionid' => $transaction->id
                ]);           
            }
            else if (substr($fpx_sellerExOrderNo, 0, 1) == 'B')
            {
                $result = DB::table('bus_bookings')
                ->where('id', $bookingId)
                ->update([
                    'transactionid' => $transaction->id
                ]);           
            }
            else {
                $transaction->donation()->attach($id, ['payment_type_id' => 1]);
            }
        }
        else
        {
            return view('errors.500');
        }
        $private_key= "aaa";
        return view('directpay.index',compact(
            'fpx_buyerEmail',
            'fpx_buyerName',
            'private_key',
            'fpx_txnAmount',
            'fpx_sellerExOrderNo'
            
        ));
        // return view('fpx.index', compact(
        //     'fpx_msgType',
        //     'fpx_msgToken',
        //     'fpx_sellerExId',
        //     'fpx_sellerExOrderNo',
        //     'fpx_sellerTxnTime',
        //     'fpx_sellerOrderNo',
        //     'fpx_sellerId',
        //     'fpx_sellerBankCode',
        //     'fpx_txnCurrency',
        //     'fpx_txnAmount',
        //     'fpx_buyerEmail',
        //     'fpx_checkSum',
        //     'fpx_buyerName',
        //     'fpx_buyerBankId',
        //     'fpx_buyerBankBranch',
        //     'fpx_buyerAccNo',
        //     'fpx_buyerId',
        //     'fpx_makerName',
        //     'fpx_buyerIban',
        //     'fpx_productDesc',
        //     'fpx_version',
        //     'telno',
        //     'data',
        //     'getstudentfees',
        //     'getparentfees'
        // ));
    }
}
