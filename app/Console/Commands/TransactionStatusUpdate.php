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
    



    public function getTransactionInfo($transaction_id) {

        $transaction = Transaction::find($transaction_id);
        $fpx_sellerOrderNo= $transaction->nama;

        $fpx_productDesc = explode("_", $transaction->nama)[0];

        if ($fpx_productDesc == "Donation")
        {
            $organ = DB::table("transactions as t")
                ->leftJoin('donation_transaction as dt', 't.id', 'dt.transaction_id')
                ->leftJoin('donations as d', 'd.id', 'dt.donation_id')
                ->leftJoin('donation_organization as do', 'do.donation_id', 'd.id')
                ->leftJoin('organizations as o', 'o.id', 'do.organization_id')
                ->select('o.private_key')
                ->where('t.id', $transaction->id)
                ->first();
        }
        else if ($fpx_productDesc == "School")
        {
            $organ = DB::table('organizations as o')
                ->leftJoin('fees_new as fn', 'o.id', 'fn.organization_id')
                ->leftJoin('fees_new_organization_user as fou', 'fou.fees_new_id', 'fn.id')
                ->where('fou.transaction_id', $transaction->id)
                ->select('o.private_key')
                ->first();

            if ($organ == null)
            {
                $organ = DB::table('organizations as o')
                    ->leftJoin('fees_new as fn', 'o.id', 'fn.organization_id')
                    ->leftJoin('student_fees_new as sfn', 'sfn.fees_id', 'fn.id')
                    ->leftJoin('fees_transactions_new as ftn', 'ftn.student_fees_id', 'sfn.id')
                    ->where('ftn.transactions_id', $transaction->id)
                    ->select('o.private_key')
                    ->first();
            }
        }
        else if ($fpx_productDesc == "Merchant" || $fpx_productDesc == "Koperasi"){
            $order = PgngOrder::where('transaction_id', $transaction->id)->first();

            $organ = Organization::find($order->organization_id);
        }
        else if ($fpx_productDesc == "Homestay"){

            $booking = Booking::where('transactionid', $transaction->id)
            ->orWhere('transaction_balance_id', $transaction->id)
            ->first();
            $room = Room::find($booking->roomid);
       
            $organ = Organization::find($room->homestayid);
        }
        else if ($fpx_productDesc == "OrderS"){
            $orders = Order::where('transaction_id', '=', $transaction->id)->first();

            $organ = Organization::find($orders->organ_id);
        }
        //dd($organ);
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
        return $resultArray ;
    }

    public function handle()
    {
        $transactions = DB::table('transactions')
            ->whereIn('status', ['Pending', 'Failed'])
            ->whereBetween('datetime_created', [now()->subDays(2), now()])
            ->get();

        // $transaction = DB::table('transactions')
        //             ->where('id',29268)
        //             ->get();
        foreach ($transactions as $transaction)
        {
        //old method()
            $fpx_sellerOrderNo = $transaction->nama;
            try{
                $response_value = $this->getTransactionInfo($transaction->id);
                if (!isset($response_value['fpx_DebitAuthCode']))
                {
                    continue;
                }

                if ($response_value['fpx_DebitAuthCode'] == '00') {
                    $fpx_productDesc = explode("_", $transaction->nama)[0];
                    switch ($fpx_productDesc) {
                        case 'School':
        
                            Transaction::where('nama', '=', $fpx_sellerOrderNo)->update(
                                [
                                    'status' => 'Success',
                                    'amount'=>$response_value['transactionAmount'] ,
                                    'description'=>$response_value['fpx_SellerExOrderNo']]
                            );
                            $transaction = Transaction::where('nama', '=', $fpx_sellerOrderNo)->first();
        
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
        
                            $result =  Transaction::where('nama', '=', $fpx_sellerOrderNo)->update(
                                [
                                    'status' => 'Success',
                                    'amount'=>$response_value['transactionAmount'] ,
                                    'description'=>$response_value['fpx_SellerExOrderNo']]
                            );
                           // return response()->json(['res'=>$result, 'sellerNo'=>$fpx_sellerOrderNo,'resp'=>$response_value]);
                            break;
                        
                        case 'Merchant':
                            Transaction::where('nama', '=', $fpx_sellerOrderNo)->update(
                                [
                                    'status' => 'Success',
                                    'amount'=>$response_value['transactionAmount'] ,
                                    'description'=>$response_value['fpx_SellerExOrderNo']]
                            );
                            $t = Transaction::where('nama', '=', $fpx_sellerOrderNo)->first();
                            
                            PgngOrder::where('transaction_id', $t->id)->first()->update([
                                'status' => 'Paid'
                            ]);
        
                            $order = PgngOrder::where('transaction_id', $t->id)->first();
        
                            $organization = Organization::find($order->organization_id);
                            $user = User::find($order->user_id);
                            
                            $relatedProductOrder =DB::table('product_order')
                            ->where([
                                ['pgng_order_id',$order->id],
                                ['deleted_at',NULL]
                            ])
                            ->select('product_item_id as itemId','quantity')
                            ->get();
                    
                            foreach($relatedProductOrder as $item){
                                $relatedItem=DB::table('product_item')
                                ->where('id',$item->itemId);
                                
                                $relatedItemQuantity=$relatedItem->first()->quantity_available;
                    
                                $newQuantity= intval($relatedItemQuantity - $item->quantity);
                               
                                if($newQuantity<=0){
                                    $relatedItem
                                    ->update([
                                        'quantity_available'=>0,
                                        'type' => 'no inventory',
                                        'status'=> 0
                                    ]);
                                }
                                else{
                                    $relatedItem
                                    ->update([
                                        'quantity_available'=>$newQuantity
                                ]);
                                }
                                
                            }
                            $item = DB::table('product_order as po')
                            ->join('product_item as pi', 'po.product_item_id', 'pi.id') 
                            ->where([
                                ['po.pgng_order_id', $order->id],
                                ['po.deleted_at',NULL],
                                ['pi.deleted_at',NULL],
                            ])
                            ->select('pi.name', 'po.quantity', 'pi.price')
                            ->get();
        
                            Mail::to($user->email)->send(new MerchantOrderReceipt($order, $organization, $t, $user));
                            Mail::to($organization->email)->send(new MerchantOrderReceipt($order, $organization, $t, $user));

                            break;
        
                        case 'Koperasi':
                            Transaction::where('nama', '=', $fpx_sellerOrderNo)->update(
                                [
                                    'status' => 'Success',
                                    'amount'=>$response_value['transactionAmount'] ,
                                    'description'=>$response_value['fpx_SellerExOrderNo']]
                            );
                            $t = Transaction::where('nama', '=', $fpx_sellerOrderNo)->first();
        
                            $order = PgngOrder::where('transaction_id', $t->id)->first();
                            
        
                            $pgngOrder= PgngOrder::where('transaction_id', $t->id)->first();
                            $pgngOrder->status=2;
                            $pgngOrder->created_at=now();
                            $pgngOrder->updated_at=now();
                            $pgngOrder->save();
                            
                            $organization = Organization::where('id','=',$order->organization_id)->first();
                            $user = User::where('id','=',$order->user_id)->first();
        
                            $relatedProductOrder =DB::table('product_order')
                            ->where('pgng_order_id',$order->id)
                            ->select('product_item_id as itemId','quantity')
                            ->get();
        
                            foreach($relatedProductOrder as $item){
                                $relatedItem=DB::table('product_item')
                                ->where('id',$item->itemId);
                                
                                $relatedItemQuantity=$relatedItem->first()->quantity_available;
        
                                $newQuantity= intval($relatedItemQuantity - $item->quantity);
                            
                                if($newQuantity<=0){
                                    $relatedItem
                                    ->update([
                                        'quantity_available'=>0,
                                        'status'=>0
                                    ]);
                                }
                                else{
                                    $relatedItem
                                    ->update([
                                        'quantity_available'=>$newQuantity
                                ]);
                                }
                                //dd($relatedItem);
                            }
                            
                            $item = DB::table('product_order as po')
                            ->join('product_item as pi', 'po.product_item_id', 'pi.id')
                            ->where('po.pgng_order_id', $order->id)
                            ->select('pi.name', 'po.quantity', 'pi.price')
                            ->get();
                            
                            Mail::to($user->email)->send(new MerchantOrderReceipt($order, $organization, $t, $user));
                            
        
                            break;
        
                        case 'Homestay':
                            Transaction::where('nama', '=', $fpx_sellerOrderNo)->update(
                                [
                                    'status' => 'Success',
                                    'amount'=>$response_value['transactionAmount'] ,
                                    'description'=>$response_value['fpx_SellerExOrderNo']]
                            );
                            $t = Transaction::where('nama', '=', $fpx_sellerOrderNo)->first();
        
                            // update booking table
                            // check whether is paying for deposit/full or balance 
                            $booking = Booking::where('transactionid', $t->id)
                            ->orWhere('transaction_balance_id', $t->id)
                            ->first();
                        
                            if($booking->deposit_amount > 0 ){
                                if($booking->status == "Deposited"){ // for paying balance
                                    $booking->status = "Balance Paid";
                                    $booking->updated_at = Carbon::now();
                                    $booking->save();                
                                }else{//paying deposit
                                    $booking->status = "Deposited";
                                    $booking->updated_at = Carbon::now();
                                    $booking->save();
                                }
                            }else{
                                // for full payment 
                                $booking->status = "Booked";
                                $booking->updated_at = Carbon::now();
                                $booking->save();
                            }
                            
                            $userid = $t->user_id;
                            
                            $room = Room::find($booking->roomid);
                            $user = User::find($t->user_id);
                            $organization = Organization::find($room->homestayid);
                            
                            $booking_order = Organization::join('rooms', 'organizations.id', '=', 'rooms.homestayid')
                            ->join('bookings','rooms.roomid','=','bookings.roomid')
                            ->where('bookings.bookingid',$booking->bookingid) // Filter by the selected homestay
                            ->select('organizations.id','organizations.nama','organizations.address', 'rooms.roomid', 'rooms.roomname', 'rooms.details', 'rooms.roompax', 'rooms.price','bookings.bookingid','bookings.checkin','bookings.checkout','bookings.totalprice','bookings.discount_received','bookings.increase_received','bookings.booked_rooms','bookings.deposit_amount','bookings.status')
                            ->get();
        
                            if($t->email != NULL)
                            {
                                Mail::to($t->email)->send(new HomestayReceipt($room,$booking, $organization, $t, $user));//mail to customer
                            }
                            Mail::to($organization->email)->send(new HomestayReceipt($room,$booking, $organization, $t, $user));//mail to homestay admin 
        
                            break;
        
                            case 'Grab Student':
                                Transaction::where('nama', '=', $fpx_sellerOrderNo)->update(
                                    [
                                        'status' => 'Success',
                                        'amount'=>$response_value['transactionAmount'] ,
                                        'description'=>$response_value['fpx_SellerExOrderNo']]
                                );
                                    $t = Transaction::where('nama', '=', $fpx_sellerOrderNo)->first();
                
                
                                    $booking = Grab_Booking::where('transactionid', '=', $t->id)->first();

        
                                    $result = DB::table('grab_bookings')
                                    ->where('id', $booking->id)
                                    ->update([
                                    'status' => "PAID"
                                    ]);   

                
                                    break;
                            case 'Bus':
                                Transaction::where('nama', '=', $fpx_sellerOrderNo)->update(
                                    [
                                        'status' => 'Success',
                                        'amount'=>$response_value['transactionAmount'] ,
                                        'description'=>$response_value['fpx_SellerExOrderNo']]
                                );
                                    $t = Transaction::where('nama', '=', $fpx_sellerOrderNo)->first();
                
                                    $userid = $t->user_id;
                
                                    $booking = Bus_Booking::where('transactionid', '=', $t->id)->first();
                                    $user = User::find($t->user_id);
                            
                                    if($t->email != NULL)
                                    {
                                        Mail::to($transaction->email)->send(new ResitBayaranBus($booking, $user));
                                    }
        
                                    $result = DB::table('bus_bookings')
                                    ->where('id', $booking->id)
                                    ->update([
                                    'status' => "PAID"
                                    ]);

                                    break;
                                
                        default:
                        Transaction::where('nama', '=', $fpx_sellerOrderNo)->update(
                            [
                                'status' => 'Success',
                                'amount'=>$response_value['transactionAmount'] ,
                                'description'=>$response_value['fpx_SellerExOrderNo']]
                        );

                            break;
                    }
                } 
                else 
                {
                    Transaction::where('nama', '=', $fpx_sellerOrderNo)->update(['status' => 'Failed']);
                }
            } 
            catch (\Throwable $th) {
                \Log::info('Error :'. ($th->getMessage()).' '. $transaction->id);
            }
        }
    
        \Log::info("Update Transaction Command Run Successfully!");
    }


    public function oldMethod(){
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
                        Transaction::where('nama', '=', $fpx_sellerExOrderNo)->update(['transac_no' => $response_value['fpx_fpxTxnId'], 'status' => 'Success']);

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
}
