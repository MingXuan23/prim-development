<?php

namespace App\Console\Commands;

use App\Models\Organization;
use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\AssignOp\ShiftLeft;

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

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $transactions = DB::table('transactions')
            ->where('status', 'pending')
            ->whereBetween('datetime_created', [now()->subDays(2), now()])
            ->get();

        foreach ($transactions as $transaction)
        {
            $fpx_msgType = "AE";
            $fpx_msgToken = "01";
            $fpx_sellerExId = config('app.env') == 'production' ? "EX00011125" : "EX00012323";
            $fpx_sellerExOrderNo = $transaction->nama;
    
            $date_time_string = $transaction->datetime_created;
            $date_time = date_create_from_format('Y-m-d H:i:s', $date_time_string);
    
            $fpx_sellerTxnTime = $date_time->format('YmdHis');
            $fpx_sellerOrderNo = $transaction->description;
            $fpx_productDesc = explode("_", $transaction->nama)[0];
    
            if ($fpx_productDesc == "Donation")
            {
                $organ = DB::table("transactions as t")
                    ->leftJoin('donation_transaction as dt', 't.id', 'dt.transaction_id')
                    ->leftJoin('donations as d', 'd.id', 'dt.donation_id')
                    ->leftJoin('donation_organization as do', 'do.donation_id', 'd.id')
                    ->leftJoin('organizations as o', 'o.id', 'do.organization_id')
                    ->select('o.seller_id')
                    ->where('t.id', $transaction->id)
                    ->first();
            }
            else if ($fpx_productDesc == "School")
            {
                $organ = DB::table('organizations as o')
                    ->leftJoin('fees_new as fn', 'o.id', 'fn.organization_id')
                    ->leftJoin('fees_new_organization_user as fou', 'fou.fees_new_id', 'fn.id')
                    ->where('fou.transaction_id', $transaction->id)
                    ->select('o.seller_id')
                    ->first();

                if ($organ == null)
                {
                    $organ = DB::table('organizations as o')
                        ->leftJoin('fees_new as fn', 'o.id', 'fn.organization_id')
                        ->leftJoin('student_fees_new as sfn', 'sfn.fees_id', 'fn.id')
                        ->leftJoin('fees_transactions_new as ftn', 'ftn.student_fees_id', 'sfn.id')
                        ->where('ftn.transactions_id', $transaction->id)
                        ->select('o.seller_id')
                        ->first();
                }
            }
    
            $fpx_sellerId = $organ->seller_id;
            $fpx_sellerBankCode = "01";
            $fpx_txnCurrency = "MYR";
            $fpx_txnAmount = $transaction->amount;
            $fpx_buyerEmail = $transaction->email;
            $fpx_checkSum = "";
            $fpx_buyerName = $transaction->username;
            $fpx_buyerBankId = $transaction->buyerBankId;
            $fpx_buyerAccNo = "";
            $fpx_buyerId = "";
            $fpx_buyerIban = "";
            $fpx_buyerBankBranch = "";
            $fpx_makerName = "";
            $fpx_version = "6.0";
    
            $data = $fpx_buyerAccNo . "|" . $fpx_buyerBankBranch . "|" . $fpx_buyerBankId . "|" . $fpx_buyerEmail . "|" . $fpx_buyerIban . "|" . $fpx_buyerId . "|" . $fpx_buyerName . "|" . $fpx_makerName . "|" . $fpx_msgToken . "|" . $fpx_msgType . "|" . $fpx_productDesc . "|" . $fpx_sellerBankCode . "|" . $fpx_sellerExId . "|" . $fpx_sellerExOrderNo . "|" . $fpx_sellerId . "|" . $fpx_sellerOrderNo . "|" . $fpx_sellerTxnTime . "|" . $fpx_txnAmount . "|" . $fpx_txnCurrency . "|" . $fpx_version;
            $priv_key = getenv('FPX_KEY');
            $pkeyid = openssl_get_privatekey($priv_key, null);
            openssl_sign($data, $binary_signature, $pkeyid, OPENSSL_ALGO_SHA1);
            $fpx_checkSum = strtoupper(bin2hex($binary_signature));
    
            $fields_string="";
    
            //set POST variables
            $url = ($fpx_buyerBankId == 'TEST0021' ||  $fpx_buyerBankId == 'TEST0022' || $fpx_buyerBankId == 'TEST0023') 
                    ? config('app.UAT_AE_AQ_URL') 
                    : config('app.PRODUCTION_AE_AQ_URL');
    
            $fields = array(
                'fpx_msgType' => urlencode("AE"),
                'fpx_msgToken' => urlencode($fpx_msgToken),
                'fpx_sellerExId' => urlencode($fpx_sellerExId),
                'fpx_sellerExOrderNo' => urlencode($fpx_sellerExOrderNo),
                'fpx_sellerTxnTime' => urlencode($fpx_sellerTxnTime),
                'fpx_sellerOrderNo' => urlencode($fpx_sellerOrderNo),
                'fpx_sellerId' => urlencode($fpx_sellerId),
                'fpx_sellerBankCode' => urlencode($fpx_sellerBankCode),
                'fpx_txnCurrency' => urlencode($fpx_txnCurrency),
                'fpx_txnAmount' => urlencode($fpx_txnAmount),
                'fpx_buyerEmail' => urlencode($fpx_buyerEmail),
                'fpx_checkSum' => urlencode($fpx_checkSum),
                'fpx_buyerName' => urlencode($fpx_buyerName),
                'fpx_buyerBankId' => urlencode($fpx_buyerBankId),
                'fpx_buyerBankBranch' => urlencode($fpx_buyerBankBranch),
                'fpx_buyerAccNo' => urlencode($fpx_buyerAccNo),
                'fpx_buyerId' => urlencode($fpx_buyerId),
                'fpx_makerName' => urlencode($fpx_makerName),
                'fpx_buyerIban' => urlencode($fpx_buyerIban),
                'fpx_productDesc' => urlencode($fpx_productDesc),
                'fpx_version' => urlencode($fpx_version)
            );
    
            $response_value = array();
    
            try{
                //url-ify the data for the POST
                foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
                rtrim($fields_string, '&');
    
                //open connection
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    
                //set the url, number of POST vars, POST data
                curl_setopt($ch,CURLOPT_URL, $url);
    
                curl_setopt($ch,CURLOPT_POST, count($fields));
                curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
    
                // receive server response ...
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                //execute post
                $result = curl_exec($ch);
    
                //close connection
                curl_close($ch);
    
                $token = strtok($result, "&");
                while ($token !== false)
                {
                    list($key1,$value1) = explode("=", $token);
                    $value1 = urldecode($value1);
                    $response_value[$key1] = $value1;
                    $token = strtok("&");
                }

                if (!isset($response_value['fpx_debitAuthCode']))
                {
                    continue;
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
        
        
                            for ($i = 0; $i < count($list_student_fees_id); $i++) {
        
                                // ************************* update student fees status fees by transactions *************************
                                $res  = DB::table('student_fees_new')
                                    ->where('id', $list_student_fees_id[$i]->student_fees_id)
                                    ->update(['status' => 'Paid']);
        
                                // ************************* check the student if have still debt *************************
                                
                                if ($i == count($list_student_fees_id) - 1)
                                {
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
                                if ($i == count($list_student_fees_id) - 1)
                                {
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
                            break;
                    }
                } 
                else 
                {
                    Transaction::where('nama', '=', $fpx_sellerExOrderNo)->update(['transac_no' =>  $response_value['fpx_fpxTxnId'], 'status' => 'Failed']);
                }
            } 
            catch (\Throwable $th) {
                echo 'Error :', ($th->getMessage());
            }
        }

        $this->info('transactionstatus:cron Command Run Successfully !');
    }
}
