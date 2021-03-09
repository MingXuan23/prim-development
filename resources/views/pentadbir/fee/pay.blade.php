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
        $bank_list[$key1]=$value1;
        $token = strtok(",");
    }


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
                                @if ($value == "A")
                                    <option value="{{ $key }}">{{ $key }}</option>
                                @else
                                    <option value="{{ $key }}">{{ $key }} (Offline)</option>
                                @endif
                           @endforeach
                           <option value="ABB0234">Affin B2C - Test ID</option>
                           <option value="ABB0233">Affin Bank</option>
                           <option value="ABMB0212">Alliance Bank (Personal)</option>
                           <option value="AGRO01">AGRONet</option>
                           <option value="AMBB0209">AmBank</option>
                           <option value="BIMB0340">Bank Islam</option>
                           <option value="BMMB0341">Bank Muamalat</option>
                           <option value="BKRM0602">Bank Rakyat</option>
                           <option value="BSN0601">BSN</option>
                           <option value="BCBB0235">CIMB Clicks</option>
                           <option value="CIT0219">Citibank</option>
                           <option value="HLB0224">Hong Leong Bank</option>
                           <option value="HSBC0223">HSBC Bank</option>
                           <option value="KFH0346">KFH</option>
                           <option value="MBB0228">Maybank2E</option>
                           <option value="MB2U0227"> Maybank2U</option>
                           <option value="OCBC0229">OCBC Bank</option>
                           <option value="PBB0233">Public Bank</option>
                           <option value="RHB0218">RHB Bank</option>
                           <option value="TEST0021">SBI Bank A</option>
                           <option value="TEST0022">SBI Bank B</option>
                           <option value="TEST0023">SBI Bank C</option>
                           <option value="SCB0216">Standard Chartered</option>
                           <option value="UOB0226">UOB Bank</option>
                           <option value="UOB0229">UOB Bank - Test ID</option>
                       </select>
                    </div>
                    <div class="col-md-4 p-2">
                            {{ csrf_field() }}
                            <input type="hidden" name="amount" id="amount" value="0.00">
                            <input type="hidden" name="o_id" id="o_id" value="{{ $getfees->id }}">
                            <input type="hidden" name="desc" id="desc" value="School Fees">
                            <br>
                            <br>
                            <button class="btn btn-success float-right" type="submit" onclick="return checkBank();">Bayar Sekarang</button>
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
            <a href="https://www.mepsfpx.com.my/FPXMain/termsAndConditions.jsp">
                <img src="assets/images/FPX_ParticipatingBanks.PNG" alt="FPXBanks">
            </a>
    </div>
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
