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


                <h4 class=" mb-3" style="text-align: center">{{ $organization->nama }}</h4>

                @if ($order_dishes)

                <div class="row">
                    @foreach($order_dishes as $order_dish)
                    <div class="col-6">
                        <div class="row">
                            <div class="col-12">
                                <h4>{{ $loop->iteration }}. {{ $order_dish->name }}</h4>
                            </div>
                            <div class="col-12">
                                <h5 class="mt-0" style="color:#8699ad">
                                    Kuantiti x{{ $order_dish->quantity }}
                                </h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        @php
                        $i += $order_dish->quantity * $order_dish->price ;
                        @endphp
                        <h4 class="float-right">RM {{ number_format($order_dish->quantity * $order_dish->price, 2) }} </h4>
                    </div>
                    @endforeach
                </div>

                @endif

                <div class="row mb-4">
                    <div class="col-6">
                        <h5 class=" mb-3">Caj yang dikenakan </h5>
                    </div>
                    <div class="col-6">
                        <h5 class="float-right mb-3">RM<span id="amount">
                                {{ number_format($organization->fixed_charges, 2) }}</span> </h5>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-6">
                        <h4 class=" mb-3">Jumlah Bayaran</h4>
                    </div>
                    <div class="col-6">
                        <h4 class="float-right mb-3">RM<span id="amount" style="font-size: 22px;">
                                {{ number_format($i + $organization->fixed_charges, 2) }}</span> </h4>
                    </div>
                </div>

                <form method="POST" action="{{ route('api.fpxIndex') }}" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="sel1">Sila Pilih Bank:</label>
                    <select name="bankid" id="bankid" class="form-control" data-parsley-required-message="Sila pilih bank" required>
                        <option value="">Pilih bank</option>
                        @foreach ($banklists as $key => $value)
                        <option value="{{ $value['code'] }}">{{ $value['nama'] }}</option>
                        @endforeach
                    </select>
                </div>

                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <input type="hidden" name="user_id" value="{{ $user->id }}">

                <ul>
                    <li>
                        <p>Minimum Transaction is RM1 and Maximum Transaction is RM30,000.</p>
                    </li>
                </ul>
                {{ csrf_field() }}
                <input type="hidden" name="amount" id="amount" value={{ $i + $organization->fixed_charges }}>
                <input type="hidden" name="o_id" id="o_id" value="{{ $organization->id }}">
                <input type="hidden" name="desc" id="desc" value="Food_Order">
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
                    type="submit" onclick="return checkBank();" disabled>Teruskan Pembayaran</button>
                </form>
            </div>
        </div>
    </div>
</body>
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
</html>
