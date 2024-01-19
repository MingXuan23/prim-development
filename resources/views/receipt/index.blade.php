@extends('layouts.master')
<?php
/// Summary description for Controller
///  ErrorCode  : Description
///  00         : Your signature has been verified successfully.  
///  06         : No Certificate found 
///  07         : One Certificate Found and Expired
///  08         : Both Certificates Expired
///  09         : Your Data cannot be verified against the Signature.
// error_reporting(E_ALL);

// extract($_POST);

// $fpx_msgType="AE";
// $fpx_msgToken="01";
// $fpx_sellerExId= config('app.env') == 'production' ? "EX00011125" : "EX00012323";
// $fpx_sellerExOrderNo=$request->fpx_sellerExOrderNo;
// $fpx_sellerTxnTime=$request->fpx_fpxTxnTime;
// $fpx_sellerOrderNo=$request->fpx_sellerOrderNo;
// // $fpx_sellerId="SE00013841";
// $fpx_sellerId=$request->fpx_sellerId;
// $fpx_sellerBankCode="01";
// $fpx_txnCurrency="MYR";
// $fpx_txnAmount=$request->fpx_txnAmount;
// $fpx_buyerEmail=$transaction->email;
// // $fpx_checkSum=$user->fpx_checksum;
// $fpx_checkSum="";
// $fpx_buyerName=$transaction->username;
// $fpx_buyerBankId=$request->fpx_buyerBankId;
// $fpx_buyerAccNo="";
// $fpx_buyerId="";
// $fpx_buyerIban="";
// $fpx_buyerBankBranch="";
// $fpx_makerName="";
// $fpx_productDesc=explode("_", $request->fpx_sellerExOrderNo)[0];
// $fpx_version="6.0";

// // dd($fpx_checkSum);
// $data = $fpx_buyerAccNo . "|" . $fpx_buyerBankBranch . "|" . $fpx_buyerBankId . "|" . $fpx_buyerEmail . "|" . $fpx_buyerIban . "|" . $fpx_buyerId . "|" . $fpx_buyerName . "|" . $fpx_makerName . "|" . $fpx_msgToken . "|" . $fpx_msgType . "|" . $fpx_productDesc . "|" . $fpx_sellerBankCode . "|" . $fpx_sellerExId . "|" . $fpx_sellerExOrderNo . "|" . $fpx_sellerId . "|" . $fpx_sellerOrderNo . "|" . $fpx_sellerTxnTime . "|" . $fpx_txnAmount . "|" . $fpx_txnCurrency . "|" . $fpx_version;
// // $data=$fpx_buyerBankBranch."|".$fpx_buyerBankId."|".$fpx_buyerIban."|".$fpx_buyerId."|".$fpx_buyerName."|".$fpx_creditAuthCode."|".$fpx_creditAuthNo."|".$fpx_debitAuthCode."|".$fpx_debitAuthNo."|".$fpx_fpxTxnId."|".$fpx_fpxTxnTime."|".$fpx_makerName."|".$fpx_msgToken."|".$fpx_msgType."|".$fpx_sellerExId."|".$fpx_sellerExOrderNo."|".$fpx_sellerId."|".$fpx_sellerOrderNo."|".$fpx_sellerTxnTime."|".$fpx_txnAmount."|".$fpx_txnCurrency;

// $priv_key = getenv('FPX_KEY');
// $pkeyid = openssl_get_privatekey($priv_key, null);
// openssl_sign($data, $binary_signature, $pkeyid, OPENSSL_ALGO_SHA1);
// $fpx_checkSum = strtoupper(bin2hex($binary_signature));

// $fields_string="";

// //set POST variables
// $url = ($fpx_buyerBankId == 'TEST0021' ||  $fpx_buyerBankId == 'TEST0022' || $fpx_buyerBankId == 'TEST0023') 
//         ? config('app.UAT_AE_AQ_URL') 
//         : config('app.PRODUCTION_AE_AQ_URL');

// $fields = array(
// 						'fpx_msgType' => urlencode("AE"),
// 						'fpx_msgToken' => urlencode($fpx_msgToken),
// 						'fpx_sellerExId' => urlencode($fpx_sellerExId),
// 						'fpx_sellerExOrderNo' => urlencode($fpx_sellerExOrderNo),
// 						'fpx_sellerTxnTime' => urlencode($fpx_sellerTxnTime),
// 						'fpx_sellerOrderNo' => urlencode($fpx_sellerOrderNo),
// 						'fpx_sellerId' => urlencode($fpx_sellerId),
// 						'fpx_sellerBankCode' => urlencode($fpx_sellerBankCode),
// 						'fpx_txnCurrency' => urlencode($fpx_txnCurrency),
// 						'fpx_txnAmount' => urlencode($fpx_txnAmount),
// 						'fpx_buyerEmail' => urlencode($fpx_buyerEmail),
// 						'fpx_checkSum' => urlencode($fpx_checkSum),
// 						'fpx_buyerName' => urlencode($fpx_buyerName),
// 						'fpx_buyerBankId' => urlencode($fpx_buyerBankId),
// 						'fpx_buyerBankBranch' => urlencode($fpx_buyerBankBranch),
// 						'fpx_buyerAccNo' => urlencode($fpx_buyerAccNo),
// 						'fpx_buyerId' => urlencode($fpx_buyerId),
// 						'fpx_makerName' => urlencode($fpx_makerName),
// 						'fpx_buyerIban' => urlencode($fpx_buyerIban),
// 						'fpx_productDesc' => urlencode($fpx_productDesc),
// 						'fpx_version' => urlencode($fpx_version)
// 				);
// $response_value=array();

// try{
// //url-ify the data for the POST
// foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
// rtrim($fields_string, '&');

// //open connection
// $ch = curl_init();
// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
// curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

// //set the url, number of POST vars, POST data
// curl_setopt($ch,CURLOPT_URL, $url);

// curl_setopt($ch,CURLOPT_POST, count($fields));
// curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

// // receive server response ...
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// //execute post
// $result = curl_exec($ch);
// //echo "RESULT";
// //echo $result;

// //close connection
// curl_close($ch);

// $token = strtok($result, "&");
// while ($token !== false)
// {
// 	list($key1,$value1)=explode("=", $token);
// 	$value1=urldecode($value1);
// 	$response_value[$key1]=$value1;
// 	$token = strtok("&");
// }

// $fpx_debitAuthCode=reset($response_value);
// //Response Checksum Calculation String
// $data=$response_value['fpx_buyerBankBranch']."|".$response_value['fpx_buyerBankId']."|".$response_value['fpx_buyerIban']."|".$response_value['fpx_buyerId']."|".$response_value['fpx_buyerName']."|".$response_value['fpx_creditAuthCode']."|".$response_value['fpx_creditAuthNo']."|".$fpx_debitAuthCode."|".$response_value['fpx_debitAuthNo']."|".$response_value['fpx_fpxTxnId']."|".$response_value['fpx_fpxTxnTime']."|".$response_value['fpx_makerName']."|".$response_value['fpx_msgToken']."|".$response_value['fpx_msgType']."|".$response_value['fpx_sellerExId']."|".$response_value['fpx_sellerExOrderNo']."|".$response_value['fpx_sellerId']."|".$response_value['fpx_sellerOrderNo']."|".$response_value['fpx_sellerTxnTime']."|".$response_value['fpx_txnAmount']."|".$response_value['fpx_txnCurrency'];
// // dd($data);

// } catch (Exception $e) {
//     echo 'Error :', ($e->getMessage());
// }

// $val=verifySign_fpx($fpx_checkSum, $data);

// $fpx_buyerBankBranch=$request->fpx_buyerBankBranch;
// $fpx_buyerBankId=$request->fpx_buyerBankId;
// $fpx_buyerIban=$request->fpx_buyerIban;
// $fpx_buyerId=$request->fpx_buyerId;
// $fpx_buyerName=$request->fpx_buyerName;
// $fpx_creditAuthCode=$request->fpx_creditAuthCode;
// $fpx_creditAuthNo=$request->fpx_creditAuthNo;
// $fpx_debitAuthCode=$request->fpx_debitAuthCode;
// $fpx_debitAuthNo=$request->fpx_debitAuthNo;
// $fpx_fpxTxnId=$request->fpx_fpxTxnId;
// $fpx_fpxTxnTime=$request->fpx_fpxTxnTime;
// $fpx_makerName=$request->fpx_makerName;
// $fpx_msgToken=$request->fpx_msgToken;
// $fpx_msgType=$request->fpx_msgType;
// $fpx_sellerExId=$request->fpx_sellerExId;
// $fpx_sellerExOrderNo=$request->fpx_sellerExOrderNo;
// $fpx_sellerId=$request->fpx_sellerId;
// $fpx_sellerOrderNo=$request->fpx_sellerOrderNo;
// $fpx_sellerTxnTime=$request->fpx_sellerTxnTime;
// $fpx_txnAmount=$request->fpx_txnAmount;
// $fpx_txnCurrency=$request->fpx_txnCurrency;
// $fpx_checkSum=$request->fpx_checkSum;

//  $val="00";
//  $ErrorCode=" Your signature has been verified successfully. "." ErrorCode :[00]";

// if val is 00 sucess 
?>
@section('css')
<link href="{{ URL::asset('assets/css/receipt.css') }}" rel="stylesheet">
@endsection

@section('content')
<div>You are being redirected to our homepage in <span id="time">5</span> seconds</div>

<table class="body-wrap">
    <tbody>
        <tr>
            <td></td>
            <td class="container" width="600">
                <div class="content">
                    <table class="main" width="100%" cellpadding="0" cellspacing="0">
                        <tbody>
                            <tr>
                                <td class="content-wrap aligncenter">
                                    <table width="100%" cellpadding="0" cellspacing="0">
                                        <tbody>
                                            <tr>
                                                <td class="content-block">
                                                    <h2>{{ $organization->nama }}</h2>
                                                    <h3 style="font-size: 14px !important">{{ $organization->telno }} |
                                                        {{ $organization->email }}</h3>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="content-block">
                                                    <table class="invoice">
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    Nama: {{ $transaction->username }}<br>
                                                                    Email: {{ $transaction->email }}<br>
                                                                    Donation ID: {{ $transaction->nama }}<br>
                                                                    Tarikh Derma:
                                                                    {{ date('d-m-Y', strtotime($transaction->datetime_created)) }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <table class="invoice-items" cellpadding="0"
                                                                        cellspacing="0">
                                                                        <tbody>
                                                                            <tr>
                                                                                <td>{{ $donation->nama }}</td>
                                                                                <td class="alignright">RM
                                                                                    {{ number_format($transaction->amount , 2, '.', '') }}
                                                                                </td>
                                                                            </tr>
                                                                            {{-- @if ($donation->tax_payer)
                                                        <tr>
                                                            <td>Tax (Paid By {{ $donation->tax_payer }})
                                                                </td>
                                                                <td class="alignright">{{ $donation->total_tax }}</td>
                                                            </tr>
                                                            @endif --}}
                                                            <tr class="total">
                                                                <td class="alignright" width="80%">Total</td>
                                                                <td class="alignright">RM
                                                                    {{ number_format(($transaction->amount) , 2, '.', '')}}
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
            </td>
        </tr>
    </tbody>
</table>
</div>
</td>
<td></td>
</tr>
</tbody>
</table>
@endsection

@section('script')
<script>
    var count = 5;
    setInterval(function(){
        count--;
        document.getElementById('time').innerHTML = count;
        if (count == 1) {
            window.location = '/derma'; 
        }
    },1000);
</script>
@endsection