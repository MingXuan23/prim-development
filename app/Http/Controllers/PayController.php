<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Detail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $fpx_msgType = "AR";
        $fpx_msgToken = "01";
        $fpx_sellerExId = "EX00012323";
        $fpx_sellerExOrderNo = 'T' . rand(1000000000, 9999999999);
        $fpx_sellerTxnTime = date('YmdHis');
        $fpx_sellerOrderNo = $request->o_id;
        $fpx_sellerId = "SE00013841";
        $fpx_sellerBankCode = "01";
        $fpx_txnCurrency = "MYR";
        $fpx_txnAmount = $request->amount;
        $fpx_buyerEmail = "ahmadraziqdanish@gmail.com";
        $fpx_checkSum = "";
        $fpx_buyerName = "";
        $fpx_buyerBankId = $request->bankid;
        $fpx_buyerBankBranch = "";
        $fpx_buyerAccNo = "";
        $fpx_buyerId = "";
        $fpx_makerName = "";
        $fpx_buyerIban = "";
        $fpx_productDesc = "SampleProduct";
        $fpx_version = "6.0";

        /* Generating signing String */
        $data = $fpx_buyerAccNo . "|" . $fpx_buyerBankBranch . "|" . $fpx_buyerBankId . "|" . $fpx_buyerEmail . "|" . $fpx_buyerIban . "|" . $fpx_buyerId . "|" . $fpx_buyerName . "|" . $fpx_makerName . "|" . $fpx_msgToken . "|" . $fpx_msgType . "|" . $fpx_productDesc . "|" . $fpx_sellerBankCode . "|" . $fpx_sellerExId . "|" . $fpx_sellerExOrderNo . "|" . $fpx_sellerId . "|" . $fpx_sellerOrderNo . "|" . $fpx_sellerTxnTime . "|" . $fpx_txnAmount . "|" . $fpx_txnCurrency . "|" . $fpx_version;

        /* Reading key */
        $priv_key = file_get_contents('C:\\pki-keys\\DevExchange\\EX00012323.key');
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
            'data'
        ));
    }

    public function paymentStatus () {
        return view('fpx.pStatus');
    }

    public function transactionReceipt() {
        return view('fpx.tStatus');
    }
}
