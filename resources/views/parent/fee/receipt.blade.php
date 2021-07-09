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
                            <div class="col-2 p-0">
                                <center>
                                    <img src="{{ URL::asset('assets/images/logo/prim-logo.svg') }}" height="80"
                                        alt="" />
                                </center>
                            </div>
                            <div class="col-6 p-0">
                                <h4>PRIM</h4>
                                <p>Jalan Hang Tuah Jaya,
                                    <br />
                                    76100 Durian Tunggal, Melaka
                                </p>
                            </div>
                            <div class="col-4">
                                <table style="width: 100%">
                                    <tr style="background-color:#e9ecef">
                                        <th colspan="6" class="text-center">Resit</th>
                                    </tr>
                                    <tr>
                                        <td>No Resit</td>
                                        <td style="width: 50px">:</td>
                                        <td>{{ $get_transaction->description }}</td>
                                    </tr>
                                    <tr>
                                        <td>Tarikh</td>
                                        <td style="width: 50px">:</td>
                                        <td>{{ $get_transaction->datetime_created->format('j M Y H:i:s A')}}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-12 pt-3">
                                <table style="width:100%" class="infotbl">
                                    <tr style="background-color:#e9ecef">
                                        <th colspan="9" class="text-center">Maklumat Penjaga</th>
                                    </tr>
                                    <tr>
                                        <td class="py-2">Nama</td>
                                        <td class="py-2">:</td>
                                        <td class="py-2">{{ $getparent->name }}</td>
                                        <td class="py-2" colspan="3"></td>
                                        <td class="py-2">No. Kad Pengenalan</td>
                                        <td class="py-2">:</td>
                                        <td class="py-2">{{ $getparent->icno }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2">Bayaran Kepada</td>
                                        <td class="py-2">:</td>
                                        <td class="py-2">{{ $get_fee_organization->oname }}</td>
                                        <td class="py-2" colspan="3"></td>
                                    </tr>
                                    <tr style="background-color:#e9ecef">
                                        <th colspan="9" class="text-center">Maklumat Yuran</th>
                                    </tr>
                                    {{-- <tr style="border-bottom:2px solid #e0e0e0">
                                        <td colspan="9" class="pt-2" style="font-size: 18px">
                                            Syuhaidi Bin Halim
                                        </td>
                                    </tr> --}}
                                </table>

                                @foreach ($getstudent as $student)
                                <div class="pt-2" style="border-bottom:2px solid #e0e0e0;font-size: 18px">
                                    {{ $student->studentnama }}
                                </div>
                                <center class="my-2">
                                    <span style="font-weight: bold;text-transform: uppercase;">
                                        {{ $student->feename }}
                                    </span>
                                </center>

                                @foreach ($getcategory->where('catid', $student->categoryid) as $category)

                                <span class="pt-2 pb-2">{{ $category->catname }}</span>

                                <table class="table table-bordered table-striped" style="">
                                    <tr style="text-align: center">
                                        <th style="width:3%">Bil.</th>
                                        <th>Item</th>
                                        <th style="width:10%">Kuantiti</th>
                                        <th style="width:20%">Amaun per item (RM)</th>
                                        <th style="width:20%">Amaun (RM)</th>
                                    </tr>
                                    @foreach ($getdetail->where('studentid', $student->studentid)->where('catid',
                                    $category->catid) as $item)
                                    <tr>
                                        <td style="text-align: center"> {{ $loop->iteration }}.</td>
                                        <td class="pl-2">{{ $item->detailsname }}</td>
                                        <td style="text-align: center">{{ $item->quantity }}</td>
                                        <td style="text-align: center">
                                            {{  number_format((float)$item->detailsprice, 2, '.', '') }} </td>
                                        <td style="text-align: center">
                                            {{  number_format((float)$item->totalamount, 2, '.', '')  }}</td>
                                    </tr>
                                    @endforeach

                                    <tr>
                                        <td></td>
                                        <td colspan="3" style="text-align:center"><b>Jumlah</b> </td>
                                        <td style="text-align:center">
                                            <b>{{ number_format($getdetail->where('catid', $student->categoryid)->sum('totalamount'), 2)  }}</b>
                                        </td>
                                    </tr>

                                </table>
                                @endforeach



                                @endforeach

                                <table style="width:100%" class="infotbl">
                                    <tr>
                                        <td></td>
                                        <td colspan="3" style="text-align:right">
                                            Cas yang dikenakan oleh organisasi (RM) 
                                        </td>
                                        <td style="text-align:center;width:20%">
                                            <b>{{  number_format((float)$get_fee_organization->fixed_charges, 2, '.', '') }}</b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td colspan="3" style="text-align:right"><b>Jumlah Bayaran (RM) </b> </td>
                                        <td style="text-align:center; width:20%">
                                            <b>{{  number_format((float)$get_transaction->amount, 2, '.', '') }}</b>
                                        </td>
                                    </tr>
                                </table>

                                <div class="col-12 pt-5 text-center">
                                    <button class="btn btn-primary p-2 w-10 mx-2 btn-fill" style="font-size:18px"
                                        onclick="window.print();">
                                        <span class="mdi mdi-file-pdf"> Print </span>
                                    </button>
                                    <a href="/home">
                                        <button class="btn btn-danger p-2 w-10 mx-2" style="font-size:18px;">
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

</html>