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
        body{
            color:black;
        }
        .table td,
        .table th {
            padding: .3rem !important;
            color: black;
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
                            <div class="col-lg-8 col-sm-12 p-0">
                                <center>
                                    <img src="{{ URL::asset('/organization-picture/'.$details->organization_picture) }}" height="80"
                                        alt="" />
                                </center>
                                <br>
                                <h4 style="text-align: center">{{ $details->schoolName }}</h4>
                                <p style="text-align: center">{{ $details->schoolAddress }},
                                    <br />
                                    {{ $details->schoolPostcode }} {{ $details->schoolState }}
                                </p>
                                <br>
                                <br>
                                <div class="pt-2 pb-2">
                                    Laporan Permintaan Keluar Berdasarkan Kategori Pada {{$start}} Hingga {{$end}}
                                </div>
                                <table class="table table-bordered table-striped" style="">
                                    <tr style="text-align: center">
                                        <th style="width:3%">No.</th>
                                        <th style="width:10%">Kategori</th>
                                        <th style="width:20%">Kuantiti</th>
                                    </tr>
                                    @foreach ($data as $item)
                                    <tr>
                                        <td style="text-align: center"> {{ $loop->iteration }}.</td>
                                        <td style="text-align: center">{{ $item->catname }}</td>
                                        <td style="text-align: center">{{ $item->total }}</td>
                                    </tr>
                                    @endforeach

                                    <tr>
                                        <td colspan="2" style="text-align:center"><b>Jumlah</b> </td>
                                        <td style="text-align:center">
                                            <b>{{ number_format($data->sum('total'))  }}</b>
                                        </td>
                                    </tr>

                                </table>
                                <div class="col-12 pt-5 text-center">
                                    <button id="print" class="btn btn-primary p-2 w-10 mx-2 btn-fill" style="font-size:18px"
                                        onclick="demoprint();">
                                        <span class="mdi mdi-file-pdf"> Print </span>
                                    </button>
                                    <a href="{{ URL::previous() }}">
                                        <button id="kembali" class="btn btn-danger p-2 w-10 mx-2" style="font-size:18px;">
                                            <span class="mdi mdi-chevron-left-circle"> Kembali</span>
                                        </button>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<script>
    function demoprint(){
        document.getElementById("print").style.display = "none";
        document.getElementById("kembali").style.display = "none";
        window.print();
    }
</script>
</html>