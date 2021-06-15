@extends('layouts.master')

@section('css')
    <link href="{{ URL::asset('assets/libs/chartist/chartist.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/libs/datatables/datatables.min.css') }}" rel="stylesheet">

    {{-- @include('layouts.datatable') --}}
@endsection

@section('content')
    <!-- start page title -->
    <div style="padding-top: 12px" class="row">
        <div class="col-md-12 ">
            <div class=" align-items-center">
                <div class="form-group card-title">
                    <select name="organization" id="organization_dropdown" class="form-control col-md-12">
                        <option value="" selected>Pilih Organisasi</option>
                        @isset($organizations)
                            @foreach ($organizations as $organization)
                                <option value="{{ $organization->id }}">{{ $organization->nama }}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6 col-md-9">
            <div class="card mini-stat bg-primary text-white">
                <div class="card-body">
                    <div class="mb-4">
                        <div class="float-left mini-stat-img mr-4">
                            <img src="assets/images/services-icon/donation.png" alt="">
                        </div>
                        <h5 class="font-size-16 text-uppercase mt-0 text-white-50">Jumlah Penderma</h5>
                        <h4 class="font-weight-medium font-size-24" id="total_donor">0</h4>
                        <div class="mini-stat-label bg-success">
                            <p id="p_donor_day" class="mb-0">Hari Ini</p>
                        </div>
                    </div>
                    <div class="pt-2 float-right">
                        <div class="btn-group" role="group" aria-label="Basic example">
                            <button id="btn_donor_day" onclick="getTotalDonor(this.id)" class="btn btn-secondary">Hari
                                ini</button>
                            <button id="btn_donor_week" onclick="getTotalDonor(this.id)" class="btn btn-secondary">Minggu
                                ini</button>
                            <button id="btn_donor_month" onclick="getTotalDonor(this.id)" class="btn btn-secondary">Bulan
                                ini</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-md-9">
            <div class="card mini-stat bg-primary text-white">
                <div class="card-body">
                    <div class="mb-4">
                        <div class="float-left mini-stat-img mr-4">
                            <img src="assets/images/services-icon/donate.png" style="max-width: 40px" alt="">
                        </div>
                        <h5 class="font-size-16 text-uppercase mt-0 text-white-50">Derma Terkumpul</h5>
                        <h4 class="font-weight-medium font-size-24" id="total_donation">RM 0.00</h4>
                        <div class="mini-stat-label bg-success">
                            <p id="p_donation_day" class="mb-0">Minggu Ini</p>
                        </div>
                    </div>
                    <div class="pt-2 float-right">
                        <div class="btn-group" role="group" aria-label="Basic example">
                            <button id="btn_day" class="btn btn-secondary" onclick="getTotalDonation(this.id)">Hari
                                ini</button>
                            <button id="btn_week" class="btn btn-secondary" onclick="getTotalDonation(this.id)">Minggu
                                ini</button>
                            <button id="btn_month" class="btn btn-secondary" onclick="getTotalDonation(this.id)">Bulan
                                ini</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Carta Transaksi Derma</h4>
                    <div id="ct-chart" class="ct-chart wid"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Derma Terbaru</h4>
                    <div class="table-responsive">
                        <table id="donorTable" class="table table-bordered table-striped dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr style="text-align:center">
                                    <th> No. </th>
                                    <th> Nama Penderma </th>
                                    <th> Tarikh </th>
                                    <th> Jumlah (RM) </th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->


@endsection

@section('script')
    <!-- Peity chart-->
    <script src="{{ URL::asset('assets/libs/peity/peity.min.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/chartist/chartist.min.js') }}"></script>

    <script src="{{ URL::asset('assets/libs/moment/moment.min.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/datatables/datatables.min.js') }}" defer></script>
    {{-- <script src="//cdn.datatables.net/plug-ins/1.10.12/sorting/datetime-moment.js"></script> --}}


    {{-- <script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script> --}}

    @include('dashboard.index')
@endsection
