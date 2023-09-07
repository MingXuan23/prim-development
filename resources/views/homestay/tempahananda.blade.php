@extends('layouts.master')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
@include('layouts.datatable');

@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Tempahan Anda</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">

            <div class="card-body">

            @if(Session::has('success'))
            <div class="alert alert-success">
              <p>{{ Session::get('success') }}</p>
            </div>
          @elseif(Session::has('error'))
            <div class="alert alert-danger">
              <p>{{ Session::get('error') }}</p>
            </div>
          @endif

                <div class="flash-message"></div>
                <div class="table-responsive">
                    <table id="homestaytable" class="table table-bordered table-striped dt-responsive wrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                    <tr class="text-center">

                      <th hidden>Booking ID</th>
                      <th>Nama Homestay</th>
                      <th>Nama Bilik</th>
                      <th>Alamat</th>
                      <th>Detail Bilik</th>
                      <th>Tarikh Dari</th>
                      <th>Tarikh Hingga</th>
                      <th>Harga Keseluruhan (RM)</th>
                      <th>Proses Bayaran</th>
                    </tr>
                </thead>
                <tbody>
                        @foreach($data as $record)
                        <tr>
                            <td hidden>{{ $record->bookingid }}</td>
                            <td>{{ $record->nama }}</td>
                            <td>{{ $record->roomname }}</td>
                            <td>{{ $record->address }}</td>
                            <td>{{ $record->details }}</td>
                            <td>{{ $record->checkin }}</td>
                            <td>{{ $record->checkout }}</td>
                            <td>{{ $record->totalprice }}</td>
                            <td><button class="btn btn-success bayar-button" data-booking-id="{{ $record->bookingid }}">Teruskan</button></td>
                            </td>
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
    
        $('#homestaytable').DataTable();

        $('.bayar-button').click(function() {
            var id = $(this).data('booking-id');
            console.log(id);
            window.location.href = '/homestayresit/' + id;
             // Change '/book/' to your actual route
        });

$('.alert').delay(3000).fadeOut()
});

</script>
@endsection