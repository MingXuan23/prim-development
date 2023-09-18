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

<form method="post" action="/passengerbayartempahan-grab/{{ $item->grabid }}"> 
    @if(Session::has('success'))
        <div class="alert alert-success">{{Session::get('success')}}</div>
    @endif
    @if(Session::has('fail'))
        <div class="alert alert-danger">{{Session::get('fail')}}</div>
    @endif
    @csrf
    <div class="table-responsive">
    <table id="bookgrab" class="table table-bordered table-striped dt-responsive wrap" style="border-collapse: collapse; border-spacing: 0; width: 80%;">
    <tr>
    <th>Destination ID :</th>
    <td><input type="text" class="form-control" aria-describedby="emailHelp" value="{{ $item->desid }}" readonly name="iddestination"></td>
    </tr>
    <tr>
    <th hidden>Passenger ID :</th>
    <td  hidden><input type="text" class="form-control" aria-describedby="emailHelp" value="{{ $userId }}" readonly name="idpassenger"></td>
    </tr>
    <tr>
    <th hidden>Notify ID :</th>
    <td hidden><input type="text" class="form-control" aria-describedby="emailHelp" value="{{ $item->notifyid }}" readonly name="notify"></td>
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
    <button type="submit" class="btn btn-success">Make Payment</button>
    <button class="btn btn-danger"><a href="/passenger-grab" style="text-decoration: none; color: white;">Cancel</a></button>
</form>
@endforeach 


@endsection
