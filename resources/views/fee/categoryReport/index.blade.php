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
                        <label> Tahun </label>
                        <select name="fee_year" id="fee_year" class="form-control">
                            <option value="0" disabled selected>Pilih Tahun</option>
                        </select>
                    </div>

                    <div id="yuran" class="form-group">
                        <label> Yuran </label>
                        <select name="fees" id="fees" class="form-control">
                            <option value="0" disabled selected>Pilih Yuran</option>
                        </select>
                    </div>

                </div>

                <div class="col-md-12">
                    <a style="float: right; margin: 0px 0px 10px 10px" class="btn btn-primary" data-toggle="modal"
                        data-target="#modalJumlahBayaran"><i class="fas fa-plus"></i> Export Jumlah Bayaran Ibu/Bapa</a>
                    <a style="float: right; margin: 0px 0px 10px 10px" class="btn btn-primary" data-toggle="modal"
                        data-target="#modalByYuran"><i class="fas fa-plus"></i> Export Yuran</a>
                    <a style="float: right; margin: 0px 0px 10px 10px" class="btn btn-primary" data-toggle="modal"
                        data-target="#modalExportSemuaYuran"><i class="fas fa-plus"></i> Export Semua Yuran</a>
                    <div style="float: left; margin-left: 15px;">
                        <input type="checkbox" name="includeMasihBerhutang" id="includeMasihBerhutangCheckbox" checked>
                        <label for="includeMasihBerhutang">Termasuk Pelajar Masih Berhutang</label>
                    </div>
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
                                        <th>Kelas</th>
                                        <th>Status Pembayaran</th>
                                        <th>Tarikh Transaksi</th>
                                        <th>Jumlah Bayaran (RM)</th>
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
                            <label> Tahun </label>
                            <select name="class_export" id="class_export" class="form-control">
                                <option value="0" disabled selected>Pilih Kelas</option>
                            </select>
                        </div>
                        <div id="yuran" class="form-group">
                            <label> Tahun </label>
                            <select name="fee_year" id="fee_year_export" class="form-control">
                                <option value="0" disabled selected>Pilih Tahun</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Nama Yuran</label>
                            <select name="yuranExport" id="yuranExport" class="form-control">

                            </select>
                        </div>
                        <div class="form-group mx-2">
                            <input type="checkbox" name="includeMasihBerhutang" checked>
                            <label for="includeMasihBerhutang">Termasuk Pelajar Masih Berhutang</label>
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

                <form action="{{ route('exportJumlahBayaranIbuBapa') }}" method="post" onsubmit="remindMessage()"
                    id="exportJumlahBayaranIbuBapaForm">
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
                            <select name="yuranExport1" id="yuranExport1" class="form-control" required>

                            </select>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-12 required">
                                <label class="control-label">Tempoh Transaksi</label>

                                <div class="input-daterange input-group" id="date">
                                    <input type="text" class="form-control" id="date_started" name="date_started"
                                        placeholder="Tarikh Awal" autocomplete="off"
                                        data-parsley-required-message="Sila masukkan tarikh awal"
                                        data-parsley-errors-container=".errorMessage" required />
                                    <input type="text" class="form-control" id="date_end" name="date_end"
                                        placeholder="Tarikh Akhir" autocomplete="off"
                                        data-parsley-required-message="Sila masukkan tarikh akhir"
                                        data-parsley-errors-container=".errorMessage" required />
                                </div>
                                <div class="errorMessage"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button id="buttonExport" type="submit" class="btn btn-primary">Export</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- modal export semua yuran --}}
    <div class="modal fade" id="modalExportSemuaYuran" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Export Semua Yuran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form action="{{ route('exportAllYuran') }}" method="post">
                    <div class="modal-body">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>Organisasi</label>
                            <select name="organExportAllYuran" id="organExportAllYuran" class="form-control">
                                <option value="" disabled selected>Pilih Organisasi</option>
                                @foreach($organization as $row)
                                    <option value="{{ $row->id }}">{{ $row->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="yuran" class="form-group">
                            <label> Tahun </label>
                            <select name="feeYearExportAllYuran" id="feeYearExportAllYuran" class="form-control">
                                <option value="0" disabled selected>Pilih Tahun</option>
                            </select>
                        </div>
                        <div class="form-group mx-2">
                            <input type="checkbox" name="includeMasihBerhutang" checked>
                            <label for="includeMasihBerhutang">Termasuk Pelajar Masih Berhutang</label>
                        </div>
                        <div class="modal-footer">
                            <button id="buttonExportSemua" type="submit" class="btn btn-primary">Export</button>
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
    <script src="{{ URL::asset('assets/libs/chartist/chartist.min.js')}}"></script>
    <script src="{{ URL::asset('assets/libs/parsleyjs/parsleyjs.min.js')}}"></script>
    <script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script>
    <script src="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}" defer></script>

    <script>
        $(document).ready(function () {

            function validateDateRange() {
                var startDate = $('#date_started').datepicker('getDate');
                var endDate = $('#date_end').datepicker('getDate');

                // Check if both dates are selected and validate the date range
                if (startDate && endDate) {
                    if (endDate < startDate) {
                        // Clear end date and display error message
                        $('#date_end').val('');
                        $('.errorMessage').text('Tarikh Akhir mesti selepas Tarikh Awal');
                    } else {
                        // Clear error message
                        $('.errorMessage').text('');
                    }
                }
            }

            $('#modalJumlahBayaran').on('shown.bs.modal', function () {

                $('#date .form-control').datepicker({
                    toggleActive: true,
                    todayHighlight: true,
                    format: 'yyyy-mm-dd',
                    autoclose: true,
                    container: '#modalJumlahBayaran .modal-body'
                });

                $('#date_started, #date_end').off('change').on('change', function () {
                    // Call validateDateRange function when either datepicker changes
                    validateDateRange();
                });
            });

            $('#organExport').change(function () {
                if ($(this).val() != 0) {
                    var organizationid = $("#organExport").val();
                    var classid = $('#class_export').val();
                    var _token = $('input[name="_token"]').val();
                    fetch_class_data(organizationid, '#class_export', '#fee_year_export', '#yuranExport');
                }
            });

            $('#class_export').change(function () {
                var organizationid = $("#organExport").val();
                var classid = $('#class_export').val();
                var _token = $('input[name="_token"]').val();

                fetch_yuran_data(organizationid, classid, '#fee_year_export', '#yuranExport');
            });

            $('#fee_year_export').change(function () {
                $('#class_export').trigger('change');
            })

            $('#organExportAllYuran').change(function () {
                var organizationid = $("#organExportAllYuran").val();
                var _token = $('input[name="_token"]').val();
                fetch_class_data(organizationid, '', '#feeYearExportAllYuran', '');
            });

            $('#organExport1').change(function () {
                var organizationid = $("#organExport1").val();
                var _token = $('input[name="_token"]').val();
                fetch_class_data(organizationid, '#yuranExport1', "", "");
            });

            $("#organization").prop("selectedIndex", 0);

            function fetch_class_data(oid = '', classHtmlId = '', feeYearHtmlId, feeHtmlId) {
                var _token = $('input[name="_token"]').val();
                $.ajax({
                    url: "{{ route('fees.fetchClassForCateYuran') }}",
                    method: "POST",
                    data: {
                        oid: oid,
                        _token: _token
                    },
                    success: function (result) {
                        $(classHtmlId).empty();
                        $(classHtmlId).append("<option value=''> Semua Kelas</option>");
                        jQuery.each(result.success, function (key, value) {
                            $(classHtmlId).append("<option value='" + value.cid + "'>" + value.cname + "</option>");
                        });

                        $(feeYearHtmlId).empty();
                        jQuery.each(result.years, function (key, value) {
                            $(feeYearHtmlId).append("<option value='" + value.year + "'>Tahun " + value.year + "</option>");
                        });

                        fetch_yuran_data(oid, classHtmlId, feeYearHtmlId, feeHtmlId);
                    }
                })
            }

            function fetch_yuran_data(oid, classid, feeYearHtmlId, feeHtmlId) {
                var _token = $('input[name="_token"]').val();
                var fee_year = $(feeYearHtmlId).val();
                var feeSelect = $(feeHtmlId);

                $.ajax({
                    url: "{{ route('fees.fetchYuran') }}",
                    method: "POST",
                    data: {
                        classid: classid,
                        oid: oid,
                        _token: _token,
                        fee_year: fee_year
                    },
                    success: function (result) {
                        feeSelect.empty();
                        feeSelect.append("<option value='0'>Pilih Yuran</option>");

                        jQuery.each(result.success, function (key, value) {
                            feeSelect.append("<option value='" + value.id + "'>" + value.name + "</option>");
                        });
                    }
                })
            }

            function loadDatatable() {
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
                            feeYear: $('#fee_year').val(),
                            includeMasihBerhutang: $('#includeMasihBerhutangCheckbox').is(':checked') ? 1 : 0
                        }
                    },
                    'columnDefs': [{
                        "targets": [0, 1, 2, 3], // your case first column
                        "className": "text-center",
                        "width": "2%"
                    }],
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
                    },
                    {
                        data: "class_name",
                        name: "class_name",
                        "width": "10%"
                    },
                    {
                        data: "status",
                        name: "status",
                        "width": "10%"
                    },
                    {
                        data: "transaction_date",
                        name: "transaction_date",
                        width: "10%"
                    },
                    {
                        data: "amount",
                        name: "amount",
                        width: "10%"
                    }]
                });
            }

            $('#organization').change(function () {
                if ($(this).val() != '') {
                    fetch_class_data($(this).val(), '#classes', '#fee_year', '#fees');
                }
            });

            $('#classes').change(function () {
                var organizationid = $('#organization').val();
                fetch_yuran_data(organizationid, '#classes', '#fee_year', '#fees');
            });

            $('#fee_year').change(function () {
                $('#classes').trigger('change');
            });

            $('#fees').change(function () {
                if ($(this).val() != 0) {
                    loadDatatable();
                }
            });

            $('#includeMasihBerhutangCheckbox').on('change', function () {
                if ($("#fees").val() != 0 && $("#organization").val() != '') {
                    loadDatatable();
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