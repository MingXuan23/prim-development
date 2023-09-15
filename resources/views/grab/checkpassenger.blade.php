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
            <h4 class="font-size-18">Semak Tempahan Grab Anda :</h4>
        </div>
    </div>
</div>
<table id="organizationTable" class="table table-bordered table-striped dt-responsive wrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
    <thead>
            <tr>
                <th>Passenger Name</th>
                <th>Car Brand</th>
                <th>Destination Name</th>
                <th>Book Date </th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
            <tr>      
            <td>
                    @if($index === 0)
                        Newly Booked - {{ $item->name }}
                    @else
                        {{ $item->name }}
                    @endif
                </td>
                      <td>{{ $item->car_brand }} - {{ $item->car_name }}</td>
                      <td>{{ $item->destination_name}}</td>
                      <td>{{ $item->book_date}}</td>
                      <td>
                    @if($index === 0)
                    <button>Cancel Booking</button>
                    @else
                   Status
                    @endif
                </td>
                </tr>
            @endforeach
        </tbody>
</table>

@endsection