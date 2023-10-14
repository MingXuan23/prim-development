@extends('layouts.master')

@section('css')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.css" media="screen">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <link rel="stylesheet" href="{{URL::asset('assets/homestay-assets/jquery-ui-datepicker.theme.min.css')}}">
    <link rel="stylesheet" href="{{URL::asset('assets/homestay-assets/jquery-ui-datepicker.structure.min.css')}}">
    <link rel="stylesheet" href="{{ URL::asset('assets/homestay-assets/style.css')}}">

@endsection

@section('content')
    <section aria-label="Homestay or Room Details">
        <h3 class="color-purple"><span><a href="{{route('homestay.homePage')}}" class="color-dark-purple" target="_self">Laman Utama >> </a></span>{{$room->roomname}}</h3>
        <h5 class="color-dark-purple"><span><i class="fas fa-map-marker-alt"></i></span> {{$room->address}}, {{$room->area}}, {{$room->postcode}}, {{$room->district}}, {{$room->state}}</h5>
            <div class="gallery-container">
                <img src="../{{$roomImages[0]}}" id="first-gallery-image" alt="Room Image">
                <div class="inner-gallery-container">
                    @for ($i = 1; $i < 5; $i++)
                        @if($roomImages[$i] != null)
                                <img src="../{{$roomImages[$i]}}" alt="Room Image">
                        @endif
                    @endfor                    
                </div>

                <div class="btn-gallery-container">
                    <button class="btn-gallery">
                        <span class="btn-gallery-content">See all photos</span>
                    </button>
                </div>
            </div>
            <div class="row mt-4 ">
                <div class="col-md-7">
                    <p class="room-details">
                    </p>
                </div>
                <div class="col-md-5">
                    <form class="booking-container" action="{{route('homestay.bookRoom')}}" method="post" enctype="multipart/form-data" id="form-book">
                        @if(Session::has('success'))
                            <div class="alert alert-success text-center">
                                <p>{{ Session::get('success') }}</p>
                            </div>
                        @elseif(Session::has('error'))
                            <div class="alert alert-danger text-center">
                                <p>{{ Session::get('error') }}</p>
                            </div>
                        @endif
                        <input type="text" name="roomId" id="roomId" value={{$room->roomid}} hidden>
                        <input type="text" name="roomPrice" id="roomPrice" value={{$room->price}} hidden>
                        <h5 class="text-white mb-2">RM{{$room->price}}/malam</h5>
                        <div class="form-floating mb-2" >
                            <input type="text" autocomplete="off" name="checkIn" id="check-in" class="form-control" placeholder=" ">
                            <label for="check-in">Daftar Masuk</span>
                        </div>
                        <div class="form-floating mb-2" >
                            <input type="text" autocomplete="off"  name="checkOut" id="check-out" class="form-control" placeholder=" " disabled>
                            <label for="check-out">Daftar Keluar</label>
                        </div>
                        <div class="text-white d-flex gap-2 align-items-center mb-2">
                            <div id="total-price"></div>
                        </div>
                        <div class="d-flex justify-content-end">
                            @csrf
                            <input type="text" name="amount" id="amount" value="0" hidden> 
                            <button class="btn-book" type="submit">
                                <span>Tempah Sekarang</span>
                                <svg width="34" height="34" viewBox="0 0 74 74" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="37" cy="37" r="35.5" stroke="white" stroke-width="3"></circle>
                                    <path d="M25 35.5C24.1716 35.5 23.5 36.1716 23.5 37C23.5 37.8284 24.1716 38.5 25 38.5V35.5ZM49.0607 38.0607C49.6464 37.4749 49.6464 36.5251 49.0607 35.9393L39.5147 26.3934C38.9289 25.8076 37.9792 25.8076 37.3934 26.3934C36.8076 26.9792 36.8076 27.9289 37.3934 28.5147L45.8787 37L37.3934 45.4853C36.8076 46.0711 36.8076 47.0208 37.3934 47.6066C37.9792 48.1924 38.9289 48.1924 39.5147 47.6066L49.0607 38.0607ZM25 38.5L48 38.5V35.5L25 35.5V38.5Z" fill="white"></path>
                                </svg>
                            </button>                            
                        </div>
                    </form>
                </div>
            </div>
            <div>
                <h3 class="color-dark-purple">Maklumat Penting</h3>
                <div class="row text-center p-5" id="room-info">
                    <div class="col-md-4">
                        <h5>Daftar Masuk Selepas: <span class="color-purple">{{ date('H:i', strtotime($room->check_in_after)) }}</span></h5>
                    </div>
                    <div class="col-md-4">
                        <h5>Daftar Keluar Sebelum: <span class="color-purple">{{ date('H:i', strtotime($room->check_out_before)) }}</span></h5>
                    </div>
                    <div class="col-md-4">
                        <h5>Room Pax: <span class="color-purple">{{$room->roompax}}</span></h5>
                    </div>
                </div>
            </div>
            <div>
                <h3 class="color-dark-purple">Lokasi Penginapan</h3>
                <iframe
                width="100%"
                height="400"
                frameborder="0" style="border:0"
                src="https://www.google.com/maps?q={{ urlencode($room->address.",".$room->area.",".$room->postcode.$room->district.",". $room->state) }}&output=embed" class="google-map">
                </iframe>
            </div>
    </section>
    
    <textarea name="details" id="details" hidden>{{$room->details}}</textarea>

    <div class="modal" id="modal-gallery">
        <div class="modal-dialog modal-xl my-0">
            <div class="modal-content my-0">
                <div class="modal-header my-0" >
                    <button type="button" id="btn-close-gallery"><i class="far fa-times-circle"></i></button>
                </div>
                <div class="modal-body">
                    @foreach($roomImages as $roomImage)
                    <div class="thumb">
                        <a href="../{{$roomImage}}" class="fancybox" rel="lightbox">
                            <img  src="../{{$roomImage}}" class="zoom"  alt="">
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal" id="modal-fullscreen">
        <div class="modal-dialog modal-xl my-0">
            <div class="modal-content my-0">
                <div class="modal-header my-0" >
                    <button type="button" id="btn-close-image"><i class="far fa-times-circle"></i></button>
                </div>
                <div class="modal-body" id="fullscreen-image-container">

                </div>
            </div>
        </div>
    </div>

@endsection


@section('script')
{{-- sweet alert --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script>
$(document).ready(function() {    
    $(".fancybox").fancybox({
        openEffect: "none",
        closeEffect: "none"
    });
    
    $(".zoom").hover(function(){
		
		$(this).addClass('transition');
	}, function(){
        
		$(this).removeClass('transition');
	});
    $('.btn-gallery').on('click', function(){
        $('#modal-gallery').modal('show');
    })
    $('#btn-close-gallery').on('click', function(){
        $('#modal-gallery').modal('hide');
    });
    $('.gallery-container img').on('click', function(){
        const image = $(this).attr('src');
        $('#fullscreen-image-container').empty();
        $('#fullscreen-image-container').html(`
            <img src="${image}" alt="Fullscreen Image">        
        `);
        $('#modal-fullscreen').modal('show');
    })
    $('#btn-close-image').on('click', function(){
        $('#modal-fullscreen').modal('hide');
    });
    // need to put the details by this way or else there will be indentations
    $('.room-details').html($('#details').val());


    // for booking and datetimepickers

    function calculateTotalPrice(){
        const checkInDate = $('#check-in').datepicker('getDate');
        const checkOutDate = $('#check-out').datepicker('getDate');
        const checkInDateMoment = moment(checkInDate);
        const checkOutDateMoment = moment(checkOutDate); // Parse check-out date using moment.js

        // Calculate the difference in days
        const daysDifference = checkOutDateMoment.diff(checkInDateMoment, 'days');
        const pricePerDay = $('#roomPrice').val();
        const totalPrice = (pricePerDay * daysDifference).toFixed(2);
        $('#total-price').html(`
            <h5>Jumlah Harga: RM${totalPrice} (${daysDifference} malam)</h5>
        `);
        $('#amount').val(totalPrice);    
    }

    function initializeCheckInOut(){
        let roomId = $('#roomId').val();
        $.ajax({
            url: "{{route('homestay.fetchUnavailableDates')}}",
            method: "GET",
            data: {
                roomId: roomId,
            },
            success: function(result){
                const disabledDates = result.disabledDates;
                // for check in datepicker
                $('#check-in').datepicker({
                    dateFormat: 'dd/mm/yy',
                    minDate: 0,
                    beforeShowDay: function(date) {
                        var formattedDate = $.datepicker.formatDate('dd/mm/yy', date);
                        var isDisabled = (disabledDates.indexOf(formattedDate) !== -1);
                        return [!isDisabled];
                    },
                    onSelect: function(selectedDate) {
                        // Parse the selectedDate as a JavaScript Date object
                        var selectedDateObject = $.datepicker.parseDate('dd/mm/yy', selectedDate);

                        // Add one day to the selectedDate
                        selectedDateObject.setDate(selectedDateObject.getDate() + 1);
                        
                        // Format the new date as 'dd/mm/yy'
                        var newMinDate = $.datepicker.formatDate('dd/mm/yy', selectedDateObject);

                        // Set the newMinDate as the minimum date for #check-out datepicker
                        $("#check-out").datepicker("option", "minDate", newMinDate);
                        $("#check-out").datepicker("option", "disabled", false);

                        // if already selected a check in date and want to choose a different check in date
                        if($('#check-in').datepicker('getDate') != null &&  $('#check-out').datepicker('getDate') != null){
                            if(!checkDisabledDatesBetween()){
                                calculateTotalPrice();
                            }else{
                                $('#total-price').empty();
                                $('#amount').val(0);   
                                Swal.fire('Sila pastikan masa antara daftar masuk dan daftar keluar homestay/bilik adalah kosong');
                                $('#check-in').datepicker("setDate", null);
                                $('#check-out').datepicker("setDate", null); // Clear the selected check-out date
                            }
                 
                        }
                    }
                });    
                $('#check-out').datepicker({
                    dateFormat: 'dd/mm/yy',
                    disabled: true,
                    beforeShowDay: function(date) {
                        var formattedDate = $.datepicker.formatDate('dd/mm/yy', date);
                        var isDisabled = (disabledDates.indexOf(formattedDate) !== -1);
                        return [!isDisabled];
                    },
                    onSelect: function(checkOutDate) {
                        if(!checkDisabledDatesBetween()){
                                calculateTotalPrice();
                        }else{
                            $('#total-price').empty();
                            $('#amount').val(0);   
                            Swal.fire('Sila pastikan masa antara daftar masuk dan daftar keluar homestay tersebut adalah kosong');
                            $('#check-in').datepicker("setDate", null);
                            $('#check-out').datepicker("setDate", null); // Clear the selected check-out date
                        }
                    }
                });
                // Function to check for disabled dates between check-in and check-out
                function checkDisabledDatesBetween() {
                    var checkInDate = $('#check-in').datepicker('getDate');
                    var checkOutDate = $('#check-out').datepicker('getDate');

                    var currentDate = new Date(checkInDate);
                    checkOutDate = new Date(checkOutDate);
                    var isDisabledFound = false;

                    while (currentDate <= checkOutDate) {
                        var formattedCurrentDate = $.datepicker.formatDate('dd/mm/yy', currentDate);
                        if (disabledDates.indexOf(formattedCurrentDate) !== -1) {
                            isDisabledFound = true;
                            break;
                        }
                        currentDate.setDate(currentDate.getDate() + 1);
                    }
                    return isDisabledFound;
                }
            },
            error: function(result){
                console.log('Fetch Disabled Dates Error');
            }
        });
    }
    initializeCheckInOut();

    $('#form-book').on('submit',function(e){
        if($('#amount').val() == 0){
            e.preventDefault();
            Swal.fire('Sila pilih masa daftar masuk dan daftar keluar sebelum membuat tempahan');
        }
    });

    $('.alert').delay(3000).fadeOut()
});
</script>
@endsection