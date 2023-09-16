@extends('layouts.master')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
@include('layouts.datatable');

@endsection

@section('content')
@foreach ($data as $item)
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Grab Anda ke {{ $item->destination_name }} :</h4>
        </div>
    </div>
</div>

@if ($item->status == 'TRIP CONFIRM')
<form  method="post" action="/passengerpay-grab/{{ $item->id }}"> 
    @if(Session::has('success'))
        <div class="alert alert-success">{{Session::get('success')}}</div>
    @endif
    @if(Session::has('fail'))
        <div class="alert alert-danger">{{Session::get('fail')}}</div>
    @endif
    @csrf
    <div class="table-responsive">
    <table id="bookgrab" class="table table-bordered table-striped dt-responsive wrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
    @foreach ($datadestinationid as $itemdes)
    <tr>
    <th>Destination ID :</th>
    <td><input type="text" class="form-control" aria-describedby="emailHelp" value="{{ $itemdes->id }}" readonly name="iddestination"></td>
    </tr>
    @endforeach 
    <tr>
    <th hidden>Passenger ID :</th>
    <td  hidden><input type="text" class="form-control" aria-describedby="emailHelp" value="{{ $userId }}" readonly name="idpassenger"></td>
    </tr>
    <tr>
    <th>Car Brand :</th>
    <td>{{ $item->car_brand }}</td>
    </tr>
    <tr>
    <th>Car Name :</th>
    <td>{{ $item->car_name }}</td>
    </tr>
    <tr>
    <th>Pick Up Point :</th>
    <td>{{ $item->pick_up_point }}</td>
    </tr>
    <tr>
    <th>Seat :</th>
    <td>{{ $item->number_of_seat }} Seater</td>
    </tr>
    <tr>
    <th>Time :</th>
    <td>{{ $item->available_time }}</td>
    </tr>
    <tr>
    <th>Price Destination :</th>
    <td>RM {{ $item->price_destination }}</td>
    </tr>
    </table>
</div>
    <br>
    <button class="btn btn-success">Make Payment</button>
    <button class="btn btn-danger"><a href="/passenger-grab" style="text-decoration: none; color: white;">Cancel</a></button>
</form>

@elseif ($item->status == 'NOT CONFIRM')
<form  method="post" action="/passengernotify-grab/{{ $item->id }}"> 
    @if(Session::has('success'))
        <div class="alert alert-success">{{Session::get('success')}}</div>
    @endif
    @if(Session::has('fail'))
        <div class="alert alert-danger">{{Session::get('fail')}}</div>
    @endif
    @csrf
    <div class="table-responsive">
    <table id="bookgrab" class="table table-bordered table-striped dt-responsive wrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
    @foreach ($datadestinationid as $itemdes)
    <tr>
    <th>Destination ID :</th>
    <td><input type="text" class="form-control" aria-describedby="emailHelp" value="{{ $itemdes->id }}" readonly name="iddestination"></td>
    </tr>
    @endforeach 
    <tr>
    <th hidden>Passenger ID :</th>
    <td  hidden><input type="text" class="form-control" aria-describedby="emailHelp" value="{{ $userId }}" readonly name="idpassenger"></td>
    </tr>
    <tr>
    <th>Car Brand :</th>
    <td>{{ $item->car_brand }}</td>
    </tr>
    <tr>
    <th>Car Name :</th>
    <td>{{ $item->car_name }}</td>
    </tr>
    <tr>
    <th>Pick Up Point :</th>
    <td>{{ $item->pick_up_point }}</td>
    </tr>
    <tr>
    <th>Seat :</th>
    <td>{{ $item->number_of_seat }} Seater</td>
    </tr>
    <tr>
    <th>Time :</th>
    <td>{{ $item->available_time }}</td>
    </tr>
    <tr>
    <th>Price Destination :</th>
    <td>RM {{ $item->price_destination }}</td>
    </tr>
    </table>
</div>
    <br>
    <button class="btn btn-warning">Add to Book List</button>
    <button class="btn btn-danger"><a href="/passenger-grab" style="text-decoration: none; color: white;">Cancel</a></button>
</form>
@endif
@endforeach 


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