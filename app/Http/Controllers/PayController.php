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

    public function successPay(){
        return view('pentadbir.fee.successpay');
    }
}
