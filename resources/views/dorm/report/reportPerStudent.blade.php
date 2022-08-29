@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@include('layouts.datatable')
@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Laporan Pelajar</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">

            {{csrf_field()}}
            <div class="card-body">

                <div class="form-group">
                    <label> Sebab Kategori</label>
                    <select name="applicationCategory" id="applicationCategory" class="form-control">
                        <option value="0" selected>All</option>
                        @foreach($applicationCat as $row)
                        <option value="{{ $row->id }}">{{ $row->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>


        </div>
    </div>

    <div class="col-md-12">
        <div class="card">
            <div class="card-header">Laporan {{$studentName->studentName}}</div>
            <div>
                <span hidden id="getId">{{$id}}</span>
                <!-- <a style="margin: 19px;" href="#" class="btn btn-primary allBtn"> <i class="far fa-id-badge"></i> All</a>
                <a style="margin: 19px;" href="#" class="btn btn-primary blacklistBtn"> <i class="far fa-id-badge"></i> Blacklist</a>-->
                <a style="margin: 19px; float: right;" href="#" class="btn btn-success exportCat" data-toggle="modal" data-target="#modelId1"> <i class="fas fa-plus"></i> Export Category</a>
                <a style="margin: 19px; float: right;" href="#" class="btn btn-success exportAll" data-toggle="modal" data-target="#modelId2"> <i class="fas fa-plus"></i> Export All</a>
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
                    <table id="reasonTable" class="table table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr style="text-align:center">
                                <th> No. </th>
                                <th>Kategori</th>
                                <th>Tarikh Masa Keluar</th>
                                <th>Sebab Balik</th>
                                <th>Warden Bertanggungjawab</th>
                                <th>Tarikh Masa Masuk</th>
                                <th>Guard Bertanggungjawab</th>
                            </tr>
                        </thead>
                    </table>
                </div>

                <!-- export particular reason modal-->
                <div class="modal fade" id="modelId1" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Export Category</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <!-- export category -->
                            <form action="{{ route('exportcategory') }}" method="post">
                                <div class="modal-body">
                                    {{ csrf_field() }}
                                    <div class="form-group">
                                        <!-- category list -->
                                        <label>Kategori</label>
                                        <select name="category" id="category" class="form-control">
                                            <option value="" selected disabled>Pilih Kategori</option>
                                            @foreach($applicationCat as $row)
                                            <option value="{{ $row->id }}">{{ $row->name }}</option>
                                            @endforeach
                                        </select>
                                        <input name="studentid" value="{{$id}}" hidden></input>

                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Export Kategori</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- export all reason modal-->
                <div class="modal fade" id="modelId2" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Export All</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <!-- export pelajar -->
                            <form action="{{ route('exportallcategory') }}" method="post">
                                <div class="modal-footer">
                                    {{ csrf_field() }}
                                    <input name="studentid" value="{{$id}}" hidden></input>

                                    <button type="submit" class="btn btn-primary">Export Semua</button>
                                </div>
                            </form>
                        </div>
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

<script>
    $(document).ready(function() {

        var studentTable;
        var student_id = $("#getId").html();



        if ($("#applicationCategory").val() != "") {
            $("#applicationCategory").prop("selectedIndex", 0).trigger('change');
            fetch_data($("#applicationCategory").val());
        }

        $('#applicationCategory').change(function() {
            var catId = $("#applicationCategory option:selected").val();
            $('#reasonTable').DataTable().destroy();

            fetch_data(catId);
        });

        function fetch_data(catId = '') {
            reasonTable = $('#reasonTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "/dorm/dorm/getReportDatatable/" + student_id,
                    data: {
                        catId: catId,
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
                    },
                    {
                        data: 'classificationName',
                        name: 'classificationName',
                        orderable: false,
                        searchable: true,
                    },
                    {
                        data: 'outTime',
                        name: 'outTime',
                        orderable: false,
                        searchable: false,
                    }, {
                        data: 'reason',
                        name: 'reason',
                        orderable: false,
                        searchable: false,
                    }, {
                        data: 'wardenName',
                        name: 'wardenName',
                        orderable: false,
                        searchable: true,
                    }, {
                        data: 'inTime',
                        name: 'inTime',
                        orderable: false,
                        searchable: false
                    }, {
                        data: 'guardName',
                        name: 'guardName',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

        }

        // csrf token for ajax
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // $(document).on('click', '.exportAll', function() {
        //     //student_id = $(this).attr('id');
        //     $('#unblockConfirmationModal').modal('show');
        // });

        // $(document).on('click', '.exportCat', function() {
        //     //student_id = $(this).attr('id');
        //     $('#unblockConfirmationModal').modal('show');
        // });

        // $('#block').click(function() {
        //     block_status = 1;
        //     $.ajax({
        //         type: 'POST',
        //         dataType: 'html',
        //         data: {
        //             "_token": "{{ csrf_token() }}",
        //             //_method: 'DELETE'
        //         },
        //         url: "/dorm/dorm/blockStudent/" + student_id + "/" + block_status,
        //         success: function(data) {
        //             setTimeout(function() {
        //                 $('#confirmModal').modal('hide');
        //             }, 2000);
        //             console.log("it Works");

        //             $('div.flash-message').html(data);

        //             studentTable.ajax.reload();
        //         },
        //         error: function(data) {
        //             console.log("it doesn't Works");

        //             $('div.flash-message').html(data);
        //         }
        //     })
        // });

        // $('#unblock').click(function() {
        //     block_status = 0;
        //     $.ajax({
        //         type: 'POST',
        //         dataType: 'html',
        //         data: {
        //             "_token": "{{ csrf_token() }}",
        //             //_method: 'DELETE'
        //         },
        //         url: "/dorm/dorm/blockStudent/" + student_id + "/" + block_status,
        //         success: function(data) {
        //             setTimeout(function() {
        //                 $('#confirmModal').modal('hide');
        //             }, 2000);
        //             console.log("it Works");

        //             $('div.flash-message').html(data);

        //             studentTable.ajax.reload();
        //         },
        //         error: function(data) {
        //             $('div.flash-message').html(data);
        //         }
        //     })
        // });

        // $('.alert').delay(3000).fadeOut();

    });
</script>
@endsection