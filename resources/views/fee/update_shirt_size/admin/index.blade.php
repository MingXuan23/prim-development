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
                        <select name="organization" id="organization" class="form-control" disabled>
                            <!-- <option value="" disabled>Pilih Organisasi</option> -->
                            @foreach($organizations as $row)
                                <option value="{{ $row->id }}">{{ $row->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <a style="margin: 1px;" href="#" class="btn btn-success" data-toggle="modal" data-target="#exportModal"
                            id="exportModalBtn"> <i class="fas fa-plus"></i> Export</a>
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
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
                    aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Export Pengemaskinian Saiz Baju</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            
                            <form action="{{ route('fees.updateShirtSize.admin.exportFormResponses') }}" method="post">
                                <div class="modal-body">
                                    {{ csrf_field() }}
                                    <div class="form-group" id="organization">
                                        <label>Organisasi</label>
                                        <select name="organExport" id="organExport" class="form-control" disabled>
                                            <!-- <option value="" disabled selected>Pilih organisasi</option> -->
                                            @foreach($organizations as $row)
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


            // $('#organization').change(function () {
            // if ($(this).val() != '') {
            var yuranTable = $('#yuranTable');

            // remove the initial data when the classes selection is being reselected
            yuranTable.DataTable().clear().destroy();

            // add data to the yuran table to display all students with respective exportAllYuranStatus
            yuranTable = yuranTable.DataTable({
                ordering: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('fees.updateShirtSize.admin.getShirtSizeResponsesDatatable') }}",
                    method: "GET"
                },
                'columnDefs': [{
                    "targets": [0, 1, 2, 3, 4, 5, 6, 7],
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
                    }
                ]
            })
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