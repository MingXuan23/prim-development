<?php

namespace App\Console\Commands;

use App\Models\Organization;
use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\AssignOp\ShiftLeft;

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
use App\Http\Controllers\DirectPayController;


class TransactionStatusUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transactionstatus:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used to update pending transaction. (AE requery)';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    // public function getTransactionInfo($privateKey, $sellerExOrderNo, $sellerOrderNo) {
    //     $url = "https://directpay.my/api/fpx/GetTransactionInfo";

    //     $params = [
    //         'PrivateKey' => $privateKey,
    //         'Fpx_SellerExOrderNo' => $sellerExOrderNo,
    //         'Fpx_SellerOrderNo' => $sellerOrderNo,
    //     ];

    //     $url .= '?' . http_build_query($params);

    //     $ch = curl_init($url);

    //     // Set cURL options
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    //     // Execute cURL session and get the response
    //     $response = curl_exec($ch);

    //     // Check for cURL errors
    //     if (curl_errno($ch)) {
    //         echo 'Curl error: ' . curl_error($ch);
    //     }

    //     // Close cURL session
    //     curl_close($ch);

    //     return $response;
    // }




    public function getTransactionInfo($transaction_id)
    {

        $transaction = Transaction::find($transaction_id);
        $fpx_sellerOrderNo = $transaction->nama;

        $fpx_productDesc = explode("_", $transaction->nama)[1];

        if ($fpx_productDesc == "Dona") {
            $organ = DB::table("transactions as t")
                ->leftJoin('donation_transaction as dt', 't.id', 'dt.transaction_id')
                ->leftJoin('donations as d', 'd.id', 'dt.donation_id')
                ->leftJoin('donation_organization as do', 'do.donation_id', 'd.id')
                ->leftJoin('organizations as o', 'o.id', 'do.organization_id')
                ->select('o.private_key')
                ->where('t.id', $transaction->id)
                ->first();
        } else if ($fpx_productDesc == "Scho") {
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
        } else if ($fpx_productDesc == "Merchant" || $fpx_productDesc == "Kope") {
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
        $buyerName = $transaction->username;
        $requestTimestamp = now()->format('YmdHis');
        $signaturePlainText = "$fpx_sellerOrderNo|$privateKey|$buyerName|$requestTimestamp";
        $bytes = mb_convert_encoding($signaturePlainText, 'UTF-8');
        $signatureHash = hash_hmac('sha256', $bytes, $privateKey);

        $url = "https://api.directpay.my/api/v1/Pay/MerchantInquiry";

        $params = [
            'fpx_sellerOrderNo' => $fpx_sellerOrderNo,
            'signature' => $signatureHash,
            'request_timestamp' => $requestTimestamp
        ];

        $url .= '?' . http_build_query($params);
        //dd($url);


        $ch = curl_init($url);

        $headers = [
            "accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7",
            "accept-language: en-US,en;q=0.9",
            "user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0"
        ];

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Execute cURL session and get the response
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        }

        // Close cURL session
        curl_close($ch);
        $resultArray = json_decode($response, true);

        if (!isset($resultArray['payload']['Fpx_SellerOrderNo'])) {
            return "Error: " . $url . "Message: " . $resultArray['errorMessage'];
        }

        return $resultArray;
    }

    public function handle()
    {
        \Log::info("Start handle Transaction Command Run");
        $directPayController = new DirectPayController();
        $directPayController->handle();
        \Log::info("Update Transaction Command Run Successfully!");
    }


    public function oldMethod()
    {
        try {
            //url-ify the data for the POST
            foreach ($fields as $key => $value) {
                $fields_string .= $key . '=' . $value . '&';
            }
            rtrim($fields_string, '&');

            //open connection
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

            //set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_URL, $url);

            curl_setopt($ch, CURLOPT_POST, count($fields));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);

            // receive server response ...
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            //execute post
            $result = curl_exec($ch);

            //close connection
            curl_close($ch);

            $token = strtok($result, "&");
            while ($token !== false) {
                list($key1, $value1) = explode("=", $token);
                $value1 = urldecode($value1);
                $response_value[$key1] = $value1;
                $token = strtok("&");
            }

            if (!isset($response_value['fpx_debitAuthCode'])) {
                //continue;
                return;
            }

            if ($response_value['fpx_debitAuthCode'] == '00') {
                switch ($fpx_productDesc) {
                    case 'School':

                        Transaction::where('nama', '=', $fpx_sellerExOrderNo)->update(['transac_no' => $response_value['fpx_fpxTxnId'], 'status' => 'Success']);
                        $transaction = Transaction::where('nama', '=', $fpx_sellerExOrderNo)->first();

                        $list_student_fees_id = DB::table('student_fees_new')
                            ->join('fees_transactions_new', 'fees_transactions_new.student_fees_id', '=', 'student_fees_new.id')
                            ->join('transactions', 'transactions.id', '=', 'fees_transactions_new.transactions_id')
                            ->select('student_fees_new.id as student_fees_id', 'student_fees_new.class_student_id')
                            ->where('transactions.id', $transaction->id)
                            ->get();

                        $list_parent_fees_id = DB::table('fees_new')
                            ->join('fees_new_organization_user', 'fees_new_organization_user.fees_new_id', '=', 'fees_new.id')
                            ->join('organization_user', 'organization_user.id', '=', 'fees_new_organization_user.organization_user_id')
                            ->select('fees_new_organization_user.*')
                            ->orderBy('fees_new.category')
                            ->where('organization_user.user_id', $transaction->user_id)
                            ->where('organization_user.role_id', 6)
                            ->where('organization_user.status', 1)
                            ->where('fees_new_organization_user.transaction_id', $transaction->id)
                            ->get();


                        for ($i = 0; $i < count($list_student_fees_id); $i++) {

                            // ************************* update student fees status fees by transactions *************************
                            $res = DB::table('student_fees_new')
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

                        for ($i = 0; $i < count($list_parent_fees_id); $i++) {

                            // ************************* update status fees for parent *************************
                            DB::table('fees_new_organization_user')
                                ->where('id', $list_parent_fees_id[$i]->id)
                                ->update([
                                    'status' => 'Paid'
                                ]);

                            // ************************* check the parent if have still debt *************************
                            if ($i == count($list_student_fees_id) - 1) {
                                $check_debt = DB::table('organization_user')
                                    ->join('fees_new_organization_user', 'fees_new_organization_user.organization_user_id', '=', 'organization_user.id')
                                    ->where('organization_user.user_id', $transaction->user_id)
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

                        break;

                    case 'Donation':

                        Transaction::where('nama', '=', $fpx_sellerExOrderNo)->update(['transac_no' => $response_value['fpx_fpxTxnId'], 'status' => 'Success']);

                        break;

                    default:
                        Transaction::where('nama', '=', $fpx_sellerExOrderNo)->update(['transac_no' => $response_value['fpx_fpxTxnId'], 'status' => 'Success']);

                        break;
                }
            } else {
                Transaction::where('nama', '=', $fpx_sellerExOrderNo)->update(['transac_no' => $response_value['fpx_fpxTxnId'], 'status' => 'Failed']);
            }
        } catch (\Throwable $th) {
            echo 'Error :', ($th->getMessage());
        }
    }
}
