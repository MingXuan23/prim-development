@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
<style>
    .legendLabel {
        font-weight: bold;
        color: #5b626b !important;
        font-size: 15px;
    }
</style>
@include('layouts.datatable')
@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Laporan Yuran</h4>
            <!-- <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Welcome to Veltrix Dashboard</li>
            </ol> -->
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">

            {{csrf_field()}}
            <div class="card-body">

                <h4 class="card-title mb-4 te">Jumlah bilangan murid mengikut</h4>

                <div class="row justify-content-center">
                    <div class="col-sm-6">
                        <div class="text-center">
                            <h5 class="mb-0 font-size-20">{{ $student_complete }} / {{ $all_student }}</h5>
                            <span id="completed" hidden>{{ $student_complete }}</span>
                            <p class="text-muted">Selesai Membayar Yuran</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-center">
                            <h5 class="mb-0 font-size-20">{{ $student_notcomplete }} / {{ $all_student }}</h5>
                            <span id="notcompleted" hidden>{{ $student_notcomplete }}</span>
                            <p class="text-muted">Belum Selesai Membayar Yuran</p>
                        </div>
                    </div>
                </div>

                {{-- 
                <div>
                    <div id="pie-chart-yuran" style="width:500px;height:400px;">

                    </div>
                </div> --}}

                <div id="pie-chart-yuran" style="width:500px;height:250px; margin: 0 auto;">
                    <div id="pie-chart-container" class="flot-charts flot-charts-height">
                    </div>
                </div>


            </div>

            {{-- <div class="">
                <button onclick="filter()" style="float: right" type="submit" class="btn btn-primary"><i
                        class="fa fa-search"></i>
                    Tapis</button>
            </div> --}}

        </div>
    </div>

    <div class="col-md-12">
        <div class="card">
            <div class="card-body">

                @if(count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                        <li>{{$error}}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                @if(\Session::has('success'))
                <div class="alert alert-success">
                    <p>{{ \Session::get('success') }}</p>
                </div>
                @endif

                <div class="table-responsive">
                    <table id="reportClass" class="table table-bordered table-striped dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr style="text-align:center">
                                <th> No. </th>
                                <th>Nama Kelas</th>
                                <th>Bilangan Murid</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>


@endsection


@section('script')
<!-- Peity chart-->
<script src="{{ URL::asset('assets/libs/peity/peity.min.js')}}"></script>

<!-- Plugin Js-->
<script src="{{ URL::asset('assets/libs/chartist/chartist.min.js')}}"></script>

<script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script>
<script src="{{ URL::asset('assets/libs/flot-charts/flot-charts.min.js')}}"></script>
<script src="{{ URL::asset('assets/js/pages/flot.init.js')}}"></script>

<script>
    $(document).ready(function() {
  
        var reportClass;
        var colors = ["#E0E0E0", '#02a499'];

        var completed       = parseInt(<?php echo $student_complete; ?>);
        var notcompleted    = parseInt(<?php echo $student_notcomplete; ?>);

        var data = [{
            label: "Belum Selesai",  
            data: notcompleted,
        }, {
            label: "Selesai",  
            data: completed,
        }];

        var options = {
        series: {
            pie: {
            show: true,
            }
        },
        legend: {
            show: true,
            backgroundColor: "transparent"
        },
        grid: {
            hoverable: true,
            clickable: true
        },
            colors: colors,
            tooltip: true,
            tooltipOpts: {
                content: "%s, %p.0%",
                defaultTheme: false
            }
        };

        $.plot($("#pie-chart-yuran"), data, options);


        $("#pie-chart-yuran").bind("plotclick", function(event, pos, item){
            // alert(item.datapoint);
            // console.log(item);
            // console.log(item.datapoint[1][0][1]);
            $('#reportClass').DataTable().destroy();

            console.log(item.series.label);
            var type = item.series.label;
            fetch_data(type);

        });

        if($("#organization").val() != ""){
            $("#organization").prop("selectedIndex", 1).trigger('change');
            // fetch_data($("#organization").val());
        }

        function fetch_data(type = '') {
            reportClass = $('#reportClass').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('fees.getTypeDatatable') }}",
                        data: {
                            type: type,
                        },
                        type: 'GET',
  
                    },
                    'columnDefs': [{
                        "targets": [0], // your case first column
                        "className": "text-center",
                        "width": "2%"
                    },{
                        "targets": [1,2,3], // your case first column
                        "className": "text-center",
                    },],
                    order: [
                        [1, 'asc']
                    ],
                    columns: [{
                        "data": null,
                        searchable: false,
                        "sortable": false,
                        render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    }, {
                        data: "nama",
                        name: 'nama'
                    }, {
                        data: "total",
                        name: 'total',
                        orderable: false,
                        searchable: false,
                        defaultContent: 0,
                        
                    }, {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },],
                    error: function (error) {
                        alert('error');
                        alert(error.toString());
                    }
            });
        }

        // csrf token for ajax
        $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
  
        var teacher_id;
  
          $('.alert').delay(3000).fadeOut();
  
    });
</script>
@endsection