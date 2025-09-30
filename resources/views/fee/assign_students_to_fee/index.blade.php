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
                <h4 class="font-size-18">Ubah Pembayar Yuran</h4>
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
                </div>

                <div class="col-md-12">
                    <div class="card">
                        <div class="table-responsive">
                            <table id="yuranTable" class="table table-bordered table-striped dt-responsive wrap"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr style="text-align:center">
                                        <th>No</th>
                                        <th>Kategori Yuran</th>
                                        <th>Nama Yuran</th>
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
                        $(classId).append("<option value='0'> Semua Kelas</option>");
                        jQuery.each(result.success, function (key, value) {
                            $(classId).append("<option value='" + value.cid + "'>" + value.cname + "</option>");
                        });
                    }
                })
            }

            $('#organization').on("change", function () {
                var oid = $("#organization").val();
                var yuranTable = $('#yuranTable');

                // remove the initial data when the classes selection is being reselected
                yuranTable.DataTable().clear().destroy();

                // add data to the yuran table to display all students with respective exportAllYuranStatus
                yuranTable = yuranTable.DataTable({
                    ordering: true,
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('fees.fetchAllFeesDatatableByOrg') }}",
                        method: "GET",
                        data: {
                            oid: oid,
                        }
                    },
                    'columnDefs': [{
                        "targets": [0, 1, 2, 3, 4],
                        "className": "text-center",
                        "width": "2%"
                    }],
                    order: [
                        [1, 'asc']
                    ],
                    columns: [
                        {
                            // index number in table
                            "data": null,
                            searchable: false,
                            "sortable": false,
                            render: function (data, type, row, meta) {
                                return meta.row + meta.settings._iDisplayStart + 1;
                            }
                        },
                        {
                            data: "fee_category",
                            name: "fee_category",
                            "width": "10%"
                        },
                        {
                            data: "fee_name",
                            name: "fee_name",
                            "width": "20%"
                        },
                        {
                            data: "fee_status",
                            render: function (data, type, row) {
                                return "<p>" + (data == 1) ? "Active" : "Inactive" + "</p>"
                            },
                            "width": "10%"
                        },
                        {
                            data: "action",
                            name: "action",
                            "width": "15%"
                        }
                    ]
                })
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