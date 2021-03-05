@extends('layouts.master-without-nav')

@section('content')
<div class="container">
    <h2>Failed</h2>

    <br>
    <strong><p>The transaction has been cancelled by your own/rejected by your banking service. Please try again later.</p></strong>
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
