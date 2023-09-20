@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ URL::asset('assets/css/datatable.css')}}">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@include('layouts.datatable')
@endsection

@section('content')
<div class="row align-items-center">
  <div class="col-sm-6">
    <div class="page-title-box">
      <h4 class="font-size-18">Lihat Keuntungan</h4>
    </div>
  </div>
</div>
<div class="row">

  <div class="col-md-12">
    <div class="card card-primary">

      {{csrf_field()}}
      <div class="card-body">
        <form method="POST" action="">
        <div class="form-group">
          <label>Nama Homestay</label>
          <select name="homestayid" id="homestayid" class="form-control">
            <option value="" selected>Pilih Homestay</option>
            @foreach($data as $row)
            <option value="{{ $row->id }}">{{ $row->nama }}</option>
            @endforeach
          </select>
        </div>
        <button type="button" id="showCustomersBtn" class="btn btn-primary" style="margin: 19px; float: right;">Lihat Hasil</button>
        </form>
        
      </div>

    </div>
  </div>

  <div id="customerResults" class="col-md-12">
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
        <canvas id="homestaySalesChart"></canvas>
        
      </div>
    </div>
  </div>
</div>

@endsection


@section('script')
<script>
$(document).ready(function() {
    let myChart = null;

    $('#showCustomersBtn').click(function() {
        var homestayid = $('#homestayid').val();

        if (myChart) {
            myChart.destroy();
        }

        $.ajax({
            url: '/homestaysales/' + homestayid,
            type: 'GET',
            dataType: 'json',
            success: function(chartData) {
                // Create and render the chart using Chart.js
                var ctx = document.getElementById('homestaySalesChart').getContext('2d');
                myChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: chartData.labels,
                        datasets: [{
                            label: 'Total Dapat (RM)',
                            data: chartData.dataset,
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Total Jualan (RM)' // Label for the y-axis
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Bulan' // Label for the x-axis
                    }
                }
            }
        }
    });
},
error: function(error) {
    console.log(error);
}
        });
    });

    $('.alert').delay(3000).fadeOut();
});
</script>
@endsection