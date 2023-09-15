@extends('layouts.master')

@section('css')

@include('layouts.datatable');


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
            <h4 class="font-size-18">Tempah Grab Student Anda :</h4>
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
                <form action="{{ route('book.grab') }}" method="get">
                @csrf
                <select class="form-select" aria-label="Default select example"  name="availabledestination">
                <option selected disabled>Available Destination</option>
                @foreach($uniqueDestinations as $destination) 
                <option value="{{ $destination }}" {{ $selectedDestination == $destination ? 'selected' : '' }}>{{ $destination  }}</option>
                @endforeach
                </select>
                 <br>
                <button type="submit" class="btn btn-success">View Detail</button>
                </form>
                </div> <br><br>
                @if ($selectedData)
                <h4 class="font-size-18">Pilih Grab Student :</h4><br>
                <div class="table-responsive">
                <table id="organizationTable" class="table table-bordered table-striped dt-responsive wrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                <thead>
                <tr>
                    <th>Car Brand</th>   
                    <th>Number of Seat</th>
                    <th>Available Time</th>
                    <th>Pick Up Point</th>
                    <th>Destination</th>
                    <th>Price</th>
                    <th>Choose Grab</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($selectedData as $item)
                <tr>                
                    <form action="passengerselect-grab/{{ $item->id }}" method="POST">
                    @csrf
                    <td>{{ $item->car_brand }}</td>
                    <td>{{ $item->number_of_seat}}</td>
                    <td>{{ $item->available_time}}</td>
                    <td>{{ $item->pick_up_point}}</td>
                    <td>{{ $item->destination_name}}</td>
                    <td>RM {{ $item->price_destination}}</td>
                    <td> <button type="submit" class="btn btn-success">Select Grab</button></td>
                    </form>
                </tr>
                @endforeach
                </tbody>
                @endif
                </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection