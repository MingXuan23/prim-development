<?php

namespace App\Http\Controllers;

use App\User;
use App\Models\Order;
use App\Models\Fee_New;
use App\Models\Student;
use App\Models\Donation;
use App\Mail\OrderReceipt;
use App\Models\Transaction;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Mail\DonationReceipt;
use App\Models\Dev\DevTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use phpDocumentor\Reflection\Types\Null_;
use App\Http\Controllers\AppBaseController;
use League\CommonMark\Inline\Parser\EscapableParser;

class PayController extends AppBaseController
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
        $user = "";

        $donationId   = $request->id;
        $donation  = $this->donation->getDonationById($donationId);

        if (Auth::id()) {
            $user = $this->user->getUserById();
        }

        return view('paydonate.pay', compact('donation', 'user'));
    }

    public function donateFromMobile(Request $request)
    {
        $user = "";

        $donationId   = $request->donationId;
        $donation  = $this->donation->getDonationById($donationId);
        
        if (isset($request->userId)) {
            $user = DB::table("users")->where('id', $request->userId)->first();
        }

        // dd(!is_null($user));

        return view('paydonate.pay', compact('donation', 'user'));
    }

    // pay fees latest 15 july code
    public function pay(Request $request)
    {

        $getorganization_category       = "";
        $getfees_category_A             = "";
        $getfees_category_A_byparent    = "";
        $get_fees_by_parent             = "";

        $getstudent         = "";
        $getorganization    = "";
        $getfees            = "";
        $getfees_bystudent  = "";
        $getstudentfees     = "";

        $cb_categoryA       = $request->get('category');
        $cb_fees_student    = $request->get('id');

        if ($cb_fees_student) {

            // ************  id from value checkbox (hidden) **************
            $size_cb  = count(collect($request)->get('id'));
            $data_cb  = collect($request)->get('id');

            $student_fees  = array();
            $studentid  = array();
            $feesid     = array();
            for ($i = 0; $i < $size_cb; $i++) {

                //want seperate data from request
                //case 0 = student id
                //case 1 = fees id
                //format req X-X

                $case           = explode("-", $data_cb[$i]);
                $studentid[]    = $case[0];
                $feesid[]       = $case[1];

                $getfees_req     = DB::table('students')
                    ->join('class_student', 'class_student.student_id', '=', 'students.id')
                    ->join('student_fees_new', 'student_fees_new.class_student_id', '=', 'class_student.id')
                    ->join('fees_new', 'fees_new.id', '=', 'student_fees_new.fees_id')
                    ->select('student_fees_new.id')
                    ->where('fees_new.id', $feesid[$i])
                    ->where('students.id', $studentid[$i])
                    ->first();

                $student_fees[] = $getfees_req->id;
            }

            // dd($student_fees);

            $res_student = array_unique($studentid);
            $res_fee     = array_unique($feesid);

            // ************************* get student from array student id *******************************

            $getstudent  = DB::table('students')
                ->select('id as studentid', 'nama as studentname')
                ->whereIn('id', $res_student)
                ->get();

            // ************************* get organization from array student id *******************************

            $getstudent2  = DB::table('students')
                ->join('class_student', 'class_student.student_id', '=', 'students.id')
                ->join('student_fees_new', 'student_fees_new.class_student_id', '=', 'class_student.id')
                ->join('fees_new', 'fees_new.id', '=', 'student_fees_new.fees_id')
                ->select('students.id as studentid', 'students.nama as studentname', 'fees_new.id as feeid', 'fees_new.organization_id as organizationid')
                ->whereIn('students.id', $res_student)
                ->first();

            $getorganization  = DB::table('organizations')
                ->where('id', $getstudent2->organizationid)
                ->first();

            // ************************* get fees from array fees id *******************************


            $getfees     = DB::table('students')
                ->join('class_student', 'class_student.student_id', '=', 'students.id')
                ->join('student_fees_new', 'student_fees_new.class_student_id', '=', 'class_student.id')
                ->join('fees_new', 'fees_new.id', '=', 'student_fees_new.fees_id')
                ->select('fees_new.category', 'students.id as studentid')
                ->distinct()
                ->whereIn('student_fees_new.id', $student_fees)
                ->get();

            $getfees_bystudent     = DB::table('students')
                ->join('class_student', 'class_student.student_id', '=', 'students.id')
                ->join('student_fees_new', 'student_fees_new.class_student_id', '=', 'class_student.id')
                ->join('fees_new', 'fees_new.id', '=', 'student_fees_new.fees_id')
                ->select('fees_new.id', 'fees_new.name', 'fees_new.quantity', 'fees_new.price', 'fees_new.category', 'students.id as studentid')
                ->whereIn('student_fees_new.id', $student_fees)
                ->get();

            // dd($getfees_bystudent);

            // ************************* get student_fees_id from array *******************************

            $getstudentfees  = DB::table('students')
                ->join('class_student', 'class_student.student_id', '=', 'students.id')
                ->join('student_fees_new', 'student_fees_new.class_student_id', '=', 'class_student.id')
                ->join('fees_new', 'fees_new.id', '=', 'student_fees_new.fees_id')
                ->select('student_fees_new.id')
                ->whereIn('student_fees_new.id', $student_fees)
                ->get();
        }



        if ($cb_categoryA) {

            // ************  id from value checkbox category (hidden)  **************

            $size_cb_cat  = count(collect($request)->get('category'));
            $data_cb_cat  = collect($request)->get('category');

            $parentid  = array();
            $feesid_A  = array();
            for ($i = 0; $i < $size_cb_cat; $i++) {

                //want seperate data from request
                //case 0 = parent id
                //case 1 = fees id
                //format req X-X

                $case           = explode("-", $data_cb_cat[$i]);
                $parentid[]     = $case[0];
                $feesid_A[]     = $case[1];
            }
            $res_parent     = array_unique($parentid);
            $res_fee_A     = array_unique($feesid_A);


            // ************************* get fees category A from array *******************************

            $getorganization  = DB::table('fees_new')
                ->join('fees_new_organization_user', 'fees_new_organization_user.fees_new_id', '=', 'fees_new.id')
                ->join('organization_user', 'organization_user.id', '=', 'fees_new_organization_user.organization_user_id')
                ->join('organizations', 'organizations.id', '=', 'organization_user.organization_id')
                ->select('organizations.*')
                ->distinct()
                ->orderBy('fees_new.category')
                ->where('organization_user.user_id', $res_parent)
                ->where('organization_user.role_id', 6)
                ->where('organization_user.status', 1)
                ->whereIn('fees_new.id', $res_fee_A)
                ->first();

            $getfees_category_A = DB::table('fees_new')
                ->join('fees_new_organization_user', 'fees_new_organization_user.fees_new_id', '=', 'fees_new.id')
                ->join('organization_user', 'organization_user.id', '=', 'fees_new_organization_user.organization_user_id')
                ->select('fees_new.category', 'organization_user.organization_id')
                ->distinct()
                ->orderBy('fees_new.category')
                ->where('organization_user.user_id', $res_parent)
                ->where('organization_user.role_id', 6)
                ->where('organization_user.status', 1)
                ->whereIn('fees_new.id', $res_fee_A)
                ->get();

            $getfees_category_A_byparent  = DB::table('fees_new')
                ->join('fees_new_organization_user', 'fees_new_organization_user.fees_new_id', '=', 'fees_new.id')
                ->join('organization_user', 'organization_user.id', '=', 'fees_new_organization_user.organization_user_id')
                ->select('fees_new.*')
                ->orderBy('fees_new.category')
                ->where('organization_user.user_id', $res_parent)
                ->where('organization_user.role_id', 6)
                ->where('organization_user.status', 1)
                ->whereIn('fees_new.id', $res_fee_A)
                ->get();

            $get_fees_by_parent   = DB::table('fees_new')
                ->join('fees_new_organization_user', 'fees_new_organization_user.fees_new_id', '=', 'fees_new.id')
                ->join('organization_user', 'organization_user.id', '=', 'fees_new_organization_user.organization_user_id')
                ->select('fees_new_organization_user.*')
                ->where('organization_user.user_id', $res_parent)
                ->where('organization_user.role_id', 6)
                ->where('organization_user.status', 1)
                ->whereIn('fees_new.id', $res_fee_A)
                ->get();

            // dd($getorganization->seller_id);
        }
        return view('fee.pay.pay', compact('getstudent', 'getorganization', 'getfees', 'getfees_bystudent', 'getstudentfees', 'getfees_category_A', 'getfees_category_A_byparent', 'get_fees_by_parent'))->render();
    }

    public function billIndex()
    {
        $data['users'] = User::where('id', '!=', Auth::id())->get();
        $data['authInfo'] = User::find(Auth::id());

        return view('layouts.bill', $data);
    }

    public function transaction(Request $request)
    {
        $user       = User::find(Auth::id());
        $transaction = new Transaction();
        $transaction->nama          = $request->fpx_sellerExOrderNo;
        $transaction->description   = $request->fpx_sellerOrderNo;
        $transaction->transac_no    = $request->fpx_fpxTxnId;
        $transaction->datetime_created = now();
        $transaction->amount        = $request->fpx_txnAmount;
        $transaction->status        = 'Pending';
        $transaction->email         = $request->fpx_buyerEmail;
        $transaction->telno         = $request->telno;
        $transaction->username      = strtoupper($request->fpx_buyerName);
        $transaction->fpx_checksum  = $request->fpx_checkSum;

        if ($user) {
            $transaction->user_id   = Auth::id();
        }

        $list_student_fees_id   = $request->student_fees_id;
        $list_parent_fees_id    = $request->parent_fees_id;

        $id = explode("_", $request->fpx_sellerOrderNo);
        $id = (int) str_replace("PRIM", "", $id[0]);

        if ($transaction->save()) {

            // ******* save bridge transaction *********
            // type = S for school fees and D for donation

            if (substr($request->fpx_sellerExOrderNo, 0, 1) == 'S') {

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
            } else {
                $transaction->donation()->attach($id, ['payment_type_id' => 1]);
            }
        }
    }

    public function transactionDev(Request $request)
    {
        $user       = User::find(Auth::id());
        $transaction = new DevTransaction();
        $transaction->nama          = $request->fpx_sellerExOrderNo;
        $transaction->description   = $request->fpx_sellerOrderNo;
        $transaction->transac_no    = $request->fpx_fpxTxnId;
        $transaction->datetime_created = now();
        $transaction->amount        = $request->fpx_txnAmount;
        $transaction->status        = 'Pending';
        $transaction->email         = $request->fpx_buyerEmail;
        $transaction->telno         = $request->telno;
        $transaction->username      = strtoupper($request->fpx_buyerName);
        $transaction->fpx_checksum  = $request->fpx_checkSum;

        if ($user) {
            $transaction->user_id   = Auth::id();
        }
        if ($transaction->save()) {
            // $id = substr($request->fpx_sellerOrderNo, -1);
            $id = explode("_", $request->fpx_sellerOrderNo);
            $transaction->donation()->attach($id[1], ['payment_type_id' => 1]);

            /// ******************* utk bridge yuran ****************************

            // dd('done');
            // return view('fpx.tStatus', compact('request', 'user'));
        }
    }

    public function successPay()
    {
        return view('fpx.transactionFailed');
        return view('pentadbir.fee.successpay');
    }

    public function fpxIndex(Request $request)
    {
        $getstudentfees = ($request->student_fees_id) ? $request->student_fees_id : "";
        $getparentfees  = ($request->parent_fees_id) ? $request->parent_fees_id : "";
        $icno = isset($request->icno) ? $request->icno : NULL;
        $address = isset($request->address) ? $request->address : NULL;
        
        if ($request->desc == 'Donation') {
            $user = User::find(Auth::id());
            $organization = $this->organization->getOrganizationByDonationId($request->d_id);

            if(isset($request->email))
            {
                $fpx_buyerEmail = $request->email;
                $telno = "+6" . $request->telno;
                $fpx_buyerName = $request->name;
            }
            else
            {
                $fpx_buyerEmail =  NULL;
                $telno = NULL;
                $fpx_buyerName = "Penderma Tanpa Nama";
            }

            $fpx_sellerExOrderNo = $request->desc . "_" . $request->d_code . "_" . date('YmdHis') . "_" . $organization->id;

            $success_transaction = DB::table('donation_transaction as dt')
                ->leftJoin('transactions as t', 't.id', 'dt.transaction_id')
                ->where('t.status', 'success')
                ->where('dt.donation_id', $request->d_id)
                ->count();

            $fpx_sellerOrderNo  = "PRIM" . str_pad($request->d_id, 3, "0", STR_PAD_LEFT)  . "_" . date('YmdHis') . str_pad($success_transaction, 6, '0', STR_PAD_LEFT);
            $fpx_sellerExId     = config('app.env') == 'production' ? "EX00011125" : "EX00012323";

            $fpx_sellerId       = config('app.env') == 'production' ? $organization->seller_id : "SE00013841";

            // $fpx_buyerIban      = $request->name . "/" . $telno . "/" . $request->email;
        } 
        else if ($request->desc == 'School_Fees')
        {
            $user = User::find(Auth::id());
            $organization = Organization::find($request->o_id);
            
            $fpx_buyerEmail      = $user->email;
            $telno               = $user->telno;
            $fpx_buyerName       = User::where('id', '=', Auth::id())->pluck('name')->first();
            $fpx_sellerExOrderNo = $request->desc . "_" . date('YmdHis');
            $fpx_sellerOrderNo  = "YSPRIM" . date('YmdHis') . rand(10000, 99999);

            $fpx_sellerExId     = config('app.env') == 'production' ? "EX00011125" : "EX00012323";
            $fpx_sellerId       = config('app.env') == 'production' ? $organization->seller_id : "SE00013841";
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

            $fpx_sellerExId     = config('app.env') == 'production' ? "EX00011125" : "EX00012323";
            $fpx_sellerId       = config('app.env') == 'production' ? $organization->seller_id : "SE00013841";
        }


        $fpx_msgType        = "AR";
        $fpx_msgToken       = "01";
        $fpx_sellerTxnTime  = date('YmdHis');
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
        // $priv_key = file_get_contents('C:\\pki-keys\\DevExchange\\EX00012323.key');
        $pkeyid = openssl_get_privatekey($priv_key, null);
        openssl_sign($data, $binary_signature, $pkeyid, OPENSSL_ALGO_SHA1);
        $fpx_checkSum = strtoupper(bin2hex($binary_signature));

        $transaction = new Transaction();
        $transaction->nama          = $fpx_sellerExOrderNo;
        $transaction->description   = $fpx_sellerOrderNo;
        $transaction->transac_no    = NULL;
        $transaction->datetime_created = now();
        $transaction->amount        = $fpx_txnAmount;
        $transaction->status        = 'Pending';
        $transaction->email         = $fpx_buyerEmail;
        $transaction->telno         = $telno;
        $transaction->user_id       = $user ? $user->id : null;
        $transaction->username      = strtoupper($fpx_buyerName);
        $transaction->fpx_checksum  = $fpx_checkSum;
        $transaction->icno  = $icno;
        $transaction->address  = $address;

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
            else {
                $transaction->donation()->attach($id, ['payment_type_id' => 1]);
            }
        }
        else
        {
            return view('errors.500');
        }

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
            'data',
            'getstudentfees',
            'getparentfees'
        ));
    }

    // callback for FPX
    public function paymentStatus(Request $request)
    {
        $case = explode("_", $request->fpx_sellerExOrderNo);

        if ($request->fpx_debitAuthCode == '00') {
            switch ($case[0]) {
                case 'School Fees':
                    break;

                case 'Donation':
                    Transaction::where('nama', '=', $request->fpx_sellerExOrderNo)->update(['transac_no' => $request->fpx_fpxTxnId, 'status' => 'Success']);

                    $request->fpx_debitAuthCode == "00" ? $status = "Success" : $status = "Failed/Pending";
                    \Log::channel('PRIM_transaction')->info("Transaction Callback : " .  $request->fpx_sellerExOrderNo . " , " . $status);

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
            Transaction::where('nama', '=', $request->fpx_sellerExOrderNo)->update(['transac_no' => $request->fpx_fpxTxnId, 'status' => 'Failed']);
        }
    }

    // callback for FPX
    public function transactionReceipt(Request $request)
    {

        $case = explode("_", $request->fpx_sellerExOrderNo);
        // $text = explode("/", $request->fpx_buyerIban);

        if ($request->fpx_debitAuthCode == '00') {
            switch ($case[0]) {
                case 'School':
                    // $response = Http::post('https://dev.prim.my/api/devtrans', [
                    //     $this->sendResponse($request->toArray(), "Success")
                    // ]);

                    // return Redirect::away('https://dev.prim.my/api/devtrans')->with();
                    // return Redirect::away('https://dev.prim.my/api/devtrans')->with($request->toArray());

                    $transaction = Transaction::where('nama', '=', $request->fpx_sellerExOrderNo)->first();
                    $userid = $transaction->user_id;
                    $transaction->transac_no = $request->fpx_fpxTxnId;
                    $transaction->status = "Success";

                    $res_student = DB::table('student_fees_new')
                        ->join('fees_transactions_new', 'fees_transactions_new.student_fees_id', '=', 'student_fees_new.id')
                        ->join('transactions', 'transactions.id', '=', 'fees_transactions_new.transactions_id')
                        ->select('student_fees_new.id as student_fees_id', 'student_fees_new.class_student_id')
                        ->where('transactions.id', $transaction->id)
                        ->get();

                    $res_parent  = DB::table('fees_new')
                        ->join('fees_new_organization_user', 'fees_new_organization_user.fees_new_id', '=', 'fees_new.id')
                        ->join('organization_user', 'organization_user.id', '=', 'fees_new_organization_user.organization_user_id')
                        ->select('fees_new_organization_user.*')
                        ->orderBy('fees_new.category')
                        ->where('organization_user.user_id', $userid)
                        ->where('organization_user.role_id', 6)
                        ->where('organization_user.status', 1)
                        ->where('fees_new_organization_user.transaction_id', $transaction->id)
                        ->get();

                    $res_student ? $list_student_fees_id = $res_student : $list_student_fees_id = "";
                    $res_parent ? $list_parent_fees_id = $res_parent : $list_student_fees_id = "";

                    if ($transaction->save()) {

                        if ($list_student_fees_id) {
                            for ($i = 0; $i < count($list_student_fees_id); $i++) {

                                // ************************* update student fees status fees by transactions *************************
                                $res  = DB::table('student_fees_new')
                                    ->where('id', $list_student_fees_id[$i]->student_fees_id)
                                    ->update(['status' => 'Paid']);

                                // ************************* check the student if have still debt *************************

                                $check_debt = DB::table('students')
                                    ->join('class_student', 'class_student.student_id', '=', 'students.id')
                                    ->join('student_fees_new', 'student_fees_new.class_student_id', '=', 'class_student.id')
                                    ->select('students.*')
                                    ->where('class_student.id', $list_student_fees_id[$i]->class_student_id)
                                    ->where('student_fees_new.status', 'Debt')
                                    ->get();


                                // ************************* update status fees for student if all fees completed paid*************************

                                if (count($check_debt) == 0) {
                                    DB::table('class_student')
                                        ->where('id', $list_student_fees_id[$i]->class_student_id)
                                        ->update(['fees_status' => 'Completed']);
                                }
                            }
                        }

                        if ($list_parent_fees_id) {
                            for ($i = 0; $i < count($list_parent_fees_id); $i++) {

                                // ************************* update status fees for parent *************************
                                $res = DB::table('fees_new_organization_user')
                                    ->where('id', $list_parent_fees_id[$i]->id)
                                    ->update([
                                        'status' => 'Paid'
                                    ]);

                                // ************************* check the parent if have still debt *************************

                                $check_debt = DB::table('organization_user')
                                    ->join('fees_new_organization_user', 'fees_new_organization_user.organization_user_id', '=', 'organization_user.id')
                                    ->select('fees_new_organization_user.*')
                                    ->where('organization_user.user_id', $userid)
                                    ->where('organization_user.role_id', 6)
                                    ->where('organization_user.status', 1)
                                    ->where('fees_new_organization_user.status', 'Debt')
                                    ->get();

                                // ************************* update status fees for organization user (parent) if all fees completed paid *************************

                                if (count($check_debt) == 0) {
                                    DB::table('organization_user')
                                        ->where('user_id', $userid)
                                        ->where('role_id', 6)
                                        ->where('status', 1)
                                        ->update(['fees_status' => 'Completed']);
                                }
                            }
                        }

                        if ($res) {
                            return $this->ReceiptFees($transaction->id);
                        } else {
                            return view('errors.500');
                        }
                    }
                    break;

                case 'Donation':

                    Transaction::where('nama', '=', $request->fpx_sellerExOrderNo)->update(['transac_no' => $request->fpx_fpxTxnId, 'status' => 'Success']);

                    $donation = $this->donation->getDonationByTransactionName($request->fpx_sellerExOrderNo);

                    $organization = $this->organization->getOrganizationByDonationId($donation->id);
                    $transaction = $this->transaction->getTransactionByName($request->fpx_sellerExOrderNo);

                    if($transaction->username != "Penderma Anonymous" && $transaction->email != NULL)
                    {
                        Mail::to($transaction->email)->send(new DonationReceipt($donation, $transaction, $organization));
                    }

                    if ($donation->lhdn_reference_code != null)
                    {
                        return view('receipt.indexlhdn', compact('request', 'donation', 'organization', 'transaction'));
                    }

                    return view('receipt.index', compact('request', 'donation', 'organization', 'transaction'));

                    break;
                    
                case 'Food':
                    $transaction = Transaction::where('nama', '=', $request->fpx_sellerExOrderNo)->first();
                    $transaction->transac_no = $request->fpx_fpxTxnId;
                    $transaction->status = "Success";
                    $transaction->save();

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

                    return view('order.receipt', compact('order_dishes', 'organization', 'transaction', 'user'));

                    break;
                        
                default:
                    return view('errors.500');
                    break;
            }
            return view('errors.500');
        } else {
            Transaction::where('nama', '=', $request->fpx_sellerExOrderNo)->update(['transac_no' => $request->fpx_fpxTxnId, 'status' => 'Failed']);
            $user = Transaction::where('nama', '=', $request->fpx_sellerExOrderNo)->first();
            return view('fpx.transactionFailed', compact('request', 'user'));
        }
    }

    public function showReceipt()
    {
        // $donation = $this->donation->getDonationByTransactionName("Donation_23210315210448");
        // $organization = $this->organization->getOrganizationByDonationId(3);
        // dd($organization);
        return view('receipt.index');
    }

    public function devtrans($request)
    {
        // $donation = $this->donation->getDonationByTransactionName("Donation_23210315210448");
        // $organization = $this->organization->getOrganizationByDonationId(3);
        // dd($organization);
        // Log::
        // dd($request);

        // return $request;
        // dd($request);
        \Log::channel('PRIM_api')->info("API Request : "  . $request);
        return view('parent.fee.receipt');
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

        return view('fee.pay.receipt', compact('getparent', 'get_transaction', 'get_student', 'get_category', 'get_fees', 'getfees_categoryA', 'get_organization'));
    }

    public function getDetailReceipt($id)
    {
        // parent user id
        $userid = Auth::id();

        // details parents
        $getparent = DB::table('users')
            ->where('id', $userid)
            ->first();

        // details transaction
        $get_transaction = Transaction::where('id', $id)->first();


        // get fee and organization name
        $get_fee_organization = DB::table('transactions')
            ->join('fees_transactions', 'fees_transactions.transactions_id', '=', 'transactions.id')
            ->join('student_fees', 'student_fees.id', '=', 'fees_transactions.student_fees_id')
            ->join('fees_details', 'fees_details.id', '=', 'student_fees.fees_details_id')
            ->join('fees', 'fees.id', '=', 'fees_details.fees_id')
            ->join('class_fees', 'class_fees.fees_id', '=', 'fees.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_fees.class_organization_id')
            ->join('organizations', 'organizations.id', '=', 'class_organization.organization_id')
            ->select('organizations.id as oid', 'organizations.nama as oname', 'organizations.fixed_charges')
            ->where('transactions.id', $id)
            ->first();


        // list student by transaction
        $getstudent = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('student_fees', 'student_fees.class_student_id', '=', 'class_student.id')
            ->join('fees_details', 'fees_details.id', '=', 'student_fees.fees_details_id')
            ->join('details', 'details.id', '=', 'fees_details.details_id')
            ->join('fees', 'fees.id', '=', 'fees_details.fees_id')
            ->join('fees_transactions', 'fees_transactions.student_fees_id', '=', 'student_fees.id')
            ->join('transactions', 'transactions.id', '=', 'fees_transactions.transactions_id')
            ->select('students.id as studentid', 'students.nama as studentnama', 'fees.id as feeid', 'fees.nama as feename')
            ->distinct()
            ->where('class_student.status', '1')
            ->where('transactions.id', $id)
            ->where('student_fees.status', 'Paid')
            ->orderBy('fees.nama')
            ->get();

        // list category by fees
        $getcategory = DB::table('categories')
            ->join('details', 'details.category_id', '=', 'categories.id')
            ->join('fees_details', 'fees_details.details_id', '=', 'details.id')
            ->join('fees', 'fees.id', '=', 'fees_details.fees_id')
            ->join('student_fees', 'student_fees.fees_details_id', '=', 'fees_details.id')
            ->join('fees_transactions', 'fees_transactions.student_fees_id', '=', 'student_fees.id')
            ->join('transactions', 'transactions.id', '=', 'fees_transactions.transactions_id')
            ->select('categories.id as catid', 'categories.nama as catname')
            ->distinct()
            ->where('transactions.id', $id)
            ->orderBy('categories.nama')
            ->get();

        // get detail item by transaction
        $getdetail = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('student_fees', 'student_fees.class_student_id', '=', 'class_student.id')
            ->join('fees_transactions', 'fees_transactions.student_fees_id', '=', 'student_fees.id')
            ->join('transactions', 'transactions.id', '=', 'fees_transactions.transactions_id')
            ->join('fees_details', 'fees_details.id', '=', 'student_fees.fees_details_id')
            ->join('details', 'details.id', '=', 'fees_details.details_id')
            ->join('fees', 'fees.id', '=', 'fees_details.fees_id')
            ->join('categories', 'categories.id', '=', 'details.category_id')
            ->select('students.id as studentid', 'students.nama as studentnama', 'details.nama as detailsname', 'details.price as detailsprice', 'details.quantity as quantity', 'details.totalamount as totalamount', 'categories.id as catid')
            ->where('class_student.status', '1')
            ->where('transactions.id', $id)
            ->where('student_fees.status', 'Paid')
            ->orderBy('details.nama')
            ->get();



        return view('parent.fee.receipt', compact('getparent', 'get_transaction', 'get_fee_organization', 'getcategory', 'getstudent', 'getdetail'));
    }

    public function viewReceipt()
    {
        return view('parent.fee.receipt');
    }
}