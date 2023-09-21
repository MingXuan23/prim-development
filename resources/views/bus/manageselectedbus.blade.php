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
            <h4 class="font-size-18">Ubah dan Urus Data Bas ( {{ $databus->bus_registration_number }} ) </h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
            <div class="flash-message"></div>
                <div class="table-responsive">
                    <form action="/updatemanagebus-bus/{{ $databus->id }}" method="post">
                    @if(Session::has('success'))
                    <div class="alert alert-success">{{Session::get('success')}}</div>
                    @endif
                    @if(Session::has('fail'))
                    <div class="alert alert-danger">{{Session::get('fail')}}</div>
                    @endif
                    @csrf
                    <div class="form-group">
                    <label>Bus Trip Number</label>
                    <input type="text" class="form-control" aria-describedby="emailHelp" placeholder="Bus Trip Number" name="bustripnumber"  value="{{ $databus->trip_number }}">
                    </div><br>
                    <div class="form-group">
                    <label>Minimum Seat before Pay</label>
                    <input type="number" class="form-control" aria-describedby="emailHelp" placeholder="Minimum Seat" name="minimumseat"  value="{{ $databus->minimum_seat }}">
                    </div><br>
                    <div class="form-group">
                    <label>Trip Description</label>
                    <input type="text" class="form-control" aria-describedby="emailHelp" placeholder="Trip Description" name="tripdesc"  value="{{ $databus->trip_description }}">
                    </div><br>
                    <div class="form-group">
                    <label>Bus Depart From</label>
                    <input type="text" class="form-control" aria-describedby="emailHelp" placeholder="Depart From" name="busdepart" value="{{ $databus->bus_depart_from }}">
                    </div><br>
                    <div class="form-group">
                    <label>Bus Destination</label>
                    <input type="text" class="form-control" aria-describedby="emailHelp" placeholder="Bus Destination" name="busdestination" value="{{ $databus->bus_destination }}">
                    </div><br>
                    <div class="form-group">
                    <label>Departure Time</label>
                    <input type="time" class="form-control" placeholder="Departure Time"  name="time" value="{{ $databus->departure_time }}" >
                    </div><br>
                    <div class="form-group">
                    <label>Estimation Arrival Time</label>
                    <input type="time" class="form-control" placeholder="Estimate Arrival Time"  name="estimatetime" value="{{ $databus->estimate_arrive_time }}" >
                    </div><br>
                    <div class="form-group">
                    <label>Departure Date</label>
                    <input type="date" class="form-control" placeholder="Departure Date"  id="date" name="date" value="{{ $databus->departure_date }}">
                    </div><br>
                    <div class="form-group">
                    <label>Price Per Seat (RM)</label>
                    <input type="number" class="form-control" aria-describedby="emailHelp" placeholder="Price Per Seat" name="priceperseat" value="{{ $databus->price_per_seat }}"  min="0">
                    </div><br>
                    <div class="form-group">
                    <label>Status</label>
                    <select class="form-select" aria-label="Default select example"  name="status">         
                    <option hidden value="{{ $databus->status }}">{{ $databus->status }}</option>
                    <option value="AVAILABLE">AVAILABLE</option>
                    <option value="NOT CONFIRM">NOT CONFIRM</option>
                    <option value="NOT AVAILABLE">NOT AVAILABLE</option>
                    </select>
                    </div><br>
                    <div class="form-group mb-0">
                    <div class="text-right">
                    <a type="button" href="{{ url()->previous() }}"
                    class="btn btn-secondary waves-effect waves-light mr-1">
                     Kembali
                    </a>
                    <button type="submit" class="btn btn-primary waves-effect waves-light mr-1">
                     Simpan
                    </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
   // Get the input element by its ID
   const dateInput = document.getElementById('date');

// Get the current date in the format "YYYY-MM-DD"
const currentDate = new Date();

// Add 1 day to the current date
currentDate.setDate(currentDate.getDate() + 1);

// Format the current date as "YYYY-MM-DD"
const minDate = currentDate.toISOString().split('T')[0];

// Set the minimum date for the input element to the current date + 1 day
dateInput.min = minDate;

// Add an event listener to the input element to check for changes
dateInput.addEventListener('change', function() {
  // Get the selected date from the input
  const selectedDate = new Date(dateInput.value);

  // If the selected date is before the current date + 1 day, clear the input value
  if (selectedDate < currentDate) {
    alert('Please select a date at least 1 day ahead.');
    dateInput.value = '';
  }
});
</script>

@endsection