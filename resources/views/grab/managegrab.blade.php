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
            <h4 class="font-size-18">Urus Kenderaan Anda</h4>
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
                <table id="managegrabtable" class="table table-bordered table-striped dt-responsive wrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                        <tr>
                            <th>Number</th>
                            <th>Car Brand</th>
                            <th>Car Name</th>
                            <th>Car Registration Number</th>
                            <th>Seat </th>
                            <th>Available Time</th>
                            <th>Status</th>
                            <th>Edit</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $item)
                        <tr>                
                            <form action="/updaterow-grab/{{ $item->id }}" method="POST">
                            @csrf
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->car_brand }}</td>
                            <td>{{ $item->car_name }}</td>
                            <td>{{ $item->car_registration_num}}</td>
                            <td>{{ $item->number_of_seat}}</td>
                            <td><input type="time" name="time" value="{{ $item->available_time }}"></td>
                            <td>            
                            <select class="form-select" aria-label="Default select example"  name="status">         
                            <option hidden value="{{ $item->status }}">{{ $item->status }}</option>
                            <option value="AVAILABLE">AVAILABLE</option>
                            <option value="NOT AVAILABLE">NOT AVAILABLE</option>
                            </select>
                            </td>
                            <td> <button type="submit" class="btn btn-secondary">Update</button></td>
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
