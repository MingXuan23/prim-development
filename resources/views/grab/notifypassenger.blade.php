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
            <h4 class="font-size-18">Hantar Notifikasi Kepada Penumpang </h4>
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
<form action="{{ route('grab.notifypassenger') }}" method="get">
@csrf
        <label>Pilih Destinasi :</label>
        <select class="form-select" aria-label="Default select example"  name="availabledestination">
        <option selected disabled>Not Confirm Destination</option>
        @foreach($uniqueDestinations as $destination) 
        <option value="{{ $destination }}" {{ $selectedDestination == $destination ? 'selected' : '' }}>{{ $destination  }}</option>
        @endforeach
        </select>
        <br>
        <button type="submit" class="btn btn-success">View Detail</button>
</form>
                <br><br>
                @if ($selectedData)
                <table id="managecar" class="table table-bordered table-striped dt-responsive wrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                        <tr>
                            <th>Nama Penumpang</th>
                            <th>Pick Up Point</th>
                            <th>Destinasi</th>
                            <th>Kenderaan </th>
                            <th>No Plat</th>
                            <th>Masa Notify Tempahan</th>
                            <th>Status Notify</th>
                            <th>Hantar Email</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($selectedData as $item)
                        <tr>                
                            <form action="notifygrab-passenger/{{ $item->id }}" method="POST">
                            @csrf
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->pick_up_point }}</td>
                            <td>{{ $item->destination_name}}</td>
                            <td>{{ $item->car_brand}} - {{ $item->car_name}}</td>
                            <td>{{ $item->car_registration_num}}</td>
                            <td>{{ $item->time_notify}}</td>
                            <td>{{ $item->status}}</td>
                            <td> <button type="submit" class="btn btn-success">Hantar Notifikasi Email</button></td>
                            </form>
                        </tr>
                        @endforeach
                        </tbody>
                </table>
                @endif
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