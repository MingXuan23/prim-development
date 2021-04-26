@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@include('layouts.datatable')

@endsection

@section('content')

<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Senarai Penderma untuk {{ $listdonor->dname }}</h4>
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

                <input hidden type="text" id="don" name="don" class="form-control" value="{{ $listdonor->id }}">

                <div class="table-responsive">
                    <table id="donorTable" class="table table-bordered table-striped dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr style="text-align:center">
                                <th> No. </th>
                                <th> Nama Penderma </th>
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
<!-- Peity chart-->
<script src="{{ URL::asset('assets/libs/peity/peity.min.js')}}"></script>

<!-- Plugin Js-->
<script src="{{ URL::asset('assets/libs/chartist/chartist.min.js')}}"></script>

<script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script>

<script>
    $(document).ready(function() {

        var donateid = $('#don').val();

        $('#donorTable').DataTable({
                  processing: true,
                  serverSide: true,
                  ajax: {
                      url: "{{ route('donate.donorlist') }}",
                      data: {
                          did: donateid,
                      },
                      type: 'GET',

                  },
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
                      data: "username",
                      name: 'username'
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
                      orderable: false,
                      searchable: false
                  }, {
                      data: 'amount',
                      name: 'amount',
                      orderable: false,
                      searchable: false
                  }, ]
          });
        

});
</script>
@endsection