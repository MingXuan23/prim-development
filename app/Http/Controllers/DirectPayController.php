<?php

namespace App\Http\Controllers;

use stdClass;
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
use App\Mail\SHelperReceipt;
use App\Mail\SHelperReminder;

use App\Mail\DonationReceipt;
use App\Mail\ResitBayaranGrab;
use App\Mail\ResitBayaranBus;
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
use App\Models\DonationStreak;
use App\Models\Dev\DevTransaction;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use phpDocumentor\Reflection\Types\Null_;
use App\Http\Controllers\AppBaseController;
use App\Http\Controllers\Merchant\Regular\OrderController;
use League\CommonMark\Inline\Parser\EscapableParser;




class DirectPayController extends Controller
{

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

        try {
            $getstudentfees = ($request->student_fees_id) ? $request->student_fees_id : "";
            $getparentfees  = ($request->parent_fees_id) ? $request->parent_fees_id : "";
            $user = null;
            $prefixCode = "";

            if ($request->desc == 'Donation') {
                $user = User::find(Auth::id());
                $organization = $this->organization->getOrganizationByDonationId($request->d_id);

                if (isset($request->email)) {
                    $fpx_buyerEmail = $request->email;
                    $telno = $request->telno;
                    $fpx_buyerName = $request->name;
                } else {
                    $fpx_buyerEmail =  NULL;
                    $telno = NULL;
                    $fpx_buyerName = "Penderma Tanpa Nama";
                }

                $fpx_sellerExOrderNo = $request->desc . "_" . $request->d_code . "_" . date('YmdHis') . "_" . $organization->id;

                $fpx_sellerOrderNo  = "PRIM" . str_pad($request->d_id, 3, "0", STR_PAD_LEFT)  . "_" . date('YmdHis') . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

                $private_key = $organization->private_key;
                // $fpx_sellerExId     = config('app.env') == 'production' ? "EX00011125" : "EX00012323";

                //$fpx_sellerId       = config('app.env') == 'production' ? $organization->seller_id : "SE00013841";

                // $fpx_buyerIban      = $request->name . "/" . $telno . "/" . $request->email;
            } else if ($request->desc == 'School_Fees') {
                $user = User::find(isset($request->user_id) ? $request->user_id : Auth::id());
                $organization = Organization::find($request->o_id);

                $fpx_buyerEmail      = $user->email;
                $telno               = $user->telno;
                $fpx_buyerName       = User::where('id', '=', Auth::id())->pluck('name')->first();

                //add _M_ for mobile payment
                if ($request->has('source') && $request->source == 'mobile') {
                    $fpx_sellerExOrderNo = $request->desc . "_M_" . date('YmdHis');
                } else {
                    $fpx_sellerExOrderNo = $request->desc . "_" . date('YmdHis');
                }

                $fpx_sellerOrderNo  = "YSPRIM" . date('YmdHis') . rand(10000, 99999);


                $private_key = $organization->private_key;
                //$fpx_sellerExId     = config('app.env') == 'production' ? "EX00011125" : "EX00012323";
                //$fpx_sellerId       = config('app.env') == 'production' ? $organization->seller_id : "SE00013841";
            } else if ($request->desc == 'Food_Order') {
                $user = User::find($request->user_id);
                $organization = Organization::find($request->o_id);
                $fpx_buyerEmail      = $user->email;
                $telno               = $user->telno;
                $fpx_buyerName       = User::where('id', '=', Auth::id())->pluck('name')->first();
                $fpx_sellerExOrderNo = $request->desc . "_" . date('YmdHis');
                $fpx_sellerOrderNo  = "FOPRIM" . date('YmdHis') . rand(10000, 99999);
                $private_key = $organization->private_key;
                // $fpx_sellerExId     = config('app.env') == 'production' ? "EX00011125" : "EX00012323";
                //$fpx_sellerId       = config('app.env') == 'production' ? $organization->seller_id : "SE00013841";
            } else if ($request->desc == 'Merchant') {
                $note = $request->note;
                $order_type = $request->order_type;
                $gng_order_id = $request->order_id;

                // for get n go or pick-up
                if ($order_type == 'Pick-Up') {
                    $pickup_date = $request->pickup_date;
                    $pickup_time = $request->pickup_time;
                    if (OrderController::validateRequestedPickupDate($pickup_date, $pickup_time, $request->org_id) == false) {
                        return back()->with('error', 'Sila pilih masa yang sesuai');
                    }
                    $pickup_datetime = Carbon::createFromFormat('d/m/Y', $pickup_date)->format('Y-m-d') . ' ' . Carbon::parse($pickup_time)->format('H:i:s');

                    DB::table('pgng_orders')->where('id', $gng_order_id)->update([
                        'updated_at' => Carbon::now(),
                        'order_type' => $order_type,
                        'pickup_date' => $pickup_datetime,
                        'note' => $note,
                        'status' => 'Pending'
                    ]);
                }

                $gng_order = DB::table('pgng_orders')
                    ->where('id', $gng_order_id)
                    ->select('user_id', 'organization_id')
                    ->first();


                $user = User::find($gng_order->user_id);
                $organization = Organization::find($gng_order->organization_id);
                $fpx_buyerEmail      = $user->email;
                $telno               = $user->telno;
                $fpx_buyerName       = User::where('id', '=', Auth::id())->pluck('name')->first();
                $fpx_sellerExOrderNo = $request->desc . "_" . date('YmdHis');
                $fpx_sellerOrderNo  = "MUPRIM" . date('YmdHis') . rand(10000, 99999);
                $private_key = $organization->private_key;
                // $fpx_sellerExId     = config('app.env') == 'production' ? "EX00011125" : "EX00012323";
                //$fpx_sellerId       = config('app.env') == 'production' ? $organization->seller_id: "SE00013841";
            } else if ($request->desc == 'Koperasi') {
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
                $private_key = $organization->private_key;
                // $fpx_sellerExId     = config('app.env') == 'production' ? "EX00011125" : "EX00012323";
                // $fpx_sellerId       = config('app.env') == 'production' ? $organization->seller_id : "SE00013841";
            } else if ($request->desc == 'Homestay') {
                $homestay = Booking::find($request->bookingid);
                $user = User::find($homestay->customerid);
                $room = Room::find($homestay->roomid);

                $request->amount = $homestay->totalprice;
                $bookingId = $request->bookingid;

                $paymentType =  $request->paymentType;
                $depositAmount =  NULL;
                if ($paymentType == 'deposit') {
                    $depositCharge = $room->organization->fixed_charges;
                    $depositAmount = $homestay->totalprice * $depositCharge / 100;
                    $request->amount = $depositAmount;
                } else if ($paymentType == 'balance') {
                    $request->amount = $homestay->totalprice - $homestay->deposit_amount;
                    $depositCharge = $room->organization->fixed_charges;
                    $depositAmount = $homestay->totalprice * $depositCharge / 100;
                }
                if ($paymentType == 'balance') {
                    DB::table('bookings')->where('bookingid', $bookingId)->update([
                        'updated_at' => Carbon::now(),
                    ]);
                } else {
                    DB::table('bookings')->where('bookingid', $bookingId)->update([
                        'updated_at' => Carbon::now(),
                        'status' => 'Pending',
                        'deposit_amount' => $depositAmount,
                    ]);
                }


                $organization = Organization::find($room->homestayid);
                $fpx_buyerEmail      = $user->email;
                $telno               = $user->telno;
                $fpx_buyerName       = User::where('id', '=', Auth::id())->pluck('name')->first();
                $fpx_sellerExOrderNo = $request->desc . "_" . date('YmdHis');
                $fpx_sellerOrderNo  = "HOPRIM" . date('YmdHis') . rand(10000, 99999);

                //https://directpay.my/api/fpx/GetTransactionInfo?PrivateKey=9BB6D047-2FB3-4B7A-9199-09441E7F4B0C&Fpx_SellerOrderNo=Merchant_20240115210137&Fpx_SellerExOrderNo=DirectPay20240115210144
                //$fpx_sellerExId     = config('app.env') == 'production' ? "EX00011125" : "EX00012323";
                //$fpx_sellerId       = config('app.env') == 'production' ? $organization->seller_id : "SE00013841";
                $private_key = $organization->private_key;
            } else if ($request->desc == 'Grab Student') {
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
                $private_key = $organization->private_key;
                //$fpx_sellerExId     = config('app.env') == 'production' ? "EX00011125" : "EX00012323";
                //$fpx_sellerId       = config('app.env') == 'production' ? $organization->seller_id : "SE00013841";
            } else if ($request->desc == 'Bus') {
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
                $private_key = $organization->private_key;
                //$fpx_sellerExId     = config('app.env') == 'production' ? "EX00011125" : "EX00012323";
                //$fpx_sellerId       = config('app.env') == 'production' ? $organization->seller_id : "SE00013841";
            } else if ($request->desc == 'OrderS') {
                if ($request->mobile != 'true') {
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
                    $private_key = $organization->private_key;
                } else {
                    $user = User::find($request->user_id);
                    $user_name = $request->name;
                    $user_email = $request->email;
                    $user_telno = $request->telno;
                    $organ_id = $request->organ_id;
                    $order_cart_id = $request->order_cart_id;

                    DB::table('order_cart')
                        ->where('id', $order_cart_id)
                        ->update([
                            'updated_at' => Carbon::now(),
                            'order_status' => 'checkout-cart-pending-payment',
                            'totalamount' => $request->amount
                        ]);

                    $organization = Organization::find($organ_id);
                    $fpx_buyerEmail      = $user_email;
                    $telno               = $user_telno;
                    $fpx_buyerName       = $user_name;
                    $fpx_sellerExOrderNo = $request->desc . "_" . date('YmdHis');
                    $fpx_sellerOrderNo  = "OSPRIM" . date('YmdHis') . rand(10000, 99999);
                    $private_key = $organization->private_key;
                }
                // $fpx_sellerExId     = config('app.env') == 'production' ? "EX00011125" : "EX00012323";
                //$fpx_sellerId       = config('app.env') == 'production' ? $organization->seller_id : "SE00013841";
            } else if ($request->desc == "Request_Help") {

                $draft = DB::table('code_requests')->where('status', 'Draft')->where('id', $request->request_id)->first();


                $user_name = $draft->name;
                $user_email = $draft->email;
                $user_telno = $draft->phone;
                $organ_id = 168; //nex way enterprise, change it in the future


                $organization = Organization::find($organ_id);
                $fpx_buyerEmail      = $user_email;
                $telno               = $user_telno;
                $fpx_buyerName       = $user_name;
                $fpx_sellerExOrderNo = $request->desc . "_" . date('YmdHis');
                $fpx_sellerOrderNo  = "RSPRIM" . date('YmdHis') . rand(10000, 99999);
                $private_key = $organization->private_key;
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

                $transaction->nama = $transaction->nama . '_' . ($transaction->id % 100); //to makesure it is unique
                $fpx_sellerExOrderNo = $transaction->nama;
                $transaction->save();
                //dd($transaction->nama);
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
                } else if (substr($fpx_sellerExOrderNo, 0, 1) == 'F') {
                    $result = DB::table('orders')
                        ->where('id', $request->order_id)
                        ->update([
                            'transaction_id' => $transaction->id
                        ]);
                } else if (substr($fpx_sellerExOrderNo, 0, 1) == 'M') {
                    $result = DB::table('pgng_orders')
                        ->where('id', $gng_order_id)
                        ->update([
                            'transaction_id' => $transaction->id
                        ]);

                    //     $result = $this->getReferralCodeFromSource($request->referral_code);
                    //     //dd($referral_code);
                    //     $code = $result['code'];

                    //     $referral_code = DB::table('referral_code')
                    //                     ->where('code',$code)
                    //                     ->first();

                    //    // $own_code_id = $own_code !=null ?$own_code->id:0;
                    //      if ($result['source'] != 'none'){
                    //         $this->insertPointHistory($referral_code->id,$transaction->id,1,1,'Transaksi Get & Go RM'.$transaction->amount);
                    //     }

                } else if (substr($fpx_sellerExOrderNo, 0, 1) == 'K') {
                    $daySelect = (int)$request->week_status;
                    if ($daySelect == -1) {
                        $pickUp = Carbon::create(1, 1, 1)->toDateString(); //mindate
                    } else {
                        $pickUp = Carbon::now()->next($daySelect)->toDateString();
                    }

                    $result = DB::table('pgng_orders')
                        ->where('id', $request->cartId)
                        ->update([
                            'pickup_date' => $pickUp,
                            'note' => $request->note,
                            'transaction_id' => $transaction->id
                        ]);
                } else if (substr($fpx_sellerExOrderNo, 0, 1) == 'H') {
                    if ($paymentType == 'deposit' || $paymentType == 'full') {
                        $result = DB::table('bookings')
                            ->where('bookingid', $bookingId)
                            ->update([
                                'transactionid' => $transaction->id
                            ]);
                    } else if ($paymentType == 'balance') {
                        $result = DB::table('bookings')
                            ->where('bookingid', $bookingId)
                            ->update([
                                'transaction_balance_id' => $transaction->id
                            ]);
                    }

                    $result = $this->getReferralCodeFromSource($request->referral_code);
                    //dd($referral_code);
                    $code = $result['code'];

                    $referral_code = DB::table('referral_code')
                        ->where('code', $code)
                        ->first();

                    // $own_code_id = $own_code !=null ?$own_code->id:0;
                    if ($result['source'] != 'none') {
                        $entity = new stdClass();
                        $entity->room_booking_id = (int)$bookingId;
                        $entity_json = json_encode($entity);
                        // dd($entity,$entity_json);
                        $this->insertPointHistory($referral_code->id, $transaction->id, 1, 1, 'Transaksi Book & Stay RM' . $transaction->amount, $entity_json);
                    }
                } else if (substr($fpx_sellerExOrderNo, 0, 1) == 'O') {
                    if ($request->mobile != 'true') {
                        $result = DB::table('orders')
                            ->where('id', $orderId)
                            ->update([
                                'transaction_id' => $transaction->id
                            ]);
                    } else {
                        $result = DB::table('order_cart')
                            ->where('id', $order_cart_id)
                            ->update([
                                'transactions_id' => $transaction->id
                            ]);
                    }
                } else if (substr($fpx_sellerExOrderNo, 0, 1) == 'G') {
                    $result = DB::table('grab_bookings')
                        ->where('id', $bookingId)
                        ->update([
                            'transactionid' => $transaction->id
                        ]);
                } else if (substr($fpx_sellerExOrderNo, 0, 1) == 'B') {
                    $result = DB::table('bus_bookings')
                        ->where('id', $bookingId)
                        ->update([
                            'transactionid' => $transaction->id
                        ]);
                } else if (substr($fpx_sellerExOrderNo, 0, 1) == 'R') {
                    DB::table('code_requests')->where('status', 'Draft')->where('id', $request->request_id)->update([
                        'transaction_id' => $transaction->id,
                        'status' => 'Pending Payment'
                    ]);
                } else {
                    $transaction->donation()->attach($id, ['payment_type_id' => 1]);
                    $result = $this->getReferralCodeFromSource($request->referral_code);
                    //dd($referral_code);
                    $code = $result['code'];

                    $referral_code = DB::table('referral_code')
                        ->where('code', $code)
                        ->first();

                    // $own_code_id = $own_code !=null ?$own_code->id:0;

                    if ($result['own_code'] != "") {
                        $own_code =  DB::table('referral_code')
                            ->where('code', $result['own_code'])
                            ->first();
                        $this->insertPointHistory($own_code->id, $transaction->id, 0, 1, 'Transaksi Derma daripada sendiri');
                    }
                    if ($result['source'] != 'none') {
                        $this->insertPointHistory($referral_code->id, $transaction->id, 1, 1, 'Transaksi Derma daripada kod');
                    }
                    //dd('stop');
                }
            } else {
                Log::error('Transaction save() returned false', [
                    'source' => 'mobile',
                    'attributes' => $transaction->getAttributes(),
                    'dirty' => $transaction->getDirty(),
                    'errors' => session()->get('errors'), // 如果有 validator
                ]);
                return view('errors.500');
            }
            //dd('fpxsellerOrderNo:'.$fpx_sellerOrderNo.'\nlength:'.strlen($fpx_sellerOrderNo),'fpx_sellerExOrderNo:'.$fpx_sellerExOrderNo.'\nlength:'.strlen($fpx_sellerOrderNo));
            //$private_key = '9BB6D047-2FB3-4B7A-9199-09441E7F4B0C';

            // dd($fpx_buyerEmail, $fpx_buyerName, $private_key, $fpx_txnAmount, $fpx_sellerExOrderNo, $fpx_sellerOrderNo);

            //dd($pos,$fpx_sellerOrderNo);
          
            return view('directpay.index', compact(
                'fpx_buyerEmail',
                'fpx_buyerName',
                'private_key',
                'fpx_txnAmount',
                'fpx_sellerExOrderNo',
                'fpx_sellerOrderNo',
                'getstudentfees',
                'getparentfees'

            ));
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }


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

     public function newDirectpayIndex(Request $request)
    {

        try {
            $getstudentfees = ($request->student_fees_id) ? $request->student_fees_id : "";
            $getparentfees  = ($request->parent_fees_id) ? $request->parent_fees_id : "";
            $user = null;
            $prefixCode = "";

            if ($request->desc == 'Donation') {
                $user = User::find(Auth::id());
                $organization = $this->organization->getOrganizationByDonationId($request->d_id);

                if (isset($request->email)) {
                    $fpx_buyerEmail = $request->email;
                    $telno = $request->telno;
                    $fpx_buyerName = $request->name;
                } else {
                    $fpx_buyerEmail =  NULL;
                    $telno = NULL;
                    $fpx_buyerName = "Penderma Tanpa Nama";
                }

                $fpx_sellerExOrderNo = $request->desc . "_" . $request->d_code . "_" . date('YmdHis') . "_" . $organization->id;

                $fpx_sellerOrderNo  = "PRIM" . str_pad($request->d_id, 3, "0", STR_PAD_LEFT)  . "_" . date('YmdHis') . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

                $private_key = $organization->private_key;
                // $fpx_sellerExId     = config('app.env') == 'production' ? "EX00011125" : "EX00012323";

                //$fpx_sellerId       = config('app.env') == 'production' ? $organization->seller_id : "SE00013841";

                // $fpx_buyerIban      = $request->name . "/" . $telno . "/" . $request->email;
            } else if ($request->desc == 'School_Fees') {
                $user = User::find(isset($request->user_id) ? $request->user_id : Auth::id());
                $organization = Organization::find($request->o_id);

                $fpx_buyerEmail      = $user->email;
                $telno               = $user->telno;
                $fpx_buyerName       = User::where('id', '=', Auth::id())->pluck('name')->first();

                //add _M_ for mobile payment
                if ($request->has('source') && $request->source == 'mobile') {
                    $fpx_sellerExOrderNo = $request->desc . "_M_" . date('YmdHis');
                } else {
                    $fpx_sellerExOrderNo = $request->desc . "_" . date('YmdHis');
                }

                $fpx_sellerOrderNo  = "YSPRIM" . date('YmdHis') . rand(10000, 99999);


                $private_key = $organization->private_key;
                //$fpx_sellerExId     = config('app.env') == 'production' ? "EX00011125" : "EX00012323";
                //$fpx_sellerId       = config('app.env') == 'production' ? $organization->seller_id : "SE00013841";
            } else if ($request->desc == 'Food_Order') {
                $user = User::find($request->user_id);
                $organization = Organization::find($request->o_id);
                $fpx_buyerEmail      = $user->email;
                $telno               = $user->telno;
                $fpx_buyerName       = User::where('id', '=', Auth::id())->pluck('name')->first();
                $fpx_sellerExOrderNo = $request->desc . "_" . date('YmdHis');
                $fpx_sellerOrderNo  = "FOPRIM" . date('YmdHis') . rand(10000, 99999);
                $private_key = $organization->private_key;
                // $fpx_sellerExId     = config('app.env') == 'production' ? "EX00011125" : "EX00012323";
                //$fpx_sellerId       = config('app.env') == 'production' ? $organization->seller_id : "SE00013841";
            } else if ($request->desc == 'Merchant') {
                $note = $request->note;
                $order_type = $request->order_type;
                $gng_order_id = $request->order_id;

                // for get n go or pick-up
                if ($order_type == 'Pick-Up') {
                    $pickup_date = $request->pickup_date;
                    $pickup_time = $request->pickup_time;
                    if (OrderController::validateRequestedPickupDate($pickup_date, $pickup_time, $request->org_id) == false) {
                        return back()->with('error', 'Sila pilih masa yang sesuai');
                    }
                    $pickup_datetime = Carbon::createFromFormat('d/m/Y', $pickup_date)->format('Y-m-d') . ' ' . Carbon::parse($pickup_time)->format('H:i:s');

                    DB::table('pgng_orders')->where('id', $gng_order_id)->update([
                        'updated_at' => Carbon::now(),
                        'order_type' => $order_type,
                        'pickup_date' => $pickup_datetime,
                        'note' => $note,
                        'status' => 'Pending'
                    ]);
                }

                $gng_order = DB::table('pgng_orders')
                    ->where('id', $gng_order_id)
                    ->select('user_id', 'organization_id')
                    ->first();


                $user = User::find($gng_order->user_id);
                $organization = Organization::find($gng_order->organization_id);
                $fpx_buyerEmail      = $user->email;
                $telno               = $user->telno;
                $fpx_buyerName       = User::where('id', '=', Auth::id())->pluck('name')->first();
                $fpx_sellerExOrderNo = $request->desc . "_" . date('YmdHis');
                $fpx_sellerOrderNo  = "MUPRIM" . date('YmdHis') . rand(10000, 99999);
                $private_key = $organization->private_key;
                // $fpx_sellerExId     = config('app.env') == 'production' ? "EX00011125" : "EX00012323";
                //$fpx_sellerId       = config('app.env') == 'production' ? $organization->seller_id: "SE00013841";
            } else if ($request->desc == 'Koperasi') {
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
                $private_key = $organization->private_key;
                // $fpx_sellerExId     = config('app.env') == 'production' ? "EX00011125" : "EX00012323";
                // $fpx_sellerId       = config('app.env') == 'production' ? $organization->seller_id : "SE00013841";
            } else if ($request->desc == 'Homestay') {
                $homestay = Booking::find($request->bookingid);
                $user = User::find($homestay->customerid);
                $room = Room::find($homestay->roomid);

                $request->amount = $homestay->totalprice;
                $bookingId = $request->bookingid;

                $paymentType =  $request->paymentType;
                $depositAmount =  NULL;
                if ($paymentType == 'deposit') {
                    $depositCharge = $room->organization->fixed_charges;
                    $depositAmount = $homestay->totalprice * $depositCharge / 100;
                    $request->amount = $depositAmount;
                } else if ($paymentType == 'balance') {
                    $request->amount = $homestay->totalprice - $homestay->deposit_amount;
                    $depositCharge = $room->organization->fixed_charges;
                    $depositAmount = $homestay->totalprice * $depositCharge / 100;
                }
                if ($paymentType == 'balance') {
                    DB::table('bookings')->where('bookingid', $bookingId)->update([
                        'updated_at' => Carbon::now(),
                    ]);
                } else {
                    DB::table('bookings')->where('bookingid', $bookingId)->update([
                        'updated_at' => Carbon::now(),
                        'status' => 'Pending',
                        'deposit_amount' => $depositAmount,
                    ]);
                }


                $organization = Organization::find($room->homestayid);
                $fpx_buyerEmail      = $user->email;
                $telno               = $user->telno;
                $fpx_buyerName       = User::where('id', '=', Auth::id())->pluck('name')->first();
                $fpx_sellerExOrderNo = $request->desc . "_" . date('YmdHis');
                $fpx_sellerOrderNo  = "HOPRIM" . date('YmdHis') . rand(10000, 99999);

                //https://directpay.my/api/fpx/GetTransactionInfo?PrivateKey=9BB6D047-2FB3-4B7A-9199-09441E7F4B0C&Fpx_SellerOrderNo=Merchant_20240115210137&Fpx_SellerExOrderNo=DirectPay20240115210144
                //$fpx_sellerExId     = config('app.env') == 'production' ? "EX00011125" : "EX00012323";
                //$fpx_sellerId       = config('app.env') == 'production' ? $organization->seller_id : "SE00013841";
                $private_key = $organization->private_key;
            } else if ($request->desc == 'Grab Student') {
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
                $private_key = $organization->private_key;
                //$fpx_sellerExId     = config('app.env') == 'production' ? "EX00011125" : "EX00012323";
                //$fpx_sellerId       = config('app.env') == 'production' ? $organization->seller_id : "SE00013841";
            } else if ($request->desc == 'Bus') {
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
                $private_key = $organization->private_key;
                //$fpx_sellerExId     = config('app.env') == 'production' ? "EX00011125" : "EX00012323";
                //$fpx_sellerId       = config('app.env') == 'production' ? $organization->seller_id : "SE00013841";
            } else if ($request->desc == 'OrderS') {
                if ($request->mobile != 'true') {
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
                    $private_key = $organization->private_key;
                } else {
                    $user = User::find($request->user_id);
                    $user_name = $request->name;
                    $user_email = $request->email;
                    $user_telno = $request->telno;
                    $organ_id = $request->organ_id;
                    $order_cart_id = $request->order_cart_id;

                    DB::table('order_cart')
                        ->where('id', $order_cart_id)
                        ->update([
                            'updated_at' => Carbon::now(),
                            'order_status' => 'checkout-cart-pending-payment',
                            'totalamount' => $request->amount
                        ]);

                    $organization = Organization::find($organ_id);
                    $fpx_buyerEmail      = $user_email;
                    $telno               = $user_telno;
                    $fpx_buyerName       = $user_name;
                    $fpx_sellerExOrderNo = $request->desc . "_" . date('YmdHis');
                    $fpx_sellerOrderNo  = "OSPRIM" . date('YmdHis') . rand(10000, 99999);
                    $private_key = $organization->private_key;
                }
                // $fpx_sellerExId     = config('app.env') == 'production' ? "EX00011125" : "EX00012323";
                //$fpx_sellerId       = config('app.env') == 'production' ? $organization->seller_id : "SE00013841";
            } else if ($request->desc == "Request_Help") {

                $draft = DB::table('code_requests')->where('status', 'Draft')->where('id', $request->request_id)->first();


                $user_name = $draft->name;
                $user_email = $draft->email;
                $user_telno = $draft->phone;
                $organ_id = 168; //nex way enterprise, change it in the future


                $organization = Organization::find($organ_id);
                $fpx_buyerEmail      = $user_email;
                $telno               = $user_telno;
                $fpx_buyerName       = $user_name;
                $fpx_sellerExOrderNo = $request->desc . "_" . date('YmdHis');
                $fpx_sellerOrderNo  = "RSPRIM" . date('YmdHis') . rand(10000, 99999);
                $private_key = $organization->private_key;
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

                $transaction->nama = $transaction->nama . '_' . ($transaction->id % 100); //to makesure it is unique
                $fpx_sellerExOrderNo = $transaction->nama;
                $transaction->save();
                //dd($transaction->nama);
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
                } else if (substr($fpx_sellerExOrderNo, 0, 1) == 'F') {
                    $result = DB::table('orders')
                        ->where('id', $request->order_id)
                        ->update([
                            'transaction_id' => $transaction->id
                        ]);
                } else if (substr($fpx_sellerExOrderNo, 0, 1) == 'M') {
                    $result = DB::table('pgng_orders')
                        ->where('id', $gng_order_id)
                        ->update([
                            'transaction_id' => $transaction->id
                        ]);

                    //     $result = $this->getReferralCodeFromSource($request->referral_code);
                    //     //dd($referral_code);
                    //     $code = $result['code'];

                    //     $referral_code = DB::table('referral_code')
                    //                     ->where('code',$code)
                    //                     ->first();

                    //    // $own_code_id = $own_code !=null ?$own_code->id:0;
                    //      if ($result['source'] != 'none'){
                    //         $this->insertPointHistory($referral_code->id,$transaction->id,1,1,'Transaksi Get & Go RM'.$transaction->amount);
                    //     }

                } else if (substr($fpx_sellerExOrderNo, 0, 1) == 'K') {
                    $daySelect = (int)$request->week_status;
                    if ($daySelect == -1) {
                        $pickUp = Carbon::create(1, 1, 1)->toDateString(); //mindate
                    } else {
                        $pickUp = Carbon::now()->next($daySelect)->toDateString();
                    }

                    $result = DB::table('pgng_orders')
                        ->where('id', $request->cartId)
                        ->update([
                            'pickup_date' => $pickUp,
                            'note' => $request->note,
                            'transaction_id' => $transaction->id
                        ]);
                } else if (substr($fpx_sellerExOrderNo, 0, 1) == 'H') {
                    if ($paymentType == 'deposit' || $paymentType == 'full') {
                        $result = DB::table('bookings')
                            ->where('bookingid', $bookingId)
                            ->update([
                                'transactionid' => $transaction->id
                            ]);
                    } else if ($paymentType == 'balance') {
                        $result = DB::table('bookings')
                            ->where('bookingid', $bookingId)
                            ->update([
                                'transaction_balance_id' => $transaction->id
                            ]);
                    }

                    $result = $this->getReferralCodeFromSource($request->referral_code);
                    //dd($referral_code);
                    $code = $result['code'];

                    $referral_code = DB::table('referral_code')
                        ->where('code', $code)
                        ->first();

                    // $own_code_id = $own_code !=null ?$own_code->id:0;
                    if ($result['source'] != 'none') {
                        $entity = new stdClass();
                        $entity->room_booking_id = (int)$bookingId;
                        $entity_json = json_encode($entity);
                        // dd($entity,$entity_json);
                        $this->insertPointHistory($referral_code->id, $transaction->id, 1, 1, 'Transaksi Book & Stay RM' . $transaction->amount, $entity_json);
                    }
                } else if (substr($fpx_sellerExOrderNo, 0, 1) == 'O') {
                    if ($request->mobile != 'true') {
                        $result = DB::table('orders')
                            ->where('id', $orderId)
                            ->update([
                                'transaction_id' => $transaction->id
                            ]);
                    } else {
                        $result = DB::table('order_cart')
                            ->where('id', $order_cart_id)
                            ->update([
                                'transactions_id' => $transaction->id
                            ]);
                    }
                } else if (substr($fpx_sellerExOrderNo, 0, 1) == 'G') {
                    $result = DB::table('grab_bookings')
                        ->where('id', $bookingId)
                        ->update([
                            'transactionid' => $transaction->id
                        ]);
                } else if (substr($fpx_sellerExOrderNo, 0, 1) == 'B') {
                    $result = DB::table('bus_bookings')
                        ->where('id', $bookingId)
                        ->update([
                            'transactionid' => $transaction->id
                        ]);
                } else if (substr($fpx_sellerExOrderNo, 0, 1) == 'R') {
                    DB::table('code_requests')->where('status', 'Draft')->where('id', $request->request_id)->update([
                        'transaction_id' => $transaction->id,
                        'status' => 'Pending Payment'
                    ]);
                } else {
                    $transaction->donation()->attach($id, ['payment_type_id' => 1]);
                    $result = $this->getReferralCodeFromSource($request->referral_code);
                    //dd($referral_code);
                    $code = $result['code'];

                    $referral_code = DB::table('referral_code')
                        ->where('code', $code)
                        ->first();

                    // $own_code_id = $own_code !=null ?$own_code->id:0;

                    if ($result['own_code'] != "") {
                        $own_code =  DB::table('referral_code')
                            ->where('code', $result['own_code'])
                            ->first();
                        $this->insertPointHistory($own_code->id, $transaction->id, 0, 1, 'Transaksi Derma daripada sendiri');
                    }
                    if ($result['source'] != 'none') {
                        $this->insertPointHistory($referral_code->id, $transaction->id, 1, 1, 'Transaksi Derma daripada kod');
                    }
                    //dd('stop');
                }
            } else {
                Log::error('Transaction save() returned false', [
                    'source' => 'mobile',
                    'attributes' => $transaction->getAttributes(),
                    'dirty' => $transaction->getDirty(),
                    'errors' => session()->get('errors'), // 如果有 validator
                ]);
                return view('errors.500');
            }
            //dd('fpxsellerOrderNo:'.$fpx_sellerOrderNo.'\nlength:'.strlen($fpx_sellerOrderNo),'fpx_sellerExOrderNo:'.$fpx_sellerExOrderNo.'\nlength:'.strlen($fpx_sellerOrderNo));
            //$private_key = '9BB6D047-2FB3-4B7A-9199-09441E7F4B0C';

            // dd($fpx_buyerEmail, $fpx_buyerName, $private_key, $fpx_txnAmount, $fpx_sellerExOrderNo, $fpx_sellerOrderNo);

            //dd($pos,$fpx_sellerOrderNo);
            $data = [
                'fpx_buyerEmail'      => $fpx_buyerEmail,
                'fpx_buyerName'       => $fpx_buyerName,
                'private_key'         => $private_key,
                'fpx_txnAmount'       => $fpx_txnAmount,
                'fpx_sellerExOrderNo' => $fpx_sellerExOrderNo,
                'fpx_sellerOrderNo'   => $fpx_sellerOrderNo,
                'getstudentfees'      => $getstudentfees,
                'getparentfees'       => $getparentfees,
            ];

            $url = $this->signRequestandGetPaymentUrl($data);
            return redirect()->away($url);
            // return view('directpay.index', compact(
            //     'fpx_buyerEmail',
            //     'fpx_buyerName',
            //     'private_key',
            //     'fpx_txnAmount',
            //     'fpx_sellerExOrderNo',
            //     'fpx_sellerOrderNo',
            //     'getstudentfees',
            //     'getparentfees'

            // ));
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }


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
    public function signRequestandGetPaymentUrl($data)
    {
        // 1. Construct the Payload Object
        // Mapping $data to the API's expected keys
        $payloadObj = [
            'SellerOrderNo' => $data['fpx_sellerOrderNo'], 
            'Amount'        => number_format((float)$data['fpx_txnAmount'], 2, '.', ''),
            'BuyerEmail'    => $data['fpx_buyerEmail'],
            'BuyerName'     => $data['fpx_buyerName'],
            // Use provided merchant ID or fallback to config/TEST001
            'MerchantId'    => $data['merchant_id'] ?? 'TEST001', 
            //'CallBackUrl'   => '{default}', // Update this
            'TimeStamp'     => Carbon::now()->toIso8601String(),
            'RequestId'     => Str::random(12),
        ];

        $payloadObj['MerchantId'] = 'TEST001';
        //$payloadObj['BuyerEmail'] = 'test@email.com';

        // 2. Convert to JSON String (Raw data to be signed)
        $jsonPayload = json_encode($payloadObj);
        //dd($jsonPayload);
        // 3. Generate RSA Signature
        // Ensure the private key is formatted correctly
        $privateKey = env('PRIM_PRIVATE_KEY');
       // dd(env('PRIM_PRIVATE_KEY'));
        // if (!str_contains($privateKey, 'BEGIN RSA PRIVATE KEY')) {
        //     $privateKey = "-----BEGIN RSA PRIVATE KEY-----\n" . 
        //                 wordwrap($privateKey, 64, "\n", true) . 
        //                 "\n-----END RSA PRIVATE KEY-----";
        // }

        $binarySignature = '';
        // Corresponds to: alg: "SHA256withRSA"
        openssl_sign($jsonPayload, $binarySignature, $privateKey, OPENSSL_ALGO_SHA256);
        $signatureBase64 = base64_encode($binarySignature);

        // 4. Prepare Request Body
        // The API expects the body to be the Base64 string of the JSON, wrapped in quotes.
        $base64Payload = base64_encode($jsonPayload);
        // json_encode the string to ensure it is sent as "eyJ..." (valid JSON string)
        $requestBody = json_encode($base64Payload);

       // dd($requestBody, $base64Payload);
        // 5. Send Request
        $response = Http::withoutVerifying()
        ->withHeaders([
            'X-MerchantId' => $payloadObj['MerchantId'],
            'X-Signature'  => $signatureBase64,
            'Content-Type' => 'application/json',
        ])
        ->withBody($requestBody, 'application/json')
        ->post('https://localhost:7129/api/v1/Pay/GetPaymentUrl');
        // ->post('https://sit.directpay.my/api/v1/Pay/GetPaymentUrl');
           
        $res =  $response->json();

      // 5. Handle Response or Throw Exception
        if (isset($res['status']) && $res['status'] === true) {
            return $res['payload']['paymentUrl'];
        }

        $errorMessage = $res['errorMessage'] ?? 'Unknown error occurred. Cannot proceed to FPX online banking';
        throw new \Exception("Failed to pay: " . $errorMessage);
    }

    public function getReferralCodeFromSource($requestReferralCode)
    {

        $own_code = DB::table('referral_code')
            ->where('user_id', Auth::id() ?? 0)
            ->first();
        $leader_code = DB::table('referral_code_member as rcm')
            ->join('referral_code as rc', 'rc.id', 'rcm.leader_referral_code_id')
            ->where('rcm.member_user_id', Auth::id() ?? 0)
            ->select('rc.code')
            ->first();

        $result = ['own_code' => "", 'code' => "", 'source' => 'none'];

        if ($own_code != null) {
            $result['own_code'] = $own_code->code;
        }

        if (!empty($requestReferralCode)) {
            if ($result['own_code'] != $requestReferralCode) {
                $result['code'] = $requestReferralCode;
                $result['source'] = 'request';
            }
        } else if (session()->has('referral_code') && !empty(session('referral_code'))) {
            $referral_code = session()->pull('referral_code');
            if ($referral_code != $result['own_code']) {
                $result['code'] = $referral_code;
                $result['source'] = 'session';
            }
        }

        if ($leader_code != null && $result['source'] == 'none') {
            $result['code'] = $leader_code->code;
            $result['source'] = 'leader';
            //$result = ['code' => $leader_code->code, 'source' => 'leader'];
        }




        //dd($result,$leader_code);
        return $result;
    }

    public function insertPointHistory($code_id, $transaction_id, $fromSubline, $point, $desc, $entity_json = null)
    {

        $insertMember = DB::table('referral_code_member')->where('leader_referral_code_id', $code_id)->where('member_user_id', Auth::id())->first();
        $member_id = $insertMember != null ? $insertMember->id : null;
        DB::table('point_history')
            ->insert([
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'referral_code_id' => $code_id,
                'transaction_id' => $transaction_id,
                'isDebit' => 0,
                'fromSubline' => $fromSubline,
                'status' => 0,
                'points' => $point,
                'desc' => $desc,
                'member_id' => $member_id,
                'entity_id' => $entity_json

            ]);
    }
    // callback for FPX
    public function paymentStatus(Request $request)
    {
        $case = explode("_", $request->Fpx_SellerOrderNo);

        if ($request->fpx_debitAuthCode == '00') {
            switch ($case[0]) {
                case 'School Fees':
                    break;

                case 'Donation':
                    Transaction::where('nama', '=', $request->Fpx_SellerOrderNo)
                        ->update(['transac_no' => $request->Fpx_FpxTxnId, 'status' => 'Success', 'amount' => $request->TransactionAmount, 'description' => $request->Fpx_SellerExOrderNo]);

                    $request->fpx_debitAuthCode == "00" ? $status = "Success" : $status = "Failed/Pending";
                    \Log::channel('PRIM_transaction')->info("Transaction Callback : " .  $request->Fpx_SellerExOrderNo . " , " . $status);

                    // $donation = $this->donation->getDonationByTransactionName($request->fpx_sellerExOrderNo);
                    // $organization = $this->organization->getOrganizationByDonationId($donation->id);
                    // $transaction = $this->transaction->getTransactionByName($request->fpx_sellerExOrderNo);

                    // Mail::to($transaction->email)->send(new DonationReceipt($donation, $transaction, $organization));
                    break;

                default:
                    return view('errors.500');
                    break;
            }
        } else {
            Transaction::where('nama', '=', $request->Fpx_SellerOrderNo)->update(['transac_no' => $request->Fpx_FpxTxnId, 'status' => 'Failed']);
        }
    }

    // callback for FPX
    public function directpayReceipt(Request $request)
    {
        $case = explode("_", $request->Fpx_SellerOrderNo);
        //return response()->json(['request'=>'success']);
        if ($request->Fpx_DebitAuthCode == '00') {
            // dd($case[0]);
            Transaction::where('nama', '=', $request->Fpx_SellerOrderNo)
                ->update(
                    [
                        'transac_no' => $request->Fpx_FpxTxnId,
                        'status' => 'Success',
                        'buyerBankId' => $request->Fpx_BuyerBankBranch,
                        'amount' => $request->TransactionAmount,
                        'description' => $request->Fpx_SellerExOrderNo
                    ]
                );


            return $this->updateTransaction($case[0], $request->Fpx_SellerOrderNo, true);
        } else {

            Transaction::where('nama', '=', $request->Fpx_SellerOrderNo)->update(['transac_no' => $request->Fpx_FpxTxnId, 'status' => 'Failed']);
            $this->failTransactionAction($case[0], $request->Fpx_SellerOrderNo);
            $user = Transaction::where('nama', '=', $request->Fpx_SellerOrderNo)->first();
            //gitdd($user,$request->Fpx_SellerOrderNo);
            //for mobile handle payment failed
            if ($user && strpos($user->nama, '_M_') !== false) {
                try {
                    $curl_options = [];

                    if (config('app.env') === 'local') {
                        $curl_options = [
                            CURLOPT_SSL_VERIFYHOST => 0,
                            CURLOPT_SSL_VERIFYPEER => 0,
                        ];
                    }

                    $options = array(
                        'cluster' => env('PUSHER_APP_CLUSTER'),
                        'useTLS' => true,
                        'curl_options' => $curl_options,
                    );

                    $pusher = new \Pusher\Pusher(
                        env('PUSHER_APP_KEY'),
                        env('PUSHER_APP_SECRET'),
                        env('PUSHER_APP_ID'),
                        $options
                    );

                    $data = [
                        'transactionId' => $user->id,
                        'status' => 'Failed'
                    ];

                    $pusher->trigger('payment-user-' . $user->user_id, 'payment.failed', $data);
                } catch (\Exception $e) {
                    Log::error("Pusher Manual Error: " . $e->getMessage());
                }
            }
            return view('fpx.transactionFailed', compact('request', 'user'));

            // Transaction::where('nama', '=', $request->fpx_sellerExOrderNo)->update(['transac_no' => $request->Fpx_FpxTxnId, 'status' => 'Failed']);
            // $user = Transaction::where('nama', '=', $request->fpx_sellerExOrderNo)->first();
            // return view('fpx.transactionFailed', compact('request', 'user'));
        }
    }


    public function ReceiptFees($transaction_id)
    {
        // parent user id

        // dd($transaction_id);
        $userid = DB::table("transactions")
            ->where('id', $transaction_id)
            ->select('user_id as id')
            ->first();

        $userid = $userid->id;

        $id = $transaction_id;

        // details parents
        $getparent = DB::table('users')
            ->where('id', $userid)
            ->first();

        // details transaction
        $get_transaction = Transaction::where('id', $id)->first();

        // details students by transactions
        $get_student = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization', 'class_organization.id', 'class_student.organclass_id')
            ->join('classes', 'classes.id', 'class_organization.class_id')
            ->join('student_fees_new', 'student_fees_new.class_student_id', '=', 'class_student.id')
            ->join('fees_new', 'fees_new.id', '=', 'student_fees_new.fees_id')
            ->join('fees_transactions_new', 'fees_transactions_new.student_fees_id', '=', 'student_fees_new.id')
            ->select('students.*', 'classes.nama as classname')
            ->distinct()
            ->orderBy('students.id')
            ->orderBy('fees_new.category')
            ->where('fees_transactions_new.transactions_id', $id)
            ->where('student_fees_new.status', 'Paid')
            ->get();

        // get category fees by transactions
        $get_category = DB::table('fees_new')
            ->join('student_fees_new', 'student_fees_new.fees_id', '=', 'fees_new.id')
            ->join('fees_transactions_new', 'fees_transactions_new.student_fees_id', '=', 'student_fees_new.id')
            ->join('class_student', 'class_student.id', '=', 'student_fees_new.class_student_id')
            ->join('students', 'students.id', '=', 'class_student.student_id')
            ->select('fees_new.category', 'students.id as studentid')
            ->distinct()
            ->orderBy('students.id')
            ->orderBy('fees_new.category')
            ->where('fees_transactions_new.transactions_id', $id)
            ->where('student_fees_new.status', 'Paid')
            ->get();

        // dd($get_category);

        // get fees
        $get_fees = DB::table('fees_new')
            ->join('student_fees_new', 'student_fees_new.fees_id', '=', 'fees_new.id')
            ->join('fees_transactions_new', 'fees_transactions_new.student_fees_id', '=', 'student_fees_new.id')
            ->join('class_student', 'class_student.id', '=', 'student_fees_new.class_student_id')
            ->join('students', 'students.id', '=', 'class_student.student_id')
            ->select('fees_new.*', 'students.id as studentid')
            ->orderBy('students.id')
            ->orderBy('fees_new.category')
            ->where('fees_transactions_new.transactions_id', $id)
            ->where('student_fees_new.status', 'Paid')
            ->get();

        // get transaction for fees category A
        $getfees_categoryA  = DB::table('fees_new')
            ->join('fees_new_organization_user', 'fees_new_organization_user.fees_new_id', '=', 'fees_new.id')
            ->join('organization_user', 'organization_user.id', '=', 'fees_new_organization_user.organization_user_id')
            ->select('fees_new.*')
            ->orderBy('fees_new.name')
            ->where('organization_user.user_id', $userid)
            ->where('organization_user.role_id', 6)
            ->where('organization_user.status', 1)
            ->where('fees_new_organization_user.status', 'Paid')
            ->where('fees_new_organization_user.transaction_id', $id)
            ->get();

        // $getfees_categoryA ? $getfees_categoryA = 1 : $getfees_categoryA = "";
        // dd(count($getfees_categoryA));
        if (count($get_category) != 0) {
            $oid = DB::table('fees_new')
                ->join('student_fees_new', 'student_fees_new.fees_id', '=', 'fees_new.id')
                ->join('fees_transactions_new', 'fees_transactions_new.student_fees_id', '=', 'student_fees_new.id')
                ->select('fees_new.organization_id')
                ->distinct()
                ->where('fees_transactions_new.transactions_id', $id)
                ->where('student_fees_new.status', 'Paid')
                ->first();

            $get_organization = DB::table('organizations')->where('id', $oid->organization_id)->first();
        }

        if (count($getfees_categoryA) != 0) {
            // dd($getfees_categoryA);
            $oid = DB::table('fees_new')
                ->join('fees_new_organization_user', 'fees_new_organization_user.fees_new_id', '=', 'fees_new.id')
                ->join('organization_user', 'organization_user.id', '=', 'fees_new_organization_user.organization_user_id')
                ->select('fees_new.organization_id')
                ->distinct()
                ->where('organization_user.user_id', $userid)
                ->where('organization_user.role_id', 6)
                ->where('organization_user.status', 1)
                ->where('fees_new_organization_user.status', 'Paid')
                ->where('fees_new_organization_user.transaction_id', $id)
                ->first();

            $get_organization = DB::table('organizations')->where('id', $oid->organization_id)->first();
        }
        // dd($get_fees);

        //for mobile payment use different receipt
        if (strpos($get_transaction->nama, '_M_') !== false) {
            return view('fee.pay.newreceipt2', compact(
                'getparent',
                'get_transaction',
                'get_student',
                'get_category',
                'get_fees',
                'getfees_categoryA',
                'get_organization'
            ));
        }
        return view('fee.pay.receipt', compact('getparent', 'get_transaction', 'get_student', 'get_category', 'get_fees', 'getfees_categoryA', 'get_organization'));
    }

    public function viewReceiptFees($transaction_id)
    {
        // parent user id

        // dd($transaction_id);
        $userid = DB::table("transactions")
            ->where('id', $transaction_id)
            ->select('user_id as id')
            ->first();

        $userid = $userid->id;

        $id = $transaction_id;

        // details parents
        $getparent = DB::table('users')
            ->where('id', $userid)
            ->first();

        // details transaction
        $get_transaction = Transaction::where('id', $id)->first();

        // details students by transactions
        $get_student = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization', 'class_organization.id', 'class_student.organclass_id')
            ->join('classes', 'classes.id', 'class_organization.class_id')
            ->join('student_fees_new', 'student_fees_new.class_student_id', '=', 'class_student.id')
            ->join('fees_new', 'fees_new.id', '=', 'student_fees_new.fees_id')
            ->join('fees_transactions_new', 'fees_transactions_new.student_fees_id', '=', 'student_fees_new.id')
            ->select('students.*', 'classes.nama as classname')
            ->distinct()
            ->orderBy('students.id')
            ->orderBy('fees_new.category')
            ->where('fees_transactions_new.transactions_id', $id)
            ->where('student_fees_new.status', 'Paid')
            ->get();

        // get category fees by transactions
        $get_category = DB::table('fees_new')
            ->join('student_fees_new', 'student_fees_new.fees_id', '=', 'fees_new.id')
            ->join('fees_transactions_new', 'fees_transactions_new.student_fees_id', '=', 'student_fees_new.id')
            ->join('class_student', 'class_student.id', '=', 'student_fees_new.class_student_id')
            ->join('students', 'students.id', '=', 'class_student.student_id')
            ->select('fees_new.category', 'students.id as studentid')
            ->distinct()
            ->orderBy('students.id')
            ->orderBy('fees_new.category')
            ->where('fees_transactions_new.transactions_id', $id)
            ->where('student_fees_new.status', 'Paid')
            ->get();

        // dd($get_category);

        if ($get_category == "Kategori Berulang") {
            // get fees for Kategori Berulang
            $get_fees = DB::table('fees_new')
                ->join('student_fees_new', 'student_fees_new.fees_id', '=', 'fees_new.id')
                ->join('fees_transactions_new', 'fees_transactions_new.student_fees_id', '=', 'student_fees_new.id')
                ->join('class_student', 'class_student.id', '=', 'student_fees_new.class_student_id')
                ->join('students', 'students.id', '=', 'class_student.student_id')
                ->join('fees_recurring as fr', 'fr.student_fees_new_id', '=', 'student_fees_new.id')
                ->select('fees_new.*', 'students.id as studentid', 'fr.finalAmount as fr_finalamount', 'fr.desc as fr_desc')
                ->orderBy('students.id')
                ->orderBy('fees_new.category')
                ->where('fees_transactions_new.transactions_id', $id)
                ->where('student_fees_new.status', 'Paid')
                ->get();
        } else {
            // get fees
            $get_fees = DB::table('fees_new')
                ->join('student_fees_new', 'student_fees_new.fees_id', '=', 'fees_new.id')
                ->join('fees_transactions_new', 'fees_transactions_new.student_fees_id', '=', 'student_fees_new.id')
                ->join('class_student', 'class_student.id', '=', 'student_fees_new.class_student_id')
                ->join('students', 'students.id', '=', 'class_student.student_id')
                ->select('fees_new.*', 'students.id as studentid')
                ->orderBy('students.id')
                ->orderBy('fees_new.category')
                ->where('fees_transactions_new.transactions_id', $id)
                ->where('student_fees_new.status', 'Paid')
                ->get();
        }

        // get transaction for fees category A
        $getfees_categoryA  = DB::table('fees_new')
            ->join('fees_new_organization_user', 'fees_new_organization_user.fees_new_id', '=', 'fees_new.id')
            ->join('organization_user', 'organization_user.id', '=', 'fees_new_organization_user.organization_user_id')
            ->select('fees_new.*')
            ->orderBy('fees_new.name')
            ->where('organization_user.user_id', $userid)
            ->where('organization_user.role_id', 6)
            ->where('organization_user.status', 1)
            ->where('fees_new_organization_user.status', 'Paid')
            ->where('fees_new_organization_user.transaction_id', $id)
            ->get();

        // $getfees_categoryA ? $getfees_categoryA = 1 : $getfees_categoryA = "";
        // dd(count($getfees_categoryA));
        if (count($get_category) != 0) {
            $oid = DB::table('fees_new')
                ->join('student_fees_new', 'student_fees_new.fees_id', '=', 'fees_new.id')
                ->join('fees_transactions_new', 'fees_transactions_new.student_fees_id', '=', 'student_fees_new.id')
                ->select('fees_new.organization_id')
                ->distinct()
                ->where('fees_transactions_new.transactions_id', $id)
                ->where('student_fees_new.status', 'Paid')
                ->first();

            $get_organization = DB::table('organizations')->where('id', $oid->organization_id)->first();
        }

        if (count($getfees_categoryA) != 0) {
            // dd($getfees_categoryA);
            $oid = DB::table('fees_new')
                ->join('fees_new_organization_user', 'fees_new_organization_user.fees_new_id', '=', 'fees_new.id')
                ->join('organization_user', 'organization_user.id', '=', 'fees_new_organization_user.organization_user_id')
                ->select('fees_new.organization_id')
                ->distinct()
                ->where('organization_user.user_id', $userid)
                ->where('organization_user.role_id', 6)
                ->where('organization_user.status', 1)
                ->where('fees_new_organization_user.status', 'Paid')
                ->where('fees_new_organization_user.transaction_id', $id)
                ->first();

            $get_organization = DB::table('organizations')->where('id', $oid->organization_id)->first();
        }
        // dd($get_fees);

        return view('fee.pay.view-receipt', compact('getparent', 'get_transaction', 'get_student', 'get_category', 'get_fees', 'getfees_categoryA', 'get_organization'));
    }


    public function adminTestFpx($transaction_id)
    {


        $transactions = DB::table('transactions')
            ->where('id', $transaction_id)
            ->get();

        foreach ($transactions as $transaction) {
            //old method()
            $fpx_sellerOrderNo = $transaction->nama;
            try {
                $response_value = $this->getTransactionInfo($transaction->id);
                if (!isset($response_value['fpx_DebitAuthCode'])) {
                    continue;
                }

                $fpx_productDesc = explode("_", $transaction->nama)[0];

                if ($response_value['fpx_DebitAuthCode'] == '00') {

                    Transaction::where('nama', '=', $fpx_sellerOrderNo)->update(
                        [
                            'status' => 'Success',
                            'amount' => $response_value['transactionAmount'],
                            'description' => $response_value['fpx_SellerExOrderNo'],
                            'transac_no' => $response_value['fpx_FpxTxnId']
                        ]

                    );

                    $this->updateTransaction($fpx_productDesc, $fpx_sellerOrderNo, false);
                } else {
                    Transaction::where('nama', '=', $fpx_sellerOrderNo)->update(['status' => 'Failed']);
                    //Transaction::where('nama', '=', $request->Fpx_SellerOrderNo)->update(['transac_no' => $request->Fpx_FpxTxnId, 'status' => 'Failed']);
                    $this->failTransactionAction($fpx_productDesc, $fpx_sellerOrderNo);
                }
            } catch (\Throwable $th) {
                echo 'Error :', ($th->getMessage());
            }
        }

        \Log::info("Update Transaction Command Run Successfully!");
    }

    public function getTransactionInfo($transaction_id)
    {

        $transaction = Transaction::find($transaction_id);
        $fpx_sellerOrderNo = $transaction->nama;

        $fpx_productDesc = explode("_", $transaction->nama)[0];

        if ($fpx_productDesc == "Donation") {
            $organ = DB::table("transactions as t")
                ->leftJoin('donation_transaction as dt', 't.id', 'dt.transaction_id')
                ->leftJoin('donations as d', 'd.id', 'dt.donation_id')
                ->leftJoin('donation_organization as do', 'do.donation_id', 'd.id')
                ->leftJoin('organizations as o', 'o.id', 'do.organization_id')
                ->select('o.private_key')
                ->where('t.id', $transaction->id)
                ->first();
        } else if ($fpx_productDesc == "School") {
            $organ = DB::table('organizations as o')
                ->leftJoin('fees_new as fn', 'o.id', 'fn.organization_id')
                ->leftJoin('fees_new_organization_user as fou', 'fou.fees_new_id', 'fn.id')
                ->where('fou.transaction_id', $transaction->id)
                ->select('o.private_key')
                ->first();

            if ($organ == null) {
                $organ = DB::table('organizations as o')
                    ->leftJoin('fees_new as fn', 'o.id', 'fn.organization_id')
                    ->leftJoin('student_fees_new as sfn', 'sfn.fees_id', 'fn.id')
                    ->leftJoin('fees_transactions_new as ftn', 'ftn.student_fees_id', 'sfn.id')
                    ->where('ftn.transactions_id', $transaction->id)
                    ->select('o.private_key')
                    ->first();
                //dd($organ);
            }
        } else if ($fpx_productDesc == "Merchant" || $fpx_productDesc == "Koperasi") {
            $order = PgngOrder::where('transaction_id', $transaction->id)->first();

            $organ = Organization::find($order->organization_id);
        } else if ($fpx_productDesc == "Homestay") {

            $booking = Booking::where('transactionid', $transaction->id)
                ->orWhere('transaction_balance_id', $transaction->id)
                ->first();
            $room = Room::find($booking->roomid);

            $organ = Organization::find($room->homestayid);
        } else if ($fpx_productDesc == "OrderS") {
            //$orders = Order::where('transaction_id', '=', $transaction->id)->first();
            $orders = DB::table('order_cart')->where('transactions_id', '=', $transaction->id)->first();

            $organ = Organization::find($orders->organ_id);
        } else if ($fpx_productDesc == "Request") {
            $organ = Organization::find(168);
        }
        if ($organ == null) {
            return null;
        }
        $privateKey = $organ->private_key;

        $url = "https://directpay.my/api/fpx/GetTransactionInfo";

        $params = [
            'PrivateKey' => $privateKey,

            'Fpx_SellerOrderNo' => $fpx_sellerOrderNo,
        ];

        $url .= '?' . http_build_query($params);
        //dd($url);


        $ch = curl_init($url);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute cURL session and get the response
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        }

        // Close cURL session
        curl_close($ch);
        $resultArray = json_decode($response, true);

        if (!isset($resultArray['fpx_SellerOrderNo'])) {
            return "Error: " . $url;
        }

        return $resultArray;
    }

    public function failTransactionAction($fpx_productDesc, $fpx_sellerOrderNo)
    {
        $transaction = Transaction::where('nama', '=', $fpx_sellerOrderNo)->first();
        if ($transaction->status != 'Failed') {
            return;
        }

        switch ($fpx_productDesc) {
            case 'Request':
                $update = DB::table('code_requests')->where('transaction_id', $transaction->id)->update([
                    'status' => 'Payment Failed',

                ]);
        }
    }
    public function updateTransaction($fpx_productDesc, $fpx_sellerOrderNo, $returnView)
    {
        $transaction = Transaction::where('nama', '=', $fpx_sellerOrderNo)->first();
        if ($transaction->status != 'Success') {
            return;
        }

        //create event for mobile payment success pusher
        if (strpos($transaction->nama, '_M_') !== false) {
            try {

                $curl_options = [];

                if (config('app.env') === 'local') {
                    $curl_options = [
                        CURLOPT_SSL_VERIFYHOST => 0,
                        CURLOPT_SSL_VERIFYPEER => 0,
                    ];
                }

                $options = array(
                    'cluster' => env('PUSHER_APP_CLUSTER'),
                    'useTLS' => true,
                    'curl_options' => $curl_options,
                );

                $pusher = new \Pusher\Pusher(
                    env('PUSHER_APP_KEY'),
                    env('PUSHER_APP_SECRET'),
                    env('PUSHER_APP_ID'),
                    $options
                );

                $data = [
                    'userId' => $transaction->user_id,
                    'transactionId' => $transaction->id
                ];

                $pusher->trigger('payment-user-' . $transaction->user_id, 'payment.success', $data);
            } catch (\Exception $e) {
                Log::error("Pusher Manual Error: " . $e->getMessage());
            }
        }

        $transaction = Transaction::where('nama', '=', $fpx_sellerOrderNo)->first();
        switch ($fpx_productDesc) {
            case 'School':
                $list_student_fees_id_by_student = DB::table('student_fees_new')
                    ->join('fees_transactions_new', 'fees_transactions_new.student_fees_id', '=', 'student_fees_new.id')
                    ->join('transactions', 'transactions.id', '=', 'fees_transactions_new.transactions_id')
                    ->select('student_fees_new.id as student_fees_id', 'student_fees_new.class_student_id')
                    ->where('transactions.id', $transaction->id)
                    ->get()
                    ->groupBy('class_student_id');

                $list_parent_fees_id  = DB::table('fees_new')
                    ->join('fees_new_organization_user', 'fees_new_organization_user.fees_new_id', '=', 'fees_new.id')
                    ->join('organization_user', 'organization_user.id', '=', 'fees_new_organization_user.organization_user_id')
                    ->select('fees_new_organization_user.*')
                    ->orderBy('fees_new.category')
                    ->where('organization_user.user_id', $transaction->user_id)
                    ->where('organization_user.role_id', 6)
                    ->where('organization_user.status', 1)
                    ->where('fees_new_organization_user.transaction_id', $transaction->id)
                    ->get();


                foreach ($list_student_fees_id_by_student as $list_student_fees_id) {
                    for ($i = 0; $i < count($list_student_fees_id); $i++) {

                        // ************************* update student fees status fees by transactions *************************
                        $res  = DB::table('student_fees_new')
                            ->where('id', $list_student_fees_id[$i]->student_fees_id)
                            ->update(['status' => 'Paid']);

                        // ************************* check the student if have still debt *************************

                        if ($i == count($list_student_fees_id) - 1) {
                            $check_debt = DB::table('students')
                                ->join('class_student', 'class_student.student_id', '=', 'students.id')
                                ->join('student_fees_new', 'student_fees_new.class_student_id', '=', 'class_student.id')
                                ->select('students.*')
                                ->where('class_student.id', $list_student_fees_id[$i]->class_student_id)
                                ->where('student_fees_new.status', 'Debt')
                                ->count();


                            // ************************* update status fees for student if all fees completed paid*************************

                            if ($check_debt == 0) {
                                DB::table('class_student')
                                    ->where('id', $list_student_fees_id[$i]->class_student_id)
                                    ->update(['fees_status' => 'Completed']);
                            }
                        }
                    }
                }


                for ($i = 0; $i < count($list_parent_fees_id); $i++) {

                    // ************************* update status fees for parent *************************
                    DB::table('fees_new_organization_user')
                        ->where('id', $list_parent_fees_id[$i]->id)
                        ->update([
                            'status' => 'Paid'
                        ]);

                    // ************************* check the parent if have still debt *************************
                    if ($i == count($list_parent_fees_id) - 1) {
                        $org = DB::table('organization_user as ou')->where('ou.user_id', $transaction->user_id)->get();
                        foreach ($org as $o) {
                            $check_debt = DB::table('organization_user')
                                ->join('fees_new_organization_user', 'fees_new_organization_user.organization_user_id', '=', 'organization_user.id')
                                ->where('organization_user.user_id', $transaction->user_id)
                                ->where('organization_user.organization_id', $o->organization_id)
                                ->where('organization_user.role_id', 6)
                                ->where('organization_user.status', 1)
                                ->where('fees_new_organization_user.status', 'Debt')
                                ->count();

                            // ************************* update status fees for organization user (parent) if all fees completed paid *************************

                            if ($check_debt == 0) {
                                DB::table('organization_user')
                                    ->where('user_id', $transaction->user_id)
                                    ->where('role_id', 6)
                                    ->where('status', 1)
                                    ->update(['fees_status' => 'Completed']);
                            }
                        }
                    }
                }

                if ($returnView)
                    return $this->ReceiptFees($transaction->id);
                break;

            case 'Donation':

                $donation = $this->donation->getDonationByTransactionName($fpx_sellerOrderNo);

                $organization = $this->organization->getOrganizationByDonationId($donation->id);
                $transaction = $this->transaction->getTransactionByName($fpx_sellerOrderNo);
                DonationStreak::updateStreak($transaction->user_id, $transaction->id);
                if ($transaction->username != "PENDERMA TANPA NAMA" && $transaction->email != NULL) {
                    Mail::to($transaction->email)->send(new DonationReceipt($donation, $transaction, $organization));
                }

                if ($returnView)
                    return view('receipt.index', compact('donation', 'organization', 'transaction'));

                break;

            case 'Food':

                $userid = $transaction->user_id;

                $order = Order::where('transaction_id', '=', $transaction->id)->first();
                $user = User::find($transaction->user_id);
                $organization = Organization::find($order->organ_id);

                $order_dishes = DB::table('order_dish as od')
                    ->leftJoin('dishes as d', 'd.id', 'od.dish_id')
                    ->leftJoin('orders as o', 'o.id', 'od.order_id')
                    ->where('od.order_id', $order->id)
                    ->orderBy('d.name')
                    ->get();

                Mail::to($transaction->email)->send(new OrderReceipt($order, $organization, $transaction, $user));
                if ($returnView)
                    return view('order.receipt', compact('order_dishes', 'organization', 'transaction', 'user'));

                break;

            case 'Merchant':

                $order = PgngOrder::where('transaction_id', $transaction->id)->first();
                $updateQuantity = $order->status != 'Paid';
                $order->status = "Paid";
                $order->updated_at = Carbon::now();
                $order->save();

                $organization = Organization::find($order->organization_id);
                $user = User::find($order->user_id);

                $relatedProductOrder = DB::table('product_order')
                    ->where([
                        ['pgng_order_id', $order->id],
                        ['deleted_at', NULL]
                    ])
                    ->select('product_item_id as itemId', 'quantity')
                    ->get();
                if ($updateQuantity) {
                    foreach ($relatedProductOrder as $item) {
                        $relatedItem = DB::table('product_item')
                            ->where('id', $item->itemId);

                        $relatedItemQuantity = $relatedItem->first()->quantity_available;

                        $newQuantity = intval($relatedItemQuantity - $item->quantity);

                        if ($newQuantity <= 0) {
                            $relatedItem
                                ->update([
                                    'quantity_available' => 0,
                                    'type' => 'no inventory',
                                    'status' => 0,
                                ]);
                        } else {
                            $relatedItem
                                ->update([
                                    'quantity_available' => $newQuantity
                                ]);
                        }
                    }
                }

                $item = DB::table('product_order as po')
                    ->join('product_item as pi', 'po.product_item_id', 'pi.id')
                    ->where([
                        ['po.pgng_order_id', $order->id],
                        ['po.deleted_at', NULL],
                        ['pi.deleted_at', NULL],
                    ])
                    ->select('pi.name', 'po.quantity', 'pi.price')
                    ->get();

                Mail::to($user->email)->send(new MerchantOrderReceipt($order, $organization, $transaction, $user));
                Mail::to($organization->email)->send(new MerchantOrderReceipt($order, $organization, $transaction, $user));

                if ($returnView)
                    return view('merchant.receipt', compact('order', 'item', 'organization', 'transaction', 'user'));

                break;

            case 'Koperasi':

                $order = PgngOrder::where('transaction_id', $transaction->id)->first();


                $pgngOrder = PgngOrder::where('transaction_id', $transaction->id)->first();
                $updateQuantity = $pgngOrder->status != "2";
                $pgngOrder->status = 2;
                $pgngOrder->created_at = now();
                $pgngOrder->updated_at = now();
                $pgngOrder->save();

                $organization = Organization::find($order->organization_id);
                $user = User::where('id', '=', $order->user_id)->first();

                $relatedProductOrder = DB::table('product_order')
                    ->where('pgng_order_id', $order->id)
                    ->select('product_item_id as itemId', 'quantity')
                    ->get();

                if ($updateQuantity) {
                    foreach ($relatedProductOrder as $item) {
                        $relatedItem = DB::table('product_item')
                            ->where('id', $item->itemId);

                        $relatedItemQuantity = $relatedItem->first()->quantity_available;

                        $newQuantity = intval($relatedItemQuantity - $item->quantity);

                        if ($newQuantity <= 0) {
                            $relatedItem
                                ->update([
                                    'quantity_available' => 0,
                                    'status' => 0
                                ]);
                        } else {
                            $relatedItem
                                ->update([
                                    'quantity_available' => $newQuantity
                                ]);
                        }
                        //dd($relatedItem);
                    }
                }


                $item = DB::table('product_order as po')
                    ->join('product_item as pi', 'po.product_item_id', 'pi.id')
                    ->where('po.pgng_order_id', $order->id)
                    ->select('pi.name', 'po.quantity', 'pi.price')
                    ->get();

                Mail::to($user->email)->send(new MerchantOrderReceipt($order, $organization, $transaction, $user));

                if ($returnView) {
                    return view('merchant.receipt', compact('order', 'item', 'organization', 'transaction', 'user'));
                }


                break;

            case 'Homestay':

                // update booking table
                // check whether is paying for deposit/full or balance
                $booking = Booking::where('transactionid', $transaction->id)
                    ->orWhere('transaction_balance_id', $transaction->id)
                    ->first();

                if ($booking->deposit_amount > 0) {
                    if ($booking->status == "Deposited") { // for paying balance
                        $booking->status = "Balance Paid";
                        $booking->updated_at = Carbon::now();
                        $booking->save();
                    } else { //paying deposit
                        $booking->status = "Deposited";
                        $booking->updated_at = Carbon::now();
                        $booking->save();
                    }
                } else {
                    // for full payment
                    $booking->status = "Booked";
                    $booking->updated_at = Carbon::now();
                    $booking->save();
                }

                $userid = $transaction->user_id;

                $room = Room::find($booking->roomid);
                $user = User::find($transaction->user_id);
                $organization = Organization::find($room->homestayid);

                $booking_order = Organization::join('rooms', 'organizations.id', '=', 'rooms.homestayid')
                    ->join('bookings', 'rooms.roomid', '=', 'bookings.roomid')
                    ->where('bookings.bookingid', $booking->bookingid) // Filter by the selected homestay
                    ->select('organizations.id', 'organizations.nama', 'organizations.address', 'rooms.roomid', 'rooms.roomname', 'rooms.details', 'rooms.roompax', 'rooms.price', 'bookings.bookingid', 'bookings.checkin', 'bookings.checkout', 'bookings.totalprice', 'bookings.discount_received', 'bookings.increase_received', 'bookings.booked_rooms', 'bookings.deposit_amount', 'bookings.status')
                    ->get();

                if ($transaction->email != NULL) {
                    Mail::to($transaction->email)->send(new HomestayReceipt($room, $booking, $organization, $transaction, $user)); //mail to customer
                }
                Mail::to($organization->email)->send(new HomestayReceipt($room, $booking, $organization, $transaction, $user)); //mail to homestay admin
                if ($returnView)
                    return view('homestay.receipt', compact('room', 'booking_order', 'organization', 'transaction', 'user'));
                break;

            case 'Grab Student':

                $userid = $transaction->user_id;

                $booking = Grab_Booking::where('transactionid', '=', $transaction->id)->first();
                $destination = Destination_Offer::find($booking->id_destination_offer);
                $user = User::find($transaction->user_id);
                $grab = Grab_Student::find($destination->id_grab_student);
                $organization = Organization::find($grab->id_organizations);

                $grab_booking = Organization::join('grab_students', 'organizations.id', '=', 'grab_students.id_organizations')
                    ->join('destination_offers', 'grab_students.id', '=', 'destination_offers.id_grab_student')
                    ->join('grab_bookings', 'destination_offers.id', '=', 'grab_bookings.id_destination_offer')
                    ->where('grab_bookings.id', $booking->id)
                    ->select('destination_offers.pick_up_point', 'destination_offers.destination_name', 'destination_offers.available_time', 'grab_students.car_brand', 'grab_students.car_name', 'grab_students.car_registration_num', 'grab_students.number_of_seat')
                    ->get();


                if ($transaction->email != NULL) {
                    Mail::to($transaction->email)->send(new ResitBayaranGrab($booking, $user));
                }

                $result = DB::table('grab_bookings')
                    ->where('id', $booking->id)
                    ->update([
                        'status' => "PAID"
                    ]);

                if ($returnView)
                    return view('grab.resitbayaran', compact('grab_booking', 'booking', 'user'));

                break;
            case 'Bus':

                $userid = $transaction->user_id;

                $booking = Bus_Booking::where('transactionid', '=', $transaction->id)->first();
                $bus = Bus::find($booking->id_bus);
                $user = User::find($transaction->user_id);
                $organization = Organization::find($bus->id_organizations);

                $bus_booking = Organization::join('buses', 'organizations.id', '=', 'buses.id_organizations')
                    ->join('bus_bookings', 'buses.id', '=', 'bus_bookings.id_bus')
                    ->where('bus_bookings.id', $booking->id)
                    ->select('bus_bookings.id as bookid', 'buses.bus_registration_number', 'buses.booked_seat', 'buses.available_seat', 'buses.trip_number', 'buses.bus_depart_from', 'buses.bus_destination', 'buses.departure_time', 'buses.departure_date', 'buses.price_per_seat')
                    ->get();

                if ($transaction->email != NULL) {
                    Mail::to($transaction->email)->send(new ResitBayaranBus($booking, $user));
                }

                $result = DB::table('bus_bookings')
                    ->where('id', $booking->id)
                    ->update([
                        'status' => "PAID"
                    ]);

                if ($returnView)
                    return view('bus.resitbayaran', compact('bus_booking', 'booking', 'user'));

                break;
            case 'OrderS':
                $userid = $transaction->user_id;

                $updateQuantity = DB::table('order_cart')
                    ->where('transactions_id', $transaction->id)
                    ->where('order_status', '<>', 'cart-payment-completed')
                    ->exists();

                $result = DB::table('order_cart')
                    ->where('transactions_id', $transaction->id)
                    ->update([
                        'order_status' => "cart-payment-completed",
                        'updated_at' => Carbon::now()
                    ]);

                //dd($result);

                $user = DB::table('users')
                    ->where('id', $transaction->user_id)
                    ->first();

                $order_cart = DB::table('order_cart')
                    ->where('transactions_id', $transaction->id)
                    ->first();

                $organization = DB::table('organizations')
                    ->where('id', $order_cart->organ_id)
                    ->first();

                $order_available_dish = DB::table('order_available_dish as oad')
                    ->leftjoin('order_available as ou', 'oad.order_available_id', '=', 'ou.id')
                    ->leftjoin('dishes as d', 'ou.dish_id', '=', 'd.id')
                    ->where('oad.order_cart_id', $order_cart->id)
                    ->select('*', 'oad.quantity as oad_quantity', 'oad.id as oad_id', 'oad.quantity as oad_quantity')
                    ->get();


                foreach ($order_available_dish as $oad) {
                    DB::table('order_available_dish')
                        ->where('id', $oad->oad_id)
                        ->update([
                            'delivery_status' => "order-preparing"
                        ]);
                    if ($updateQuantity) {
                        DB::table('order_available')
                            ->where('id', $oad->order_available_id)
                            ->decrement('quantity', $oad->oad_quantity);
                    }
                }
                if ($returnView)
                    return view('orders.mobile.receipt', compact('transaction', 'user', 'order_cart', 'organization', 'order_available_dish'));

                break;
            case 'Request':
                $update = DB::table('code_requests')->where('transaction_id', $transaction->id)->update([
                    'status' => 'Pending Helper',

                ]);


                $codeRequest = DB::table('code_requests')->where('transaction_id', $transaction->id)->first();
                //dd($codeRequest,$transaction);
                $details = DB::table('code_requests as cr')
                    ->join('code_language as cl', 'cl.id', 'cr.language_id')
                    ->join('code_package as cp', 'cp.id', 'cr.package_id')
                    ->join('transactions as t', 't.id', 'cr.transaction_id')
                    ->where('cr.id', $codeRequest->id)
                    ->select('cl.name as language_name', 'cp.name as package_name', 't.transac_no')
                    ->first();
                if ($update) {
                    Mail::to($codeRequest->email)->send(new SHelperReceipt($codeRequest, $details));
                    $count = DB::table('code_requests')->where('status', 'Pending Helper')->count();
                    $helpers = DB::table('users as u')
                        ->join('model_has_roles as m', 'm.model_id', 'u.id')
                        ->where('role_id', 23) //the helper role is 23
                        ->pluck('u.email')
                        ->toArray();

                    if (!empty($helpers)) {
                        // Send SHelperReminder to the first helper and CC the rest, excluding the first
                        Mail::to(array_shift($helpers)) // Sends to the first helper
                            ->cc($helpers)              // CCs the remaining helpers
                            ->send(new SHelperReminder($count));
                    }

                    //Mail::to($helpers[0])->cc($helpers)->send(new SHelperReminder($count));


                    // Update the email_sent field
                    $sent = DB::table('code_requests')->where('id', $codeRequest->id)->update([
                        'email_sent' => 1,
                        'created_at' => now(),
                        'updated_at' => now()

                    ]);
                }
                //dd($codeRequest);
                if ($returnView)
                    return view('code_request.receipt', compact('codeRequest', 'details'));

                break;

            default:
                if ($returnView)
                    return view('errors.500');
                break;
        }
        if ($returnView)
            return view('errors.500');
    }

    public function updateDonationStreak() {}

    public function handle()
    {
        $type = request()->query('type');
        //comment only
        set_time_limit(1500);

        $transactions = DB::table('transactions')
            ->whereIn('status', ['Pending', 'Failed'])
            ->whereBetween('datetime_created', [now()->subDays(2), now()])
            ->get();

        if ($type == 'all') {
            $transactions = DB::table('transactions')
                ->whereIn('status', ['Pending', 'Failed'])
                ->orderByDesc('id')
                ->get();
        }

        echo 'Start Handle :', "<br>";

        foreach ($transactions as $transaction) {
            //old method()

            echo 'Start transaction :', $transaction->id, "<br>";

            $fpx_sellerOrderNo = $transaction->nama;
            try {
                $response_value = $this->getTransactionInfo($transaction->id);
                //if invalid params -> no record in direct pay
                if (is_string($response_value)) {
                    if (strpos($response_value, 'Error') === 0) {
                        Transaction::where('nama', '=', $fpx_sellerOrderNo)->update(['status' => 'Failed']);
                        echo 'Failed :', $transaction->id;
                        continue;
                    }
                }

                if (!isset($response_value['fpx_DebitAuthCode']) || $response_value['status'] == "Pending") {
                    echo 'transaction skip <br>';
                    continue;
                }

                if ($response_value['fpx_DebitAuthCode'] == '00') {
                    $fpx_productDesc = explode("_", $transaction->nama)[0];

                    Transaction::where('nama', '=', $fpx_sellerOrderNo)->update(
                        [
                            'status' => 'Success',
                            'amount' => $response_value['transactionAmount'],
                            'description' => $response_value['fpx_SellerExOrderNo'],
                            'transac_no' => $response_value['fpx_FpxTxnId']
                        ]

                    );

                    $this->updateTransaction($fpx_productDesc, $fpx_sellerOrderNo, false);

                    echo 'Success :', $transaction->id, "<br>";
                } else {
                    Transaction::where('nama', '=', $fpx_sellerOrderNo)->update(['status' => 'Failed']);
                    echo 'Failed :', $transaction->id;
                }
            } catch (\Throwable $th) {
                echo 'Error :', ($th->getMessage()), $transaction->id, "<br>";
                \Log::info("Update Transaction Command Run Error :, " . $th->getMessage());
            }
        }

        \Log::info("Update Transaction Command Run Successfully!");
    }


    public function testcallback()
    {
        return view('directpay.testcallback');
    }
}
