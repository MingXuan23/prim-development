<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Parental Relationship Information Management" name="description" />
    <meta content="UTeM" name="author" />
    <title>PRiM | Resit Pembayaran</title>

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
    <div class="d-flex justify-content-center mt-4">You are being redirected to our homepage in&nbsp;<span id="time"> 5 </span> &nbsp;seconds</div>
    <div class="container">
        <div class="row mt-3">
            <div class="col-12">
                <div class="card mb-1">
                    <div class="card-body py-5">
                        <div class="row">
                            <div class="col-lg-6 col-sm-12 p-0 d-flex align-items-center flex-wrap">
                                <img src="{{ $room->homestayImage[0]->image_path }}" height="100" width="150"/>
                                <div>
                                    <h4>{{ $room->roomname }}</h4>
                                    <p>{{ $room->address }}, {{ $room->area }},
                                        <br />
                                        {{ $room->postcode }}, {{$room->district}},{{ $room->state }}
                                    </p>
                                    <div>Organisasi: {{ $organization->nama }}</div>
                                    <div>Tel No: {{$organization->telno}}</div>
                                    <div>Email: {{$organization->email}}</div>
                                </div>

                            </div>
                            <div class="col-lg-4 col-sm-12">
                                <table style="width: 100%">
                                    <tr style="background-color:#e9ecef">
                                        <th colspan="6" class="text-center">Resit</th>
                                    </tr>
                                    <tr>
                                        <td>No Resit</td>
                                        <td style="width: 50px">:</td>
                                        <td>{{ $transaction->description }}</td>
                                    </tr>
                                    <tr>
                                        <td>Tarikh</td>
                                        <td style="width: 50px">:</td>
                                        <td>{{ $transaction->datetime_created->format('j M Y H:i:s A')}}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-12 pt-3">
                                <table style="width:100%" class="infotbl">
                                    <tr style="background-color:#e9ecef">
                                        <th colspan="9" class="text-center">Maklumat Pelanggan</th>
                                    </tr>
                                    <tr>
                                        <td class="py-3">Nama</td>
                                        <td class="py-2">:</td>
                                        <td class="py-2 w-50">{{ $user->name }}</td>
                                        <td class="py-2" colspan="3"></td>
                                        <td class="py-2">Tel No.
                                        </td>
                                        <td class="py-2">:</td>
                                        <td class="py-2">{{ $user->telno }}</td>
                                    </tr>
                                    <tr style="background-color:#e9ecef">
                                        <th colspan="9" class="text-center">Maklumat Tempahan</th>
                                    </tr>
                                    {{-- <tr style="border-bottom:2px solid #e0e0e0">
                                        <td colspan="9" class="pt-2" style="font-size: 18px">
                                            Syuhaidi Bin Halim
                                        </td>
                                    </tr> --}}
                                </table>
                                
                                <div class="pt-2 pb-2">
                                    
                                </div>

                                <table class="table table-bordered table-striped table-responsive" style="overflow-x:auto;">
                                    <tr style="text-align: center">
                                        <th style="width:3%">Bil.</th>
                                        <th style="width:10%">Nama Homestay</th>
                                        <th style="width:20%">Tarikh Dari</th>
                                        <th style="width:20%">Tarikh Hingga</th>
                                        <th style="width:20%">Amaun Semalam (RM)</th>
                                    </tr>
                                    @foreach ($booking_order as $item)
                                    <tr>
                                        <td style="text-align: center"> {{ $loop->iteration }}.</td>
                                        <td style="text-align: center">{{ $item->roomname }}</td>
                                        <td style="text-align: center">{{ $item->checkin }}</td>
                                        <td style="text-align: center">{{ $item->checkout }}</td>
                                        @if($booking_order[0]->booked_rooms == null)
                                            <td style="text-align: center">{{  $item->price  }}</td>
                                        @else
                                            <td style="text-align: center">{{  number_format($item->price * $booking_order[0]->booked_rooms , 2) }} (x{{$booking_order[0]->booked_rooms}} unit) </td>
                                        @endif

                                    </tr>
                                    @endforeach
                                    @if($booking_order[0]->discount_received  > 0)
                                    <tr>
                                        <td></td>
                                        <td colspan="3" style="text-align:center"><b>Diskaun Diterima</b> </td>
                                        <td style="text-align:center">
                                            <b>-{{ $booking_order[0]->discount_received  }}</b>
                                        </td>
                                    </tr>
                                    @endif
                                    @if($booking_order[0]->increase_received  > 0)
                                    <tr>
                                        <td></td>
                                        <td colspan="3" style="text-align:center"><b>Penambahan Diterima</b> </td>
                                        <td style="text-align:center">
                                            <b>+{{ $booking_order[0]->increase_received  }}</b>
                                        </td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td></td>
                                        <td colspan="3" style="text-align:center"><b>Jumlah</b> </td>
                                        <td style="text-align:center">
                                            <b>{{ $item->totalprice  }}</b>
                                        </td>
                                    </tr>

                                </table>

                                <table style="width:100%" class="infotbl">
                                    <tr>
                                        <td></td>
                                        <td colspan="3" style="text-align:right;font-size:18px;"><b>Jumlah Bayaran
                                                (RM)</b> </td>
                                        <td style="text-align:center; width:20%; font-size:18px">
                                            <b>{{  number_format((float)$transaction->amount, 2, '.', '') }}</b>
                                        </td>
                                    </tr>
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
    // setInterval(function(){
    //     count--;
    //     document.getElementById('time').innerHTML = count;
    //     if (count <= 0) {
    //         window.location = '/tempahananda'; 
    //     }
    // },1000);
</script>
</html>