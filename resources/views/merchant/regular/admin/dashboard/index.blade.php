@extends('layouts.master')

@section('css')

<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/libs/datatables/datatables.min.css') }}" rel="stylesheet">

@endsection

@section('content')
<div style="padding-top: 12px" class="row">
    <div class="col-md-12 ">
        <div class=" align-items-center">
            <div class="form-group card-title">
                <select name="org" id="org_dropdown" class="form-control col-md-12">
                    <option value="" selected>Pilih Organisasi</option>
                    @foreach($merchant as $row)
                    <option value="{{ $row->id }}">{{ $row->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

        <div class="container-fluid">
            <!-- start page title -->
            <div class="page-title-box">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="page-title">Dashboard Peniaga</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item active "><span class="org-name"></span></li> 
                        </ol>
                        
                    </div>
                    {{-- <div class="col-md-4 d-flex justify-content-end">
                        <div class="d-none d-md-block">
                            <a class="btn btn-primary" href="{{ route('admin-reg.edit-merchant') }}">
                                <i class="fas fa-cog mr-2"></i> Kemaskini
                            </a>
                        </div>
                    </div> --}}
                </div>
            </div>
            <!-- end page title -->
            
            <div class="row">
                <div class="col-xl-6 col-md-9">
                    <div class="card mini-stat bg-primary text-white">
                        <div class="card-body">
                            <div class="mb-4">
                                <div class="float-left mini-stat-img mr-4">
                                    <img src="{{ URL('assets/images/services-icon/02.png')}}" alt="">
                                </div>
                                <h5 class="font-size-16 text-uppercase mt-0 text-white-50">Jumlah Pesanan</h5>
                                <div class="font-weight-medium font-size-24" id="total_order">                        
                                </div>
                                <div id="order_label" class="mini-stat-label bg-success mb-0">
                                </div>
                            </div>
                            <div class="pt-2 float-right">
                                <div class="btn-group" role="group" aria-label="Basic example">
                                    <button id="btn_order_day" onclick="getTotalOrder(this.id)" class="btn btn-secondary">Hari
                                        ini</button>
                                    <button id="btn_order_week" onclick="getTotalOrder(this.id)" class="btn btn-secondary">Minggu
                                        ini</button>
                                    <button id="btn_order_month" onclick="getTotalOrder(this.id)" class="btn btn-secondary">Bulan
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
                                    <img src="{{ URL('assets/images/services-icon/03.png')}}" style="max-width: 40px" alt="">
                                </div>
                                <h5 class="font-size-16 text-uppercase mt-0 text-white-50">Keuntungan</h5>
                                <div class="font-weight-medium font-size-24" id="total_income">                        
                                </div>
                                <div id="income_label" class="mini-stat-label bg-success mb-0">
                                </div>
                            </div>
                            <div class="pt-2 float-right">
                                <div class="btn-group" role="group" aria-label="Basic example">
                                    <button id="btn_income_day" class="btn btn-secondary" onclick="getTotalIncome(this.id)">Hari
                                        ini</button>
                                    <button id="btn_income_week" class="btn btn-secondary" onclick="getTotalIncome(this.id)">Minggu
                                        ini</button>
                                    <button id="btn_income_month" class="btn btn-secondary" onclick="getTotalIncome(this.id)">Bulan
                                        ini</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end row -->

            <div class="row">
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h4 class="card-title mt-2">Carta Transaksi Semua Pesanan</h4>
                                </div>
                                <div class="d-flex">
                                    {{-- <button id="btn-all" class="btn btn-secondary" onclick="getChart()">Lihat Semua</button> --}}
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col">
                                    <div>
                                        <div id="chart-with-area" class="ct-chart earning ct-golden-section">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- end row -->
                        </div>
                    </div>
                    <!-- end card -->
                </div>
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <h4 class="card-title mb-4">Pesanan Diambil Hari ini</h4>
                                <a href="{{ route('admin-reg.orders') }}">Lihat Semua</a>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="order-table table table-hover table-centered table-nowrap mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col">Nama</th>
                                            <th scope="col">Tarikh Ambil</th>
                                            <th scope="col">Jumlah</th>
                                            <th scope="col">Status</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end row -->

            

        </div> <!-- container-fluid -->
    </div>
    <!-- End Page-content -->

@endsection

@section('script')

<script src="{{ URL::asset('assets/libs/peity/peity.min.js') }}"></script>
<script src="{{ URL::asset('assets/libs/chartist/chartist.min.js') }}"></script>

<script src="{{ URL::asset('assets/libs/moment/moment.min.js') }}"></script>
<script src="{{ URL::asset('assets/libs/datatables/datatables.min.js') }}" defer></script>

<script>
    
    let orgId, dropdownLength = $('#org_dropdown').children('option').length

    $(document).ready(function(){
        if(dropdownLength > 1) {
            $('#org_dropdown option')[1].selected = true
            orgId = $('#org_dropdown option')[1].value
            $('#orderTable').DataTable().destroy()
            getTable(orgId)
            getChart(orgId)
        }
    })

    $('#org_dropdown').change(function() {
        
        orgId = $("#org_dropdown option:selected").val()
        
        $('#orderTable').DataTable().destroy()
        getTable(orgId)
        getChart(orgId)
    })

    function getTable(orgId = '') {
        orderTable = $('.order-table').DataTable({
            processing:   true,
            serverSide:   true,
            destroy:      true,
            paging:       false,
            ordering:     false,
            info:         false,
            searching:    false,
            ajax: {
                url: "{{ route('admin-reg.latest-orders') }}",
                type: 'GET',
                data: {id: orgId},
            },
            language : {
                "infoEmpty": "Tiada Rekod",
                "emptyTable": "<i>Tiada Pesanan Buat Masa Sekarang</i>",
                "zeroRecords": "<i>Tiada Pesanan Buat Masa Sekarang</i>",
            },
            'columnDefs': [{
                "targets": [0, 1, 2, 3], // your case first column
                "className": "align-middle",
            },],
            columns: [{
                data: "name",
                name: 'name',
            },  {
                data: "pickup_date",
                name: 'pickup_date',
            }, {
                data: 'total_price',
                name: 'total_price',
            }, {
                data: 'status',
                name: 'status',
                orderable: false,
                searchable: false,
                "className": "align-middle text-center",
            }, ]
        });
    }


    function getChart(orgId = '') {
        $.ajax({
            type: 'GET',
            url: '{{ route("admin-reg.all-transaction") }}',
            data: {id: orgId},
            success: (result) => {
                var date = new Array();
                var username = new Array();
                var amount = new Array();
                var series = new Array();
                var total_amount = 0;
                var total_order = 0;

                for (var i=0; i < result.transac.length; i++) {
                    var NowMoment = moment(result.transac[i].pickup_date); 
                    date[i] = NowMoment.format('D-M');
                    username[i] = result.transac[i].name;
                    amount[i] = result.transac[i].total_price;  
                    series = [username,amount];
                    total_amount += amount[i]; 
                    total_order++;                 
                }

                $('#order_label').html('Keseluruhan')
                $('#total_income').html('RM ' + (Math.round(total_amount * 100) / 100).toFixed(2))
                $('#income_label').html('Keseluruhan')
                $('#total_order').html(total_order)

                $('.org-name').empty().append('Selamat Datang ' + result.org_name)

                var chart = new Chartist.Line('.ct-chart', {
                    labels: date,
                    series: series
                }, {
                    // Remove this configuration to see that chart rendered with cardinal spline interpolation
                    // Sometimes, on large jumps in data values, it's better to use simple smoothing.
                    // lineSmooth: Chartist.Interpolation.simple({
                    //     divisor: 2
                    // }),
                    // fullWidth: true,
                    // chartPadding: {
                    //     right: 20
                    // },
                    low: 0,
                    plugins: [
                        Chartist.plugins.tooltip()
                    ]
                });
            }
        })
    }

    let getTotalOrder = (id) => {
        let duration, orderLabel = $('#order_label');

        if (id == "btn_order_day") {
            duration = "day";
            orderLabel.html("Hari ini")
        } else if (id == "btn_order_week") {
            duration = "week";
            orderLabel.html("Minggu ini")
        } else if (id == "btn_order_month") {
            duration = "month";
            orderLabel.html("Bulan ini")
        }
        
        $.ajax({
            type: 'GET',
            url: '{{ route("admin-reg.total-order") }}',
            data: {
                duration: duration,
                id: orgId
            },
            success: function(result) {
                let order = result.order
                let display_order = (order === null) ? 0 : order
                
                $('#total_order').html(display_order)
            }
        });
    }

    let getTotalIncome = (id) => {
        let duration, incomeLabel = $('#income_label');

        if (id == "btn_income_day") {
            duration = "day";
            incomeLabel.html("Hari ini")
        } else if (id == "btn_income_week") {
            duration = "week";
            incomeLabel.html("Minggu ini")
        } else if (id == "btn_income_month") {
            duration = "month";
            incomeLabel.html("Bulan ini")
        }
        
        $.ajax({
            type: 'GET',
            url: '{{ route("admin-reg.total-income") }}',
            data: {
                duration: duration,
                id: orgId
            },
            success: function(result) {
                var income = result.income
                var display_income = (income === null) ? 'RM 0.00' : 'RM ' + income
                
                $('#total_income').html(display_income)
            }
        });
    }
    
</script>

@endsection