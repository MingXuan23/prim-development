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
<form action="{{ route('exportYuranOverview') }}" method="post">
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">

            {{csrf_field()}}
            <div class="card-body">
                <div class="form-group">
                    <label>Nama Organisasi</label>
                    <div class="row"> <!-- Assuming you're using Bootstrap's grid system -->
                    <div class="col-md-9"> <!-- Adjust the column size based on your layout -->
                        <select name="organization" id="organization" class="form-control">
                            <option value="" selected disabled>Pilih Organisasi</option>
                            @foreach($organization as $row)
                                <option value="{{ $row->id }}">{{ $row->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3"> <!-- Adjust the column size based on your layout -->
                        <button id="buttonExport" type="submit" class="btn btn-primary" >Export Overview Report</button>
                    </div>
                </div>
                    

                    
                </div>
                <br>
                <div class="row">
                    <div class="col-sm-6">
                        <h4 class="card-title mb-4 text-center">Carta pie untuk pembayaran yuran <br> murid mengikut
                            pecahan kelas </h4>

                        <div class="row justify-content-center">
                            <div class="col-sm-6">
                                <div class="text-center">
                                    <h5 class="mb-0 font-size-20" id="student-complete">0 /
                                        0</h5>
                                    <p class="text-muted">Orang Selesai Membayar</p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="text-center">
                                    <h5 class="mb-0 font-size-20" id="student-not-complete">0 /
                                        0</h5>
                                    <p class="text-muted">Orang Belum Selesai Membayar</p>
                                </div>
                            </div>
                        </div>

                        <div id="pie-chart-yuran" style="width:500px;height:250px; margin: 0 auto;">
                            <div id="pie-chart-container" class="flot-charts flot-charts-height">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <h4 class="card-title mb-4 text-center">Carta pie untuk pembayaran yuran <br> Kategori A
                            mengikut keluarga </h4>

                        <div class="row justify-content-center">
                            <div class="col-sm-6">
                                <div class="text-center">
                                    <h5 class="mb-0 font-size-20" id="parent-complete">0 /
                                        0</h5>
                                    <p class="text-muted">Orang Selesai Membayar</p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="text-center">
                                    <h5 class="mb-0 font-size-20" id="parent-not-complete">0 /
                                        0</h5>
                                    <p class="text-muted">Orang Belum Selesai Membayar</p>
                                </div>
                            </div>
                        </div>

                        <div id="pie-chart-yuran-category-A" style="width:500px;height:250px; margin: 0 auto;">
                            <div id="pie-chart-container" class="flot-charts flot-charts-height">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="col-md-12">
        <div class="card">
            <div id="table" class="card-body">

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

                <div class="card-title text-center">
                    <span id="type-name"> </span>
                </div>

                <br>
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

                <div class="table-responsive">
                    <table id="table-parent" class="table table-bordered table-striped dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr style="text-align:center">
                                <th> No. </th>
                                <th>Nama Penjaga</th>
                                <th>No. Telefon</th>
                                <th>Email</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        
                
                    
                    
                    
                </div>
            </form>
        <!-- Modal -->
        <div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
</form>

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

        var completed;
        var notcompleted;

        var parent_completed;
        var parent_notcompleted;

        var oid;

        $('#table').hide();
        $('#reportClass').hide();
        $('#table-parent').hide();
        $('#type-name').hide();


        function data_pie(oid) {

            $.ajax({
                url: "{{ route('fees.reportByOid') }}",
                type: 'get',
                data: {
                    oid : oid
                },
                success: function(response){ 
                    // console.log(response.student_notcomplete);
                    completed       = response.student_complete;
                    notcompleted    = response.student_notcomplete;

                    parent_completed       = response.parent_complete;
                    parent_notcompleted    = response.parent_notcomplete;
                    console.log(response);
                    document.getElementById("student-complete").innerHTML = completed +" / "+ response.all_student;
                    document.getElementById("student-not-complete").innerHTML = notcompleted +" / "+ response.all_student;
                    document.getElementById("parent-complete").innerHTML = parent_completed +" / "+ response.all_parent;
                    document.getElementById("parent-not-complete").innerHTML = parent_notcompleted +" / "+ response.all_parent;

                    var data = [{
                        label: "Belum Selesai",  
                        data: notcompleted,
                    }, {
                        label: "Selesai",  
                        data: completed,
                    }];

                    var data_category_A = [{
                        label: "Belum Selesai",  
                        data: parent_notcompleted,
                    }, {
                        label: "Selesai",  
                        data: parent_completed,
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

                    $.plot($("#pie-chart-yuran-category-A"), data_category_A, options);

                    

                    $("#pie-chart-yuran").bind("plotclick", function(event, pos, item){
                        // alert(item.datapoint);
                        // console.log(item);
                        // console.log(item.datapoint[1][0][1]);
                        $('#reportClass').DataTable().destroy();
                        $('#table-parent').hide();
                        $('#table-parent_wrapper').hide();
                        $('#table').show();
                        $('#reportClass').show();

                        var type = item.series.label;
                        oid = $("#organization option:selected").val();
                        
                        fetch_data(oid, type);

                        document.getElementById("type-name").innerHTML="Senarai Kelas Yang " + type + " Membayar Yuran";
                        $('#type-name').show();

                    });

                    $("#pie-chart-yuran-category-A").bind("plotclick", function(event, pos, item){
                        // alert(item.datapoint);
                        // console.log(item);
                        // console.log(item.datapoint[1][0][1]);
                        $('#table-parent').DataTable().destroy();
                        $('#reportClass').hide();
                        $('#reportClass_wrapper').hide();

                        $('#table').show();
                        $('#table-parent').show();
                        
                        var type = item.series.label;
                        oid = $("#organization option:selected").val();

                        fetch_data_parent(oid, type);

                        document.getElementById("type-name").innerHTML="Senarai Penjaga Yang " + type + " Membayar Yuran";
                        $('#type-name').show();
                    });
                    
                }
            });
        }

        if($("#organization").val() != ""){
            $("#organization").prop("selectedIndex", 1).trigger('change');
            // fetch_data($("#organization").val());
            oid = $("#organization option:selected").val();
            data_pie(oid);
        }

        $('#organization').change(function() {
            var organizationid = $("#organization option:selected").val();
            data_pie(organizationid);
        });

        function fetch_data(oid, type) {
            reportClass = $('#reportClass').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('fees.getTypeDatatable') }}",
                        data: {
                            type: type,
                            oid: oid,
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

        // category A (parent)
        function fetch_data_parent(oid, type) {
            $('#table-parent').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('fees.getParentDatatable') }}",
                        data: {
                            type: type,
                            oid: oid,
                        },
                        type: 'GET',
  
                    },
                    'columnDefs': [{
                        "targets": [0], // your case first column
                        "className": "text-center",
                        "width": "2%"
                    },{
                        "targets": [1,2,3,4], // your case first column
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
                        data: "name",
                        name: 'name'
                    }, {
                        data: "telno",
                        name: 'telno',
                        orderable: false,
                        searchable: false
                    },  {
                        data: "email",
                        name: 'email',
                        orderable: false,
                        searchable: false
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

        // modal for dependent by parent
        var user_id;
        $(document).on('click', '.user-id', function(){
            user_id = $(this).attr('id');

            $.ajax({
                url: "{{ route('fees.parent_dependent') }}",
                type: 'get',
                data: {
                    data : user_id
                },
                success: function(response){ 

                    var html="";
                    $('.modal-body').empty();

                    $('.modal-title').text('Senarai Nama Tanggungan - ' + response[0].username);

                    html += '<table class="table table-bordered" >';
                        html += '<tr style="text-align:center">';
                        html += '<th> Nama Murid </th>';
                        html += '<th> Kelas </th>';
                        html += '</tr>';
                    for(var i=0; i < response.length; i++){

                        html += '<tr>';
                        html += '<td><div style="text-align:center">'+response[i].nama +'</div></td>';  
                        html += '<td><div  style="text-align:center">'+response[i].classname+'</div></td>';  
                        html += '</tr>';
                    }
                    html += '</table>';      
                    
                // Add response in Modal body
                    $('.modal-body').append(html) 

                // Display Modal
                    $('#modelId').modal('show');

                }
            });



        });

        // csrf token for ajax
        $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
  
        $('.alert').delay(3000).fadeOut();
  
    });
</script>
@endsection