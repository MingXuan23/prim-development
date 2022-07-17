@extends('layouts.master-without-nav')

<?php

error_reporting(E_ALL);

extract($_POST);

$fpx_msgType="AE";
$fpx_msgToken="01";
$fpx_sellerExId=config('app.env') == 'production' ? "EX00011125" : "EX00012323";
$fpx_sellerExOrderNo=$request->fpx_sellerExOrderNo;
$fpx_sellerTxnTime=$request->fpx_fpxTxnTime;
$fpx_sellerOrderNo=$request->fpx_sellerOrderNo;
$fpx_sellerId=config('app.env') == 'production' ? "SE00045101" : "SE00013841";
$fpx_sellerBankCode="01";
$fpx_txnCurrency="MYR";
$fpx_txnAmount=$request->fpx_txnAmount;
$fpx_buyerEmail=$user->email;
// $fpx_checkSum=$user->fpx_checksum;
$fpx_checkSum="";
$fpx_buyerName=$user->username;
$fpx_buyerBankId=$request->fpx_buyerBankId;
$fpx_buyerAccNo="";
$fpx_buyerId="";
$fpx_buyerIban="";
$fpx_buyerBankBranch="";
$fpx_makerName="";
$fpx_productDesc=explode("_", $request->fpx_sellerExOrderNo)[0];
$fpx_version="6.0";

$data = $fpx_buyerAccNo . "|" . $fpx_buyerBankBranch . "|" . $fpx_buyerBankId . "|" . $fpx_buyerEmail . "|" . $fpx_buyerIban . "|" . $fpx_buyerId . "|" . $fpx_buyerName . "|" . $fpx_makerName . "|" . $fpx_msgToken . "|" . $fpx_msgType . "|" . $fpx_productDesc . "|" . $fpx_sellerBankCode . "|" . $fpx_sellerExId . "|" . $fpx_sellerExOrderNo . "|" . $fpx_sellerId . "|" . $fpx_sellerOrderNo . "|" . $fpx_sellerTxnTime . "|" . $fpx_txnAmount . "|" . $fpx_txnCurrency . "|" . $fpx_version;
// $data=$fpx_buyerBankBranch."|".$fpx_buyerBankId."|".$fpx_buyerIban."|".$fpx_buyerId."|".$fpx_buyerName."|".$fpx_creditAuthCode."|".$fpx_creditAuthNo."|".$fpx_debitAuthCode."|".$fpx_debitAuthNo."|".$fpx_fpxTxnId."|".$fpx_fpxTxnTime."|".$fpx_makerName."|".$fpx_msgToken."|".$fpx_msgType."|".$fpx_sellerExId."|".$fpx_sellerExOrderNo."|".$fpx_sellerId."|".$fpx_sellerOrderNo."|".$fpx_sellerTxnTime."|".$fpx_txnAmount."|".$fpx_txnCurrency;

$priv_key = getenv('FPX_KEY');
$pkeyid = openssl_get_privatekey($priv_key, null);
openssl_sign($data, $binary_signature, $pkeyid, OPENSSL_ALGO_SHA1);
$fpx_checkSum = strtoupper(bin2hex($binary_signature));

$fields_string="";

//set POST variables
$url =config('app.env') == 'production' ? config('app.PRODUCTION_AE_AQ_URL') : config('app.UAT_AE_AQ_URL');

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
$response_value=array();

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
//echo "RESULT";
//echo $result;


//close connection
curl_close($ch);

$token = strtok($result, "&");
while ($token !== false)
{
	list($key1,$value1)=explode("=", $token);
	$value1=urldecode($value1);
	$response_value[$key1]=$value1;
	$token = strtok("&");
}

$fpx_debitAuthCode=reset($response_value);
//Response Checksum Calculation String
$data=$response_value['fpx_buyerBankBranch']."|".$response_value['fpx_buyerBankId']."|".$response_value['fpx_buyerIban']."|".$response_value['fpx_buyerId']."|".$response_value['fpx_buyerName']."|".$response_value['fpx_creditAuthCode']."|".$response_value['fpx_creditAuthNo']."|".$fpx_debitAuthCode."|".$response_value['fpx_debitAuthNo']."|".$response_value['fpx_fpxTxnId']."|".$response_value['fpx_fpxTxnTime']."|".$response_value['fpx_makerName']."|".$response_value['fpx_msgToken']."|".$response_value['fpx_msgType']."|".$response_value['fpx_sellerExId']."|".$response_value['fpx_sellerExOrderNo']."|".$response_value['fpx_sellerId']."|".$response_value['fpx_sellerOrderNo']."|".$response_value['fpx_sellerTxnTime']."|".$response_value['fpx_txnAmount']."|".$response_value['fpx_txnCurrency'];

} catch (Exception $e) {
    echo 'Error :', ($e->getMessage());
}

?>

@section('content')
<div class="container">
    <h2>Transaction Status: <span class="text-danger">Failed</span></h2>
    
    <div class="row" style="margin-top: 20px">
        <div class="col-6">
            <p>Name: {{ $user->username }}</p>
            <p>Email: {{ $user->email }}</p>
            <p>Phone No: {{ $user->telno }}</p>
            <p>Bank: {{ $request->fpx_buyerBankBranch }}</p>
        </div>
        <div class="col-6">
            <p>Transaction Date: {{ date_format(date_create($request->fpx_fpxTxnTime), 'd/M/Y H:i') }}</p>
            <p>Transaction #: {{ $request->fpx_fpxTxnId }}</p>
            <p>Seller Order #: {{ $request->fpx_sellerOrderNo }}</p>
            <p>Transaction Amount: <span class="text-danger">RM{{ $request->fpx_txnAmount }}</span></p>
        </div>
    </div>

    <br>
    <strong><p>The transaction has been cancelled by your own/rejected by your banking service. Please try again later.</p></strong>
    <p>Please ensure with your bank account that no deductions were made. If there any, contact with your bank services.</p>
    <p>You will be redirected to the main page in <span id="time">Loading...</span></p>
    <p>Click <a href="/derma">here</a> if you're not redirecting to other page.</p>
</div>
@endsection

<script>
    var time = 15;
    setInterval(function() {
        var seconds = time % 60;
        if (seconds.toString().length == 1) {
            seconds = "0" + seconds;
        }
        document.getElementById("time").innerHTML = seconds;
        time--;
        if (time == 0) {
            window.location.href = "/derma";
        }
    }, 1000);
</script>
