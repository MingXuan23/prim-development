@extends('layouts.master')

@section('css')
        <!-- DataTables -->
        <link href="{{ URL::asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ URL::asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />

        <!-- Responsive datatable examples -->
        <link href="{{ URL::asset('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Senarai Penderma untuk {{ $donation->nama }}</h4>
            <!-- <ol class="breadcrumb mb-0">
                  <li class="breadcrumb-item active">Welcome to Veltrix Dashboard</li>
              </ol> -->
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


        </div>
    </div>

    <div class="col-md-12">
        <div class="card">

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

                <input hidden type="text" id="don" name="don" class="form-control" value="{{ $donation->id }}">

                <div class="table-responsive">
                    <table id="donorTable" class="table table-bordered table-striped dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr style="text-align:center">
                                <th> No. </th>
                                <th> Nama Penderma </th>
                                <th> No Transaksi </th>
                                <th> Email </th>
                                <th> No Telefon </th>
                                <th> Tarikh Derma </th>
                                <th> Amaun (RM) </th>
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
<!-- Required datatable js -->
<script src="{{ URL::asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }} "></script>
<script src="{{ URL::asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }} "></script>

<!-- Buttons examples -->
<script src="{{ URL::asset('assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js') }} "></script>
<script src="{{ URL::asset('assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js') }} "></script>
<script src="{{ URL::asset('assets/libs/jszip/jszip.min.js') }} "></script>
<script src="{{ URL::asset('assets/libs/pdfmake/pdfmake.min.js') }} "></script>
<script src="{{ URL::asset('assets/libs/pdfmake/vfs_fonts.js') }} "></script>
<script src="{{ URL::asset('assets/libs/datatables.net-buttons/js/buttons.html5.min.js') }} "></script>
<script src="{{ URL::asset('assets/libs/datatables.net-buttons/js/buttons.print.min.js') }} "></script>
<script src="{{ URL::asset('assets/libs/datatables.net-buttons/js/buttons.colVis.min.js') }} "></script>

<!-- Responsive examples -->
<script src="{{ URL::asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }} "></script>
<script src="{{ URL::asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }} "></script>

<script>
   $(document).ready(function() {

        let donateid = $('#don').val();

        let donor_table = $('#donorTable').DataTable({
            // processing: true,
            // serverSide: true,
            lengthChange: false,
            dom: 'Bfrtip',
            buttons:  [ { extend: 'excel', text: 'Excel', exportOptions: { modifier: {  page:   'all', }}},
                        { extend: 'pdf', text: 'PDF', exportOptions: { modifier: { page:   'all', }}} ],
            ajax: {
                url: "{{ route('donate.donor_datatable') }}",
                data: {
                    id: donateid,
                },
                type: 'GET',

            },
            'columnDefs': [{
                "targets": [2, 3, 4, 5, 6],
                "className": "text-center",
                "width": "2%"
            },{
                "targets": [0],
                "className": "text-center",
                "width": "2%"
            },
            {
                "targets": [1],
                "width": "2%"
            }],
            columns: [{
                "data": null,
                searchable: false,
                "sortable": false,
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            }, {
                data: "username",
                name: 'username'
            },
            {
                data: "description",
                name: 'description'
            }, {
                data: "email",
                name: 'email',
                orderable: false
            }, {
                data: "telno",
                name: 'telno',
                orderable: false
            }, {
                data: "datetime_created",
                name: 'datetime_created',
                // orderable: false,
                // searchable: false
            }, {
                data: 'amount',
                name: 'amount',
            }, ]
        });

    });
</script>
@endsection