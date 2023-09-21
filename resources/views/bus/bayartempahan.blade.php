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
            <h4 class="font-size-18">Bayar Tempahan Bas Anda</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
            <div class="flash-message"></div>
                <div class="table-responsive">
                @if(Session::has('success'))
                <div class="alert alert-success">{{Session::get('success')}}</div>
                @endif
                @if(Session::has('fail'))
                <div class="alert alert-danger">{{Session::get('fail')}}</div>
                @endif
                <br><br>
                <table id="managecar" class="table table-bordered table-striped dt-responsive wrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                        <tr>
                            <th>Berlepas Dari</th>
                            <th>Destinasi</th>
                            <th>Nombor Trip Bas </th>
                            <th>Waktu Berlepas</th>
                            <th>Tarikh Berlepas</th>
                            <th>Masa Notify Tempahan</th>
                            <th>Harga Trip Bas</th>
                            <th>Bayar Tempahan</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($list as $item)
                        <tr>                
                            <form action="passengerpilihtempahan-bus/{{ $item->id }}" method="POST">
                            @csrf
                            <td>{{ $item->bus_depart_from }}</td>
                            <td>{{ $item->bus_destination}}</td>
                            <td>{{ $item->trip_number}}</td>
                            <td>{{ $item->departure_time}}</td>
                            <td>{{ $item->departure_date}}</td>
                            <td>{{ $item->time_notify}}</td>
                            <td>RM {{ $item->price_per_seat}}</td>
                            <td> <button type="submit" class="btn btn-success">Bayar Tempahan</button></td>
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
    
        $('#managecar').DataTable();
});

</script>

@endsection