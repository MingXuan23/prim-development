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
            <h4 class="font-size-18">Bayar Tempahan Grab Student</h4>
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
                            <th>Pick Up Point</th>
                            <th>Destinasi</th>
                            <th>Kenderaan </th>
                            <th>No Plat</th>
                            <th>Masa Notify Tempahan</th>
                            <th>Bayar Tempahan</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($list as $item)
                        <tr>                
                            <form action="passengertempahan-grab/{{ $item->id }}" method="POST">
                            @csrf
                            <td>{{ $item->pick_up_point }}</td>
                            <td>{{ $item->destination_name}}</td>
                            <td>{{ $item->car_brand}} - {{ $item->car_name}}</td>
                            <td>{{ $item->car_registration_num}}</td>
                            <td>{{ $item->time_notify}}</td>
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