<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Parental Relationship Information Management" name="description" />
    <meta content="UTeM" name="author" />
    <title>PRiM | Notify Penumpang</title>

    <link rel="shortcut icon" href="{{ URL::asset('assets/images/logo/fav-logo-prim.png')}}">
    <link href="{{ URL::asset('assets/libs/chartist/chartist.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/css/checkbox.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/css/accordion.css') }}" rel="stylesheet" type="text/css" />
    @include('layouts.head')

    <style>
        .table td,
        .table th {
            padding: .3rem !important;
        }
    </style>
</head>

<body>
<div hidden>You are being redirected to our homepage in <span id="time">5</span> seconds</div>
    <div class="container">
        <div class="row mt-3">
            <div class="col-12">
                <div class="card mb-1">
                    <div class="card-body py-5">
                        <div class="row">
                            <div class="col-lg-2 col-sm-12 p-0">
                            </div>
                            @foreach ($bus_booking as $item)
                            <div class="col-lg-6 col-sm-12 p-0">
                                <h4>{{ $user->name }}</h4>
                            </div>
                            <div class="col-12 pt-3" >
                                <h4>Ticket perjalanan anda ke {{ $item->bus_destination }}</h4>
                            </div>
                            <div class="col-12 pt-3">
                            <h4>Berikut adalah butiran tempahan perjalanan anda : </h4>
                                <br>
                                <table class="table table-bordered table-striped" style="">
                                <thead class="thead-dark">
                                    <tr class="text-center">
                                    <th>Ticket Number</th>
                                    <th>Bus Registration Number</th>
                                    <th>Trip Number</th>
                                    <th>Berlepas Dari</th>
                                    <th>Destinasi Bas</th>
                                    <th>Waktu Berlepas</th>
                                    <th>Tarikh Berlepas</th>
                                    <th>Harga Tiket</th>
                                    </tr>
                                </thead>
                                    <tr>
                                    <td>T-{{ $item->trip_number }}-{{ $item->bookid }}{{ $item->available_seat }}{{ $item->booked_seat }}</td>
                                    <td>{{ $item->bus_registration_number }}</td>
                                    <td>{{ $item->trip_number }}</td>
                                    <td>{{ $item->bus_depart_from }}</td>
                                    <td>{{ $item->bus_destination }}</td>
                                    <td>{{ $item->departure_time }}</td>
                                    <td>{{ $item->departure_date }}</td>
                                    <td>RM {{ $item->price_per_seat }}</td>
                                    </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body> 
<script>
    var count = 5;
    setInterval(function(){
        count--;
        document.getElementById('time').innerHTML = count;
        if (count <= 0) {
            window.location = '/home'; 
        }
    },1000);
</script>
</html>