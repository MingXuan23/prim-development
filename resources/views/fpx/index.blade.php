@extends('layouts.master')

<div class="container" style="margin-top: 5%;">
    <form name="form1" id="form1" method="post"
        action="{{ $fpx_buyerBankId == 'TEST0021' ? config('app.UAT_AR_AD_URL')  :  config('app.PRODUCTION_AR_AD_URL')}}">
        @csrf
        <div class="card">
            <div class="card-body">
                <table border="0" cellpadding="2" cellspacing="1" width="100%">
                    <tbody>
                        <tr class="infoBoxContents">
                            <td valign="top" width="30%">
                                <table border="0" cellpadding="2" cellspacing="0" width="100%">
                                    <tbody>
                                        <tr>
                                            <td height="164" align="center" class="main"><b>Payment Method via FPX</b>
                                                <p>&nbsp;</p>
                                                <div><strong>You are being redirected to secure payment site in <span
                                                            id="time">5</span> seconds</strong></div>

                                                {{-- <input type="submit" style="cursor:hand" class="btn btn-primary"
                                                    onclick="pay()" value="Click to Pay" /> --}}
                                                <br>
                                                <br>
                                                <p> <img src="assets/images/FPXButton.PNG" border="2" /></p>
                                                <p class="main">&nbsp;</p>
                                                <p class="main"><strong>* You must have Internet Banking Account in
                                                        order to
                                                        make transaction using FPX.</strong></p>
                                                <p>&nbsp;</p>
                                                <p class="main"><strong>* Please ensure that your browser's pop up
                                                        blocker has
                                                        been disabled to avoid any interruption during making
                                                        transaction.</strong></p>
                                                <p>&nbsp;</p>
                                                <p class="main"><strong>* You will be redirected to secure payment site.
                                                        Do not close browser / refresh page until you receive
                                                        response.</strong></p>
                                                <p>&nbsp;</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>


        <input type=hidden value="{{ $fpx_msgType }}" name="fpx_msgType">
        <input type=hidden value="{{ $fpx_msgToken }}" name="fpx_msgToken">
        <input type=hidden value="{{ $fpx_sellerExId }}" name="fpx_sellerExId">
        <input type=hidden value="{{ $fpx_sellerExOrderNo }}" name="fpx_sellerExOrderNo">
        <input type=hidden value="{{ $fpx_sellerTxnTime }}" name="fpx_sellerTxnTime">
        <input type=hidden value="{{ $fpx_sellerOrderNo }}" name="fpx_sellerOrderNo">
        <input type=hidden value="{{ $fpx_sellerId }}" name="fpx_sellerId">
        <input type=hidden value="{{ $fpx_sellerBankCode }}" name="fpx_sellerBankCode">
        <input type=hidden value="{{ $fpx_txnCurrency }}" name="fpx_txnCurrency">
        <input type=hidden value="{{ $fpx_txnAmount }}" name="fpx_txnAmount">
        <input type=hidden value="{{ $fpx_buyerEmail }}" name="fpx_buyerEmail">
        <input type=hidden value="{{ $fpx_checkSum }}" name="fpx_checkSum">
        <input type=hidden value="{{ $fpx_buyerName }}" name="fpx_buyerName">
        <input type=hidden value="{{ $fpx_buyerBankId }}" name="fpx_buyerBankId">
        <input type=hidden value="{{ $fpx_buyerBankBranch }}" name="fpx_buyerBankBranch">
        <input type=hidden value="{{ $fpx_buyerAccNo }}" name="fpx_buyerAccNo">
        <input type=hidden value="{{ $fpx_buyerId }}" name="fpx_buyerId">
        <input type=hidden value="{{ $fpx_makerName }}" name="fpx_makerName">
        <input type=hidden value="{{ $fpx_buyerIban }}" name="fpx_buyerIban">
        <input type=hidden value="{{ $fpx_version }}" name="fpx_version">
        <input type=hidden value="{{ $fpx_productDesc }}" name="fpx_productDesc">
        <input type=hidden value="{{ $telno }}" name="telno">

        @if (substr($fpx_sellerExOrderNo, 0, 1) == 'S')

        @if ($getstudentfees)
            @foreach ($getstudentfees as $student_fees_id)
            <input type="hidden" name="student_fees_id[]" value="{{ $student_fees_id }}">
            @endforeach
        @endif

        @if ($getparentfees)
            @foreach ($getparentfees as $parent_fees_id)
            <input type="hidden" name="parent_fees_id[]" value="{{ $parent_fees_id }}">
            @endforeach
        @endif

        @endif


    </form>

</div>

@section('script')
<script>
    var values = $("#form1").serialize();
    
    // function redirectFPX() {
    //     $('#form1').submit();
    //     pay();
    // }
    
    // setTimeout("redirectFPX();",3000);
    var count = 5;
    setInterval(function(){
        count--;
        document.getElementById('time').innerHTML = count;
        if (count == 0) {
            $('#form1').submit();
            pay();
        }
    },1000);

    function pay(){
    $.ajax({
            url: "{{ route('trn') }}",
            type: "post",
            data: values ,
            success: function (response) {
            // You will get response from your PHP page (what you echo or print)
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    }
</script>
@endsection