@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ URL::asset('assets/css/datatable.css')}}">
<link rel="stylesheet" href="{{ URL::asset('assets/homestay-assets/style.css')}}">
<link rel="stylesheet" href="{{ URL::asset('assets/homestay-assets/calendar-js/calendar.js.min.css')}}">
<style>
    :root{
        --calendar-js-dark-color: #391582!important;
        --calendar-js-event-color: #852aff!important;
    }
    div.calendar div.full-month-view div.title-bar div.title-container span.year-dropdown-button{
        background-color: #852aff!important;
    }
    footer {
        background-color: var(--primary-color) !important;
        color: white !important;
    }
    .hide{
        display: none!important;
    }
</style>
@include('layouts.datatable')

@endsection

@section('content')
  @include('homestay.adminNavBar')

<div class="row mb-5">

  <div class="col-md-12">
    <div class="card mx-auto card-primary card-org">

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
          <select name="org" id="org" class="form-control">
            <option value="" selected disabled>Pilih Organisasi</option>
            @foreach($data as $row)
            <option value="{{ $row->id }}">{{ $row->nama }}</option>
            @endforeach
          </select>
        </div>
      </div>

    </div>
  </div>

  <div id="customerResults" class="col-md-12 border-purple p-0">
    <div class="card  mb-0">
    <h2  class="mt-2 text-center color-purple">Urus Tarikh Buka Untuk Tempahan</h2>

      <div class="d-flex justify-content-between align-items-center flex-wrap p-2">
        <div class="d-flex align-items-center">
          <label for="homestay_id" class="mx-2">Homestay: </label>
          <select name="homestay_id" id="homestay_id" class="form-control">
            @foreach($homestays as $key=>$homestay)
                @if($key == 0)
                    <option value="{{$homestay->roomid}}" selected>{{$homestay->roomname}}</option>
                @else
                    <option value="{{$homestay->roomid}}">{{$homestay->roomname}}</option>
                @endif
            @endforeach
          </select>            
        </div>
    </div>
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
        
        <div id="calendar">

        </div>

    </div>
      </div>
    </div>
  </div>
</div>

@endsection


@section('script')
{{-- sweet alert --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
{{-- calendar js --}}
<script src="{{URL::asset('assets/homestay-assets/calendar-js/calendar.jquery.min.js')}}"></script>
<script src="{{URL::asset('assets/homestay-assets/calendar-js/calendar.min.js')}}"></script>
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

    let calendarInstance;
    createCalendar();

    function createCalendar(){
        calendarInstance = new calendarJs( "calendar", { 
            manualEditingEnabled: true, 
            tooltipDelay: 10,
            defaultEventBackgroundColor: "#852AFF",
            views: {
                fullMonth: {
                    showTimesInEvents: false,
                    minimumDayHeight: 65,
                    addYearButtons: false,
                    // showExtraTitleBarButtons: false
                },
            },
            fullScreenModeEnabled: false,
            visibleDays: [ 0, 1, 2, 3, 4, 5, 6 ],
            eventColorsEditingEnabled: false,
            // remove auto add when double click that only allow for updation
            useTemplateWhenAddingNewEvent: false,
            // triggers
            onEventAdded: function(event){
                addData(event);
            },
            onEventUpdated: function(event){
                updateData(event);
            },
            onEventRemoved: function(event){
                deleteData(event);
            },
            // change the labels
            addEventTooltipText: "Tambah Tarikh Tutup Tempahan",
            viewAllEventsTooltipText: "Lihat Semua Tarikh Tutup Tempahan",
            viewFullYearTooltipText: "Lihat Tahun",
            nextMonthTooltipText: "Bulan Depan",
            previousMonthTooltipText: "Bulan Lepas",
            currentMonthTooltipText: "Bulan Terkini",
            jumpToDateTitle: "Pergi Ke Tarikh",
            searchTooltipText: "Cari",
            addEventTitle: "Tambah Tarikh Tutup Tempahan",
            titleText: "Sebab",
            fromText: "Dari",
            toText: "Hingga",
            addText: "Tambah",
            updateText: "Kemas Kini",
            cancelText: "Batal",
            eventText:"Tarikh Tutup Tempahan",
            removeEventText: "Padam",
            searchEventsTitle: "Cari Tarikh",
            searchTextBoxPlaceholder: "Sebab tutup tempahan...",
            nextText: "Seterusnya",
            previousText: "Sebelumnya",

        } );
        // hide some items in the calendar
        $('#calendar .ib-arrow-left-full').prev('.left-divider-line').addClass('hide');
        $('#calendar .ib-hourglass').addClass('hide');
        $('#calendar .ib-bar-graph').addClass('hide');
        $('#calendar .ib-hamburger-side').addClass('hide');
        $('#calendar .ib-hamburger').addClass('hide');
        $('input[type="time"]').addClass('hide');
        $('label.checkbox').addClass('hide');
        $('div.tab-control:not(:selected)').addClass('hide');
    }

    // get disabled dates from database
    async function getData(){
      var homestayId = $("#homestay_id option:selected").val();
      let response = await fetch("{{ route('homestay.getDisabledDates') }}?homestayId=" + homestayId);
      let data = await response.json();
        let calendarDates = [];
        data.disabledDates.forEach(function(date){
            calendarDates.push({                    
                from: new Date(date.date_from + " 00:00:00"),
                to:  new Date(date.date_to + " 23:59:59"),
                id: date.id.toString(),
                title: date.title,
            })
        })
        calendarInstance.setEvents(calendarDates);
    }
    //save new dates to database
    async function addData(eventObject) {
        try {
            let homestayId = $("#homestay_id option:selected").val();
            let date_from = eventObject.from.getFullYear() + '-' + (eventObject.from.getMonth() + 1).toString().padStart(2, '0') + '-' + eventObject.from.getDate().toString().padStart(2, '0');
            let date_to = eventObject.to.getFullYear() + '-' + (eventObject.to.getMonth() + 1).toString().padStart(2, '0') + '-' + eventObject.to.getDate().toString().padStart(2, '0');
            let title = eventObject.title;

            const formData = new FormData();
            formData.append('homestay_id', homestayId);
            formData.append('title', title);
            formData.append('date_from', date_from);
            formData.append('date_to', date_to);
            formData.append('_token', "{{ csrf_token() }}");

            const response = await fetch("{{ route('homestay.addDisabledDate') }}", {
                method: "POST",
                body: formData
            });

            const result = await response.json();
            getData();
        } catch (error) {
            console.log(error);
        }
    }


    // function addData(eventObject){
    //     let homestayId = $("#homestay_id option:selected").val();
    //     let date_from = eventObject.from.getFullYear() +'-'+ (eventObject.from.getMonth()+1).toString().padStart(2,'0')+'-'+eventObject.from.getDate().toString().padStart(2,'0');
    //     let date_to = eventObject.to.getFullYear() +'-'+ (eventObject.to.getMonth()+1).toString().padStart(2,'0')+'-'+eventObject.to.getDate().toString().padStart(2,'0');
    //     let title = eventObject.title;
    //     $.ajax({
    //     url: "{{ route('homestay.addDisabledDate') }}",
    //     method: "POST",
    //     data:{
    //         homestay_id: homestayId,
    //         title: title,
    //         date_from: date_from,
    //         date_to: date_to,
    //         token: "{{csrf_token()}}",
    //     },
    //     success: function(result){  
    //         console.log(result.message);
    //         getData();
    //     },
    //     error: function(error){
    //         console.log(error);
    //     }
    //     });
        
    // }
    // update date
    function updateData(eventObject){
        let date_from = eventObject.from.getFullYear() +'-'+ (eventObject.from.getMonth()+1).toString().padStart(2,'0')+'-'+eventObject.from.getDate().toString().padStart(2,'0');
        let date_to = eventObject.to.getFullYear() +'-'+ (eventObject.to.getMonth()+1).toString().padStart(2,'0')+'-'+eventObject.to.getDate().toString().padStart(2,'0');
        let dateId = eventObject.id;
        let title = eventObject.title;
        console.log(dateId);
        $.ajax({
            url: "{{ route('homestay.updateDisabledDate') }}",
            method: "PUT",
            data:{
                id: dateId,
                title: title,
                date_from: date_from,
                date_to: date_to,
                token: "{{csrf_token()}}",
            },
            success: function(result){  
                console.log(result.message);
                getData();
            },
            error: function(error){
                console.log(error);
            }
        });
    }
    function deleteData(eventObject) {
        let date_from = eventObject.from.getFullYear() +'-'+ (eventObject.from.getMonth()+1).toString().padStart(2,'0')+'-'+eventObject.from.getDate().toString().padStart(2,'0');
        let date_to = eventObject.to.getFullYear() +'-'+ (eventObject.to.getMonth()+1).toString().padStart(2,'0')+'-'+eventObject.to.getDate().toString().padStart(2,'0');
        let dateId = eventObject.id;
        let title = eventObject.title;
        $.ajax({
            url: "{{ route('homestay.deleteDisabledDate') }}",
            method: "DELETE",
            data:{
                id: dateId,
                title: title,
                date_from: date_from,
                date_to: date_to,
                token: "{{csrf_token()}}",
            },
            success: function(result){  
                console.log(result.message);
                getData();
            },
            error: function(error){
                console.log(error);
            }
        });
    }
    // Bind onchange event
    $('#org').change(function() {
        var orgId = $(this).val();
        $('#view-customers-review').attr('href',`{{route('homestay.viewCustomersReview')}}`);
        getHomestays();//to change the list of homestays in the filter
        getData();
    });



    function getHomestays(){
    var orgId = $("#org option:selected").val();
      $.ajax({
        url: "{{ route('homestay.getOrganizationHomestays') }}?orgId=" + orgId,
        method: "GET",
        success: function(result){
            //reset #homestay_id 
            $('#homestay_id').empty();
            // add option into #homestay_id
            $(result.homestays).each(function(i, homestay){
                if(i == 0){
                    $('#homestay_id').append(`
                        <option value="${homestay.roomid}" selected>${homestay.roomname}</option>
                    `);
                }else{
                    $('#homestay_id').append(`
                        <option value="${homestay.roomid}">${homestay.roomname}</option>
                    `);
                }

            });     
        },
        error: function(error){
            console.log(error);
        }
    });
    }
    $("#org option:nth-child(2)").prop("selected", true);
    $('#org').trigger('change');
    // Handle "Edit" button click
    $('#btn-back').on('click',function(){
      $('#roommodal').modal('hide');
    })
    $('#homestay_id').change(function() {
        var orgId = $(this).val();
        $('#view-customers-review').attr('href',`{{route('homestay.viewCustomersReview')}}`);
        getData();
    });
    $('.alert').delay(3000).fadeOut();


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