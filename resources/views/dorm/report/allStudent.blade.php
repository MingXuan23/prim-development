@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@include('layouts.datatable')
@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Laporan</h4>
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

                <div class="form-group">
                    <label>Tarikh Mula</label>
                    <input onclick="this.showPicker()" class="form-control" id="start_date" name="start_date" type="date" placeholder="Pilih Tarikh Mula">
                </div>

                <div class="form-group">
                    <label>Tarikh Tamat</label>
                    <input onclick="this.showPicker()" class="form-control" id="end_date" name="end_date" type="date" placeholder="Pilih Tarikh Tamat">
                </div>
            </div>

        </div>
    </div>

    <div class="col-md-12">
        <div class="card">
            <div class="card-header">Senarai</div>
            <div>
                <a style="margin: 19px;" href="#" class="btn btn-success" data-toggle="modal" data-target="#modelId1"> <i class="fas fa-plus"></i> Export</a>
                <a style="margin: 19px; " href="#" class="btn btn-success " data-toggle="modal" data-target="#modelId2"> <i class="fa fa-print"></i> Print</a>
                <a style="margin: 19px; float: right;" href="{{ route('dorm.resetOutingLimit') }}" class="btn btn-primary"> <i class="fas fa-plus"></i> Set Outing Limit To Empty</a>
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
                @if(\Session::has('fail'))
                <div class="alert alert-danger">
                    <p>{{ \Session::get('fail') }}</p>
                </div>
                @endif

                <div class="flash-message"></div>

                <div class="table-responsive">
                    <table id="requestTable" class="table table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr style="text-align:center">
                                <th> No. </th>
                                <th>Kategori</th>
                                <th>Bilangan Permintaan</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <!-- export -->
        <div class="modal fade" id="modelId1" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Export Laporan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form action="{{ route('exportallrequest') }}" method="post">
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
                                <label>Tarikh Mula</label>
                                <input onclick="this.showPicker()" class="form-control" id="from" name="from" type="date" placeholder="Pilih Tarikh Mula">
                            </div>

                            <div class="form-group">
                                <label>Tarikh Tamat</label>
                                <input onclick="this.showPicker()" class="form-control" id="to" name="to" type="date" placeholder="Pilih Tarikh Tamat">
                            </div>
                            <div class="modal-footer">
                                <button id="buttonExport" type="submit" class="btn btn-primary">Export</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- print -->
        <div class="modal fade" id="modelId2" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Print Laporan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form action="{{ route('dorm.printallrequest') }}" method="get">
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
                                <label>Tarikh Mula</label>
                                <input onclick="this.showPicker()" class="form-control" id="from" name="from" type="date" placeholder="Pilih Tarikh Mula">
                            </div>

                            <div class="form-group">
                                <label>Tarikh Tamat</label>
                                <input onclick="this.showPicker()" class="form-control" id="to" name="to" type="date" placeholder="Pilih Tarikh Tamat">
                            </div>
                            <div class="modal-footer">
                                <button id="buttonExport" type="submit" class="btn btn-primary">Print</button>
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
        end_date.max = start_date.max = end_date.value = start_date.value = new Date().toISOString().split("T")[0];

        var requestTable;

        if ($("#organization").val() != "") {
            $("#organization").prop("selectedIndex", 1).trigger('change');
            fetch_data($("#organization").val());
        }

        if ($("#organExport").val() != "") {
            $("#organExport").prop("selectedIndex", 1).trigger('change');
            // fetch_data($("#organExport").val());
        }

        function fetch_data(oid = '') {

            requestTable = $('#requestTable').DataTable({
                processing: true,
                serverSide: true,

                ajax: {
                    url: "{{ route('dorm.getStudentOutingByCategory') }}",
                    data: {
                        oid: oid,
                        start_date: $("#start_date").val(),
                        end_date: $("#end_date").val(),
                        hasOrganization: true
                    },
                    type: 'GET',

                },
                'columnDefs': [{
                    "targets": [0], // your case first column
                    "className": "text-center",
                    "width": "2%"
                }, {
                    "targets": [1, 2, 3], // your case first column
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
                    data: 'catname',
                    name: 'catname'
                }, {
                    data: 'total',
                    name: 'total'
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
            $('#requestTable').DataTable().destroy();
            fetch_data(organizationid);
        });

        $('#organExport').change(function() {
            var organizationid = $("#organExport").val();
            $('#requestTable').DataTable().destroy();
            // var _token            = $('input[name="_token"]').val();
            fetch_data(organizationid);
        });

        $('#end_date').change(function() {
            if (end_date.value < start_date.value) {
                end_date.value = start_date.value;
            }
            to.value = end_date.value;
            var organizationid = $("#organization option:selected").val();
            $('#requestTable').DataTable().destroy();
            fetch_data(organizationid);
        });

        $('#start_date').change(function() {
            from.value = end_date.min = start_date.value;
            var organizationid = $("#organization option:selected").val();
            $('#requestTable').DataTable().destroy();
            fetch_data(organizationid);
        });

        $('#to').change(function() {
            if (to.value < from.value) {
                to.value = from.value;
            }
            var organizationid = $("#organExport").val();
            $('#requestTable').DataTable().destroy();
            fetch_data(organizationid);
        });

        $('#from').change(function() {
            to.min = from.value;
            var organizationid = $("#organExport").val();
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