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
            @if ($type == "Completed")
            <h4 class="font-size-18">Kelas {{ $class->nama}} (Selesai Membayar)</h4>

            @else
            <h4 class="font-size-18">Kelas {{ $class->nama}} (Belum Selesai Membayar)</h4>

            @endif
            <!-- <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Welcome to Veltrix Dashboard</li>
            </ol> -->
        </div>
    </div>
</div>
<div class="row">
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
                    <table id="reportbyClass" class="table table-bordered table-striped dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr style="text-align:center">
                                <th> No. </th>
                                <th>Nama Murid</th>
                                <th>Butiran</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

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
  
        var reportbyClass;
        var status_fees     = "<?php echo $type; ?>";
        var class_id        = "<?php echo $class->id; ?>";
        // console.log(class_id);

        fetch_data(status_fees, class_id);
        if($("#organization").val() != ""){
            $("#organization").prop("selectedIndex", 1).trigger('change');
            // fetch_data($("#organization").val());
        }

        function fetch_data(status_fees, class_id) {
            reportbyClass = $('#reportbyClass').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('fees.getstudentDatatable') }}",
                        data: {
                            status: status_fees,
                            class_id: class_id,
                        },
                        type: 'GET',
  
                    },
                    'columnDefs': [{
                        "targets": [0], // your case first column
                        "className": "text-center",
                        "width": "2%"
                    },{
                        "targets": [2], // your case first column
                        "className": "text-center",
                        "width": "15%"
                    },{
                        "targets": [1,2], // your case first column
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
  
        var student_id;
        $(document).on('click', '.student-id', function(){
            student_id = $(this).attr('id');

            $.ajax({
                url: "{{ route('fees.studentfees') }}",
                type: 'get',
                data: {
                    student_id: student_id
                },
                success: function(response){ 

                    var html="";
                    $('.modal-body').empty();

                    $('.modal-title').text("Butiran Yuran - " +  response[0].studentnama);

                    html += '<table class="table table-bordered" >';
                        html += '<tr style="text-align:center">';
                        html += '<th> Nama Yuran </th>';
                        html += '<th> Jumlah Amaun (RM)</th>';
                        html += '<th> Status </th>';
                        html += '</tr>';
                    for(var i=0; i < response.length; i++){

                        html += '<tr>';
                        html += '<td><div style="text-align:center">'+response[i].name+'</div></td>';  
                        html += '<td><div  style="text-align:center">'+response[i].totalAmount.toFixed(2)+'</div></td>';  
                        if(response[i].status == 'Paid'){
                            html += '<td><div  style="text-align:center"> <span class="badge badge-success"> Selesai </span></div> </td>';  
                        }else{
                            html += '<td><div  style="text-align:center"> <span class="badge badge-danger"> Belum Selesai </span></div> </td>';  
                        }
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