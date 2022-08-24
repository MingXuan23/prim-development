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
                <a style="margin: 19px;" href="#" class="btn btn-success" data-toggle="modal" data-target="#modelId1"> <i class="fas fa-plus"></i> Export</a>
                @if($roles == "Penjaga")
                <a style="margin: 19px; float: right;" href="{{ route('dorm.create') }}" class="btn btn-primary"> <i class="fas fa-plus"></i> Tambah Permintaan</a>
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

                <div class="table-responsive">
                    <table id="requestTable" class="table table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr style="text-align:center">
                                <th> No. </th>
                                <th>Nama Pelajar</th>
                                <th>No Tel Penjaga</th>
                                <th>Tarikh Permintaan</th>
                                <th>Tarikh dan <br>Masa Keluar</th>
                                <th>Tarikh dan <br>Masa Sampai</th>
                                <th>Tarikh dan <br>Masa Masuk</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        <!-- 注意这个之后要改 import, delete, export-->
        
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
                    "targets": [1, 2, 3, 4, 5, 6], // your case first column
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
                    data: "nama",
                    name: 'nama',
                    orderable: true,
                    searchable: true
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

       

        $('.alert').delay(3000).fadeOut();

    });
</script>
@endsection