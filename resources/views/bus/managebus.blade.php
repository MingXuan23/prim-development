@extends('layouts.master')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
@include('layouts.datatable');

@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Urus Data Bas</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
            <div class="flash-message"></div>
                <div class="table-responsive">
                <table id="bookgrab" class="table table-bordered table-striped dt-responsive wrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                @if(Session::has('success'))
                    <div class="alert alert-success">{{Session::get('success')}}</div>
                    @endif
                    @if(Session::has('fail'))
                    <div class="alert alert-danger">{{Session::get('fail')}}</div>
                    @endif
        <thead>
            <tr>
                <th>Bus ID</th>
                <th>Trip Number</th>
                <th>Bus Registration Number</th>
                <th>Departure Date</th>
                <th>Bus Depart From</th>
                <th>Bus Destination</th>
                <th>Status</th>
                <th>Manage Bus</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
                <tr>
                    <form action="/managebus-bus/{{ $item->id }}" method="POST">
                    @csrf
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->trip_number }}</td>
                    <td>{{ $item->bus_registration_number}}</td>
                    <td>{{ $item->departure_date }}</td>
                    <td>{{ $item->bus_depart_from }}</td>
                    <td>{{ $item->bus_destination }}</td>
                    <td>{{ $item->status }}</td>
                    <td> <button type="submit" class="btn btn-primary">Manage and Update Bus</button></td>
                    </form>
                </tr>
            @endforeach
        </tbody>
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
    
        $('#bookgrab').DataTable();
});

</script>

@endsection