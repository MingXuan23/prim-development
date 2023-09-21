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
      <h4 class="font-size-18">Lihat Keuntungan Perkhidmatan Grab Anda</h4>
    </div>
  </div>
</div>
<div class="row">



        <div class="col-md-12">
    <div class="card card-primary">


        @if (!isset($salesData))
            <!-- Display the date selection form -->
        <form method="post" action="{{ route('generate-sales-graph') }}">
        @csrf
        <div class="row">
        <div class="col">
        <div class="form-group">
        <label for="start_date">Tarikh Mula :</label>
        <input type="date"  class="form-control" name="start_date" required>
                    </div>
                    </div>
                    <div class="col">
                    <div class="form-group">
                    <label for="end_date">Tarikh Tamat :</label>
        <input type="date"  class="form-control" name="end_date" required>
                    </div>
                    </div>
                    </div>
        @foreach($org as $row)
        <input type="text" name="org" class="form-control" value="{{ $row->id }}" hidden>
        @endforeach
                    <button type="submit" class="btn btn-primary">Generate Sales Graph</button>
        </form>
        
      </div>


        
      </div>

    </div>
  </div>

    </div>
  </div>
        @else
            <!-- Display the graph -->
            <div id="sales-graph-container">
                <canvas id="sales-chart" width="5" height="2.3"></canvas>
            </div>
        @endif

    </div>  
</div>

@if (isset($salesData))
    {{-- Include JavaScript code to render the graph using Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        var salesData = @json($salesData);

        // Extract book_date values and total_sales values from the salesData
        var bookDates = salesData.map(function(item) {
            return item.book_date;
        });

        var totalSales = salesData.map(function(item) {
            return item.total_sales;
        });

        // Render the graph
        var ctx = document.getElementById('sales-chart').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: bookDates, // Use book_date values as labels on the X-axis
                datasets: [{
                    label: 'Jumlah Diperoleh (RM) ',
                    data: totalSales,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2,
                    barThickness: 120,
                    maxBarThickness: 120,
                }]
            },
            options: {
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Book Date'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Total Sales'
                        }
                    }
                }
            }
        });
    </script>
@endif  

@endsection
