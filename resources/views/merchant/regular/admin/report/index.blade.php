@extends('layouts.master')

@section('css')

<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/libs/datatables/datatables.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ URL::asset('assets/css/datatable.css')}}">
<link href="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet">
@include('layouts.datatable')
@endsection

@section('content')
<div style="padding-top: 12px" class="row">
    <div class="col-md-12 ">
        <div class=" align-items-center">
            <div class="form-group card-title">
                <select name="org" id="org_dropdown" class="form-control col-md-12">
                    <option value="" disabled>Pilih Organisasi</option>
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
                <h4 class="page-title">Laporan Peniaga</h4>
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
        <div class="col-xl-10 justify-content-center">
            <div class="card">
                <div class="alert alert-danger">
                    <p id="failed"></p>
                </div>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Tarikh Mula dan Tamat</span>
                    </div>
                    <input type="text" class="form-control start-date" name="start-date" placeholder="Tarikh Mula" readonly />
                    <input type="text" class="form-control end-date" name="end-date" placeholder="Tarikh Tamat" readonly />
                </div>
            </div>
        </div>
        <div class="col justify-content-center">
            <div class="card">
                <button class='search-btn btn btn-primary mb-2'><i class="fas fa-search"></i></button>
                <button class='reset-btn btn btn-light btn-outline-dark'><i class="fas fa-redo-alt"></i></button>
            </div>
            
        </div>
    </div>

    
    <div class="row">
        <div class="col-xl-4 col-md-6">
            <div class="card mini-stat bg-primary text-white">
                <div class="card-body">
                    <div class="mb-4">
                        <div class="float-left mini-stat-img mr-4">
                            <img src="{{ URL('assets/images/services-icon/order.png')}}" alt="">
                        </div>
                        <h5 class="font-size-16 text-uppercase mt-0 text-white-50">Jumlah Pesanan</h5>
                        <div class="font-weight-medium font-size-24" id="total_order">                        
                        </div>
                        {{-- <div id="order_label" class="mini-stat-label bg-success mb-0">
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4 col-md-6">
            <div class="card mini-stat bg-primary text-white">
                <div class="card-body">
                    <div class="mb-4">
                        <div class="float-left mini-stat-img mr-4">
                            <img src="{{ URL('assets/images/services-icon/sales.png')}}" alt="">
                        </div>
                        <h5 class="font-size-16 text-uppercase mt-0 text-white-50">Jumlah Jualan</h5>
                        <div class="font-weight-medium font-size-24" id="total_sales">                        
                        </div>
                        {{-- <div id="order_label" class="mini-stat-label bg-success mb-0">
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card mini-stat bg-primary text-white">
                <div class="card-body">
                    <div class="mb-4">
                        <div class="float-left mini-stat-img mr-4">
                            <img src="{{ URL('assets/images/services-icon/avg_sales.png')}}" style="max-width: 40px" alt="">
                        </div>
                        <h5 class="font-size-16 text-uppercase mt-0 text-white-50">Purata Jualan</h5>
                        <div class="font-weight-medium font-size-24" id="avg_sales">                        
                        </div>
                        {{-- <div id="income_label" class="mini-stat-label bg-success mb-0">
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-xl-12">
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
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-xl-5">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h4 class="card-title mt-2">Carta Pai Jumlah Jualan Berdasarkan Jenis Produk</h4>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col">
                            <div>
                                <div id="pie-chart" class="pie-chart earning ct-golden-section">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end row -->
                </div>
            </div>
            <!-- end card -->
        </div>
        <div class="col-xl-7">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title mb-4">Senarai Jenis Produk</h4>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="group-table table dt-responsive wrap" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="width:50%" scope="col">Nama</th>
                                    <th style="width:20%" scope="col">Kuantiti Dijual</th>
                                    <th style="width:20%" scope="col">Jumlah Jualan</th>
                                    <th style="width:10%" scope="col">Action</th>
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

@endsection

@section('script')

<script src="{{ URL::asset('assets/libs/peity/peity.min.js') }}"></script>
<script src="{{ URL::asset('assets/libs/chartist/chartist.min.js') }}"></script>

<script src="{{ URL::asset('assets/libs/moment/moment.min.js') }}"></script>
<script src="{{ URL::asset('assets/libs/datatables/datatables.min.js') }}" defer></script>

<script src="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}" defer></script>

<script>
    let orgId, dropdownLength = $('#org_dropdown').children('option').length

    $(document).ready(function(){
        $('.alert').hide()

        if(dropdownLength > 1) {
            $('#org_dropdown option')[1].selected = true
            orgId = $('#org_dropdown option')[1].value
            $('.group-table').DataTable().destroy()
            getReport(orgId)
        }

        $('.start-date').datepicker({
            format: 'dd M, yyyy',
            // endDate: '+0d',
            autoclose: true,
        })

        $('.end-date').datepicker({
            format: 'dd M, yyyy',
            // endDate: '+0d',
            autoclose: true
        })
    })

    let startDate, endDate, isValid = false

    $('#org_dropdown').change(function() {
        orgId = $("#org_dropdown option:selected").val()
        $('.group-table').DataTable().destroy()
        $('.ct-chart').empty()
        $('.pie-chart').empty()
        $('.start-date').val('')
        $('.end-date').val('')
        getReport(orgId)
    })

    $('.start-date').change(function(){
        startDate = $(this).val()
        isValid = validateDate(startDate, endDate)
    })

    $('.end-date').change(function(){
        endDate = $(this).val()
        isValid = validateDate(startDate, endDate)
    })

    $('.search-btn').click(function(){
        if(isValid === true){
            $('.group-table').DataTable().destroy()
            $('.ct-chart').empty()
            $('.pie-chart').empty()
            getReport(orgId, startDate, endDate)
        } else {
            $('.alert').empty().append('Sila semak semula tarikh yang dimasukkan.').show()
        }
    })

    $('.reset-btn').click(function(){
        $('.group-table').DataTable().destroy()
        $('.ct-chart').empty()
        $('.pie-chart').empty()
        $('.start-date').val('')
        $('.end-date').val('')
        getReport(orgId)
    })

    function validateDate(startDate = '', endDate = ''){
        const formattedStartDate = new Date(startDate)
        const formattedEndDate = new Date(endDate)
        let flag = true

        if(startDate != '' && endDate != ''){
            if(startDate != endDate){
                if(formattedStartDate > formattedEndDate){
                    flag = false
                    $('.alert').empty().append('Tarikh mula mestilah kurang daripada tarikh akhir.').show()
                }
            }else{
                flag = true
            }
        }else{
            flag = false
        }

        if(flag === true){
            $('.alert').hide()
        }
        
        return flag
    }

    function getReport(orgId = '', startDate = '', endDate = ''){
        $.ajax({
            type: 'GET',
            url: '{{ route("admin-reg.get-report") }}',
            data: {
                id: orgId,
                start_date:startDate,
                end_date:endDate,
            },
            success: function(result) {
                console.log(result)
                $('#total_order').html(result.order)
                $('#total_sales').html('RM ' + result.sales)
                $('#avg_sales').html('RM ' + result.avgSales)

                initDatatable()

                if(result.order != 0){
                    getTransactionChart(result.chart)
                    getGroupPieChart(result.group.group_arr)
                    
                }
                if(startDate != '' && endDate != ''){
                    getTable(result.group.group_arr, result.startDate, result.endDate)
                } else {
                    getTable(result.group.group_arr)
                }
                
            },
            error:function(result) {
                console.log(result.responseText)
            }
        })
    }

    function initDatatable()
    {
        groupTable = $('.group-table').DataTable({
            paging:       false,
            ordering:     false,
            orderable:    false,
            info:         false,
            searching:    false,
            language : {
                "infoEmpty": "Tiada Rekod",
                "emptyTable": "<i>Tiada Rekod</i>",
                "zeroRecords": "<i>Tiada Rekod</i>",
            },
        }).clear().draw()
    }

    function getTransactionChart(transac = ''){
        var date = new Array();
        var username = new Array();
        var amount = new Array();
        var series = new Array();

        for (var i=0; i < transac.length; i++) {
            var NowMoment = moment(transac[i].pickup_date); 
            date[i] = NowMoment.format('D-M');
            username[i] = transac[i].name;
            amount[i] = transac[i].total_price;  
            series = [username,amount];                
        }

        var chart = new Chartist.Line('.ct-chart', {
                labels: date,
                series: series
            }, {
                low: 0,
                plugins: [
                    Chartist.plugins.tooltip()
                ]
            });
    }

    function getGroupPieChart(groupObject = '') {
        let name = new Array()
        let amount = new Array()
        
        for (var i=0; i < Object.keys(groupObject).length; i++) {
            name[i] = groupObject[i].name;
            amount[i] = groupObject[i].totalSales;               
        }
        
        var data = {
            labels: name,
            series: amount
        };

        var options = {
            labelInterpolationFnc: function(value, idx) {
                return value[0]
            }
        };

        var responsiveOptions = [
            ['screen and (min-width: 640px)', {
                chartPadding: 30,
                labelOffset: 100,
                labelDirection: 'explode',
                labelInterpolationFnc: function(value) {
                    return value;
                }
            }],
            ['screen and (min-width: 1024px)', {
                labelOffset: 80,
                chartPadding: 20
            }]
        ];

        let pie = new Chartist.Pie('.pie-chart', data, options, responsiveOptions)
    }

    function getTable(groupObject = '', startDate = '', endDate = '') {
        $('.group-table').DataTable().destroy()
        groupTable = $('.group-table').DataTable({
            processing:   true,
            serverSide:   true,
            paging:       false,
            ordering:     false,
            orderable:    false,
            info:         false,
            searching:    false,
            ajax: {
                url: "{{ route('admin-reg.group-table') }}",
                type: 'GET',
                data: 
                {   group: groupObject,
                    start_date:startDate,
                    end_date:endDate,
                },
            },
            language : {
                "infoEmpty": "Tiada Rekod",
                "emptyTable": "<i>Tiada Rekod</i>",
                "zeroRecords": "<i>Tiada Rekod</i>",
            },
            'columnDefs': [{
                "targets": [0, 1, 2, 3], // your case first column
                "className": "align-middle",
            },],
            columns: [{
                data: "name",
                name: 'name',
                orderable: false,
                searchable: false,
            },  {
                data: "quantitySold",
                name: 'quantitySold',
            }, {
                data: 'totalSales',
                name: 'totalSales',
            }, {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                "className": "align-middle text-center",
            }, ]
        });
    }
    
</script>

@endsection