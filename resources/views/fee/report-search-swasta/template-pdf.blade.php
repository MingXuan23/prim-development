<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Parental Relationship Information Management" name="description" />
    <meta content="UTeM" name="author" />
    <title>PRiM | Laporan Yuran Kelas {{ $get_organization->classname }}</title>

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

                                <span> Kelas {{ $get_organization->classname }}</span>
                                <br>
                                <br>
                                <table class="table table-bordered table-striped" style=" width:100%">
                                    <tr style="text-align: center">
                                        <th style="width:3%">Bil.</th>
                                        <th>Nama Murid</th>
                                        <th style="width:20%">No Kad Pengenalan</th>
                                        <th style="width:10%">Jantina</th>
                                        <th style="width:20%">Status Yuran</th>
                                    </tr>
                                    @foreach ($data as $item)
                                    <tr>
                                        <td style="text-align: center"> {{ $loop->iteration }}.</td>
                                        <td>
                                            <div class="pl-2"> {{ $item->nama }} </div>
                                        </td>
                                        <td style="text-align: center">{{ $item->icno }}</td>
                                        <td style="text-align: center">
                                            {{  $item->gender }} </td>
                                        <td style="text-align: center">
                                            @if ($item->fees_status == 'Completed')
                                            <span class="badge badge-success"> Selesai </span>
                                            @else
                                            <span class="badge badge-danger"> Belum Selesai </span>

                                            @endif
                                        </td>
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