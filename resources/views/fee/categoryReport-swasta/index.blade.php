@extends('layouts.master')
@include('layouts.datatable')
@section('css')
    {{--
    <link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" /> --}}
@endsection

@section('content')

    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="page-title-box">
                <h4 class="font-size-18">Laporan Jenis Yuran</h4>
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
                            <option value="" selected>Pilih Organisasi</option>
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

                    <div id="yuran" class="form-group">
                        <label> Yuran </label>
                        <select name="fees" id="fees" class="form-control">
                            <option value="0" disabled selected>Pilih Yuran</option>
                        </select>
                    </div>

                </div>

                <br>

                <div class="card">
                    <div class="card-body pt-3 pb-4">
                        <h4 class="card-title mb-5">Statistik Status Pembayaran Pelajar</h4>
                        <div class="row justify-content-center mt-3">
                            <div class="col-sm-4">
                                <div class="text-center">
                                    <h5 class="mb-0 font-size-20" id="student-complete">0 / 0</h5>
                                    <p class="text-muted">Telah Bayar</p>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="text-center">
                                    <h5 class="mb-0 font-size-20" id="student-not-complete">0 / 0</h5>
                                    <p class="text-muted">Belum Bayar</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <br>

                <div class="col-md-12">
                    <a style="float: right; margin: 0px 0px 10px 10px" class="btn btn-primary" data-toggle="modal"
                        data-target="#modalJumlahBayaran"><i class="fas fa-plus"></i> Export Jumlah Bayaran Ibu/Bapa</a>
                    <a style="float: right; margin: 0px 0px 10px 10px" class="btn btn-success" data-toggle="modal"
                        data-target="#modalByYuran"><i class="fas fa-plus"></i> Export Yuran</a>
                </div>

                <div class="col-md-12">
                    <div class="card">
                        <div class="table-responsive">
                            <table id="yuranTable" class="table table-bordered table-striped dt-responsive wrap"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr style="text-align:center">
                                        <th>No</th>
                                        <th>Nama Murid</th>
                                        <th>Jantina</th>
                                        <th>Tarikh Daftar</th>
                                        <th>Amaun Perlu Bayar (RM)</th>
                                        <th>Rujukan</th>
                                        <th>Status Pembayaran</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- modal export yuran --}}
    <div class="modal fade" id="modalByYuran" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Export Yuran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form action="{{ route('exportAllYuranStatus') }}" method="post">
                    <div class="modal-body">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>Organisasi</label>
                            <select name="organExport" id="organExport" class="form-control">
                                <option value="" disabled selected>Pilih Organisasi</option>
                                @foreach($organization as $row)
                                    <option value="{{ $row->id }}">{{ $row->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Nama Yuran</label>
                            <select name="yuranExport" id="yuranExport" class="form-control">

                            </select>
                        </div>
                        <div class="form-group">
                            <label>Status Yuran Untuk Dieksport</label>
                            <select name="yuranStatus" id="yuranStatus" class="form-control">
                                <option value="" selected disabled>Pilih status yuran</option>
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                                <option value="both_status">Aktif dan Tidak Aktif</option>
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

    <div class="modal fade" id="modalJumlahBayaran" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Export Jumlah Bayaran Ibu/Bapa</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form action="{{ route('exportJumlahBayaranIbuBapa') }}" method="post" onsubmit="remindMessage()">
                    <div class="modal-body">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>Organisasi</label>
                            <select name="organExport1" id="organExport1" class="form-control">
                                <option value="" disabled selected>Pilih Organisasi</option>
                                @foreach($organization as $row)
                                    <option value="{{ $row->id }}">{{ $row->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Nama Kelas</label>
                            <select name="yuranExport1" id="yuranExport1" class="form-control">

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

@endsection


@section('script')
    <!-- Peity chart-->
    <script src="{{ URL::asset('assets/libs/peity/peity.min.js')}}"></script>

    <!-- Plugin Js-->
    {{--
    <script src="{{ URL::asset('assets/libs/chartist/chartist.min.js')}}"></script>

    <script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script>
    <script src="{{ URL::asset('assets/libs/flot-charts/flot-charts.min.js')}}"></script>
    <script src="{{ URL::asset('assets/js/pages/flot.init.js')}}"></script> --}}

    <script>
        $(document).ready(function () {

            function fetchClass(organizationid = '', yuranId = '') {
                var _token = $('input[name="_token"]').val();
                $.ajax({
                    url: "{{ route('fees.fetchYuranByOrganId') }}",
                    method: "POST",
                    data: {
                        oid: organizationid,
                        _token: _token
                    },
                    success: function (result) {
                        $(yuranId).empty();
                        $(yuranId).append("<option value='0' selected>Semua Yuran</option>");
                        jQuery.each(result.success, function (key, value) {
                            $(yuranId).append("<option value='" + value.id + "'>" + value.name + "</option>");
                        });
                    }
                })
            }

            $('#organExport').change(function () {
                var organizationid = $("#organExport").val();
                var _token = $('input[name="_token"]').val();
                fetchClass(organizationid, '#yuranExport');
            });

            $('#organExport1').change(function () {
                var organizationid = $("#organExport1").val();
                var _token = $('input[name="_token"]').val();
                fetch_data(organizationid, '#yuranExport1');
            });

            if ($("#organization").val() == "") {
                $("#organization").prop("selectedIndex", 1).trigger('change');
                fetch_data($("#organization").val(), '#classes');
            }

            function fetch_data(oid = '', classId = '') {
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
                        $(classId).append('<option value="0" disabled selected>Pilih Kelas</option>');
                        //$(classId).append("<option value='all'>Semua Kelas</option>");
                        jQuery.each(result.success, function (key, value) {
                            $(classId).append("<option value='" + value.cid + "'>" + value.cname + "</option>");
                        });
                    }
                })
            }

            $('#classes').change(function () {
                if ($(this).val() != '') {
                    var classid = $("#classes option:selected").val();
                    var _token = $('input[name="_token"]').val();

                    console.log(classid);
                    $.ajax({
                        url: "{{ route('fees.fetchYuran') }}",
                        method: "POST",
                        data: {
                            classid: classid,
                            oid: $("#organization").val(),
                            _token: _token
                        },
                        success: function (result) {
                            $('#fees').empty();
                            $("#fees").append("<option value='0' disabled selected>Pilih Yuran</option>");

                            jQuery.each(result.success, function (key, value) {
                                var startdate = new Date(value.start_date).toLocaleDateString('en-GB');
                                var enddate = new Date(value.end_date).toLocaleDateString('en-GB');

                                $("#fees").append("<option value='" + value.id + "'>" + value.name + " ( RM" + value.totalAmount.toFixed(2) + " ) ( Mula: " + startdate + ", Akhir: " + enddate + " ) </option>");
                            });
                        }
                    })
                }
            });

            $('#organization').change(function () {
                if ($(this).val() != '') {
                    fetch_data($("#organization").val(), "#classes");
                }
            });

            $('#fees').change(function () {
                if ($(this).val() != 0) {
                    $('#yuranTable').DataTable().destroy();

                    var yuranTable = $('#yuranTable').DataTable({
                        ordering: true,
                        processing: true,
                        serverSide: true,
                        ajax: {
                            url: "{{ route('fees.debtDatatable') }}",
                            type: 'GET',
                            data: {
                                feeid: $("#fees").val(),
                                classid: $("#classes").val(),
                                orgId: $("#organization").val(),
                                feeYear: null
                            }
                        },
                        'columnDefs': [{
                            "targets": [0, 2, 3, 4, 5, 6], // your case first column
                            "className": "text-center",
                            "width": "2%"
                        }],
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
                            data: "nama",
                            name: "nama",
                            "width": "20%"
                        },
                        {
                            data: "gender",
                            name: "gender",
                            "width": "10%"
                        }, {
                            data: "cs_startdate",
                            name: "cs_startdate",
                            "width": "10%",
                            render: function (data, type, full) {
                                if (data) {
                                    var formattedDate = new Date(data);
                                    var day = formattedDate.getDate();
                                    var month = formattedDate.getMonth() + 1; // Months are zero-based
                                    var year = formattedDate.getFullYear();

                                    // Ensure leading zeros for day and month if needed
                                    if (day < 10) {
                                        day = '0' + day;
                                    }
                                    if (month < 10) {
                                        month = '0' + month;
                                    }

                                    return day + '/' + month + '/' + year;
                                } else {
                                    return '';
                                }
                            }
                        }, {
                            data: "finalAmount",
                            name: "finalAmount",
                            "width": "10%",
                            orderable: false,
                            searchable: false,
                            defaultContent: 0,
                            render: function (data, type, full) {
                                if (data) {
                                    return parseFloat(data).toFixed(2);
                                } else {
                                    return 0;
                                }
                            }
                        }, {
                            data: "desc",
                            name: "desc",
                            "width": "10%"
                        }, {
                            data: "status",
                            name: "status",
                            "width": "10%"
                        }]
                    });
                    fetch_statistic($("#classes").val(), $("#fees").val());
                }
            });

            function fetch_statistic(classId = '', feesId = '') {
                $('#student-complete').html('<i class="fas fa-spinner fa-spin"></i> / <i class="fas fa-spinner fa-spin"></i>');
                $('#student-not-complete').html('<i class="fas fa-spinner fa-spin"></i> / <i class="fas fa-spinner fa-spin"></i>');
                $.ajax({
                    url: "{{ route('fees.feesReportByClassId') }}",
                    method: "get",
                    data: {
                        cid: classId,
                        fid: feesId
                    },
                    success: function (result) {
                        $('#student-complete').empty();
                        $('#student-complete').text(result.total_student_paid + ' / ' + result.total_student);

                        $('#student-not-complete').empty();
                        $('#student-not-complete').text(result.total_student_debt + ' / ' + result.total_student);
                    }
                })
            }

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