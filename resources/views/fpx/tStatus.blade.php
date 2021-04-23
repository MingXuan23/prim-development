@extends('layouts.master')

<?php
/// Summary description for Controller
///  ErrorCode  : Description
///  00         : Your signature has been verified successfully.  
///  06         : No Certificate found 
///  07         : One Certificate Found and Expired
///  08         : Both Certificates Expired
///  09         : Your Data cannot be verified against the Signature.
error_reporting(E_ALL);

$fpx_msgType="AE";
$fpx_msgToken="01";
$fpx_sellerExId="EX00012323";
$fpx_sellerExOrderNo=$request->fpx_sellerExOrderNo;
$fpx_sellerTxnTime=$request->fpx_fpxTxnTime;
$fpx_sellerOrderNo=$request->fpx_sellerOrderNo;
$fpx_sellerId="SE00013841";
$fpx_sellerBankCode="01";
$fpx_txnCurrency="MYR";
$fpx_txnAmount=$request->fpx_txnAmount;
$fpx_buyerEmail="";
$fpx_checkSum="";
$fpx_buyerName="";
$fpx_buyerBankId=$request->fpx_buyerBankId;
$fpx_buyerBankBranch=$request->fpx_buyerBankBranch;
$fpx_buyerAccNo="";
$fpx_buyerId="";
$fpx_makerName=$request->fpx_makerName;
$fpx_buyerIban="";
$fpx_productDesc="SampleProduct";
$fpx_version="6.0";

// $data=$fpx_buyerBankBranch."|".$fpx_buyerBankId."|".$fpx_buyerIban."|".$fpx_buyerId."|".$fpx_buyerName."|".$fpx_creditAuthCode."|".$fpx_creditAuthNo."|".$fpx_debitAuthCode."|".$fpx_debitAuthNo."|".$fpx_fpxTxnId."|".$fpx_fpxTxnTime."|".$fpx_makerName."|".$fpx_msgToken."|".$fpx_msgType."|".$fpx_sellerExId."|".$fpx_sellerExOrderNo."|".$fpx_sellerId."|".$fpx_sellerOrderNo."|".$fpx_sellerTxnTime."|".$fpx_txnAmount."|".$fpx_txnCurrency;

extract($_POST);
$fields_string="";

//set POST variables
$url ='https://uat.mepsfpx.com.my/FPXMain/sellerNVPTxnStatus.jsp';

$fields = array(
						'fpx_msgType' => urlencode($fpx_msgType),
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

// $val=verifySign_fpx($fpx_checkSum, $data);

$fpx_buyerBankBranch=$request->fpx_buyerBankBranch;
$fpx_buyerBankId=$request->fpx_buyerBankId;
$fpx_buyerIban=$request->fpx_buyerIban;
$fpx_buyerId=$request->fpx_buyerId;
$fpx_buyerName=$request->fpx_buyerName;
$fpx_creditAuthCode=$request->fpx_creditAuthCode;
$fpx_creditAuthNo=$request->fpx_creditAuthNo;
$fpx_debitAuthCode=$request->fpx_debitAuthCode;
$fpx_debitAuthNo=$request->fpx_debitAuthNo;
$fpx_fpxTxnId=$request->fpx_fpxTxnId;
$fpx_fpxTxnTime=$request->fpx_fpxTxnTime;
$fpx_makerName=$request->fpx_makerName;
$fpx_msgToken=$request->fpx_msgToken;
$fpx_msgType=$request->fpx_msgType;
$fpx_sellerExId=$request->fpx_sellerExId;
$fpx_sellerExOrderNo=$request->fpx_sellerExOrderNo;
$fpx_sellerId=$request->fpx_sellerId;
$fpx_sellerOrderNo=$request->fpx_sellerOrderNo;
$fpx_sellerTxnTime=$request->fpx_sellerTxnTime;
$fpx_txnAmount=$request->fpx_txnAmount;
$fpx_txnCurrency=$request->fpx_txnCurrency;
$fpx_checkSum=$request->fpx_checkSum;

 $val="00";
 $ErrorCode=" Your signature has been verified successfully. "." ErrorCode :[00]";

// if val is 00 sucess 
?>

@section ('css')
<style>
    @@media print {
        #printPageButton {
            display: none;
        }

        #returnPageButton {
            display: none;
        }

        #topnav {
            display: none;
        }

        @@page {
            margin: 0;
        }

        body {
            margin: 0.5cm;
        }
    }
</style>
@endsection

@section('content')
<div class="container" style="padding:50px">
    <div class="row">
        <div class="col-md-6">
            <address>
                <strong>{{ $user->username }}</strong>
                <br>
                <abbr title="Email">E:</abbr> {{ $user->email }}
                <br>
                <abbr title="Phone">P:</abbr> {{ $user->telno }}
                <br>
                <abbr title="Bank">B:</abbr> {{ $fpx_buyerBankBranch }}
            </address>
        </div>
        <div class="col-md-6 text-right">
            <p>
                <em>Transaction Date: {{ $request->fpx_fpxTxnTime }}</em>
            </p>
            <p>
                <em>Transaction #: {{ $request->fpx_fpxTxnId }}</em>
            </p>
            <p>
                <em>Seller Order #: {{ $request->fpx_sellerOrderNo }}</em>
            </p>
        </div>
    </div>
    <div class="row">
        <div class="text-center">
            <h1>Receipt : <span style="color: green">Successful</span></h1>
        </div>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Transaction</th>
                    <th class="text-center">Price</th>
                    <th class="text-center">Tax (Paid By JAIM)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="col-md-9"><em>{{ $request->fpx_sellerExOrderNo }}</em></td>
                    <td class="col-md-1" style="text-align: center"> RM {{ $fpx_txnAmount }} </td>
                    @if (explode("_", $fpx_sellerExOrderNo)[0] == "School Fees")
                    <td class="col-md-1 text-center text-danger">RM 1.50</td>
                    @else
                    <td class="col-md-1 text-center text-danger">RM 1.00</td>
                    @endif
                </tr>
                <tr>
                    <td>   </td>
                    <td>   </td>
                    <td class="text-right">
                        <h4><strong>Total: </strong></h4>
                    </td>
                    @if (explode("_", $fpx_sellerExOrderNo)[0] == "School Fees")
                    <td class="text-center text-danger">
                        <h4><strong>RM {{ $fpx_txnAmount }}</strong></h4>
                    </td>
                    @else
                    <td class="text-center text-danger">
                        <h4><strong>RM {{ $fpx_txnAmount }}</strong></h4>
                    </td>
                    @endif
                </tr>
            </tbody>
        </table>

        <a href="https://prim.my" id="returnPageButton" class="btn btn-primary">
            <span class="mdi mdi-chevron-left-circle"> Return</span>
        </a>
        <button type="button" onclick="window.print();" id="printPageButton" class="btn btn-success ml-2">
            <span class="mdi mdi-file-pdf"> Print</span>
        </button>
    </div>
</div>
@endsection