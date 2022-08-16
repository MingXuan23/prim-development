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
            <h4 class="font-size-18">Asrama</h4>
        </div>
    </div>
</div>
<div class="row">

    <div class="col-md-12">
        <div class="card">
            <div>
                <a style="margin: 19px;" href="#" class="btn btn-success" data-toggle="modal" data-target="#modelId1"> <i
                        class="fas fa-plus"></i> Export</a>
                
                <a style="margin: 19px; float: right;" href="{{ route('dorm.createResident') }}" class="btn btn-primary"> <i
                        class="fas fa-plus"></i> Tambah Pelajar</a>
            </div>

            <div class="card-body">
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

                    <div class="form-group">
                        <label> Asrama </label>
                        <select name="dorm" id="dorm" class="form-control">
                            <option value="" disabled selected>Pilih Asrama</option>

                        </select>
                    </div>
                </div>
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
                    <table id="residentTable" class="table table-bordered table-striped dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr style="text-align:center">
                                <th> No. </th>
                                <th> Nama Pelajar </th>
                                <th> Nama Kelas </th>
                                <th> Daftar Masuk </th>
                                <th> Daftar Keluar </th>
                                <th> Status </th>
                                <th> Blacklist </th>
                                <th> Details </th>
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
                        <h4 class="modal-title">Padam Pelajar</h4>
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
        <!-- export -->
        <div class="modal fade" id="modelId1" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Export Pelajar</h5>
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
                                <label>Nama Asrama</label>
                                <select name="dormExport" id="dormExport" class="form-control">

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
        
        var residentTable;

        if($("#organization").val() != ""){
            $("#organization").prop("selectedIndex", 1).trigger('change');
            fetchDorm($("#organization").val(), '#dorm');
        }

        if($("#organExport").val() != ""){
            $("#organExport").prop("selectedIndex", 0).trigger('change');
            fetchDorm($("#organExport").val(), '#dormExport');
        }

        function fetch_data(dormid = '') {
            residentTable = $('#residentTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    // resident
                    url: "{{ route('dorm.getResidentsDatatable') }}",
                    data: {
                        dormid: dormid,
                        hasOrganization: true
                    },
                    type: 'GET',

                },
                'columnDefs': [{
                    "targets": [0], // your case first column
                    "className": "text-center",
                    "width": "2%"
                },{
                    "targets": [1,2,3,4,5,6,7], // your case first column
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
                    data: 'start_date_time',
                    name: 'start_date_time'
                }, {
                    data: 'end_date_time',
                    name: 'end_date_time'
                }, {
                    data: 'outing_status',
                    name: 'outing_status',
                    orderable: false,
                    searchable: false
                },{
                    data: 'blacklist',
                    name: 'blacklist',
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

        $('#organization').change(function() {
            var organizationid    = $("#organization").val();
            var _token            = $('input[name="_token"]').val();
            fetchDorm(organizationid, "#dorm");
        });

        $('#organExport').change(function() {
            var organizationid    = $("#organExport").val();
            var _token            = $('input[name="_token"]').val();
            fetchDorm(organizationid, '#dormExport');
        });

        function fetchDorm(organizationid = '', dormId = ''){
            var _token = $('input[name="_token"]').val();
            $.ajax({
                url:"{{ route('dorm.fetchDorm') }}",
                method:"POST",
                data:{ oid:organizationid,
                        _token:_token },
                success:function(result)
                {
                    $(dormId).empty();
                    $(dormId).append("<option value='' disabled selected> Pilih Asrama</option>");
                    jQuery.each(result.success, function(key, value){
                        $(dormId).append("<option value='"+ value.id +"'>" + value.name + "</option>");
                    });
                }
            })
        }
        var dormid;
        $('#dorm').change(function() {
            var organizationid    = $("#organization option:selected").val();

            dormid = $("#dorm option:selected").val();
            if(dormid){
                $('#residentTable').DataTable().destroy();
                fetch_data( dormid);
            }
            // console.log(organizationid);
        });

        // csrf token for ajax
        $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var resident_id;

        $(document).on('click', '.btn-danger', function(){
            resident_id = $(this).attr('id');
            $('#deleteConfirmationModal').modal('show');
        });

        $('#delete').click(function() {
            console.log(dormid + "this is rid");
                $.ajax({
                    type: 'POST',
                    dataType: 'html',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        // _method: 'DELETE'
                    },
                    url: "/dorm/dorm/destroyResident/" + resident_id,
                    success: function(data) {
                        setTimeout(function() {
                            $('#confirmModal').modal('hide');
                        }, 2000);

                        $('div.flash-message').html(data);

                        residentTable.ajax.reload();
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