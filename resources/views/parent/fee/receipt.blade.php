@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/css/checkbox.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/css/accordion.css') }}" rel="stylesheet" type="text/css" />

<style>
    .table td,
    .table th {
        padding: .3rem !important;
    }
</style>

@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Resit</h4>
            <!-- <ol class="breadcrumb mb-0">
                                                        <li class="breadcrumb-item active">Welcome to Veltrix Dashboard</li>
                                                    </ol> -->
        </div>
    </div>
</div>
<div class="row mt-3">
    <div class="col-12">
        <div class="card mb-1">
            <div class="card-body py-3">
                <div class="row">
                    <div class="col-2 p-0">
                        <center>
                            <img src="{{ URL::asset('assets/images/logo/prim-logo.svg') }}" height="80" alt="" />
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
                            <span>{{ $student->feename }}</span>
                        </center>

                        @foreach ($getcategory as $category)

                        <span>{{ $category->catname }}</span>

                        <table class="table table-bordered table-striped" style="">
                            <tr style="text-align: center">
                                <th style="width:3%">Bil</th>
                                <th>Item</th>
                                <th>Kuantiti</th>
                                <th>Amaun per item (RM)</th>
                                <th>Amaun (RM)</th>
                            </tr>
                            @foreach ($getdetail->where('studentid', $student->studentid)->where('catid',
                            $category->catid) as $item)
                            <tr>
                                <td style="text-align: center"> {{ $loop->iteration }}.</td>
                                <td>{{ $item->detailsname }}</td>
                                <td style="text-align: center">{{ $item->quantity }}</td>
                                <td style="text-align: center">
                                    {{  number_format((float)$item->detailsprice, 2, '.', '') }} </td>
                                <td style="text-align: center">
                                    {{  number_format((float)$item->totalamount, 2, '.', '')  }}</td>
                            </tr>
                            @endforeach

                        </table>
                        @endforeach



                        @endforeach


                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- <div class="col-12 pt-3">
            <button class="btn btn-light p-2 w-25 mx-2 btn-fill float-right" style="font-size:18px">PRINT</button>
            <a href="/sales">
                <button class="btn btn-light p-2 w-25 mx-2 float-right"
                    style="font-size:18px;background-color:#983535;color:white !important">CLOSE</button>
            </a>
        </div> --}}
</div>
@endsection

@section('script')

@endsection