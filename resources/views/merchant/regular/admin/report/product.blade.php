@extends('layouts.master')

@section('css')

<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/libs/datatables/datatables.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ URL::asset('assets/css/datatable.css')}}">
<link href="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet">
@include('layouts.datatable')
@endsection

@section('content')

<div class="container-fluid">
    <!-- start page title -->
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="page-title">
                    <a href="{{ route('admin-reg.report') }}" class="text-muted">Laporan Peniaga</a> 
                    <i class="fas fa-angle-right"></i> 
                    {{ $group_name }}
                </h4>
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item active "><span class="org-name"></span></li> 
                </ol>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <form>
        @csrf
        <input type="hidden" id="group-id" value="{{ $group_id }}">
        <input type="hidden" id="start-date" value="{{ $start_date }}">
        <input type="hidden" id="end-date" value="{{ $end_date }}">
    </form>
    
    <div class="row">
        <div class="col-xl-6 col-md-6">
            <div class="card mini-stat bg-primary text-white">
                <div class="card-body">
                    <div class="mb-4">
                        <div class="float-left mini-stat-img mr-4">
                            <img src="{{ URL('assets/images/services-icon/order.png')}}" alt="">
                        </div>
                        <h5 class="font-size-16 text-uppercase mt-0 text-white-50">Kuantiti Dijual</h5>
                        <div class="font-weight-medium font-size-24" id="quantity_sold">
                            {{ $quantity_sold }}                       
                        </div>
                        {{-- <div id="order_label" class="mini-stat-label bg-success mb-0">
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-6 col-md-6">
            <div class="card mini-stat bg-primary text-white">
                <div class="card-body">
                    <div class="mb-4">
                        <div class="float-left mini-stat-img mr-4">
                            <img src="{{ URL('assets/images/services-icon/sales.png')}}" alt="">
                        </div>
                        <h5 class="font-size-16 text-uppercase mt-0 text-white-50">Jumlah Jualan</h5>
                        <div class="font-weight-medium font-size-24" id="total_sales">
                            RM {{ $total_sales }}         
                        </div>
                        {{-- <div id="order_label" class="mini-stat-label bg-success mb-0">
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-xl-5">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h4 class="card-title mt-2">Carta Pai Jumlah Jualan Berdasarkan Produk Item</h4>
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
                        <h4 class="card-title mb-4">Senarai Produk Item</h4>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="item-table table dt-responsive wrap" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="width:50%" scope="col">Nama</th>
                                    <th style="width:20%" scope="col">Kuantiti Dijual</th>
                                    <th style="width:30%" scope="col">Jumlah Jualan</th>
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
    $(document).ready(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })

        let groupId = $('#group-id').val()
        let startDate = $('#start-date').val()
        let endDate = $('#end-date').val()
        
        getReport(groupId, startDate, endDate)
    })


    function getReport(groupId = '', startDate = '', endDate = ''){
        $.ajax({
            type: 'GET',
            url: '{{ route("admin-reg.get-item-report") }}',
            data: {
                group_id: groupId,
                start_date:startDate,
                end_date:endDate,
            },
            success: function(result) {
                console.log(result)

                initDatatable()

                getItemPieChart(result.item_arr)
                getTable(result.item_arr)

            },
            error:function(result) {
                console.log(result.responseText)
            }
        })
    }

    function initDatatable()
    {
        itemTable = $('.item-table').DataTable({
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

    function getItemPieChart(itemObject = '') {
        let name = new Array()
        let amount = new Array()
        
        for (var i=0; i < Object.keys(itemObject).length; i++) {
            if(itemObject[i].totalSales > 0){
                name[i] = itemObject[i].name;
                amount[i] = itemObject[i].totalSales;   
            }
             
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
                labelOffset: 50,
                chartPadding: 20
            }]
        ];

        let pie = new Chartist.Pie('.pie-chart', data, options, responsiveOptions)
    }

    function getTable(itemObject = '') {
        $('.item-table').DataTable().destroy()
        itemTable = $('.item-table').DataTable({
            processing:   true,
            serverSide:   true,
            paging:       false,
            ordering:     false,
            orderable:    false,
            info:         false,
            searching:    false,
            ajax: {
                url: "{{ route('admin-reg.item-table') }}",
                type: 'GET',
                data: {item: itemObject},
            },
            language : {
                "infoEmpty": "Tiada Rekod",
                "emptyTable": "<i>Tiada Rekod</i>",
                "zeroRecords": "<i>Tiada Rekod</i>",
            },
            'columnDefs': [{
                "targets": [0, 1, 2], // your case first column
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
            }, ]
        });
    }
    
</script>

@endsection