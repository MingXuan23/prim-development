<?php

//Merchant will need to edit the below parameter to match their environment.
error_reporting(E_ALL);

/* Generating String to send to fpx */
/*For B2C, message.token = 01
For B2B1, message.token = 02 */

$fpx_msgToken="01";
$fpx_msgType="BE";
// $fpx_sellerExId=config('app.env') == 'production' ? "EX00011125" : "EX00012323";
$fpx_sellerExId="EX00012323";
$fpx_version="6.0";
/* Generating signing String */
$data=$fpx_msgToken."|".$fpx_msgType."|".$fpx_sellerExId."|".$fpx_version;
/* Reading key */
$priv_key = getenv('FPX_KEY');
// $priv_key = file_get_contents('C:\\pki-keys\\DevExchange\\EX00012323.key');
$pkeyid = openssl_get_privatekey($priv_key, null);
openssl_sign($data, $binary_signature, $pkeyid, OPENSSL_ALGO_SHA1);
$fpx_checkSum = strtoupper(bin2hex($binary_signature));


//extract data from the post

extract($_POST);
$fields_string="";

//set POST variables
// $url = config('app.env') == 'production' ? config('app.PRODUCTION_BE_URL') : config('app.UAT_BE_URL');
$url = config('app.UAT_BE_URL');

$fields = array(
            'fpx_msgToken' => urlencode($fpx_msgToken),
            'fpx_msgType' => urlencode($fpx_msgType),
            'fpx_sellerExId' => urlencode($fpx_sellerExId),
            'fpx_checkSum' => urlencode($fpx_checkSum),
            'fpx_version' => urlencode($fpx_version)
        );
$response_value=array();
$bank_list=array();

try {
    //url-ify the data for the POST
    foreach ($fields as $key=>$value) {
        $fields_string .= $key.'='.$value.'&';
    }
    rtrim($fields_string, '&');

    //open connection
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    //set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);

    curl_setopt($ch, CURLOPT_POST, count($fields));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);

    // receive server response ...
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   
    //execute post
    $result = curl_exec($ch);
    // dd($result);
    //close connection
    curl_close($ch);

    $token = strtok($result, "&");
    while ($token !== false) {
        list($key1, $value1)=explode("=", $token);
        $value1=urldecode($value1);
        $response_value[$key1]=$value1;
        $token = strtok("&");
    }

    $fpx_msgToken=reset($response_value);

    //Response Checksum Calculation String
    $data=$response_value['fpx_bankList']."|".$response_value['fpx_msgToken']."|".$response_value['fpx_msgType']."|".$response_value['fpx_sellerExId'];

    // val == 00 verification success
    $token = strtok($response_value['fpx_bankList'], ",");
    while ($token !== false)
    {
        list($key1,$value1)=explode("~", $token);
        $value1=urldecode($value1);
        $bank_list[$key1]=[ 'value' => $value1, 'nama' => '', 'key' => $key1 ];
        $token = strtok(",");
    }

    // asort($bank_list);
    // dd($bank_list);
    // $bank_list['ABB0234']['nama']  = $bank_list['ABB0234']['value']  == "A" ? "Affin B2C - Test ID" : "Affin B2C - Test ID (OFFLINE)";
    // if (config('app.env') == 'production') {
    //     $bank_list['ABB0233']['nama']   = $bank_list['ABB0233']['value']  == "A" ? "Affin Bank" : "Affin Bank (OFFLINE)";
    //     $bank_list['ABMB0212']['nama']  = $bank_list['ABMB0212']['value'] == "A" ? "Alliance Bank (Personal)" : "Alliance Bank (OFFLINE)";
    //     $bank_list['AGRO01']['nama']    = $bank_list['AGRO01']['value']   == "A" ? "AGRONet" : "AGRONet (OFFLINE)";
    //     $bank_list['AMBB0209']['nama']  = $bank_list['AMBB0209']['value'] == "A" ? "AmBank" : "AmBank (OFFLINE)";
    //     $bank_list['BIMB0340']['nama']  = $bank_list['BIMB0340']['value'] == "A" ? "Bank Islam Sdn Bhd" : "Bank Islam Sdn Bhd (OFFLINE)";
    //     $bank_list['BMMB0341']['nama']  = $bank_list['BMMB0341']['value'] == "A" ? "Bank Muamalat" : "Bank Muamalat (OFFLINE)";
    //     $bank_list['BKRM0602']['nama']  = $bank_list['BKRM0602']['value'] == "A" ? "Bank Rakyat" : "Bank Rakyat (OFFLINE)";
    //     $bank_list['BSN0601']['nama']   = $bank_list['BSN0601']['value']  == "A" ? "BSN" : "BSN (OFFLINE)";
    //     $bank_list['BCBB0235']['nama']  = $bank_list['BCBB0235']['value'] == "A" ? "CIMB Clicks" : "CIMB Clicks (OFFLINE)";
    //     $bank_list['HLB0224']['nama']   = $bank_list['HLB0224']['value']  == "A" ? "Hong Leong Bank" : "Hong Leong Bank (OFFLINE)";
    //     $bank_list['HSBC0223']['nama']  = $bank_list['HSBC0223']['value'] == "A" ? "HSBC Bank" : "HSBC Bank (OFFLINE)";
    //     $bank_list['KFH0346']['nama']   = $bank_list['KFH0346']['value']  == "A" ? "KFH" : "KFH (OFFLINE)";
    //     $bank_list['MBB0228']['nama']   = $bank_list['MBB0228']['value']  == "A" ? "Maybank2E" : "Maybank2E (OFFLINE)";
    //     $bank_list['MB2U0227']['nama']  = $bank_list['MB2U0227']['value'] == "A" ? "Maybank2U" : "Maybank2U (OFFLINE)";
    //     $bank_list['OCBC0229']['nama']  = $bank_list['OCBC0229']['value'] == "A" ? "OCBC Bank" : "OCBC Bank (OFFLINE)";
    //     $bank_list['PBB0233']['nama']   = $bank_list['PBB0233']['value']  == "A" ? "Public Bank" : "Public Bank (OFFLINE)";
    //     $bank_list['RHB0218']['nama']   = $bank_list['RHB0218']['value']  == "A" ? "RHB Bank" : "RHB Bank (OFFLINE)";
    //     $bank_list['SCB0216']['nama']   = $bank_list['SCB0216']['value']  == "A" ? "Standard Chartered" : "Standard Chartered (OFFLINE)";
    //     $bank_list['UOB0226']['nama']   = $bank_list['UOB0226']['value']  == "A" ? "UOB Bank" : "UOB Bank (OFFLINE)";
    // } elseif (config('app.env') == 'local' || config('app.env') == 'staging') {
    //     $bank_list['ABB0234']['nama']   = $bank_list['ABB0234']['value'] == "A" ? "Affin B2C - Test ID" : "Affin B2C - Test ID (OFFLINE)";
    //     $bank_list['ABB0233']['nama']   = $bank_list['ABB0233']['value'] == "A" ? "Affin Bank" : "Affin Bank (OFFLINE)";
    //     $bank_list['ABMB0212']['nama']  = $bank_list['ABMB0212']['value'] == "A" ? "Alliance Bank (Personal)" : "Alliance Bank (OFFLINE)";
    //     $bank_list['AGRO01']['nama']    = $bank_list['AGRO01']['value']  == "A" ? "AGRONet" : "AGRONet (OFFLINE)";
    //     $bank_list['AMBB0209']['nama']  = $bank_list['AMBB0209']['value'] == "A" ? "AmBank" : "AmBank (OFFLINE)";
    //     $bank_list['BIMB0340']['nama']  = $bank_list['BIMB0340']['value'] == "A" ? "Bank Islam" : "Bank Islam (OFFLINE)";
    //     $bank_list['BMMB0341']['nama']  = $bank_list['BMMB0341']['value'] == "A" ? "Bank Muamalat" : "Bank Muamalat (OFFLINE)";
    //     $bank_list['BKRM0602']['nama']  = $bank_list['BKRM0602']['value'] == "A" ? "Bank Rakyat" : "Bank Rakyat (OFFLINE)";
    //     $bank_list['BSN0601']['nama']   = $bank_list['BSN0601']['value']  == "A" ? "BSN" : "BSN (OFFLINE)";
    //     $bank_list['BCBB0235']['nama']  = $bank_list['BCBB0235']['value'] == "A" ? "CIMB Clicks" : "CIMB Clicks (OFFLINE)";
    //     $bank_list['CIT0219']['nama']   = $bank_list['CIT0219']['value']  == "A" ? "Citibank" : "Citibank (OFFLINE)";
    //     $bank_list['HLB0224']['nama']   = $bank_list['HLB0224']['value']  == "A" ? "Hong Leong Bank" : "Hong Leong Bank (OFFLINE)";
    //     $bank_list['HSBC0223']['nama']  = $bank_list['HSBC0223']['value'] == "A" ? "HSBC Bank" : "HSBC Bank (OFFLINE)";
    //     $bank_list['KFH0346']['nama']   = $bank_list['KFH0346']['value']  == "A" ? "KFH" : "KFH (OFFLINE)";
    //     $bank_list['MBB0228']['nama']   = $bank_list['MBB0228']['value']  == "A" ? "Maybank2E" : "Maybank2E (OFFLINE)";
    //     $bank_list['MB2U0227']['nama']  = $bank_list['MB2U0227']['value'] == "A" ? "Maybank2U" : "Maybank2U (OFFLINE)";
    //     $bank_list['OCBC0229']['nama']  = $bank_list['OCBC0229']['value'] == "A" ? "OCBC Bank" : "OCBC Bank (OFFLINE)";
    //     $bank_list['PBB0233']['nama']   = $bank_list['PBB0233']['value']  == "A" ? "Public Bank" : "Public Bank (OFFLINE)";
    //     $bank_list['RHB0218']['nama']   = $bank_list['RHB0218']['value']  == "A" ? "RHB Bank" : "RHB Bank (OFFLINE)";
    //     $bank_list['TEST0021']['nama']  = $bank_list['TEST0021']['value'] == "A" ? "SBI Bank A" : "SBI Bank A (OFFLINE)";
    //     $bank_list['TEST0022']['nama']  = $bank_list['TEST0022']['value'] == "A" ? "SBI Bank B" : "SBI Bank B (OFFLINE)";
    //     $bank_list['TEST0023']['nama']  = $bank_list['TEST0023']['value'] == "A" ? "SBI Bank C" : "SBI Bank C (OFFLINE)";
    //     $bank_list['SCB0216']['nama']   = $bank_list['SCB0216']['value']  == "A" ? "Standard Chartered" : "Standard Chartered (OFFLINE)";
    //     $bank_list['UOB0226']['nama']   = $bank_list['UOB0226']['value']  == "A" ? "UOB Bank" : "UOB Bank (OFFLINE)";
    //     // $bank_list['UOB0229']['nama']   = $bank_list['UOB0229']['value']  == "A" ? "UOB Bank - Test ID" : "UOB Bank - Test ID (OFFLINE)";
    // }

    $bank_list['ABB0234']['nama']   = $bank_list['ABB0234']['value'] == "A" ? "Affin B2C - Test ID" : "Affin B2C - Test ID (OFFLINE)";
    $bank_list['ABB0233']['nama']   = $bank_list['ABB0233']['value'] == "A" ? "Affin Bank" : "Affin Bank (OFFLINE)";
    $bank_list['ABMB0212']['nama']  = $bank_list['ABMB0212']['value'] == "A" ? "Alliance Bank (Personal)" : "Alliance Bank (OFFLINE)";
    $bank_list['AGRO01']['nama']    = $bank_list['AGRO01']['value']  == "A" ? "AGRONet" : "AGRONet (OFFLINE)";
    $bank_list['AMBB0209']['nama']  = $bank_list['AMBB0209']['value'] == "A" ? "AmBank" : "AmBank (OFFLINE)";
    $bank_list['BIMB0340']['nama']  = $bank_list['BIMB0340']['value'] == "A" ? "Bank Islam" : "Bank Islam (OFFLINE)";
    $bank_list['BMMB0341']['nama']  = $bank_list['BMMB0341']['value'] == "A" ? "Bank Muamalat" : "Bank Muamalat (OFFLINE)";
    $bank_list['BKRM0602']['nama']  = $bank_list['BKRM0602']['value'] == "A" ? "Bank Rakyat" : "Bank Rakyat (OFFLINE)";
    $bank_list['BSN0601']['nama']   = $bank_list['BSN0601']['value']  == "A" ? "BSN" : "BSN (OFFLINE)";
    $bank_list['BCBB0235']['nama']  = $bank_list['BCBB0235']['value'] == "A" ? "CIMB Clicks" : "CIMB Clicks (OFFLINE)";
    $bank_list['CIT0219']['nama']   = $bank_list['CIT0219']['value']  == "A" ? "Citibank" : "Citibank (OFFLINE)";
    $bank_list['HLB0224']['nama']   = $bank_list['HLB0224']['value']  == "A" ? "Hong Leong Bank" : "Hong Leong Bank (OFFLINE)";
    $bank_list['HSBC0223']['nama']  = $bank_list['HSBC0223']['value'] == "A" ? "HSBC Bank" : "HSBC Bank (OFFLINE)";
    $bank_list['KFH0346']['nama']   = $bank_list['KFH0346']['value']  == "A" ? "KFH" : "KFH (OFFLINE)";
    $bank_list['MBB0228']['nama']   = $bank_list['MBB0228']['value']  == "A" ? "Maybank2E" : "Maybank2E (OFFLINE)";
    $bank_list['MB2U0227']['nama']  = $bank_list['MB2U0227']['value'] == "A" ? "Maybank2U" : "Maybank2U (OFFLINE)";
    $bank_list['OCBC0229']['nama']  = $bank_list['OCBC0229']['value'] == "A" ? "OCBC Bank" : "OCBC Bank (OFFLINE)";
    $bank_list['PBB0233']['nama']   = $bank_list['PBB0233']['value']  == "A" ? "Public Bank" : "Public Bank (OFFLINE)";
    $bank_list['RHB0218']['nama']   = $bank_list['RHB0218']['value']  == "A" ? "RHB Bank" : "RHB Bank (OFFLINE)";
    $bank_list['TEST0021']['nama']  = $bank_list['TEST0021']['value'] == "A" ? "SBI Bank A" : "SBI Bank A (OFFLINE)";
    $bank_list['TEST0022']['nama']  = $bank_list['TEST0022']['value'] == "A" ? "SBI Bank B" : "SBI Bank B (OFFLINE)";
    $bank_list['TEST0023']['nama']  = $bank_list['TEST0023']['value'] == "A" ? "SBI Bank C" : "SBI Bank C (OFFLINE)";
    $bank_list['SCB0216']['nama']   = $bank_list['SCB0216']['value']  == "A" ? "Standard Chartered" : "Standard Chartered (OFFLINE)";
    $bank_list['UOB0226']['nama']   = $bank_list['UOB0226']['value']  == "A" ? "UOB Bank" : "UOB Bank (OFFLINE)";



    function compareByName($a, $b) {
        return strcasecmp($a["nama"], $b["nama"]);
    }
    usort($bank_list, 'compareByName');

}
catch(Exception $e){
    echo 'Error :', ($e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link href="{{ URL::asset('assets/css/bootstrap.min.css') }}" id="bootstrap-light" rel="stylesheet"
        type="text/css" />
    <link href="{{ URL::asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/css/app.min.css') }}" id="app-light" rel="stylesheet" type="text/css" />
    <style>
        span {
            font-size: 1.09375rem;
            font-weight: bolder;
        }
    </style>
    <title>PRiM | Pembayaran</title>
    <link rel="shortcut icon" href="{{ URL::asset('assets/images/logo/fav-logo-prim.png')}}">

</head>

<body>
    <div class="container">
        <div class="card rounded-xl mt-4">
            <div class="card-body shadow rounded mb-1" style="background-color:#323447">
                <center>
                    <img src="{{ URL::asset('assets/images/logo/prim.svg') }}" alt="" height="50">
                </center>
            </div>
            @php
            $i =0 ;
            @endphp
            <div class="card-text p-4">
                @foreach($getstudent as $row)
                <h4 class=" mb-3" style="text-align: center">{{$row->studentname}}</h4>

                <hr>
                @foreach($getfees->where('studentid', $row->studentid) as $row2)

                <h4 class=" mb-3 mt-3">--{{ $row2->category }}--</h4>

                <div class="row">

                    @foreach($getfees_bystudent->where('studentid', $row->studentid)->where('category', $row2->category)
                    as $row3)
                    <div class="col-6">
                        <div class="row">
                            <div class="col-12">
                                <h4>{{ $loop->iteration }}. {{ $row3->name }}</h4>
                            </div>
                            <div class="col-12">
                                <h5 class="mt-0" style="color:#8699ad">
                                    Kuantiti x{{ $row3->quantity }}
                                </h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        @php
                        $i += $row3->quantity*$row3->price ;
                        @endphp
                        <h4 class="float-right">RM {{ number_format($row3->quantity*$row3->price, 2) }} </h4>
                    </div>

                    @endforeach



                </div>
                @endforeach
                <hr>
                @endforeach
                <div class="row mb-4">
                    <div class="col-6">
                        <h5 class=" mb-3">Cas yang dikenakan </h5>
                    </div>
                    <div class="col-6">
                        <h5 class="float-right mb-3">RM<span id="amount">
                                {{ number_format($getorganization->fixed_charges, 2) }}</span> </h5>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-6">
                        <h4 class=" mb-3">Jumlah Bayaran</h4>
                    </div>
                    <div class="col-6">
                        <h4 class="float-right mb-3">RM<span id="amount" style="font-size: 22px;">
                                {{ number_format($i + $getorganization->fixed_charges, 2) }}</span> </h4>
                    </div>
                </div>
                <div class="form-group">
                    <label for="sel1">Sila Pilih Bank:</label>

                    <form method="POST" action="{{ route('fpxIndex') }}" enctype="multipart/form-data">

                        <select name="bankid" id="bankid" class="form-control">
                            <option value="">Pilih bank</option>
                            @foreach ($bank_list as $key=>$value)
                            <option value="{{ $value['key'] }}">{{ $value['nama'] }}</option>
                            @endforeach
                        </select>
                </div>

                @foreach ($getstudentfees as $studentfees)

                <input type="hidden" name="student_fees_id[]" value="{{ $studentfees->id }}">

                @endforeach
                <ul>
                    <li>
                        <p>Minimum Transaction is RM1 and Maximum Transaction is RM30,000.</p>
                    </li>
                </ul>
                {{ csrf_field() }}
                <input type="hidden" name="amount" id="amount" value={{ $i + $getorganization->fixed_charges }}>
                {{-- <input type="hidden" name="o_id" id="o_id" value="{{ 1 }}"> --}}
                <input type="hidden" name="desc" id="desc" value="School_Fees">
                <div class="float-right">
                    <input type="checkbox" id="TC" name="TC" onchange="
                        if (this.checked)
                            document.getElementById('bayarBtn').disabled = false;
                        else
                            document.getElementById('bayarBtn').disabled = true;
                        "><label style="margin-left: 5px" for="TC"><a
                            href="https://www.mepsfpx.com.my/FPXMain/termsAndConditions.jsp" target="_blank">I Agree to
                            the FPX Terms And Condition.</a></label>
                </div>
                <button id="bayarBtn" class="btn btn-primary float-right mt-3 w-100 p-2" style="font-size:18px"
                    type="submit" onclick="return checkBank();" disabled>Teruskan
                    Pembayaran</button>

                </form>
            </div>

        </div>

    </div>
    <script>

    </script>
</body>

</html>

<script>
    function checkBank() {
        var t = jQuery('#bankid').val();
        var a = parseFloat(jQuery('#amount').val());
        if (t === '' || t === null) {
            alert('Please select a bank');
            return false;
        }
        if (a < 1.00) {
            alert('Transaction Amount is Lower than the Minimum Limit RM1.00 for B2C');
            return false;
        }
        else if (a > 30000.00) {
            alert('Transaction Amount Limit Exceeded RM30,000.00 for B2C');
            return false;
        }
    }
</script>