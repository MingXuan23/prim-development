@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@include('layouts.datatable')

@endsection

@section('content')
<!-- {{-- <p>Welcome to this beautiful admin panel.</p> --}} -->
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Pelajar</h4>
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

                <div class="form-group">
                    <label>Nama Organisasi</label>
                    <select name="organization" id="organization" class="form-control">
                        <option value="" selected disabled>Pilih Organisasi</option>
                        @foreach($organization as $row)
                        <option value="{{ $row->id }}">{{ $row->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="dkelas" class="form-group">
                    <label> Kelas</label>
                    <select name="classes" id="classes" class="form-control">
                        <option value="" disabled selected>Pilih Kelas</option>

                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card">
            <div>
                <a style="margin: 19px;" href="#" class="btn btn-primary" data-toggle="modal" data-target="#modelId"> <i
                        class="fas fa-plus"></i> Import</a>
                <a style="margin: 1px;" href="#" class="btn btn-success" data-toggle="modal" data-target="#modelId1"> <i
                        class="fas fa-plus"></i> Export</a>
                <!-- <a style="margin: 1px;" href="{{ route('exportstudent') }} " class="btn btn-success"> <i
                        class="fas fa-plus"></i> Export</a> -->
                {{-- {{ route('exportmurid') }} {{ route('murid.create') }} --}}
                <a style="margin: 19px; float: right;" href="{{ route('student.create') }}" class="btn btn-primary"> <i
                        class="fas fa-plus"></i> Tambah Murid</a>
            </div>

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
                    <table id="studentTable" class="table table-bordered table-striped dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr style="text-align:center">
                                <th> No. </th>
                                <th>Nama Penuh</th>
                                {{-- <th>Nombor Kad pengenalan</th> --}}
                                <th>Kelas</th>
                                <th>Status</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        {{-- confirmation delete modal --}}
        <div id="deleteConfirmationModal" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Padam Murid</h4>
                    </div>
                    <div class="modal-body">
                        Adakah anda pasti?
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-dismiss="modal" class="btn btn-primary" id="delete"
                            name="delete">Padam</button>
                        <button type="button" data-dismiss="modal" class="btn">Batal</button>
                    </div>
                </div>
            </div>
        </div>
        {{-- end confirmation delete modal --}}

        <!-- Modal -->
        <div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Import Murid</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    {{-- {{ route('importmurid')}} --}}
                    <form action="{{ route('importstudent')}}" method="post" enctype="multipart/form-data">
                        <div class="modal-body">

                            {{ csrf_field() }}
                            <div class="form-group">
                                <label>Organisasi</label>
                                <select name="organImport" id="organImport" class="form-control">
                                @foreach($organization as $row)
                                    <option value="{{ $row->id }}" selected>{{ $row->nama }}</option>
                                @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Nama Kelas</label>
                                <select name="classImport" id="classImport" class="form-control">

                                </select>
                            </div>

                            <div class="form-group">
                                <input type="file" name="file" required>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Import</button>
                            </div>
                        </div>

                    </form>
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
                                    @foreach($organization as $row)
                                        <option value="{{ $row->id }}" selected>{{ $row->nama }}</option>
                                    @endforeach
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
            $("#organization").prop("selectedIndex", 1).trigger('change');
            fetchClass($("#organization").val(), '#classes');
        }

        if($("#organImport").val() != ""){
            $("#organImport").prop("selectedIndex", 0).trigger('change');
            fetchClass($("#organImport").val(), '#classImport');
        }

        if($("#organExport").val() != ""){
            $("#organExport").prop("selectedIndex", 0).trigger('change');
            fetchClass($("#organExport").val(), '#classExport');
        }

        
        // fetch_data();
        // alert($("#organization").val());

            function fetch_data(cid = '') {
                studentTable = $('#studentTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('student.getStudentDatatable') }}",
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

            /* 
                {
                    data: "icno",
                    name: 'icno'
                }
            */

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
                            // $('select[name="kelas"]').append('<option value="'+ key +'">'+value+'</option>');
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
                // console.log(organizationid);
            });

            // csrf token for ajax
            $.ajaxSetup({
                    headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var student_id;

            $(document).on('click', '.btn-danger', function(){
                student_id = $(this).attr('id');
                $('#deleteConfirmationModal').modal('show');
            });

            $('#delete').click(function() {
                    $.ajax({
                        type: 'POST',
                        dataType: 'html',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            _method: 'DELETE'
                        },
                        url: "/student/" + student_id,
                        success: function(data) {
                            setTimeout(function() {
                                $('#confirmModal').modal('hide');
                            }, 2000);

                            $('div.flash-message').html(data);

                            studentTable.ajax.reload();
                        },
                        error: function (data) {
                            $('div.flash-message').html(data);
                        }
                    })
                });
                
                $('.alert').delay(3000).fadeOut();

                $('#buttonExport').click(function() {
                    $('#modelId1').modal('hide');
                });
        });
</script>

@endsection