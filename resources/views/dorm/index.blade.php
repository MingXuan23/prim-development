<!-- 要怎么做auto check out -->
<!-- for block 超过6点那个 应该是for outings和住dorm的学生 而已 所以要改那个validation -->


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
                @if($isblacklisted == 1)    
                    <div class="alert-danger" style="margin: 19px; padding: 10px;">
                        Pelajar dalam blacklist
                    </div>
                @endif
                @if($roles == "Penjaga")
                <a style="margin: 19px; float: right;" href="{{ route('dorm.create') }}" class="btn btn-primary"> <i class="fas fa-plus"></i> Tambah Permintaan</a>
                @endif
                <!-- hai xu geng gai  CHECKIN 是不是应该记录时间？-->
                @if($roles == "Warden")
                <a style="margin: 19px; float: right;" href="{{ route('dorm.updateCheckIn') }}" class="btn btn-primary"> <i class="fas fa-plus"></i> Check In</a>
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
                                @if($roles == "Penjaga" || $roles == "Guard")
                                    <th>Status</th>
                                    <th>Tarikh dan Masa Keluar</th>
                                    <th>Tarikh dan Masa Sampai</th>
                                    <th>Tarikh dan Masa Masuk</th>
                                @else
                                    <th>Alasan</th>
                                @endif
                                <th>Action</th>
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
                            name="delete">Unblock</button>
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
            if($("#roles").val() == "Penjaga" || $("#roles").val() == "Guard"){
                requestTable = $('#requestTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('dorm.getStudentOutingDatatable') }}",
                        data: {
                            oid: oid,
                            hasOrganization: true
                        },
                        type: 'GET',

                    },
                    'columnDefs': [{
                        "targets": [0], // your case first column
                        "className": "text-center",
                        "width": "2%"
                    }, {
                        "targets": [1, 2, 3, 4, 5, 6, 7, 8], // your case first column
                        "className": "text-center",
                    },],
                    
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
                        data: "nama",
                        name: 'nama',
                        orderable: true,
                        searchable: true,
                    }, {
                        data: "parent_tel",
                        name: 'parent_tel',
                        orderable: false,
                        searchable: false
                    }, {
                        data: "apply_date_time",
                        name: 'apply_date_time',
                        orderable: false,
                        searchable: false
                    }, {
                        data: "catname",
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
                        orderable: false,
                        searchable: false
                    }, {
                        data: 'arrive_date_time',
                        name: 'arrive_date_time',
                        orderable: false,
                        searchable: false
                    },{
                        data: 'in_date_time',
                        name: 'in_date_time',
                        orderable: false,
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
                
                requestTable = $('#requestTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('dorm.getStudentOutingDatatable') }}",
                        data: {
                            oid: oid,
                            hasOrganization: true
                        },
                        type: 'GET',

                    },
                    'columnDefs': [{
                        "targets": [0], // your case first column
                        "className": "text-center",
                        "width": "2%"
                    }, {
                        "targets": [1, 2, 3, 4, 5], // your case first column
                        "className": "text-center",
                    },],
                    
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
                        data: "nama",
                        name: 'nama',
                        orderable: true,
                        searchable: true,
                    }, {
                        data: "parent_tel",
                        name: 'parent_tel',
                        orderable: false,
                        searchable: false
                    }, {
                        data: "apply_date_time",
                        name: 'apply_date_time',
                        orderable: false,
                        searchable: false
                    }, {
                        data: "catname",
                        name: 'catname',
                        orderable: false,
                        searchable: false
                    },{
                        data: "reason",
                        name: 'reason',
                        orderable: false,
                        searchable: false
                    },{
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }, ]
                    
                });
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
            $.ajax({
                type: 'POST',
                dataType: 'html',
                data: {
                    "_token": "{{ csrf_token() }}",
                    // _method: 'DELETE'
                },
                url: "/dorm/updateBlacklist/" + student_outing_id,
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

        $('#delete').click(function() {
            $.ajax({
                type: 'POST',
                dataType: 'html',
                data: {
                    "_token": "{{ csrf_token() }}",
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