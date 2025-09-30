@extends('layouts.master')

@include('layouts.datatable')
@section('css')
    <link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
    <style>
        .errorMessage {
            color: red;
        }
    </style>
@endsection

@section('content')

    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="page-title-box">
                <h4 class="font-size-18">Permohonan Pendaftaran Pelajar Untuk Bayar Yuran</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">

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

                <div class="card-body">

                    <div class="form-group">
                        <label>Organisasi</label>
                        <select name="organization" id="organization" class="form-control">
                            <option value="" disabled selected>Pilih Organisasi</option>
                            @foreach($organizations as $row)
                                <option value="{{ $row->id }}">{{ $row->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="col-md-12">
                    <div class="card">
                        <div class="table-responsive">
                            <table id="pendingRegistrationsTable"
                                class="table table-bordered table-striped dt-responsive wrap"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr style="text-align:center">
                                        <th>No</th>
                                        <th>Nama Penjaga</th>
                                        <th>No. Telefon Penjaga</th>
                                        <th>Nama Pelajar</th>
                                        <th>No. Kad Pengenalan Pelajar</th>
                                        <th>Jantina Pelajar</th>
                                        <th>Kelas</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                            </table>
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
    <script src="{{ URL::asset('assets/libs/parsleyjs/parsleyjs.min.js')}}"></script>
    <script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script>
    <script src="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}" defer></script>

    <script>
        $(document).ready(function () {
            // function to fetch classes by organization
            function fetch_classes(oid = '', classId = '') {
                var _token = $('input[name="_token"]').val();
                $.ajax({
                    url: "{{ route('fees.fetchClassForCateYuran') }}",
                    method: "POST",
                    data: {
                        oid: oid,
                        _token: _token
                    },
                    success: function (result) {
                        $(classId).empty();
                        $(classId).append("<option value='' selected disabled>Pilih Kelas</option>");
                        jQuery.each(result.success, function (key, value) {
                            $(classId).append("<option value='" + value.cid + "'>" + value.cname + "</option>");
                        });
                    }
                })
            }

            $('#organization, #classes').on('change', function () {
                if ($(this).val() != '') {
                    var classId = $("#classes option:selected").val();
                    var pendingRegistrationsTable = $('#pendingRegistrationsTable');

                    // remove the initial data when the classes selection is being reselected
                    pendingRegistrationsTable.DataTable().clear().destroy();

                    // add data to the yuran table to display all students with respective exportAllYuranStatus
                    pendingRegistrationsTable = pendingRegistrationsTable.DataTable({
                        ordering: true,
                        processing: true,
                        serverSide: true,
                        ajax: {
                            url: "{{ route('student.parentRegisterStudents.getAllPendingRegistrations') }}",
                            method: "GET",
                            data: {
                                oid: $('#organization').val(),
                            }
                        },
                        'columnDefs': [{
                            "targets": [0, 1, 2, 3, 4, 5, 6],
                            "className": "text-center"
                        }],
                        order: [
                            [1, 'asc']
                        ],
                        columns: [
                            {
                                "data": null,
                                searchable: false,
                                "sortable": false,
                                render: function (data, type, row, meta) {
                                    return meta.row + meta.settings._iDisplayStart + 1;
                                },
                                "width": "5%"
                            },
                            {
                                data: "parent_name",
                                name: "parent_name",
                                "width": "10%"
                            },
                            {
                                data: "telno",
                                name: "telno",
                                "width": "10%"
                            },
                            {
                                data: "student_name",
                                name: "student_name",
                                render: function (data, type, row) {
                                    return "<span>" + data + "</span>";
                                },
                                "width": "17%"
                            },
                            {
                                data: "student_icno",
                                name: "student_icno",
                                render: function (data, type, row) {
                                    return "<span>" + data + "</span>"
                                },
                                "width": "15%"
                            },
                            {
                                data: "student_gender",
                                name: "student_gender",
                                render: function (data, type, row) {
                                    if (data == "L") {
                                        return "<span>Lelaki</span>";
                                    } else {
                                        return "<span>Perempuan</span>";
                                    }
                                },
                                "width": "5%"
                            },
                            {
                                name: "student_class",
                                data: "student_class",
                                render: function (data, type, row) {
                                    return "<span>" + data + "</span>";
                                },
                                "width": "10%"
                            },
                            {
                                data: "actions",
                                name: "actions",
                                "width": "15%"
                            }
                        ]
                    })
                }
            });

            $('#organization').on('change', function () {
                // remove the initial data when the organization selection is being reselected
                if ($(this).val() != '') {
                    fetch_classes($("#organization").val(), "#classes");
                }
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