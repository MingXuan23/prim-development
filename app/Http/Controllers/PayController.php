<?php

namespace App\Http\Controllers;

use App\Models\Fees_Transaction;
use App\Http\Controllers\Controller;
use App\Models\Detail;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Process\Process;

class PayController extends Controller
{
    public function index(Request $request)
    {
        $feesid     = $request->id;
        $getfees    = DB::table('fees')->where('id', $feesid)->first();

        $getcat = DB::table('fees')
            ->join('fees_details', 'fees_details.fees_id', '=', 'fees.id')
            ->join('details', 'details.id', '=', 'fees_details.details_id')
            ->join('categories', 'categories.id', '=', 'details.category_id')
            ->distinct('categories.nama')
            ->select('categories.id as cid', 'categories.nama as cnama')
            ->where('fees.id', $feesid)
            ->orderBy('categories.id')
            ->get();

        $getdetail  = DB::table('fees')
            ->join('fees_details', 'fees_details.fees_id', '=', 'fees.id')
            ->join('details', 'details.id', '=', 'fees_details.details_id')
            ->join('categories', 'categories.id', '=', 'details.category_id')
            ->select('categories.id as cid', 'categories.nama as cnama', 'details.nama as dnama', 'details.quantity as quantity', 'details.price as price', 'details.totalamount as totalamount', 'details.id as did')
            ->where('fees.id', $feesid)
            ->orderBy('details.nama')
            ->get();
        return view('pentadbir.fee.pay', compact('getfees', 'getcat', 'getdetail'));
    }

    public function donateindex(Request $request)
    {
        $donateid   = $request->id;
        $getdonate  = DB::table('donations')->where('id', $donateid)->first();

        // dd($getdonate);
        return view('paydonate.pay', compact('getdonate'));
    }

    public function paymentProcess(Request $request)
    {
        \Stripe\Stripe::setApiKey('sk_test_51I6AHSI3fJ2mpqjYO5tSNuiCIdR1dw7Bh2Lqgh0u8xPkVyCnyOEELQjMBPUTroSEH7DWCo6WYLTvMO2Vd58hu5gO00DQ1uX9Fj');

        $bname      = $request->bname;
        $ttlpay      = $request->ttlpay;

        $amount = $ttlpay * 100;

        $YOUR_DOMAIN = url('/');
        $checkout_session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'myr',
                    'unit_amount' => $amount,
                    'product_data' => [
                        'name' => $bname,
                        'images' => [$YOUR_DOMAIN . "/assets/images/logo/prim.svg"],
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/successpay',
            'cancel_url' => url()->previous(),
        ]);
        echo json_encode(['id' => $checkout_session->id]);
    }

    public function successPay()
    {
        return view('pentadbir.fee.successpay');
    }

    public function fpxIndex(Request $request)
    {
        // dd($request);
        // $user = Auth::id();
        if ($request->desc == 'Donation') {

            $fpx_buyerEmail = $request->email;
            $telno = $request->telno;
            $fpx_buyerName = $request->name;
            $fpx_sellerExOrderNo = $request->desc . "_" . date('YmdHis');
            // $fpx_buyerIban      = $request->name . "/" . $telno . "/" . $request->email;
        } else {
            $fpx_buyerEmail = "prim.utem@gmail.com";
            $telno = "";
            $fpx_buyerName = User::where('id', '=', Auth::id())->pluck('name')->first();
            $fpx_sellerExOrderNo = $request->desc . "_" . date('YmdHis');
            // $fpx_buyerIban      = "";
        }

        $fpx_msgType        = "AR";
        $fpx_msgToken       = "01";
        $fpx_sellerExId     = "EX00012323";
        $fpx_sellerTxnTime  = date('YmdHis');
        $fpx_sellerOrderNo  = $request->o_id;
        $fpx_sellerId       = "SE00013841";
        $fpx_sellerBankCode = "01";
        $fpx_txnCurrency    = "MYR";
        $fpx_buyerIban      = "";
        $fpx_txnAmount      = $request->amount;
        $fpx_checkSum       = "";
        $fpx_buyerBankId    = $request->bankid;
        $fpx_buyerBankBranch = "";
        $fpx_buyerAccNo     = "";
        $fpx_buyerId        = "";
        $fpx_makerName      = "";
        $fpx_productDesc    = $request->desc;
        $fpx_version        = "6.0";

        /* Generating signing String */
        $data = $fpx_buyerAccNo . "|" . $fpx_buyerBankBranch . "|" . $fpx_buyerBankId . "|" . $fpx_buyerEmail . "|" . $fpx_buyerIban . "|" . $fpx_buyerId . "|" . $fpx_buyerName . "|" . $fpx_makerName . "|" . $fpx_msgToken . "|" . $fpx_msgType . "|" . $fpx_productDesc . "|" . $fpx_sellerBankCode . "|" . $fpx_sellerExId . "|" . $fpx_sellerExOrderNo . "|" . $fpx_sellerId . "|" . $fpx_sellerOrderNo . "|" . $fpx_sellerTxnTime . "|" . $fpx_txnAmount . "|" . $fpx_txnCurrency . "|" . $fpx_version;

        /* Reading key */
        // dd(getenv('FPX_KEY'));
        $priv_key = getenv('FPX_KEY');
        $pkeyid = openssl_get_privatekey($priv_key, null);
        openssl_sign($data, $binary_signature, $pkeyid, OPENSSL_ALGO_SHA1);
        $fpx_checkSum = strtoupper(bin2hex($binary_signature));
        return view('fpx.index', compact(
            'fpx_msgType',
            'fpx_msgToken',
            'fpx_sellerExId',
            'fpx_sellerExOrderNo',
            'fpx_sellerTxnTime',
            'fpx_sellerOrderNo',
            'fpx_sellerId',
            'fpx_sellerBankCode',
            'fpx_txnCurrency',
            'fpx_txnAmount',
            'fpx_buyerEmail',
            'fpx_checkSum',
            'fpx_buyerName',
            'fpx_buyerBankId',
            'fpx_buyerBankBranch',
            'fpx_buyerAccNo',
            'fpx_buyerId',
            'fpx_makerName',
            'fpx_buyerIban',
            'fpx_productDesc',
            'fpx_version',
            'telno',
            'data'
        ));
    }

    public function paymentStatus(Request $request)
    {
        return view('fpx.pStatus', compact('request'));
    }

    public function transactionReceipt(Request $request)
    {
        $case = explode("_", $request->fpx_sellerExOrderNo);
        $text = explode("/", $request->fpx_buyerIban);

        if ($request->fpx_debitAuthCode == '00') {
            switch ($case[0]) {
                case 'School Fees':
                    $user = User::find(Auth::id());
                    $transaction = new Transaction();
                    $transaction->nama = $request->fpx_sellerExOrderNo;
                    $transaction->description = $request->fpx_sellerOrderNo;
                    $transaction->transac_no = $request->fpx_fpxTxnId;
                    $transaction->datetime_created = now();
                    $transaction->amount = $request->fpx_txnAmount;
                    $transaction->status = 'Success';
                    $transaction->user_id = Auth::id();

                    if ($transaction->save()) {
                        $res = DB::table('fees_transactions')->insert(array(
                            0 => array(
                                'student_fees_id' => 1,
                                'payment_type_id' => 1,
                                'transactions_id' => $transaction->id,
                            ),
                        ));
                        if ($res)
                            return view('fpx.tStatus', compact('request', 'user'));
                        else {
                            return view('errors.500');
                        }
                    }
                    break;

                case 'Donation':

                    $user       = User::find(Auth::id());
                    $username   = $text[0];
                    $telno      = $text[1];
                    $email      = $text[2];

                    $transaction = new Transaction();
                    $transaction->nama          = $request->fpx_sellerExOrderNo;
                    $transaction->description   = $request->fpx_sellerOrderNo;
                    $transaction->transac_no    = $request->fpx_fpxTxnId;
                    $transaction->datetime_created = now();
                    $transaction->amount        = $request->fpx_txnAmount;
                    $transaction->status        = 'Success';
                    $transaction->email         = $email;
                    $transaction->telno         = $telno;
                    $transaction->username      = $username;

                    $request->buyerName = $username;

                    if ($user) {
                        $transaction->user_id   = Auth::id();
                    }
                    if ($transaction->save()) {

                        $transaction->donation()->attach($request->fpx_sellerOrderNo, ['payment_type_id' => 1]);
                        dd($request);
                        return view('fpx.tStatus', compact('request', 'user'));
                    }

                    break;
                default:
                    return view('errors.500');
                    break;
            }
            return view('errors.500');
        } else {
            return view('fpx.transactionFailed');
        }
    }
}
