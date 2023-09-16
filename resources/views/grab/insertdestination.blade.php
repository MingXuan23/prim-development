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
            <h4 class="font-size-18">Destinasi Sedia Ada</h4>
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
                <table id="insertdestination" class="table table-bordered table-striped dt-responsive wrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                        <tr>
                        <th>Car Brand</th>
                        <th>Car Name</th>
                        <th>Car Registration Number</th>
                        <th>Status</th>
                        <th>Pick Up Point</th>
                        <th>Destination Name</th>
                        <th>Destination Price</th>
                        <th>Destination Status</th>
                        <th>Available Time</th>
                        <th>Manage Destination</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list as $item)
                        <tr>
                        <form action="/updaterow-destinationgrab/{{ $item->id }}" method="POST">
                            @csrf
                        <td>{{ $item->car_brand }}</td>
                        <td>{{ $item->car_name }}</td>
                        <td>{{ $item->car_registration_num}}</td>
                        
                        <td>{{ $item->grab_status }}</td>
                        <td>{{ $item->pick_up_point }}</td>
                        <td>{{ $item->destination_name }}</td>
                        <td>RM {{ $item->price_destination }}</td>
                        <td>
                        <select class="form-select" aria-label="Default select example"  name="status">         
                            <option hidden value="{{ $item->destination_status }}">{{ $item->destination_status }}</option>
                            <option value="TRIP CONFIRM">TRIP CONFIRM</option>
                            <option value="NOT CONFIRM">NOT CONFIRM</option>
                            </select>
                        </td>
                        <td>{{ $item->available_time }}</td>
                        <td> <button type="submit" class="btn btn-primary">Update Destination</button></td>
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
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Daftar Destinasi Baharu</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
            <div class="flash-message"></div>
                <form action="{{route('grab.insertdestination')}}" method="post">
                @csrf
                <div class="form-group">
                <label>Destination You Offer</label>
                <input type="text" class="form-control" aria-describedby="emailHelp" placeholder="Destination" name="destination">
                </div><br>
                <div class="form-group">
                <label>Pick Up Point</label>
                <input type="text" class="form-control" aria-describedby="emailHelp" placeholder="Pick Up Point" name="pickup">
                </div><br>
                <div class="form-group">
                <label>Price for the Destination</label>
                <input type="number" class="form-control" aria-describedby="emailHelp" placeholder="Price For Offered Destination" name="price">
                <input type="text" hidden class="form-control" aria-describedby="emailHelp" value="NEW" placeholder="Price For Offered Destination" name="status">
                </div><br>
                <div class="form-group">
                <label>Time for Pick Up</label>
                <input type="time" name="time" class="form-control" aria-describedby="emailHelp" placeholder="Time for Pickup" name="time">
                </div><br>
                <div class="form-group">
                <label>Choose Your Car</label>
                <select class="form-select" aria-label="Default select example"  name="grabcar">
                <option hidden >Available Car</option>
                @foreach($data as $item)
                <option value="{{ $item->id }}">{{ $item->car_brand }} - {{ $item->car_name }} ({{ $item->car_registration_num}})</option>
                @endforeach
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

@endsection

@section('script')
<!-- Peity chart-->
<script src="{{ URL::asset('assets/libs/peity/peity.min.js')}}"></script>

{{-- <script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script> --}}

<script>
    $(document).ready(function() {
    
        $('#insertdestination').DataTable();
});

</script>

@endsection
