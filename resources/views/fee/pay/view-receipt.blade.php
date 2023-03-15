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
                                    <img src="{{ URL::asset('/organization-picture/'.$get_organization->organization_picture) }}" height="80"
                                        alt="" />
                                </center>
                            </div>
                            <div class="col-lg-6 col-sm-12 p-0">
                                <h4>{{ $get_organization->nama }}</h4>
                                <p>{{ $get_organization->address }},
                                    <br />
                                    {{ $get_organization->postcode }} {{ $get_organization->city }}, {{ $get_organization->state }}
                                </p>
                            </div>
                            <div class="col-lg-4 col-sm-12">
                                <table style="width: 100%">
                                    <tr style="background-color:#e9ecef">
                                        <th colspan="6" class="text-center">Resit</th>
                                    </tr>
                                    <tr>
                                        <td>No Resit</td>
                                        <td style="width: 20px">:</td>
                                        <td>{{ $get_transaction->description }}</td>
                                    </tr>
                                    <tr>
                                        <td>No Transaksi FPX</td>
                                        <td>:</td>

                                        <td>{{ $get_transaction->transac_no }}</td>
                                    </tr>
                                    <tr>
                                        <td>Tarikh</td>
                                        <td>:</td>
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
                                        <td class="py-3">Nama</td>
                                        <td class="py-2">:</td>
                                        <td class="py-2 w-50">{{ $getparent->name }}</td>
                                        <td class="py-2" colspan="3"></td>
                                        <td class="py-2">No. Kad Pengenalan
                                        </td>
                                        <td class="py-2">:</td>
                                        <td class="py-2">{{ $getparent->icno }}</td>
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

                                @if (count($getfees_categoryA) != 0)
                                <div class="pt-2" style="border-bottom:2px solid #e0e0e0;font-size: 18px">
                                    {{ $get_organization->nama }}
                                </div>



                                <div class="pt-2 pb-2">
                                    Kategori A
                                </div>

                                <table class="table table-bordered table-striped" style="">
                                    <tr style="text-align: center">
                                        <th style="width:3%">Bil.</th>
                                        <th>Item</th>
                                        <th style="width:10%">Kuantiti</th>
                                        <th style="width:20%">Amaun per item (RM)</th>
                                        <th style="width:20%">Amaun (RM)</th>
                                    </tr>
                                    @foreach ($getfees_categoryA as $item)
                                    <tr>
                                        <td style="text-align: center"> {{ $loop->iteration }}.</td>
                                        <td>
                                            <div class="pl-2"> {{ $item->name }} </div>
                                        </td>
                                        <td style="text-align: center">{{ $item->quantity }}</td>
                                        <td style="text-align: center">
                                            {{  number_format((float)$item->price, 2, '.', '') }} </td>
                                        <td style="text-align: center">
                                            {{  number_format((float)$item->totalAmount, 2, '.', '')  }}</td>
                                    </tr>
                                    @endforeach

                                    <tr>
                                        <td></td>
                                        <td colspan="3" style="text-align:center"><b>Jumlah</b> </td>
                                        <td style="text-align:center">
                                            <b>{{ number_format($getfees_categoryA->sum('totalAmount'), 2)  }}</b>

                                        </td>
                                    </tr>

                                </table>
                                @endif

                                @if (count($get_student) != 0)

                                {{-- ******** --}}
                                @foreach ($get_student as $student)
                                <div class="pt-2" style="border-bottom:2px solid #e0e0e0;font-size: 18px">
                                    {{ $student->nama }} ({{ $student->classname }})
                                </div>
                                {{-- <center class="my-2">
                                    <span style="font-weight: bold;text-transform: uppercase;">
                                        {{ $student->feename }}
                                </span>
                                </center> --}}

                                @foreach ($get_category->where('studentid', $student->id) as $category)

                                <div class="pt-2 pb-2">
                                    {{ $category->category }}
                                </div>

                                <table class="table table-bordered table-striped" style="">
                                    <tr style="text-align: center">
                                        <th style="width:3%">Bil.</th>
                                        <th>Item</th>
                                        <th style="width:10%">Kuantiti</th>
                                        <th style="width:20%">Amaun per item (RM)</th>
                                        <th style="width:20%">Amaun (RM)</th>
                                    </tr>
                                    @foreach ($get_fees->where('studentid', $student->id)->where('category',
                                    $category->category) as $item)
                                    <tr>
                                        <td style="text-align: center"> {{ $loop->iteration }}.</td>
                                        <td>
                                            <div class="pl-2"> {{ $item->name }} </div>
                                        </td>
                                        <td style="text-align: center">{{ $item->quantity }}</td>
                                        <td style="text-align: center">
                                            {{  number_format((float)$item->price, 2, '.', '') }} </td>
                                        <td style="text-align: center">
                                            {{  number_format((float)$item->totalAmount, 2, '.', '')  }}</td>
                                    </tr>
                                    @endforeach

                                    <tr>
                                        <td></td>
                                        <td colspan="3" style="text-align:center"><b>Jumlah</b> </td>
                                        <td style="text-align:center">
                                            <b>{{ number_format($get_fees->where('studentid', $student->id)->where('category', $category->category)->sum('totalAmount'), 2)  }}</b>

                                        </td>
                                    </tr>

                                </table>
                                @endforeach



                                @endforeach
                                @endif

                                <table style="width:100%" class="infotbl">
                                    <tr>
                                        <td></td>
                                        <td colspan="3" style="text-align:right">
                                            Caj yang dikenakan oleh organisasi (RM)
                                        </td>
                                        <td style="text-align:center;width:20%">
                                            {{  number_format((float)$get_organization->fixed_charges, 2, '.', '') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td colspan="3" style="text-align:right;font-size:18px;"><b>Jumlah Bayaran
                                                (RM)</b> </td>
                                        <td style="text-align:center; width:20%; font-size:18px">
                                            <b>{{  number_format((float)$get_transaction->amount, 2, '.', '') }}</b>
                                        </td>
                                    </tr>
                                </table>

                                <div class="col-12 pt-5 text-center">
                                    <button class="btn btn-primary p-2 w-10 mx-2 btn-fill" style="font-size:18px"
                                        onclick="window.print();">
                                        <span class="mdi mdi-file-pdf"> Print </span>
                                    </button>
                                    <a href="{{ URL::previous() }}">
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