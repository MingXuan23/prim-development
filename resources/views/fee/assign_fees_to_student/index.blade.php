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
                <h4 class="font-size-18">Ubah Yuran Pelajar</h4>
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
                            @foreach($organization as $row)
                                <option value="{{ $row->id }}">{{ $row->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="dkelas" class="form-group">
                        <label> Kelas </label>
                        <select name="classes" id="classes" class="form-control">
                            <option value="0" disabled selected>Pilih Kelas</option>
                        </select>
                    </div>

                </div>

                <div class="col-md-12">
                    <div class="card">
                        <div class="table-responsive">
                            <table id="studentsTable" class="table table-bordered table-striped dt-responsive wrap"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr style="text-align:center">
                                        <th>No</th>
                                        <th>Nama Pelajar</th>
                                        <th>Jantina</th>
                                        <th>Status Pelajar</th>
                                        <th>Yuran</th>
                                        <th>Status Yuran</th>
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

            if ($("#organization").val() == "") {
                $("#organization").prop("selectedIndex", 1).trigger('change');
                fetch_classes($("#organization").val(), '#classes');
            }

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

            function fetch_students(classid = '') {
                studentsTable = $('#studentsTable');

                studentsTable = studentsTable.DataTable({
                    ordering: true,
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('fees.fetchOneStudentToManyFeesDatatable') }}",
                        method: "GET",
                        data: {
                            oid: $('#organization').val(),
                            classid: classid,
                            routeName: "fees.assignFeesToStudentEdit"
                        }
                    },
                    'columnDefs': [{
                        "targets": [0, 1, 2, 3, 4, 5],
                        "className": "text-center",
                        "width": "2%"
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
                            data: "student_name",
                            name: "student_name",
                            "width": "20%"
                        },
                        {
                            data: "gender",
                            render: function (data, type, row) {
                                if (data == 'L') {
                                    return "<p>Lelaki</p>";
                                } else if (data == 'P') {
                                    return "<p>Perempuan</p>";
                                }
                            },
                            "width": "5%"
                        },
                        {
                            data: "student_status",
                            render: function (data, type, row) {
                                if (data == 1) {
                                    return "<span class='badge badge-success'>Aktif</span>";
                                } else {
                                    return "<span class='badge badge-danger'>Tidak Aktif</span>";
                                }
                            },
                            "width": "10%"
                        },
                        {
                            data: "fees",
                            render: function (data, type, row) {
                                // if the first object in the data array has null values, return a dash (-)
                                if (data[0]['fee_category'] == null) {
                                    return "<span>-</span>";
                                }

                                // (to prevent the yuran name and status yuran from having line misarrangement)
                                return data
                                    .map(fee => {
                                        if (fee.fee_category != null && fee.fee_name != null) {
                                            return "<span class='text-truncate mb-1'>" + fee.fee_category + " - " + fee.fee_name + "</span>";
                                        }
                                    })
                                    .join("<br>");
                            },
                            "width": "40%"
                        },
                        {
                            data: "fees",
                            render: function (data, type, row) {
                                // if the first object in the data array has null values, return a dash (-)
                                if (data[0]['fee_status'] == null) {
                                    return "<span>-</span>";
                                }

                                return data
                                    .map(fee => {
                                        if (fee.fee_status == "Debt") {
                                            return "<span class='badge badge-danger mb-1'>Belum Selesai</span>";
                                        } else if (fee.fee_status == "Paid") {
                                            return "<span class='badge badge-success mb-1'>Selesai</span>";
                                        }
                                    })
                                    .join("<br>");
                            },
                            "width": "10%"
                        },
                        {
                            data: "action",
                            name: "action",
                            "width": "15%"
                        }
                    ]
                });
            }

            $('#classes').change(function () {
                if ($(this).val() != '') {
                    var classid = $("#classes option:selected").val();

                    // remove the initial data when the classes selection is being reselected
                    $('#studentsTable').DataTable().clear().destroy();

                    fetch_students(classid);
                }
            });

            $('#organization').change(function () {
                // remove the initial data when the organization selection is being reselected
                $('#studentsTable').DataTable().clear().destroy();

                if ($(this).val() != '') {
                    fetch_students();
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

        function remindMessage() {
            //document.querySelectorAll('#buttonExport').forEach(button => button.disabled = true);
            if ($('#yuranExport1').val() == 0)
                alert("To download all data may take more time,dont refresh the page");
        }
    </script>
@endsection