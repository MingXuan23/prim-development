@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css') }}" rel="stylesheet" type="text/css" />
@include('layouts.datatable')
<style>
    .modal-dialog {
        top: 15%;
    }

    .scrollheight {
        height: 400px;
    }

    @media (max-width: 576px) {
        .modal-dialog {
            max-width: none !important;
            width: 90% !important;
        }
    }
</style>
@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Yuran</h4>
            <!-- <ol class="breadcrumb mb-0">
                                                                                                                                    <li class="breadcrumb-item active">Welcome to Veltrix Dashboard</li>
                                                                                                                                </ol> -->
        </div>
    </div>
</div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card mb-2">
            <div class="card-body pb-1" style="background-color: #e6e6e6;border: 1px solid #dfdfdf;">
                <p class="text-muted mb-1">
                    Langkah-langkah menambahkan yuran :
                </p>
                <ul class="text-muted">
                    <li>Tambah kategori yuran baru dengan klik menu kategori.</li>
                    <li>Tambah yuran dengan klik butang tambah yuran.</li>
                    <li>Butang butiran untuk maklumat yuran.</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="card card-primary mb-2">

            {{ csrf_field() }}
            <div class="card-body">

                <div class="form-group">
                    <label>Nama Organisasi</label>
                    <select name="organization" id="organization" class="form-control">
                        <option value="" selected disabled>Pilih Organisasi</option>
                        @foreach ($organization as $row)
                        <option value="{{ $row->id }}">{{ $row->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

        </div>
    </div>

    <div class="col-md-12">
        <div class="card">
            <div>

                <a style="margin: 19px; float: right;" class="btn btn-primary" href="{{ route('fees.create' ) }}">
                    <i class="fas fa-plus"></i>
                    Tambah Yuran
                </a>
            </div>

            <div class="card-body">

                @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                @if (\Session::has('success'))
                <div class="alert alert-success">
                    <p>{{ \Session::get('success') }}</p>
                </div>
                @endif

                <div class="table-responsive">
                    <table id="feesTable" class="table table-bordered table-striped dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr style="text-align:center">
                                <th> No. </th>
                                <th>Nama Yuran</th>
                                <th>Jumlah Amaun (RM)</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection


@section('script')
<!-- Peity chart-->
<script src="{{ URL::asset('assets/libs/peity/peity.min.js') }}"></script>

<!-- Plugin Js-->
<script src="{{ URL::asset('assets/libs/chartist/chartist.min.js') }}"></script>

<script src="{{ URL::asset('assets/js/pages/dashboard.init.js') }}"></script>


<script>
    $(document).ready(function() {

            var feesTable;

            // fetch_data();
            if($("#organization").val() != ""){
                $("#organization").prop("selectedIndex", 1).trigger('change');
                fetch_data($("#organization").val());
            }

            function fetch_data(oid = '') {
                feesTable = $('#feesTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('fees.getFeesDatatable') }}",
                        data: {
                            oid: oid,
                        },
                        type: 'GET',

                    },
                    'columnDefs': [{
                        "targets": [0], // your case first column
                        "className": "text-center",
                        "width": "2%"
                    }, {
                        "targets": [1], // your case first column
                        "className": "text-center",
                    }, {
                        "targets": [2], // your case first column
                        "className": "text-center",
                        "width": "30%"
                    }],
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
                    }, {
                        data: "feename",
                        name: 'feename'
                    }, {
                        data: "totalamount",
                        name: 'totalamount',
                        orderable: false,
                        searchable: false,
                        defaultContent: 0,
                        render: function(data, type, full) {
                            if(data){
                                return parseFloat(data).toFixed(2);
                            }else{
                                return 0;
                            }
                        }
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
                $('#feesTable').DataTable().destroy();
                console.log(organizationid);
                fetch_data(organizationid);
            });

            // csrf token for ajax
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var teacher_id;

            $(document).on('click', '.btn-danger', function() {
                teacher_id = $(this).attr('id');
                $('#deleteConfirmationModal').modal('show');
            });

            $('#delete').click(function() {
                $.ajax({
                    type: 'POST',
                    dataType: 'html',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        _method: 'DELETE'
                    },
                    url: "/teacher/" + teacher_id,
                    success: function(data) {
                        setTimeout(function() {
                            $('#confirmModal').modal('hide');
                        }, 2000);

                        $('div.flash-message').html(data);

                        feesTable.ajax.reload();
                    },
                    error: function(data) {
                        $('div.flash-message').html(data);
                    }
                })
            });

            $('.alert').delay(3000).fadeOut();

            

        });

</script>

@endsection