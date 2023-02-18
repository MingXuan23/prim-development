

@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@include('layouts.datatable')

@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Asrama</h4>
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
            <div class="card-header">Senarai Permintaan Keluar</div>
            
            <div>
                @if(count($isblacklisted) > 0 && $roles == 6)    
                    <div class="alert-danger" style="margin: 19px; padding: 10px;">
                        Pelajar dalam blacklist
                        @foreach($isblacklisted as $row)
                        <li>{{$row->nama}}</li>
                        @endforeach
                    </div>
                @endif
                @if($roles == 6 || $roles == 1)
                <a style="margin: 19px; float: right;" href="{{ route('dorm.create') }}" class="btn btn-primary"> <i class="fas fa-plus"></i> Tambah Permintaan</a>
                @endif

                @if($roles == 13 || $roles == 1)
                    @if($checkin == 1)
                    <a style="margin: 19px; float: right;" href="{{ route('dorm.updateCheckIn', $checkNum)}}" class="btn btn-primary"> <i class="fas fa-minus"></i> Daftar Keluar</a>
                    @else
                    <a style="margin: 19px; float: right;" href="{{ route('dorm.updateCheckIn', $checkNum)}}" class="btn btn-primary"> <i class="fas fa-plus"></i> Daftar Masuk</a>
                    @endif
                @endif
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
                <input id="roles" value= "{{$roles}}" hidden>
                <div class="table-responsive">
                    <table id="requestTable" class="table table-bordered table-striped dt-responsive" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr style="text-align:center">
                                <th> No. </th>
                                <th>Nama Pelajar</th>
                                <th>No Tel Penjaga</th>
                                <th>Tarikh keluar dimohon</th>
                                <th>Kategori</th>
                                @if($roles == 6 || $roles == 8)
                                    <th>Status</th>
                                    <th>Tarikh dan Masa Keluar</th>
                                    <th>Tarikh dan Masa Sampai</th>
                                    <th>Tarikh dan Masa Masuk</th>
                                @elseif($roles == 1)
                                    <th>Alasan</th>
                                    <th>Status</th>
                                    <th>Tarikh dan Masa Keluar</th>
                                    <th>Tarikh dan Masa Sampai</th>
                                    <th>Tarikh dan Masa Masuk</th>
                                @else
                                    <th>Alasan</th>
                                    <th>Status</th>
                                    <th>Tarikh dan Masa Keluar</th>
                                    <th>Tarikh dan Masa Sampai</th>
                                    <th>Tarikh dan Masa Masuk</th>
                                @endif
                                <th>Tindakan</th>
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
                        <h4 class="modal-title">Padam Permintaan</h4>
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

        {{-- confirmation unblock modal --}}
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
                        <button type="button" data-dismiss="modal" class="btn btn-primary" id="unblock"
                            name="unblock">Unblock</button>
                        <button type="button" data-dismiss="modal" class="btn">Batal</button>
                    </div>
                </div>
            </div>
        </div>
        {{-- end confirmation unblock modal --}}
        
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

        var requestTable;

        if ($("#organization").val() != "") {
            $("#organization").prop("selectedIndex", 1).trigger('change');
            fetch_data($("#organization").val());
        }

        function fetch_data(oid = '') {
            if($("#roles").val() == 6 || $("#roles").val() == 8){
                requestTable = $('#requestTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('dorm.getStudentOutingDatatable') }}",
                        data: {
                            oid: oid,
                            rid: $("#roles").val(),
                            hasOrganization: true
                        },
                        type: 'GET',

                    },
                    'columnDefs': [{
                        "targets": [0], // your case first column
                        "className": "text-center",
                        "width": "2%"
                    }, {
                        "targets": [1, 2, 3, 4, 5, 6, 7, 8, 9], // your case first column
                        "className": "text-center",
                    },],
                    
                    order: [
                        [1, 'asc']
                    ],
                    columns: [{
                        "data": null,
                        searchable: false,
                        "sortable": true,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    }, {
                        data: "nama",
                        name: 'nama',
                        orderable: false,
                        searchable: true,
                    }, {
                        data: "parent_tel",
                        name: 'parent_tel',
                        orderable: false,
                        searchable: false
                    }, {
                        data: "apply_date_time",
                        name: 'apply_date_time',
                        orderable: true,
                        searchable: false
                    }, {
                        data: "fake_name",
                        name: 'catname',
                        orderable: false,
                        searchable: false
                    },{
                        data: "result",
                        name: 'result',
                        orderable: false,
                        searchable: false
                    },{
                        data: "out_date_time",
                        name: 'out_date_time',
                        orderable: true,
                        searchable: false
                    }, {
                        data: 'arrive_date_time',
                        name: 'arrive_date_time',
                        orderable: true,
                        searchable: false
                    },{
                        data: 'in_date_time',
                        name: 'in_date_time',
                        orderable: true,
                        searchable: false
                    },{
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }, ]
                    
                });
            }
            else if($("#roles").val() == 1){
                requestTable = $('#requestTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('dorm.getStudentOutingDatatable') }}",
                        data: {
                            oid: oid,
                            rid: $("#roles").val(),
                            hasOrganization: true
                        },
                        type: 'GET',

                    },
                    'columnDefs': [{
                        "targets": [0], // your case first column
                        "className": "text-center",
                        "width": "2%"
                    }, {
                        "targets": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10], // your case first column
                        "className": "text-center",
                    },],
                    
                    order: [
                        [1, 'asc']
                    ],
                    columns: [{
                        "data": null,
                        searchable: false,
                        "sortable": true,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    }, {
                        data: "nama",
                        name: 'nama',
                        orderable: false,
                        searchable: true,
                    }, {
                        data: "parent_tel",
                        name: 'parent_tel',
                        orderable: false,
                        searchable: false
                    }, {
                        data: "apply_date_time",
                        name: 'apply_date_time',
                        orderable: true,
                        searchable: false
                    }, {
                        data: "fake_name",
                        name: 'catname',
                        orderable: false,
                        searchable: false
                    },{
                        data: "reason",
                        name: 'reason',
                        orderable: false,
                        searchable: false
                    },{
                        data: "result",
                        name: 'result',
                        orderable: false,
                        searchable: false
                    },{
                        data: "out_date_time",
                        name: 'out_date_time',
                        orderable: true,
                        searchable: false
                    }, {
                        data: 'arrive_date_time',
                        name: 'arrive_date_time',
                        orderable: true,
                        searchable: false
                    },{
                        data: 'in_date_time',
                        name: 'in_date_time',
                        orderable: true,
                        searchable: false
                    },{
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }, ]
                    
                });
            }
            else{
                console.log(oid);
                requestTable = $('#requestTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('dorm.getStudentOutingDatatable') }}",
                        data: {
                            oid: oid,
                            rid: $("#roles").val(),
                            hasOrganization: true
                        },
                        type: 'GET',
                    },
                    'columnDefs': [{
                        "targets": [0], // your case first column
                        "className": "text-center",
                        "width": "2%"
                    }, {
                        "targets": [1, 2, 3, 4, 5, 6], // your case first column
                        "className": "text-center",
                    },],
                    
                    order: [
                        [1, 'asc']
                    ],
                    columns: [{
                        "data": null,
                        searchable: false,
                        "sortable": true,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    }, {
                        data: "nama",
                        name: 'nama',
                        orderable: false,
                        searchable: true,
                    }, {
                        data: "parent_tel",
                        name: 'parent_tel',
                        orderable: false,
                        searchable: false
                    }, {
                        data: "apply_date_time",
                        name: 'apply_date_time',
                        orderable: true,
                        searchable: false
                    }, {
                        data: "fake_name",
                        name: 'catname',
                        orderable: false,
                        searchable: false
                    },{
                        data: "reason",
                        name: 'reason',
                        orderable: false,
                        searchable: false
                    },{
                        data: "result",
                        name: 'result',
                        orderable: false,
                        searchable: false
                    },{
                        data: "out_date_time",
                        name: 'out_date_time',
                        orderable: true,
                        searchable: false
                    }, {
                        data: 'arrive_date_time',
                        name: 'arrive_date_time',
                        orderable: true,
                        searchable: false
                    },{
                        data: 'in_date_time',
                        name: 'in_date_time',
                        orderable: true,
                        searchable: false
                    },{
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }, ]    
                    
                });
                console.log("123");
            }
        }

        $('#organization').change(function() {
            var organizationid = $("#organization option:selected").val();
            $('#requestTable').DataTable().destroy();
            fetch_data(organizationid);
        });

        // csrf token for ajax
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var student_outing_id;

        $(document).on('click', '.deleteBtn', function(){
            student_outing_id = $(this).attr('id');
            $('#deleteConfirmationModal').modal('show');
        });

        $(document).on('click', '.unblockBtn', function(){
            student_outing_id = $(this).attr('id');
            $('#unblockConfirmationModal').modal('show');
        });

        $('#unblock').click(function() {
            console.log(student_outing_id);
            $.ajax({
                type: 'GET',
                dataType: 'html',
                data: {
                    "_token": "{{ csrf_token() }}",
                    rid: $("#roles").val(),
                    // _method: 'DELETE'
                },
                url: "/sekolah/dorm/updateBlacklist/" + student_outing_id,
                success: function(data) {
                    setTimeout(function() {
                        $('#confirmModal').modal('hide');
                    }, 2000);
                    
                    $('div.flash-message').html(data);

                    requestTable.ajax.reload();
                },
                error: function (data) {
                    console.log("hellllll");
                    $('div.flash-message').html(data);
                }
            })
        });

        $('#delete').click(function() {
            $.ajax({
                type: 'POST',
                dataType: 'html',
                data: {
                    "_token": "{{ csrf_token() }}",
                    rid: $("#roles").val(),
                    _method: 'DELETE'
                },
                url: "/dorm/" + student_outing_id,
                success: function(data) {
                    setTimeout(function() {
                        $('#confirmModal').modal('hide');
                    }, 2000);

                    $('div.flash-message').html(data);

                    requestTable.ajax.reload();
                },
                error: function (data) {
                    $('div.flash-message').html(data);
                }
            })
        });
        
        $('.alert').delay(3000).fadeOut();

    });
</script>
@endsection