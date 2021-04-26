@extends('layouts.master')

@section('css')
    <link href="{{ URL::asset('assets/libs/chartist/chartist.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ URL::asset('assets/libs/datatables/datatables.min.css') }}" rel="stylesheet">

    {{-- @include('layouts.datatable') --}}
@endsection

@section('content')
    <!-- start page title -->
    <div class="row align-items-center">
        <div class="card-body">
            <div class="form-group">
                <select name="organization" id="organization_dropdown" class="form-control">
                    <option value="" selected>Pilih Organisasi</option>
                    @foreach ($organizations as $organization)
                        <option value="{{ $organization->id }}">{{ $organization->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="row">
                <div class="col-xl-6 col-md-6">
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
                                <p class="text-white-50 mb-0 mt-1">
                                    <button id="btn_donor_day" onclick="getTotalDonor(this.id)"  class="btn btn-secondary mx-2">Hari ini</button>
                                    <button id="btn_donor_week" onclick="getTotalDonor(this.id)"  class="btn btn-secondary mx-2">Minggu ini</button>
                                    <button id="btn_donor_month" onclick="getTotalDonor(this.id)"  class="btn btn-secondary mx-2">Bulan ini</button>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 col-md-6">
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
                                <p class="text-white-50 mb-0 mt-1">
                                    <button id="btn_day" class="btn btn-secondary mx-2" onclick="getTotalDonation(this.id)" >Hari ini</button>
                                    <button id="btn_week" class="btn btn-secondary mx-2" onclick="getTotalDonation(this.id)" >Minggu ini</button>
                                    <button id="btn_month" class="btn btn-secondary mx-2" onclick="getTotalDonation(this.id)" >Bulan ini</button></p>
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
                            {{-- <div class="table-responsive">
                                <table class="table table-hover table-centered table-nowrap mb-0" style="text-align: justify;">
                                    <thead>
                                        <tr>
                                            <th scope="col">Name</th>
                                            <th scope="col">Date</th>
                                            <th scope="col">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div>
                                                    <img src="assets/images/users/user-2.jpg" alt=""
                                                        class="avatar-xs rounded-circle mr-2">  
                                                </div>
                                            </td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div>
                                                    <img src="assets/images/users/user-3.jpg" alt=""
                                                        class="avatar-xs rounded-circle mr-2"> 
                                                </div>
                                            </td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div>
                                                    <img src="assets/images/users/user-4.jpg" alt=""
                                                        class="avatar-xs rounded-circle mr-2"> 
                                                </div>
                                            </td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
          {{-- <div class="row">
            <div class="col-xl-4 col-md-4">
                <div class="card bg-primary">
                    <div class="card-body">
                        <div class="text-center text-white py-4">
                            <h5 class="mt-0 mb-4 text-white-50 font-size-16">Jumlah Penderma Hari Ini</h5>
                            <h1 id="donor_day"></h1>
                            <p class="font-size-14 pt-1">Orang</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-primary">
                    <div class="card-body">
                        <div class="text-center text-white py-4">
                            <h5 class="mt-0 mb-4 text-white-50 font-size-16">Jumlah Penderma Minggu Ini</h5>
                            <h1 id="donor_week"></h1>
                            <p class="font-size-14 pt-1">Orang</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-primary">
                    <div class="card-body">
                        <div class="text-center text-white py-4">
                            <h5 class="mt-0 mb-4 text-white-50 font-size-16">Jumlah Penderma Bulan Ini</h5>
                            <h1 id="donor_month"></h1>
                            <p class="font-size-14 pt-1">Orang</p>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

        {{-- <div class="row">
            <div class="col-md-4">
                <div class="card bg-success">
                    <div class="card-body">
                        <div class="text-center text-white py-4">
                            <h5 class="mt-0 mb-4 text-white-50 font-size-16">Derma Terkumpul Hari Ini (RM)</h5>
                            <h1 id="donation_day"></h1>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success">
                    <div class="card-body">
                        <div class="text-center text-white py-4">
                            <h5 class="mt-0 mb-4 text-white-50 font-size-16">Derma Terkumpul Minggu Ini (RM)</h5>
                            <h1 id="donation_week"></h1>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success">
                    <div class="card-body">
                        <div class="text-center text-white py-4">
                            <h5 class="mt-0 mb-4 text-white-50 font-size-16">Derma Terkumpul Bulan Ini (RM)</h5>
                            <h1 id="donation_month"></h1>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
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
