@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/css/checkbox.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/css/accordion.css') }}" rel="stylesheet" type="text/css" />

<style>
    .container-wrapper-scroll {
        width: 100%;
        height: 50vh;
        overflow-y: auto;
    }

    /* width */
    .container-wrapper-scroll::-webkit-scrollbar {
        width: 10px;
    }

    /* Track */
    .container-wrapper-scroll::-webkit-scrollbar-track {
        background: #f1f1f1 ;
    }

    /* Handle */
    .container-wrapper-scroll::-webkit-scrollbar-thumb {
        background: #888;
    }

    /* Handle on hover */
    .container-wrapper-scroll::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

</style>
@endsection

<?php
    //Merchant will need to edit the below parameter to match their environment.
    error_reporting(E_ALL);

    /* Generating String to send to fpx */
    /*For B2C, message.token = 01
    For B2B1, message.token = 02 */

    $fpx_msgToken="01";
    $fpx_msgType="BE";
    $fpx_sellerExId="EX00012323";
    $fpx_version="6.0";
    /* Generating signing String */
    $data=$fpx_msgToken."|".$fpx_msgType."|".$fpx_sellerExId."|".$fpx_version;
    /* Reading key */
    $priv_key = getenv('FPX_KEY');
    // $priv_key = file_get_contents('C:\\pki-keys\\DevExchange\\EX00012323.key');
    $pkeyid = openssl_get_privatekey($priv_key);
    openssl_sign($data, $binary_signature, $pkeyid, OPENSSL_ALGO_SHA1);
    $fpx_checkSum = strtoupper(bin2hex( $binary_signature ) );


    //extract data from the post

    extract($_POST);
    $fields_string="";

    //set POST variables
    $url ='https://uat.mepsfpx.com.my/FPXMain/RetrieveBankList';

    $fields = array(
                            'fpx_msgToken' => urlencode($fpx_msgToken),
                            'fpx_msgType' => urlencode($fpx_msgType),
                            'fpx_sellerExId' => urlencode($fpx_sellerExId),
                            'fpx_checkSum' => urlencode($fpx_checkSum),
                            'fpx_version' => urlencode($fpx_version)
                            
                    );
    $response_value=array();
    $bank_list=array();

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
        list($key1,$value1)=explode("=", $token);
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
    $bank_list['UOB0229']['nama']   = $bank_list['UOB0229']['value']  == "A" ? "UOB Bank - Test ID" : "UOB Bank - Test ID (OFFLINE)";
    // asort($bank_list);
    function compareByName($a, $b) {
        return strcasecmp($a["nama"], $b["nama"]);
    }
    usort($bank_list, 'compareByName');

    }
    catch(Exception $e){
        echo 'Error :', ($e->getMessage());
    }

?>

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Invois</h4>
        </div>
    </div>
</div>
<div class="card p-4">
    <div class="row">
        <div class="col-md-12 pb-3">
            <h3>{{ $getfees->nama  ?? '' }}</h3>
        </div>
        <div class="col-md-12 pb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Sila pilih item untuk dibayar</li>
            </ol>
        </div>
        <div class="col-md-12">
                <div class="row">
                    <div class="container-wrapper-scroll p-2 mb-3">
                        @foreach($getcat as $row)
                            <div class="col-md-12">
                                <div id="accordionExample{{ $row->cid }}" class="accordion shadow">
                                    <!-- Accordion item 1 -->
                                    <div class="card">
                                        <div id="heading{{ $row->cid }}"
                                            class="card-header bg-white shadow-sm border-0">
                                            <h6 class="mb-0 font-weight-bold"><a href="#" data-toggle="collapse"
                                                    data-target="#collapse{{ $row->cid }}" aria-expanded="true"
                                                    aria-controls="collapse{{ $row->cid }}"
                                                    class="d-block position-relative text-dark text-uppercase collapsible-link py-2">Kategori
                                                    {{ $row->cnama }}</a></h6>
                                        </div>
                                        <div id="collapse{{ $row->cid }}" aria-labelledby="heading{{ $row->cid }}"
                                            data-parent="#accordionExample{{ $row->cid }}" class="collapse show">
                                            <div class="card-body pl-0 pr-0">
                                                @foreach($getdetail->where('cid', $row->cid) as $row2)
                                                    <div class="inputGroup">
                                                        <input id="option{{ $row2->did }}" name="billcheck" value="{{ $row2->totalamount }}" onchange="checkD(this)" type="checkbox" />
                                                        <label for="option{{ $row2->did }}">
                                                            <span style="font-size: 18px">{{ $row2->dnama }}</span>
                                                            <br>
                                                            <span style="font-size: 14px;font-weight:100;">RM{{  number_format((float)$row2->totalamount, 2, '.', '') }} ({{ $row2->quantity }} kuantiti)</span>
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="col-md-8 p-3">
                       <h4>Jumlah : RM<span id="pay"></span> </h4>
                        <form method="POST" action="{{ route('fpxIndex') }}" enctype="multipart/form-data">
                       <select name="bankid" id="bankid" class="form-control">
                           <option value="">Select bank</option>
                           @foreach ($bank_list as $key=>$value)
                                <option value="{{ $value['key'] }}">{{ $value['nama'] }}</option>
                           @endforeach
                       </select>
                    </div>
                    <div class="col-md-4 p-2">
                            {{ csrf_field() }}
                            <input type="hidden" name="amount" id="amount" value="0.00">
                            <input type="hidden" name="o_id" id="o_id" value="{{ $getfees->id }}">
                            <input type="hidden" name="desc" id="desc" value="School Fees">
                            <div class="float-right">
                                <input type="checkbox" id="TC" name="TC" onchange="
                                    if (this.checked)
                                        document.getElementById('bayarBtn').disabled = false;
                                    else
                                        document.getElementById('bayarBtn').disabled = true;
                                    "><label style="margin-left: 5px" for="TC"><a href="https://www.mepsfpx.com.my/FPXMain/termsAndConditions.jsp" target="_blank">I Agree to the FPX Terms And Condition.</a></label>
                            </div>
                            <br>
                            <br>
                            <button id="bayarBtn" class="btn btn-success float-right" type="submit" onclick="return checkBank();" disabled>Bayar Sekarang</button>
                        </form>
                    </div>
                    <input type="hidden" name="bname" id="bname" value="{{ $getfees->nama  ?? '' }}">
                    <input type="hidden" name="ttlpay" id="ttlpay" value="0.00">
                    <input type="hidden" value="{{ route('payment') }}" id="routepay">
            </div>
        </div>
    </div>
    <hr>
    <h4>Powered by:</h4>
    <div class="row" style="align-self: center">
        <img src="assets/images/fpx/FPX.png" alt="FPXBanks" style="width: 50%">
    </div>
    {{-- <hr>
    <h4>FPX Terms & Conditions: <a href="https://www.mepsfpx.com.my/FPXMain/termsAndConditions.jsp" target="_blank">https://www.mepsfpx.com.my/FPXMain/termsAndConditions.jsp</a></h4> --}}
</div>
@endsection

@section('script')
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

    var amt = 0;

    $("#pay").html("0.00");

    function checkD(element) {
        var id = element.id;
        if (element.checked) {
            amt += parseFloat($("#" + id).val());
        } else {
            if(amt != 0)
            {
                amt -= parseFloat($("#" + id).val());
            }
        }   
        var total = parseFloat(amt).toFixed(2);
        $("#pay").html(total);
        $("input[name='amount']").val(total);

    }

    if ($('input[name="billcheck"]').not(':checked').length == 0) {
            $('input[name="checkall"]').prop("checked", true);
    }

</script>

    <script>

        var stripe = Stripe("pk_test_51I6AHSI3fJ2mpqjYMBKV0ioR1R1IA9rhHNtq2ILk4fgBfAItGOHeA0PL610VW67w55b2jHxa1tst80iuGDEarPMN00tXSQAxs7");
        var checkoutButton = document.getElementById("checkout-button");
        var linkpay = $("#routepay").val();

        checkoutButton.addEventListener("click", function () {

            var submitForm = new FormData();
            var allData = {
                "_token": "{{ csrf_token() }}",
                "bname": jQuery("#bname").val(),
                "ttlpay": jQuery("#ttlpay").val(), 
            }

            jQuery.each(allData, function(key, value) {
                submitForm.append(key, value);
            });

            jQuery.ajax({
                type: 'POST',
                url: linkpay,
                data: submitForm,
                processData: false,
                contentType: false,
                success: function(data) {
                    var obj = JSON.parse(data);
                    return stripe.redirectToCheckout({ sessionId: obj.id });
                    
                },
                error: function(data) {

                    console.error("Error:", data);
                }
            });
        });

        
    </script>
@endsection
