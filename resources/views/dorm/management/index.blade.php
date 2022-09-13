@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@include('layouts.datatable')
@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Pengurusan Asrama</h4>
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
            <div class="card-header">Senarai Asrama</div>
            <div>
                <a style="margin: 19px;" href="#" class="btn btn-primary" data-toggle="modal" data-target="#modelId"> <i class="fas fa-plus"></i> Import</a>
                <a style="margin: 1px;" href="#" class="btn btn-success" data-toggle="modal" data-target="#modelId1"> <i class="fas fa-plus"></i> Export</a>
                <a style="margin: 19px; float: right;" href="{{ route('dorm.createDorm') }}" class="btn btn-primary"> <i class="fas fa-plus"></i> Tambah Asrama</a>
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
                    <table id="dormTable" class="table table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr style="text-align:center">
                                <th> No. </th>
                                <th>Nama Asrama</th>
                                <th>Kapasiti</th>
                                <th>Bilangan pelajar dalam</th>
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
                        <h4 class="modal-title">Padam Asrama</h4>
                    </div>
                    <div class="modal-body">
                        Adakah anda pasti?
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-dismiss="modal" class="btn btn-primary" id="delete" name="delete">Padam</button>
                        <button type="button" data-dismiss="modal" class="btn">Batal</button>
                    </div>
                </div>
            </div>
        </div>
        {{-- end confirmation delete modal --}}

        <!-- Clear dorm confirmation delete modal -->
        <div id="deleteConfirmationModal1" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Kosongkan Asrama</h4>
                    </div>
                    <div class="modal-body">
                        Adakah anda pasti?
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-dismiss="modal" class="btn btn-primary" id="delete1" name="delete1">Kosong</button>
                        <button type="button" data-dismiss="modal" class="btn">Batal</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- export dorm modal-->
        <div class="modal fade" id="modelId1" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Export Asrama</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('exportdorm') }}" method="post">
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
                                <button id="buttonExport" type="submit" class="btn btn-primary">Export</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- import dorm modal -->
        <div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Import Asrama</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('importdorm') }}" method="post" enctype="multipart/form-data">
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
        <!-- import dorm residents modal -->
        <div class="modal fade" id="modelId3" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Import Residents</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>

                    </div>
                    <form action="{{ route('importresident') }}" method="post" enctype="multipart/form-data">
                        <div class="modal-body">

                            {{ csrf_field() }}
                            <div class="form-group">

                                <label>Dorm</label>
                                <input id="dorm" name="dorm" value="" hidden></input>

                            </div>
                            <div class="form-group">
                                <input type="file" name="file1" required>
                            </div>

                            <div class="modal-footer">
                                <button id="fileID" type="submit" class="btn btn-primary fileIDclass">Import</button>
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

        var dormTable;

        if ($("#organization").val() != "") {
            $("#organization").prop("selectedIndex", 1).trigger('change');
            fetch_data($("#organization").val());
        }

        function fetch_data(oid = '') {
            dormTable = $('#dormTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('dorm.getDormDataTable') }}",
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
                    "targets": [4], // your case first column
                    "className": "text-center",
                    "width": "15%"
                }, {
                    "targets": [1, 3, 2], // your case first column
                    "className": "text-center",
                }, ],
                order: [
                    [1, 'asc']
                ],
                //'name', 'accommodate_no', 'student_inside_no'
                columns: [{
                    "data": null,
                    searchable: false,
                    "sortable": false,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }, {
                    data: "name",
                    name: 'name',
                    render: function(data, type, row, meta) {

                        return '<a href = "indexResident/' + row.id + '">' + data + '</a>';
                    }
                }, {
                    data: "accommodate_no",
                    name: 'accommodate_no',
                    searchable: false
                }, {
                    data: "student_inside_no",
                    name: 'student_inside_no',
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
            $('#dormTable').DataTable().destroy();
            fetch_data(organizationid);
        });

        // csrf token for ajax
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var dorm_id;

        $(document).on('click', '.destroyDorm', function() {
            dorm_id = $(this).attr('id');
            $('#deleteConfirmationModal').modal('show');
        });

        $(document).on('click', '.importBtn', function() {
            dorm_id = $(this).attr('id');
            $('#dorm').val(dorm_id);
            // dd($('#dorm').val(dorm_id));
        });

        $(document).on('click', '.clearDorm', function() {
            dorm_id = $(this).attr('id');
            $('#deleteConfirmationModal1').modal('show');
        });

        $('#delete').click(function() {
            $.ajax({
                type: 'POST',
                dataType: 'html',
                data: {
                    "_token": "{{ csrf_token() }}",
                    _method: 'GET'
                },
                url: "/sekolah/dorm/destroyDorm/" + dorm_id,
                success: function(data) {
                    setTimeout(function() {
                        $('#confirmModal').modal('hide');
                    }, 2000);
                    //console.log('it works');

                    $('div.flash-message').html(data);

                    dormTable.ajax.reload();
                },
                error: function(data) {
                    $('div.flash-message').html(data);
                    console.log("it doesn't Works");

                }
            })
        });

        $('#delete1').click(function() {
            $.ajax({
                type: 'POST',
                dataType: 'html',
                data: {
                    "_token": "{{ csrf_token() }}",
                    _method: 'GET'
                },
                url: "/sekolah/dorm/clearDorm/" + dorm_id,
                success: function(data) {
                    setTimeout(function() {
                        $('#confirmModal').modal('hide');
                    }, 2000);
                    console.log('it works');

                    $('div.flash-message').html(data);

                    dormTable.ajax.reload();
                },
                error: function(data) {
                    $('div.flash-message').html(data);
                    console.log("it doesn't Works");

                }
            })
        });

        $('.alert').delay(3000).fadeOut();

    });
</script>
@endsection