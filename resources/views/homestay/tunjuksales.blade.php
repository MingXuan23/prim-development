@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ URL::asset('assets/css/datatable.css')}}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
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
        <div class="row">
        <div class="col">
        <div class="form-group">
          <label>Nama Homestay</label>
          <select name="homestayid" id="homestayid" class="form-control">
            <option value="" selected>Pilih Homestay</option>
            @foreach($data as $row)
            <option value="{{ $row->id }}">{{ $row->nama }}</option>
            @endforeach
          </select>
        </div>
</div></div>
<div class="row">
        <div class="col">
        <div class="form-group">
                        <label class="form-label">Tarikh Dari</label>
                        <input type="text" class="form-control" id="checkin" name="checkin">
                    </div>
                    </div>
                    <div class="col">
                    <div class="form-group">
                        <label class="form-label">Tarikh Hingga</label>
                        <input type="text" class="form-control" id="checkout" name="checkout">
                    </div>
                    </div>
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
<script src="{{ URL::asset('assets/libs/parsleyjs/parsleyjs.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/inputmask/inputmask.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/jquery-mask/jquery.mask.min.js')}}"></script>
<script>
$(document).ready(function() {
    let myChart = null;

    var checkinDatepicker = $("#checkin");
    var checkoutDatepicker = $("#checkout");
    var maxDate = new Date();
    maxDate.setMonth(maxDate.getMonth() + 1);


    checkinDatepicker.datepicker({
        dateFormat: "yy-mm-dd",
        beforeShow: function(input, inst) {
            inst.dpDiv.css({
                "background-color": "#dce0df"
            });
        },
        onSelect: function(selectedDate) {

            var selectedDateObject = new Date(selectedDate);
            var maxDateObject = new Date(selectedDate);
            maxDateObject.setMonth(maxDateObject.getMonth() + 1);
            checkoutDatepicker.datepicker("option", "minDate", selectedDateObject);
            checkoutDatepicker.datepicker("option", "maxDate", maxDateObject);
        }
      });

      checkoutDatepicker.datepicker({
          dateFormat: "yy-mm-dd",
          beforeShow: function(input, inst) {
            inst.dpDiv.css({
                "background-color": "#dce0df"
            });
        }
      });

    $('#showCustomersBtn').click(function() {
        var homestayid = $('#homestayid').val();
        var checkin = $('#checkin').val();
        var checkout = $('#checkout').val();

        

        if (myChart) {
            myChart.destroy();
        }

        $.ajax({
            url: '/homestaysales/' + homestayid + '/' + checkin + '/' + checkout,
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