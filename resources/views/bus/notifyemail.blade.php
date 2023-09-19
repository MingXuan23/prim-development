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
    <div class="container">
        <div class="row mt-3">
            <div class="col-12">
                <div class="card mb-1">
                    <div class="card-body py-5">
                        <div class="row">
                            <div class="col-lg-2 col-sm-12 p-0">
                            </div>
                            @foreach ($bus_notify as $item)
                            <div class="col-lg-6 col-sm-12 p-0">
                                <h4>{{ $user->name }}</h4>
                            </div>
                            <div class="col-12 pt-3" >
                                <h4>Anda boleh membuat bayaran perjalanan anda ke {{ $item->bus_destination }}</h4>
                            </div>
                            <div class="col-12 pt-3">
                            <h4>Berikut adalah butiran tempahan perjalanan anda : </h4>
                                <br>
                                <table class="table table-bordered table-striped" style="">
                                    <tr style="text-align: center">
                                        <th style="width:20%">Berlepas Dari</th>
                                        <th style="width:20%">Destinasi</th>
                                        <th style="width:20%">Nombor Trip</th>
                                        <th style="width:20%">No Plat Bas</th>
                                        <th style="width:20%">Waktu Perjalanan</th>
                                        <th style="width:20%">Tarikh Perjalanan</th>
                                        <th style="width:20%">Waktu Notify Tempahan</th>
                                        <th style="width:20%">Harga Trip</th>
                                    </tr>
                                    <tr>
                                        <td style="text-align: center">{{ $item->bus_depart_from }}</td>
                                        <td style="text-align: center">{{ $item->bus_destination }}</td>
                                        <td style="text-align: center">{{ $item->trip_number }}</td>
                                        <td style="text-align: center">{{ $item->bus_registration_number }}</td>
                                        <td style="text-align: center">{{ $item->departure_time }}</td>
                                        <td style="text-align: center">{{ $item->departure_date }}</td>
                                        <td style="text-align: center">{{ $item->time_notify }}</td>
                                        <td style="text-align: center">RM {{ $item->price_per_seat }}</td>
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