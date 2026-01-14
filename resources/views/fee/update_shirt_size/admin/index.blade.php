@extends('layouts.master')

@include('layouts.datatable')
@section('css')
    <link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ URL::asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />

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
                <h4 class="font-size-18">Kemaskini Saiz Baju</h4>
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
                        <select name="organization" id="organization" class="form-control" readonly>
                            <!-- <option value="" disabled>Pilih Organisasi</option> -->
                            @foreach($organizations as $row)
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
                                        <th>Nama Yuran</th>
                                        <th>Kuantiti</th>
                                        <th>Harga (RM)</th>
                                        <th>Jumlah (RM)</th>
                                        <th>Nama Penjaga</th>
                                        <th>Nama Pelajar</th>
                                        <th>Saiz Baju</th>
                                        <th>Nota Kepada Sekolah</th>
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
    <script src="{{ URL::asset('assets/jszip/3.10.1/jszip.min.js')}}"></script>
    <script src="{{ URL::asset('assets/libs/jszip/jszip.min.js') }} "></script>
    <script src="{{ URL::asset('assets/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{ URL::asset('assets/js/pages/buttons.html5.min.js')}}"></script>

    <script>
        $(document).ready(function () {


            // $('#organization').change(function () {
            // if ($(this).val() != '') {
            var yuranTable = $('#yuranTable');

            // remove the initial data when the classes selection is being reselected
            yuranTable.DataTable().clear().destroy();

            yuranTable = yuranTable.DataTable({
                ordering: true,
                processing: true,
                serverSide: false,
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: 'Export',
                        className: 'btn btn-success mb-2',
                        title: 'Pengemaskinian Saiz Baju',
                        exportOptions: {
                            columns: ':not(:first-child)',  // exclude the indexing
                            modifier: {
                                page: 'all'
                            }
                        }
                    }
                ],
                ajax: {
                    url: "{{ route('fees.updateShirtSize.admin.getShirtSizeResponsesDatatable') }}",
                    method: "GET",
                    data: {
                        oid: $('#organization').val()
                    }
                },
                'columnDefs': [{
                    "targets": [0, 1, 2, 3, 4, 5, 6, 7],
                    "className": "text-center"
                }],
                order: [
                    [0, 'asc']
                ],
                columns: [
                    {
                        "data": null,
                        searchable: false,
                        "sortable": false,
                        render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        },
                        "width": "2%"
                    },
                    {
                        data: "fee_name",
                        name: "fee_name",
                        "width": "20%"
                    },
                    {
                        data: "quantity",
                        name: "quantity",
                        "width": "3%"
                    },
                    {
                        data: "price",
                        name: "price",
                        "width": "3%"
                    },
                    {
                        data: "total_amount",
                        name: "total_amount",
                        "width": "3%"
                    },
                    {
                        data: "penjaga_name",
                        name: "penjaga_name",
                        "width": "20%"
                    },
                    {
                        data: "student_name",
                        name: "student_name",
                        "width": "20%"
                    },
                    {
                        data: "shirt_size",
                        name: "shirt_size",
                        "width": "3%"
                    },
                    {
                        data: "notes_to_school",
                        name: "notes_to_school",
                        "width": "20%"
                    }
                ]
            });
            // }
            // });

            // $('#organization').on('change', function () {
            //     // remove the initial data when the organization selection is being reselected
            //     $('#yuranTable').DataTable().clear().destroy();
            // });

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