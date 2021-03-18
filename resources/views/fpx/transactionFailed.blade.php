@extends('layouts.master-without-nav')

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
            <p>Transaction Date: {{ $request->fpx_fpxTxnTime }}</p>
            <p>Transaction #: {{ $request->fpx_fpxTxnId }}</p>
            <p>Seller Order #: {{ $request->fpx_sellerOrderNo }}</p>
            <p>Transaction Amount: <span class="text-danger">RM0.00</span></p>
        </div>
    </div>

    <br>
    <strong><p>The transaction has been cancelled by your own/rejected by your banking service. Please try again later.</p></strong>
    <p>Please ensure with your bank account that no deductions were made. If there any, contact with your bank services.</p>
    <p>You will be redirected to the main page in <span id="time">Loading...</span></p>
    <p>Click <a href="/fees">here</a> if you're not redirecting to other page.</p>
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
            window.location.href = "/fees";
        }
    }, 1000);
</script>
