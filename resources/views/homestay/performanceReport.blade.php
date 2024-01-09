@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ URL::asset('assets/css/datatable.css')}}">
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
<link rel="stylesheet" href="{{URL::asset('assets/homestay-assets/jquery-ui-datepicker.theme.min.css')}}">
<link rel="stylesheet" href="{{URL::asset('assets/homestay-assets/jquery-ui-datepicker.structure.min.css')}}">
<link rel="stylesheet" href="{{ URL::asset('assets/homestay-assets/style.css')}}">
@include('layouts.datatable')

<style>
    footer {
        background-color: var(--primary-color) !important;
        color: white !important;
    }
    /* style for print */
    @media print{
        /* set margin for the print page */
        @page{
            margin: 1px 1px 2.5px 2.5px!important;
        }
        body > *{
            visibility: hidden;
        }
        .report, .report * {
            visibility: visible;
        }
        .report {
            position: relative;
            left: 0;
            top: -300px;
            margin: 2.5px;
        }
        
    }
</style>
@endsection

@section('content')
{{-- <div class="page-title-box d-flex justify-content-between align-items-center flex-wrap">
  <h4 class="font-size-18 color-purple">Laporan Prestasi</h4>
  <div class="nav-links d-flex justify-content-center align-items-center flex-wrap">
      <a href="{{route('homestay.urusbilik')}}" class="btn-dark-purple m-2"><i class="mdi mdi-home-city-outline"></i> Urus Homestay</a>
      <a href="{{route('homestay.promotionPage')}}" class="btn-dark-purple m-2"><i class="fas fa-percentage"></i> Urus Promosi</a>
      <a href="{{route('homestay.urustempahan')}}" class="btn-dark-purple m-2"><i class="fas fa-concierge-bell"></i> Urus Tempahan Pelanggan</a>
      <a style="cursor: pointer;" id="view-customers-review" class="btn-dark-purple m-2"> <i class="fas fa-comments"></i> Nilaian Pelanggan</a>
  </div>
</div> --}}
@include('homestay.adminNavBar')
<div class="row">

  <div class="col-md-12">
    <div class="card  mx-auto card-primary card-org my-0">

      @if(count($errors) > 0)
      <div class="alert alert-danger">
        <ul>
          @foreach($errors->all() as $error)
          <li>{{$error}}</li>
          @endforeach
        </ul>
      </div>
      @endif

      {{csrf_field()}}
      <div class="card-body bg-purple">
        <div class="form-group">
          <label>Nama Organisasi</label>
          <select name="org_id" id="org_id" class="form-control">
            <option value="" selected disabled>Pilih Organisasi</option>
            @foreach($organizations as $row)
            <option value="{{ $row->id }}">{{ $row->nama }}</option>
            @endforeach
          </select>
        </div>
      </div>
      </div>
    </div>
  </div>
</div>

      <div class="mb-2 row m-4 d-flex justify-content-center">
            <div class="col-md-4 my-1 d-flex flex-column  justify-content-center align-items-center">
                <label for="report_start">Tarikh Mula</label>
                <input type="text" name="report_start" id="report_start" class="form-control" autocomplete="off" required>
            </div>
            <div class="col-md-4 my-1 d-flex flex-column justify-content-center align-items-center">
                <label for="report_end">Tarikh Berakhir</label>
                <input type="text" name="report_end" id="report_end" class="form-control" autocomplete="off" disabled required>
            </div>
            <div class="col-md-3 my-1 d-flex justify-content-center align-items-end">
                <button class="btn-purple" id="btn-generate-report">Hasilkan Report</button>
            </div>
        </div>
      <div id="report-title" class="text-center mb-4 report">

      </div>
      {{-- charts --}}        
      <div class="row report">
        <div class="col-sm-5 col-sm-offset-3 text-center">
            <label class="label label-chart">Jumlah Malam Ditempah</label>
           <div id="pie-chart-nights-booked" class="chart"></div>
        </div>
        <div class="col-sm-7 col-sm-offset-3 text-center">
            <label class="label label-chart">Jumlah Pendapatan Dijana</label>
           <div id="bar-chart-homestay-earnings" class="chart"></div>
        </div>
      </div>

      <h4 class="text-center mb-4 color-dark-purple report">
        Pendapatan Bulananan Secara Kesuluruhan
        <div id="line-chart-monthly-earnings" class="chart"></div>
      </h4>

      <h4 class="text-center mb-4 color-dark-purple report">
        Purata Nilaian Bulananan 
        <div id="line-chart-monthly-ratings" class="chart"></div>
      </h4>

      <div class="my-2 d-flex justify-content-center align-items-center">
            <button class="btn-purple" id="btn-download-report">Muat Turun</button>
      </div>
@endsection


@section('script')
{{-- sweet alert --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ URL::asset('assets/libs/parsleyjs/parsleyjs.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/inputmask/inputmask.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/jquery-mask/jquery.mask.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
{{-- for morris chart --}}
<script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
<script>
$(document).ready(function() {
    $('.navbar-header > div:first-child()').after(`
        <img src="{{URL('assets/homestay-assets/images/book-n-stay-logo(transparent).png')}}"  height="70px">
    `);
  $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $('#report_start').datepicker({
        dateFormat: 'dd/mm/yy',
        onSelect: function(selectedDate) {
            // Parse the selectedDate as a JavaScript Date object
            var selectedDateObject = $.datepicker.parseDate('dd/mm/yy', selectedDate);

            // Add one day to the selectedDate
            selectedDateObject.setDate(selectedDateObject.getDate() + 1);
            
            // Format the new date as 'dd/mm/yy'
            var newMinDate = $.datepicker.formatDate('dd/mm/yy', selectedDateObject);

            // Set the newMinDate as the minimum date for #promotion-end datepicker
            $("#report_end").datepicker("option", "minDate", newMinDate);
            $("#report_end").datepicker("option", "disabled", false);
        }
    });    
    $('#report_end').datepicker({
        dateFormat: 'dd/mm/yy',
        disabled: true,
    });
    var counter = 0;
    function getData(startDate, endDate){
        // only run during the first exucution
        if(counter == 0){
            $('#report-title').html(`
                <h4 class="color-dark-purple">Laporan Untuk Bulan ${date.toLocaleString('default', { month: 'long' })}</h4>
            `);  
            counter++;          
        }else{
            $('#report-title').html(`
                <h4 class="color-dark-purple">Laporan Untuk ${$('#report_start').val()} - ${$('#report_end').val()}</h4>
            `);
        }
        // reset charts
        $('.chart').empty();
        $('.label-chart-description').remove();
        $.ajax({
            url: '{{route("homestay.getReportData")}}',
            method: 'GET',
            data: {
                startDate: startDate,
                endDate: endDate,
                orgId: $('#org_id').val(),
            },
            success: function(result){

                var noBookings = true;
                var totalBookedNights =  totalEarnings = 0;
                var donutData = result.homestays.map(function (homestay) {
                    if(homestay.bookedNights > 0){
                        noBookings = false;
                    }
                    totalBookedNights += homestay.bookedNights;
                    return {
                        label: homestay.roomname,
                        value: homestay.bookedNights
                    };
                });
                var barData = result.homestays.map(function (homestay) {
                    totalEarnings += homestay.totalEarnings;

                    return {
                        y: homestay.roomname,
                        value: homestay.totalEarnings
                    };

                });
                // If there are 0 bookings from all homestays
                if(noBookings){
                    $('#pie-chart-nights-booked').html(`
                        <div>Tiada tempahan diterima untuk mana-mana penginapan</div>
                    `);
                    $('#bar-chart-homestay-earnings').html(`
                        <div>Tiada pendapatan dijana daripada mana-mana penginapan</div>
                    `);
                }else{
                    $('#pie-chart-nights-booked').prev().append(`
                        <div class="color-purple label-chart-description">${totalBookedNights} malam</div>
                    `);
                    $('#bar-chart-homestay-earnings').prev().append(`
                        <div class="color-purple label-chart-description">RM${totalEarnings.toFixed(2)}</div>
                    `);
                    Morris.Donut({
                        element: 'pie-chart-nights-booked',
                        data: donutData
                    });  
                    Morris.Bar({
                        element: 'bar-chart-homestay-earnings',
                        data: barData,
                        xkey: 'y',
                        ykeys: ['value'],
                        labels: ['Jumlah Pendapatan'],    
                        hideHover: true,    
                        hoverCallback: function (index, options, content, row) {
                            return `Jumlah Pendapatan: RM${row.value.toFixed(2)}`;
                        }
                    });    

                }

                // for monthly earnings
                const monthlyEarnings = result.earningsPerMonth;
                // adjust monthlyEarnings value by adding remaining earnings for next month
                const lineData = monthlyEarnings.map(function(monthlyEarning , i) {
                    var earnings = 0;
                    // if there is remainingEarnings from last month
                    if(i > 0 && monthlyEarnings[i-1].remainingEarningsForNextMonth > 0){
                        earnings = monthlyEarning.earnings + monthlyEarnings[i-1].remainingEarningsForNextMonth;
                    }else{
                        earnings = monthlyEarning.earnings;
                    }
                    
                    return {
                        label: monthlyEarning.month,
                        value: parseFloat(earnings.toFixed(2)),
                    };
                }); 
                Morris.Line({
                    element: 'line-chart-monthly-earnings',
                    data: lineData,
                    xkey: 'label',
                    ykeys: ['value'],
                    labels: ['Jumlah Pendapatan'],   
                    parseTime: false,     
                    hideHover: true,
                    hoverCallback: function (index, options, content, row) {
                        return `Jumlah Pendapatan: RM${row.value}`;
                    }
                });  

                // for monthly ratings
                const monthlyRatings = result.ratings;
                var lineRatingData = [],homestayNames = [];
                if(monthlyEarnings != null){
                    lineRatingData = monthlyRatings.map(item =>{
                        const result = {month: item.month};
                        Object.keys(item.ratings).forEach(key => {
                            !homestayNames.includes(key) ? homestayNames.push(key) : '';
                            result[key] = item.ratings[key];
                        })
                        return result;
                    });
                }else{
                    $('#line-chart-monthly-ratings').html(`Tiada nilaian diterima`);
                }

                // console.log(lineRatingData);
                // console.log(homestayNames); 
                
                Morris.Line({
                    element: 'line-chart-monthly-ratings',
                    data: lineRatingData,
                    xkey: 'month',
                    ykeys: homestayNames,  
                    labels: homestayNames,     
                    parseTime: false,     
                    hideHover:'auto',
                    ymax: [5],
                    axes: true, // Show axis lines
                    numLines: 6, // Adjust the number of gridlines on the y-axis (6 lines will give you step size of 1 if the ymax is set to 5)
                }); 
                
            },
            error: function(){
                console.log('Fetch Report Data Failed');
            }
        })
    }
    // Bind onchange event
    $('#org_id').change(function() {
        const homestayId = $(this).val();
        $('#view-customers-review').attr('href',`{{route('homestay.viewCustomersReview')}}`);
    });

    $("#org_id option:nth-child(2)").prop("selected", true);
    $('#org_id').trigger('change');
    // initialize a report for this month
    // Initialize a Date object for the current date
    var date = new Date();

    // Get the first day of the current month
    date.setDate(1);

    // Get the last day of the current month
    date.setMonth(date.getMonth() + 1);
    date.setDate(0);// 0 means the last day of previous month

    // Now, 'date' contains the first and last date of the current month
    var firstDate = new Date(date.getFullYear(), date.getMonth(), 1);
    var lastDate = date;

    // You can format the dates as per your requirements
    var firstDateFormatted = formatDate(firstDate);
    var lastDateFormatted = formatDate(lastDate);

    function formatDate(date) {
        var day = date.getDate();
        var month = date.getMonth() + 1; // Months are zero-indexed
        var year = date.getFullYear();

        // Pad single-digit day and month values with leading zeros
        if (day < 10) {
            day = '0' + day;
        }
        if (month < 10) {
            month = '0' + month;
        }

        return day + '/' + month + '/' + year;
    }

    getData(firstDateFormatted,lastDateFormatted);

    $('#btn-generate-report').on('click',function(){
        if($('#report_start').val() != '' && $('#report_end').val() != ''){
            getData($('#report_start').val(), $('#report_end').val());   
        }else{
            Swal.fire('Sila pastikan tarikh mula dan berakhir tidak dibiarkan kosong');
        }
    })

    $('#btn-download-report').on('click',function(){
        window.print();
    });
                    // to add .active to the link for current page in navbar
    // Get the current URL path
    var currentPath = window.location.pathname;

    // Loop through each anchor tag in the navigation
    $('.admin-nav-links a').each(function() {
        var linkPath = $(this).attr('href');
        // Check if the link's path matches the current URL path
        if (linkPath.includes(currentPath)) {
            // Add a class to highlight the active link
            $(this).addClass('admin-active');
        }
    });
});
</script>
@endsection