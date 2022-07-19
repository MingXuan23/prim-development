@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@include('layouts.datatable')

@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Pelajar</h4>
        </div>
    </div>
</div>
<div class="row">

    <div class="col-md-12">
        <div class="card">
            <div>
                
            </div>

            <div class="card-body">
                {{csrf_field()}}
                <div class="card-body">
                    <div class="form-group">
                        <label>Nama Organisasi</label>
                        <select name="organization" id="organization" class="form-control">
                            <option value="{{ $organization->id }}">{{ $organization->nama }}</option>
                        </select>
                    </div>

                    <div id="dkelas" class="form-group">
                        <label> Kelas</label>
                        <select name="classes" id="classes" class="form-control">
                            <option value="" disabled selected>Pilih Kelas</option>
                        </select>
                    </div>
                </div>

                <a style="margin: 0px 19px 19px 19px; float: right;" href="#" class="btn btn-success" data-toggle="modal" data-target="#modelId1"> <i
                    class="fas fa-plus"></i> Export</a>

                <div class="table-responsive">
                    <table id="studentTable" class="table table-bordered table-striped dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr style="text-align:center">
                                <th> No. </th>
                                <th>Nama Penuh</th>
                                <th>Kelas</th>
                                <th>Status</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modelId1" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Export Murid</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    {{-- {{ route('exportstudent') }} --}}
                    <form action="{{ route('exportstudent') }}" method="post">
                        <div class="modal-body">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label>Organisasi</label>
                                <select name="organExport" id="organExport" class="form-control">
                                    <option value="{{ $organization->id }}" selected>{{ $organization->nama }}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Nama Kelas</label>
                                <select name="classExport" id="classExport" class="form-control">

                                </select>
                            </div>
                            <div class="modal-footer">
                                <button id="buttonExport" type="submit" class="btn btn-primary">Export</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
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
@endsection


@section('script')
<!-- Peity chart-->
<script src="{{ URL::asset('assets/libs/peity/peity.min.js')}}"></script>

<!-- Plugin Js-->
<script src="{{ URL::asset('assets/libs/chartist/chartist.min.js')}}"></script>

<script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script>

<script>
    $(document).ready(function(){
        
        var studentTable;

        if($("#organization").val() != ""){
            $("#organization").prop("selectedIndex", 0).trigger('change');
            fetchClass($("#organization").val(), '#classes');
        }

        if($("#organExport").val() != ""){
            $("#organExport").prop("selectedIndex", 0).trigger('change');
            fetchClass($("#organExport").val(), '#classExport');
        }

        function fetch_data(cid = '') {
            studentTable = $('#studentTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('polimas.student.getStudentDatatable') }}",
                    data: {
                        classid: cid,
                        hasOrganization: true
                    },
                    type: 'GET',

                },
                'columnDefs': [{
                    "targets": [0], // your case first column
                    "className": "text-center",
                    "width": "2%"
                },{
                    "targets": [2,3,4], // your case first column
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
                    data: "studentname",
                    name: 'studentname'
                }, {
                    data: "classname",
                    name: 'classname'
                }, {
                    data: 'status',
                    name: 'status',
                    orderable: false,
                    searchable: false
                }, {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },]
            });
        }

        $('#organization').change(function() {
            var organizationid    = $("#organization").val();
            var _token            = $('input[name="_token"]').val();
            fetchClass(organizationid, "#classes");
        });

        $('#organImport').change(function() {
            var organizationid    = $("#organImport").val();
            var _token            = $('input[name="_token"]').val();
            fetchClass(organizationid, '#classImport');
        });

        $('#organExport').change(function() {
            var organizationid    = $("#organExport").val();
            var _token            = $('input[name="_token"]').val();
            fetchClass(organizationid, '#classExport');
        });

        function fetchClass(organizationid = '', classId = ''){
            var _token = $('input[name="_token"]').val();
            $.ajax({
                url:"{{ route('student.fetchClass') }}",
                method:"POST",
                data:{ oid:organizationid,
                        _token:_token },
                success:function(result)
                {
                    $(classId).empty();
                    $(classId).append("<option value='' disabled selected> Pilih Kelas</option>");
                    jQuery.each(result.success, function(key, value){
                        $(classId).append("<option value='"+ value.cid +"'>" + value.cname + "</option>");
                    });
                }
            })
        }

        $('#classes').change(function() {
            var organizationid    = $("#organization option:selected").val();
            var classid    = $("#classes option:selected").val();
            if(classid){
                $('#studentTable').DataTable().destroy();
                fetch_data( classid);
            }
        });

        // csrf token for ajax
        $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
            
        $('.alert').delay(3000).fadeOut();

        $('#buttonExport').click(function() {
            $('#modelId1').modal('hide');
        });

        var student_id;
        $(document).on('click', '.student-id', function(){
            student_id = $(this).attr('id');

            $.ajax({
                url: "{{ route('polimas.studentfees') }}",
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
    });
</script>

@endsection