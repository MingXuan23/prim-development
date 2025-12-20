@extends('layouts.master')

@section('css')
    <link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
    @include('layouts.datatable')
@endsection

@section('content')
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="page-title-box">
                <h4 class="font-size-18">Kategori - Yuran Berulang</h4>
                <!-- <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item active">Welcome to Veltrix Dashboard</li>
                                </ol> -->
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

                {{-- <div class="">
                    <button onclick="filter()" style="float: right" type="submit" class="btn btn-primary"><i
                            class="fa fa-search"></i>
                        Tapis</button>
                </div> --}}

            </div>
        </div>

        <div class="col-md-12">
            <div class="card">
                {{-- <div class="card-header">List Of Applications</div> --}}
                <div>
                    <a style="margin: 19px; float: right;" href="{{ route('fees.createRecurring') }}"
                        class="btn btn-primary"> <i class="fas fa-plus"></i> Tambah Butiran</a>
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
                        <table id="categoryRecurring" class="table table-bordered table-striped dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr style="text-align:center">
                                    <th> No. </th>
                                    <th>Nama Butiran</th>
                                    <th>Penerangan</th>
                                    <th>Jumlah Amaun (RM)</th>
                                    <th>Tarikh Mula</th>
                                    <th>Tarikh Akhir</th>
                                    <th>Rujukan</th>
                                    <th>Status</th>
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
                            <h4 class="modal-title">Padam Butiran</h4>
                        </div>
                        <div class="modal-body">
                            Adakah anda pasti?
                        </div>
                        <div class="modal-footer">
                            <button type="button" data-dismiss="modal" class="btn btn-primary" id="delete"
                                name="delete">Padam</button>
                            <button type="button" data-dismiss="modal" class="btn">Batal</button>
                        </div>
                    </div>
                </div>
            </div>
            {{-- end confirmation delete modal --}}

        {{-- confirmation close fee --}}
        <div id="closeConfirmationModal" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Tutup Yuran</h4>
                    </div>
                    <div class="modal-body">
                        Adakah anda pasti?
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-dismiss="modal" class="btn btn-primary" id="closeFee"
                            name="closeFee">Tutup</button>
                        <button type="button" data-dismiss="modal" class="btn">Batal</button>
                    </div>
                </div>
            </div>
        </div>
        {{-- end confirmation close fee --}}

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
        $(document).ready(function () {

            var categoryRecurring;

            if ($("#organization").val() != "") {
                $("#organization").prop("selectedIndex", 1).trigger('change');
                fetch_data($("#organization").val());
            }

            function fetch_data(oid = '') {
                categoryRecurring = $('#categoryRecurring').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('fees.getCategoryDatatable') }}",
                        data: {
                            oid: oid,
                            category: 'Recurring'
                        },
                        type: 'GET',

                    },
                    'columnDefs': [{
                        "targets": [0], // your case first column
                        "className": "text-center",
                        "width": "2%"
                    }, {
                        "targets": [3, 4, 5], // your case first column
                        "className": "text-center",
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
                        data: "name",
                        name: 'name'
                    }, {
                        data: "desc",
                        name: 'desc',
                        orderable: false,
                        searchable: false,
                    }, {
                        data: "totalAmount",
                        name: 'totalAmount',
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
                        data: "start_date",
                        name: 'start_date',
                        // orderable: false,
                        searchable: false,
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
                        data: "end_date",
                        name: 'end_date',
                        // orderable: false,
                        searchable: false,
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
                        data: "target",
                        name: 'target',
                        orderable: false,
                        searchable: false,
                        render: function (data, type, full) {
                            return data;
                        }
                    }, {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },{
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },],
                    error: function (error) {
                        alert('error');
                        alert(error.toString());
                    }
                });



                /* 
                    {
                        data: "target",
                        name: 'target',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full) {
                            return data;
                        }
                    }

                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                */
            }

            $('#organization').change(function () {
                var organizationid = $("#organization option:selected").val();
                $('#categoryRecurring').DataTable().destroy();
                // console.log(organizationid);
                fetch_data(organizationid);
            });

            // csrf token for ajax
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fee_id;

            $(document).on('click', '.btn-danger', function () {
                fee_id = $(this).attr('id');
                $('#deleteConfirmationModal').modal('show');
            });

            $(document).on('click', '.btn-info', function(){
                fee_id = $(this).attr('id');
                $('#closeConfirmationModal').modal('show');
            });

            $('#delete').click(function () {

                $.ajax({
                    type: 'POST',
                    dataType: 'html',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        _method: 'DELETE'
                    },
                    url: "/fees/" + fee_id,
                    success: function (data) {
                        setTimeout(function () {
                            $('#confirmModal').modal('hide');
                        }, 2000);

                        $('div.flash-message').html(data);

                        categoryRecurring.ajax.reload();
                    },
                    error: function (data) {
                        $('div.flash-message').html(data);
                    }
                })
            });

            $('#closeFee').click(function() {

            console.log(fee_id);
            $.ajax({
                type: 'POST',
                dataType: 'html',
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                url: "closeFee/" + fee_id,
                success: function(data) {
                    setTimeout(function() {
                        $('#confirmModal').modal('hide');
                    }, 2000);

                    $('div.flash-message').html(data);

                    categoryRecurring.ajax.reload();
                },
                error: function (data) {
                    $('div.flash-message').html(data);
                }
            })
        });

            $('.alert').delay(3000).fadeOut();

        });
    </script>
@endsection