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
            <h4 class="font-size-18">Tempah Perkhidmatan Bas :</h4>
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
                <form action="{{ route('book.bus') }}" method="get">
                @csrf
                <label for="pick_up_point">Select Pickup Point:</label>
                <select aria-label="Default select example"  class="form-select" name="departfrom" id="departfrom">
                <option value="">Select a Pickup Point</option>
                    @foreach($uniquePickupPoints as $pickupPoint)
                        <option value="{{ $pickupPoint }}" {{ $selectedPickupPoint == $pickupPoint ? 'selected' : '' }}>
                            {{ $pickupPoint }}
                        </option>
                    @endforeach
                </select><br>

                <label for="availabledestination">Select Destination:</label>
                <select aria-label="Default select example"  class="form-select" name="destination" id="destination">
            <option value="">Select a Destination</option>
            @foreach($uniqueDestinations as $destination)
                <option value="{{ $destination }}" {{ $selectedDestination == $destination ? 'selected' : '' }}>
                    {{ $destination }}
                </option>
            @endforeach
        </select>
        <br>
        <button type="submit" class="btn btn-success" name="button1" value="value1">Show Matched Data</button>
        <button type="submit" class="btn btn-primary" name="button2" value="value2">Show All Available Bus</button>
    </form>
                </form>
                </div> <br><br>
                @if ($matchedData)
                <h4 class="font-size-18">Pilih Grab Student :</h4><br>
                <div class="table-responsive">
                <table id="bookgrab" class="table table-bordered table-striped dt-responsive wrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                <thead>
                <tr>
                    <th>Bus Depart From</th>   
                    <th>Bus Destination</th>
                    <th>Departure Time</th>
                    <th>Departure Date</th>
                    <th>Available Seat</th>
                    <th>Price Per Seat</th>
                    <th>Status</th>
                    <th>Choose Bus</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($matchedData as $item)
                <tr>                
                    <form action="passengerselect-bus/{{ $item->id }}" method="POST">
                    @csrf
                    <td>{{ $item->bus_depart_from}}</td>
                    <td>{{ $item->bus_destination}}</td>
                    <td>{{ $item->departure_time}}</td>
                    <td>{{ $item->departure_date}}</td>
                    <td>{{ $item->available_seat}}</td>
                    <td>RM {{ $item->price_per_seat}}</td>
                    <td>{{ $item->status}}</td>
                    <td> <button type="submit" class="btn btn-success">Select Bus</button></td>
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