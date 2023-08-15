@extends('layouts.master')

@section('css')

@include('layouts.datatable');

@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Homestay</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div>
                <a style="margin: 19px; float: right;" href=""
                    class="btn btn-primary"> <i class="fas fa-plus"></i> Tambah Homestay</a>
            </div>

            <div class="card-body">

                @if(count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                        <li id="failed">{{$error}}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                @if(\Session::has('success'))
                <div class="alert alert-success">
                    <p id="success">{{ \Session::get('success') }}</p>
                </div>
                @endif

                <div class="flash-message"></div>
                <div class="table-responsive">
                    <table id="homestaytable" class="table table-bordered table-striped dt-responsive wrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr style="text-align:center">
                                <th>No</th>
                                <th>Nama Homestay</th>
                                <th>Lokasi</th>
                                <th>No Telefon</th>
                                <th>Status</th>
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
<script src="{{ URL::asset('assets/libs/peity/peity.min.js')}}"></script>

{{-- <script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script> --}}

<script>
    $(document).ready(function() {
    var homestaytable = $('#homestaytable').DataTable({
        ordering: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('homestay.getHomestayDatatable') }}",
            type: 'GET',
            success: function(data) {
                // Log the received data to the console
                console.log('Received data:', data);
            }
        },
        order: [
            [1, 'asc']
        ],
        columns: [
            {
                data: null,
                searchable: false,
                sortable: false,
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                data: "name", // Column name "name" from the database
                name: "name", // Column name "name" from the database
                width: "20%"
            },
            {
                data: "location", // Column name "location" from the database
                name: "location", // Column name "location" from the database
                width: "30%"
            },
            {
                data: "pno", // Column name "pno" from the database
                name: "pno", // Column name "pno" from the database
                width: "10%"
            },
            {
                data: "status", // Column name "status" from the database
                name: "status", // Column name "status" from the database
                width: "10%"
            }
        ]
    });

    // csrf token for ajax
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    console.log("Homestay Table:", homestaytable); // Check the DataTable instance
});

</script>
@endsection