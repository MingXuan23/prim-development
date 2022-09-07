<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Report Per Student" name="description" />
    <meta content="UTeM" name="author" />
    <title>PRiM | Laporan Pelajar</title>

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
            border: 1px solid black !important;
            border-collapse: collapse !important;
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
                            <div class="col-12 pt-3">

                                <h4 style="text-align: center">{{$details->schoolName}}</h4>
                                <h5 style="text-align: center">{{$details->schoolAddress}}, {{$details->schoolPostcode}} {{$details->schoolState}}</h5>
                                <br>
                                <br>
                                <span> Laporan </span>

                                <br>
                                <br>
                                <table class="table table-bordered table-striped" style=" width:100%">
                                    <tr style="text-align: center">
                                        <th style="width:3%"> No. </th>
                                        <th>Kategori</th>
                                        <th>Bilangan Permintaan</th>
                                    </tr>
                                    @foreach ($data as $item)
                                    <tr>
                                        <td style="text-align: center"> {{ $loop->iteration }}.</td>
                                        <td>
                                            <div class="pl-2"> {{ $item->catname }} </div>
                                        </td>
                                        <td style="text-align: center">{{ $item->total }}</td>

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

</html>