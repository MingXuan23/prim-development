@extends('layouts.master')

@section('css')
    <link href="{{ URL::asset('assets/libs/chartist/chartist.min.css') }}" rel="stylesheet" type="text/css" />
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
                                <h4 class="font-weight-medium font-size-24" id="donor_day">0</h4>
                                <div class="mini-stat-label bg-success">
                                    <p class="mb-0">Hari Ini</p>
                                </div>
                            </div>
                            <div class="pt-2 float-right">
                                <p class="text-white-50 mb-0 mt-1"><button class="btn btn-secondary mx-2">Hari
                                        ini</button><button class="btn btn-secondary mx-2">Minggu
                                        ini</button><button class="btn btn-secondary mx-2">Bulan ini</button></p>
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
                                <h4 class="font-weight-medium font-size-24" id="donor_week">RM 0.00</h4>
                                <div class="mini-stat-label bg-success">
                                    <p class="mb-0">Minggu Ini</p>
                                </div>
                            </div>
                            <div class="pt-2 float-right">
                                <p class="text-white-50 mb-0 mt-1"><button class="btn btn-secondary mx-2">Hari
                                        ini</button><button class="btn btn-secondary mx-2">Minggu
                                        ini</button><button class="btn btn-secondary mx-2">Bulan ini</button></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Carta Transaksi Derma</h4>
                            <div id="ct-chart" class="ct-chart wid"></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Derma Terbaru</h4>
                            <div class="table-responsive">
                                <table class="table table-hover table-centered table-nowrap mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col">Name</th>
                                            <th scope="col">Date</th>
                                            <th scope="col">Amount</th>
                                            <th scope="col" colspan="2">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div>
                                                    <img src="assets/images/users/user-2.jpg" alt=""
                                                        class="avatar-xs rounded-circle mr-2"> Philip Smead
                                                </div>
                                            </td>
                                            <td>01/02/2021</td>
                                            <td>RM 90.00</td>
                                            <td><span class="badge badge-success">Paid</span></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div>
                                                    <img src="assets/images/users/user-3.jpg" alt=""
                                                        class="avatar-xs rounded-circle mr-2"> Brent Shipley
                                                </div>
                                            </td>
                                            <td>06/03/2021</td>
                                            <td>RM 4.00</td>
                                            <td><span class="badge badge-warning">Pending</span></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div>
                                                    <img src="assets/images/users/user-4.jpg" alt=""
                                                        class="avatar-xs rounded-circle mr-2"> Robert Sitton
                                                </div>
                                            </td>
                                            <td>09/04/2021</td>
                                            <td>RM 15.00</td>
                                            <td><span class="badge badge-warning">Pending</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
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

    {{-- <script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script> --}}

    <script>
        // on change event for organization_dropdown
        $('#organization_dropdown').change(function() {
            var organizationid = $("#organization_dropdown option:selected").val();

            $.ajax({
                type: 'GET',
                url: '{{ route('dashboard.item') }}',
                data: {
                    id: organizationid
                },
                success: function(data) {

                    var a = ["day", "week", "month"];

                    console.log(donation);
                    for (var i = 0; i < a.length; i++) {
                        console.log("donor_" + a[i]);
                        console.log("donation_" + a[i]);
                        try {
                            var donor = data.data["donor_" + a[i]].donor;
                            var donation = data.data["donation_" + a[i]].donation_amount;

                            console.log(donor);

                            document.getElementById("donor_" + a[i]).innerHTML = donor ?? 0;
                            document.getElementById("donation_" + a[i]).innerHTML = donation ?? 0;
                        } catch (e) {
                            console.log(e);
                        }
                    }
                }
            });
        });

        var chart = new Chartist.Line('.ct-chart', {
            labels: ['01/02 10.00 pm', '06/03 3.00 pm', '09/04 11.00 am', '10/04 6.00 am', ''],
            series: [
                [{
                    meta: 'Robert Sitton',
                    value: 15
                }, {
                    meta: 'Brent Shipley',
                    value: 4
                }, {
                    meta: 'Philip Smead',
                    value: 90
                }, {
                    meta: 'Adi Iman',
                    value: 40
                }, 0]
            ]
        }, {
            // Remove this configuration to see that chart rendered with cardinal spline interpolation
            // Sometimes, on large jumps in data values, it's better to use simple smoothing.
            lineSmooth: Chartist.Interpolation.simple({
                divisor: 2
            }),
            fullWidth: true,
            chartPadding: {
                right: 20
            },
            low: 0,
            plugins: [
                Chartist.plugins.tooltip()
            ]
        });

    </script>
@endsection
