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
            </div>


        </div>
    </div>

    <div class="col-md-12">
        <div class="card">
            <div class="card-header">Senarai Pelajar</div>
            <div>
                <a style="margin: 19px;" href="#" class="btn btn-primary allBtn"> <i class="far fa-id-badge"></i> All</a>
                <a style="margin: 19px;" href="#" class="btn btn-primary blacklistBtn"> <i class="far fa-id-badge"></i> Blacklist</a>
                <a style="margin: 19px; float: right;" href="#" class="btn btn-success" data-toggle="modal" data-target="#modelId1"> <i class="fas fa-plus"></i> Export Dorm</a>
                <a style="margin: 19px; float: right;" href="#" class="btn btn-success" data-toggle="modal" data-target="#modelId2"> <i class="fas fa-plus"></i> Export All</a>
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

                <div class="flash-message"></div>

                <div class="table-responsive">
                    <table id="studentTable" class="table table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr style="text-align:center">
                                <th> No. </th>
                                <th>Nama Pelajar</th>
                                <th>Kelas</th>
                                <th>Dorm</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        {{-- confirmation Block modal --}}
        <div id="blockConfirmationModal" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Block Pelajar</h4>
                    </div>
                    <div class="modal-body">
                        Adakah anda pasti?
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-dismiss="modal" class="btn btn-primary" id="block" name="block">Block</button>
                        <button type="button" data-dismiss="modal" class="btn">Batal</button>
                    </div>
                </div>
            </div>
        </div>
        {{-- end confirmation delete modal --}}

        {{-- confirmation UnBlock modal --}}
        <div id="unblockConfirmationModal" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Unblock Pelajar</h4>
                    </div>
                    <div class="modal-body">
                        Adakah anda pasti?
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-dismiss="modal" class="btn btn-primary" id="unblock" name="unblock">Unblock</button>
                        <button type="button" data-dismiss="modal" class="btn">Batal</button>
                    </div>
                </div>
            </div>
        </div>
        {{-- end confirmation delete modal --}}

        <!-- export particular dorm student modal-->
        <div class="modal fade" id="modelId1" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Export Pelajar</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <!-- export pelajar -->
                    <form action="{{ route('exportdormstudentlist') }}" method="post">
                        <div class="modal-body">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label>Organisasi</label>
                                <select name="organ" id="organ" class="form-control">
                                    @foreach($organization as $row)
                                    <option value="{{ $row->id }}" selected>{{ $row->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <!-- dormlist -->
                                <label>Dorm</label>
                                <select name="dorm" id="dorm" class="form-control">
                                    @foreach($dormlist as $row)
                                    <option value="{{ $row->id }}" selected>{{ $row->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="modal-footer">
                                <button id="buttonExportDorm" type="submit" class="btn btn-primary">Export Dorm</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- export all student modal-->
        <div class="modal fade" id="modelId2" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Export Pelajar</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <!-- export pelajar -->
                    <form action="{{ route('exportallstudentlist') }}" method="post">
                        <div class="modal-body">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label>Organisasi</label>
                                <select name="organ" id="organ" class="form-control">
                                    @foreach($organization as $row)
                                    <option value="{{ $row->id }}" selected>{{ $row->nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="modal-footer">
                                <button id="buttonExportAll" type="submit" class="btn btn-primary">Export Semua</button>
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
    $(document).ready(function() {

        var studentTable;


        if ($("#organization").val() != "") {
            $("#organization").prop("selectedIndex", 1).trigger('change');
            fetch_data($("#organization").val());
            console.log("123");
            console.log($("#organization").val());
        }

        function fetch_data(oid = '') {
            studentTable = $('#studentTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('dorm.getAllStudentlistDatatable') }}",
                    // url: "{{ route('dorm.getResidentsDatatable') }}",

                    data: {
                        oid: oid,
                        hasOrganization: true
                    },
                    type: 'GET'

                },
                'columnDefs': [{
                    "targets": [0], // your case first column
                    "className": "text-center",
                    "width": "2%"
                }, {
                    "targets": [1, 2, 3, 4, 5], // your case first column
                    "className": "text-center",
                }, ],
                order: [
                    [1, 'asc']
                ],
                columns: [{
                    "data": null,
                    searchable: false,
                    "sortable": false,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }, {
                    data: "studentName",
                    name: 'studentName',
                }, {
                    data: "className",
                    name: 'className',
                }, {
                    data: "dormName",
                    name: 'dormName',
                }, {
                    data: "status",
                    name: 'status',
                    orderable: false,
                    searchable: false
                }, {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }, ]
            });

        }

        function fetch_blacklist_data(oid = '') {

            studentTable = $('#studentTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('dorm.getBlacklistStudentlistDatatable') }}",
                    data: {
                        oid: oid,
                        hasOrganization: true,

                    },
                    type: 'GET',
                    success: function(data) {
                        console.log("success");
                    },
                    error: function(data) {
                        console.log("fail inside blacklist");
                    }

                },
                'columnDefs': [{
                    "targets": [0], // your case first column
                    "className": "text-center",
                    "width": "2%"
                }, {
                    "targets": [1, 2, 3, 4, 5], // your case first column
                    "className": "text-center",
                }, ],
                order: [
                    [1, 'asc']
                ],
                columns: [{
                    "data": null,
                    searchable: false,
                    "sortable": false,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }, {
                    data: "studentName",
                    name: 'studentName',
                }, {
                    data: "className",
                    name: 'className',
                }, {
                    data: "dormName",
                    name: 'dormName',
                }, {
                    data: "status",
                    name: 'status',
                    orderable: false,
                    searchable: false
                }, {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }, ]
            });

        }



        $('#organization').change(function() {
            var organizationid = $("#organization option:selected").val();
            $('#studentTable').DataTable().destroy();
            fetch_data(organizationid);
        });

        $(document).on('click', '.allBtn', function() {
            var organizationid = $("#organization option:selected").val();
            $('#studentTable').DataTable().destroy();
            fetch_data(organizationid);
        });

        $(document).on('click', '.blacklistBtn', function() {
            viewStatus = 2;
            var organizationid = $("#organization option:selected").val();
            $('#studentTable').DataTable().destroy();
            fetch_blacklist_data(organizationid);
        });

        // csrf token for ajax
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var student_id;
        var block_status = 0;

        $(document).on('click', '.blockBtn', function() {
            student_id = $(this).attr('id');
            $('#blockConfirmationModal').modal('show');
        });

        $(document).on('click', '.unblockBtn', function() {
            student_id = $(this).attr('id');
            $('#unblockConfirmationModal').modal('show');
        });

        $('#block').click(function() {
            block_status = 1;
            $.ajax({
                type: 'POST',
                dataType: 'html',
                data: {
                    "_token": "{{ csrf_token() }}",
                    //_method: 'DELETE'
                },
                url: "/dorm/blockStudent/" + student_id + "/" + block_status,
                success: function(data) {
                    setTimeout(function() {
                        $('#confirmModal').modal('hide');
                    }, 2000);
                    console.log("it Works");

                    $('div.flash-message').html(data);

                    studentTable.ajax.reload();
                },
                error: function(data) {
                    $('div.flash-message').html(data);
                }
            })
        });

        $('#unblock').click(function() {
            block_status = 0;
            $.ajax({
                type: 'POST',
                dataType: 'html',
                data: {
                    "_token": "{{ csrf_token() }}",
                    //_method: 'DELETE'
                },
                url: "/dorm/blockStudent/" + student_id + "/" + block_status,
                success: function(data) {
                    setTimeout(function() {
                        $('#confirmModal').modal('hide');
                    }, 2000);
                    console.log("it Works");

                    $('div.flash-message').html(data);

                    studentTable.ajax.reload();
                },
                error: function(data) {
                    $('div.flash-message').html(data);
                }
            })
        });

        $('.alert').delay(3000).fadeOut();

    });
</script>
@endsection