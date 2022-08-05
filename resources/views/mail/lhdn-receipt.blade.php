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
    <div class="container">
        <div class="row mt-3">
            <div class="col-12">
                <div class="card mb-1">
                    <div class="card-body py-5">
                        <div class="row">
                            <div class="col-lg-2 col-sm-12 p-0">
                                <center>
                                    <img src="{{ URL::asset('/organization-picture/'. $organizationPic) }}" height="80"
                                        alt="" />
                                </center>
                            </div>

                            <div class="col-lg-6 col-sm-12 p-0">
                                <h4>{{ $organizationName }}</h4>
                                <p>{{ $ogranizationAddress }},
                                    <br />
                                    {{ $ogranizationPostCode }} {{ $ogranizationCity }}, {{ $ogranizationState }}
                                </p>
                            </div>

                            <div class="col-lg-4 col-sm-12">
                                <table style="width: 100%">
                                    <tr style="background-color:#e9ecef">
                                        <th colspan="6" class="text-center">Resit</th>
                                    </tr>
                                    <tr>
                                        <td class="py-3">No Resit</td>
                                        <td style="width: 50px">:</td>
                                        <td class="py-3">{{ $transactionName }}</td>
                                    </tr>
                                    <tr>
                                        <td class="pb-3">Tarikh</td>
                                        <td class="pb-3" style="width: 50px">:</td>
                                        <td class="pb-3">{{ $transactionDate->format('j M Y H:i:s A')}}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-12 pt-3">
                                <div class="pt-2 text-center" style="border-bottom:2px solid #e0e0e0;font-size: 18px; margin: 0px 0px 10px 0">
                                    {{ $donationName }} ({{ $doantionLHDNcode }})
                                </div>

                                <table style="width:100%" class="infotbl">

                                    <tr>
                                        <td class="py-3">Tarikh Mula Derma</td>
                                        <td class="py-2">:</td>
                                        <td class="py-2 w-50">{{ $doantionStartDate }}</td>
                                        <td class="py-2" colspan="3"></td>
                                        <td class="py-2">Tarikh Tutup Derma</td>
                                        <td class="py-2">:</td>
                                        <td class="py-2">{{ $doantionEndDate }}</td>
                                    </tr>

                                    <tr style="background-color:#e9ecef">
                                        <th colspan="9" class="text-center">Maklumat Pembayar</th>
                                    </tr>
                                    <tr>
                                        <td class="py-3">Nama</td>
                                        <td class="py-2">:</td>
                                        <td class="py-2 w-50">{{ $transactionUsername }}</td>
                                        <td class="py-2" colspan="3"></td>
                                        <td class="py-2">No. Kad Pengenalan</td>
                                        <td class="py-2">:</td>
                                        <td class="py-2">{{ $transactionIcno }}</td>
                                    </tr>
                                    <tr>
                                        <td class="pb-3">Emel</td>
                                        <td class="pb-3">:</td>
                                        <td class="pb-3 w-50">{{ $transactionEmail }}</td>
                                        <td class="pb-3" colspan="3"></td>
                                        <td class="pb-3">Alamat Menyurat</td>
                                        <td class="pb-3">:</td>
                                        <td class="pb-3">{{ $transactionUserAdress }}</td>
                                    </tr>
                                </table>

                                <table style="width:100%" class="infotbl">
                                    <tr>
                                        <td></td>
                                        <td colspan="3" style="text-align:right;font-size:18px;"><b>Jumlah Bayaran
                                                (RM)</b> </td>
                                        <td style="text-align:center; width:20%; font-size:18px">
                                            <b>{{  number_format((float)$transactionAmount, 2, '.', '') }}</b>
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
</html>