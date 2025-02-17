@extends('layouts.master')

@section('css')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.css" media="screen">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <link rel="stylesheet" href="{{URL::asset('assets/homestay-assets/jquery-ui-datepicker.theme.min.css')}}">
    <link rel="stylesheet" href="{{URL::asset('assets/homestay-assets/jquery-ui-datepicker.structure.min.css')}}">
    <link rel="stylesheet" href="{{ URL::asset('assets/homestay-assets/style.css')}}">
    
    <style>
        /* add padding to the page's content*/
        .page-content{
            padding: 80px 10%!important;
        }
        #room-info {
            display: grid;
            column-gap: 5px;
            grid-template-columns: repeat(3, 1fr);
        }
        #room-info > div {
            border: 1px solid var(--secondary-color);
            padding: 12px;
        }
        footer {
            background-color: var(--primary-color) !important;
            color: white !important;
        }
        @media screen and (max-width:500px){
            .page-content{
                padding: 80px 0!important;
            }
            #room-info {
                grid-template-columns: 100%;
                padding: 12px !important;
            }
            #room-info > div {
                margin-bottom: 12px !important;
            }
        }
    </style>
@endsection

@section('content')
    <section aria-label="Homestay or Room Details">
    <div class="d-flex justify-content-between align-items-center">
    <h3 class="color-purple">
        <span>
            <a href="{{route('homestay.homePage')}}" class="color-dark-purple" target="_self">
                <i class="fas fa-home"></i> Laman Utama >>
            </a>
        </span>
        {{$room->roomname}}
    </h3>
    <button class="boxed-btn info-btn btn-primary" type="button" onclick="copyReferralLink()">
        Share
    </button>
</div>

       
        <h5 class="color-dark-purple"><span><i class="fas fa-map-marker-alt"></i></span> {{$room->address}}, {{$room->area}}, {{$room->postcode}}, {{$room->district}}, {{$room->state}}</h5>
        
            <div class="gallery-container">
                <img src="{{URL('../' . $roomImages[0])}}" id="first-gallery-image" alt="Room Image">
                <div class="inner-gallery-container">
                    @for ($i = 1; $i < 5; $i++)
                        @if($roomImages[$i] != null)
                                <img src="{{URL('../' . $roomImages[$i])}}" alt="Room Image">
                        @endif
                    @endfor                    
                </div>

                <div class="btn-fancy-container">
                    <button class="btn-fancy" id="btn-gallery">
                        <span class="btn-fancy-content">Lihat semua gambar</span>
                    </button>
                </div>
            </div>            
            <div class="text-center my-5" id="room-info">
                <div >
                    <h5><i class="fas fa-sign-in-alt"></i>&nbsp;Daftar Masuk Selepas: <span class="color-purple">{{ date('h:i A', strtotime($room->check_in_after)) }}</span></h5>
                </div>
                <div >
                    <h5><i class="fas fa-sign-out-alt"></i>&nbsp;Daftar Keluar Sebelum: <span class="color-purple">{{ date('h:i A', strtotime($room->check_out_before)) }}</span></h5>
                </div>
                <div>
                    <h5><i class="fas fa-user-friends"></i>&nbsp;Bilangan Tetamu: <span class="color-purple">{{$room->roompax}}</span></h5>
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

                        {{-- for booking type = room --}}
                        @if($room->booking_type == 'room')
                        <h5 class="text-white mb-2">RM{{$room->price}}/malam untuk 1 unit</h5>
                        <div>
                        <div class="form-floating mb-2" >
                            <input type="number" min="1" max="{{$room->room_no}}" autocomplete="off" name="bookRoom" id="book-room" class="form-control" placeholder=" "  required>
                            <label for="book-room">Masukkan Jumlah Unit (Max: {{$room->room_no}})</span>
                        </div>   
                        <div class="d-flex justify-content-center align-items-center mb-2">
                            <button type="button" class="btn-fancy" id="btn-fetch-dates" style="border-radius: 0.25rem;">
                                <span class="btn-fancy-content">Cari Slot Tempahan</span> 
                            </button>                             
                        </div>
                           
                        </div>

                        @else
                        <h5 class="text-white mb-2">RM{{$room->price}}/malam</h5>

                        @endif

                        <div class="form-floating mb-2" >
                            <input type="text" autocomplete="off" name="checkIn" id="check-in" class="form-control" placeholder=" " {{$room->booking_type == "room" ? 'disabled' : ''}}  readonly>
                            <label for="check-in">Pilih Daftar Masuk</span>
                        </div>
                        <div class="form-floating mb-2" >
                            <input type="text" autocomplete="off"  name="checkOut" id="check-out" class="form-control" placeholder=" " disabled  readonly>
                            <label for="check-out">Pilih Daftar Keluar</label>
                        </div>
                        <div class="text-white text-center mb-2">
                            <div id="total-price"></div>
                        </div>
                        <div class="d-flex justify-content-end">
                            @csrf
                            <input type="text" name="amount" id="amount" value="0" hidden>
                            <input type="text" name="discountAmount" id="discountAmount" value="0" hidden> 
                            <input type="text" name="increaseAmount" id="increaseAmount" value="0" hidden> 
                            <input type="text" name="discountDates" id="discountDates" value="" hidden> 
                            <input type="text" name="increaseDates" id="increaseDates" value="" hidden> 
                            <input type="number" name="nightCount" id="nightCount" value="0" hidden> 

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


            <section aria-labelledby="Review Section">
                <h3 class="color-dark-purple mb-1"><i class="fas fa-comments"></i>&nbsp;&nbsp;Nilaian Pelanggan</h3>
                @if($customerReviewsCount > 0)
                    <h4 class="color-dark-purple">            <span class="rated">
                            {{$customerReviewsRating}} &#9733
                        </span>({{$customerReviewsCount}} Nilaian)
            
                    </h4>
                @endif
                <div id="user-review">
                    @include('homestay.review_data')
                </div>
            </section>

            <div class="modal" tabindex="-1" id="modal-review-image">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" id="btn-close-review-image"><i class="far fa-times-circle"></i></button>
                        </div>
                        <div class="modal-body">
                            <div class="img-modal-review-container">
                                <button type="button" class="btn-slideshow" id="btn-previous-image"><i class="far fa-arrow-alt-circle-left"></i></button>
                                <button type="button" class="btn-slideshow" id="btn-next-image"><i class="far fa-arrow-alt-circle-right"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="color-dark-purple"><i class="fas fa-map"></i>&nbsp;&nbsp;Lokasi Penginapan</h3>
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
                        <a href="{{URL('../'. $roomImage)}}" class="fancybox" rel="lightbox">
                            <img  src="{{URL('../'. $roomImage)}}" class="zoom"  alt="">
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
    $('.navbar-header > div:first-child()').after(`
        <img src="{{URL('assets/homestay-assets/images/book-n-stay-logo(transparent).png')}}"  height="70px">
    `);
    $(".fancybox").fancybox({
        openEffect: "none",
        closeEffect: "none"
    });
    
    $(".zoom").hover(function(){
		
		$(this).addClass('transition');
	}, function(){
        
		$(this).removeClass('transition');
	});
    $('#btn-gallery').on('click', function(){
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
    // book-room
    function resetFields(){
        $('#check-in').val('');
        $('#check-out').val('');
        $('#check-in').prop('disabled', true);
        $('#check-out').prop('disabled', true);
        //reset input fields
        $('#discountAmount').val(0);
        $('#increaseAmount').val(0); 
        $('#discountDates').val('');
        $('#increaseDates').val('');
        $('#total-price').html('');
    }
    let delayTimer;
    $('#book-room').on('input', function(){
        resetFields();
    })
    $('#btn-fetch-dates').on('click', function() {
        var bookRoomInput = parseInt($('#book-room').val());
        var checkInInput = $('#check-in');
        var checkOutInput = $('#check-out');
        resetFields();
        if (isNaN(bookRoomInput)||bookRoomInput === '' || bookRoomInput > $('#book-room').attr('max') || bookRoomInput < 1) {
            checkInInput.prop('disabled', true);
            checkOutInput.prop('disabled', true);
            Swal.fire(`Sila pastikan jumlah unit yang ingin ditempah adalah antara ${1} dan ${$('#book-room').attr('max')}`);
        } else {
            // // Clear previous timeout to avoid multiple AJAX calls
            // clearTimeout(delayTimer);
            
            // // Add a delay before destroying and initializing datepickers
            // delayTimer = setTimeout(function() {
            //     if ($('#check-in').hasClass('hasDatepicker')) {
            //         $('#check-in').datepicker('destroy');
            //     }
            //     if ($('#check-out').hasClass('hasDatepicker')) {
            //         $('#check-out').datepicker('destroy');
            //     }
            //     fetchDiscountOrIncrease();
            //     initializeCheckInOut();
            // }, 300); // Adjust the delay time as needed (in milliseconds)
                if ($('#check-in').hasClass('hasDatepicker')) {
                    $('#check-in').datepicker('destroy');
                }
                if ($('#check-out').hasClass('hasDatepicker')) {
                    $('#check-out').datepicker('destroy');
                }
                fetchDiscountOrIncrease();
                initializeCheckInOut();
                // put some delay
                setTimeout(() => {
                 checkInInput.prop('disabled', false);
                }, 200);
        }
    });

    // for booking and datetimepickers
    function calculateTotalPrice(){
        const checkInDate = $('#check-in').val();
        const checkOutDate = $('#check-out').val();
        const roomId = $('#roomId').val();
        const roomNo = parseInt($('#book-room').val());
        $.ajax({
            url: "{{ route('homestay.calculateTotalPrice') }}", 
            method: "GET", 
            data: {
                checkInDate: checkInDate,
                checkOutDate: checkOutDate,
                roomId: roomId,
                roomNo: roomNo,
            },
            success: function(result) {            
                //reset input fields
                $('#discountAmount').val(0);
                $('#increaseAmount').val(0); 
                $('#discountDates').val('');
                $('#increaseDates').val('');
                if(!isNaN(roomNo)){
                    $('#total-price').html(`
                            <div class="d-flex justify-content-between align-items-center flex-wrap mb-2">
                                <h5>RM${result.roomPrice} x ${result.numberOfNights} malam x ${$('#book-room').val()} unit</h5>
                                <h5>RM${result.initialPrice}</h5>
                            </div>
                    `);                    
                }else{
                    $('#total-price').html(`
                            <div class="d-flex justify-content-between align-items-center flex-wrap mb-2">
                                <h5>RM${result.roomPrice} x ${result.numberOfNights} malam</h5>
                                <h5>RM${result.initialPrice}</h5>
                            </div>
                    `);       
                }

                $('#nightCount').val(result.numberOfNights);
                //for pricing with promotions
                if(result.initialPrice != null){

                    if(result.discountTotal > 0){
                        $('#discountAmount').val(result.discountTotal);
                        const discountDate  = result.discountDate.map(date => date + 'hb');    
                        $('#discountDates').val(discountDate);                    
                        $('#total-price').append(`
                            <div class="d-flex justify-content-between align-items-center flex-wrap mb-2">
                                <h5>Diskaun(${discountDate})</h5>
                                <h5>-RM${result.discountTotal}</h5>
                            </div>
                        `);
                    }

                    if(result.increaseTotal > 0){
                        $('#increaseAmount').val(result.increaseTotal);
                        const increaseDate  = result.increaseDate.map(date => date + 'hb');  
                        $('#increaseDates').val(increaseDate);                    
                        $('#total-price').append(`
                            <div class="d-flex justify-content-between align-items-center flex-wrap mb-2">
                                <h5>Penambahan Harga(${increaseDate})</h5>
                                <h5>+RM${result.increaseTotal}</h5>
                            </div>
                        `);                        
                    }

                }

                $('#total-price').append(`
                    <div class="d-flex justify-content-between align-items-center flex-wrap mb-2">
                        <h5>Jumlah Harga: </h5>
                        <h5>RM${result.totalPrice}</h5>
                    </div>
                `);     
                //add border bottom to the div before the div of total price
                $('#total-price div:nth-last-child(2)').addClass('border-bottom');
                $('#amount').val(result.totalPrice);   
            },
            error: function(result) {
                console.log('Error calculating total price');
            }
        });
        
    }
    let discountDates = increaseDates = [];
    const discountMap = new Map();
    const increaseMap = new Map();

    // to fetch  dates that have discount or increase price
    function fetchDiscountOrIncrease(){
        $.ajax({
            url: "{{route('homestay.fetchDiscountIncreaseDates')}}",
            method: "GET",
            data:{
                homestayId : $('#roomId').val(),
            },
            success: function(result){
                discountDates = [];
                $(result.discountDates).each((i, discount)=> {
                    discountDates.push(discount.date);
                })
                increaseDates = [];
                $(result.increaseDates).each((i, increase)=> {
                    increaseDates.push(increase.date);
                })
                // create map key pairs
                $(result.discountDates).each((i, discount)=>{
                    discountMap.set(discount.date, discount.percentage);
                });
                $(result.increaseDates).each((i, increase)=>{
                    increaseMap.set(increase.date, increase.percentage);
                });

            },
            error: function(){
                console.log('Fetch discount and increase failed');
            }
        });
    }
    function isAvailableCheckOutDate(currentDate, checkinDate) {
        var nextDate = new Date(checkinDate.getTime()); // Start from the check-in date

        while (nextDate <= currentDate) {
            nextDate.setDate(nextDate.getDate() + 1); // Move to the next day
            var formattedNextDate = $.datepicker.formatDate('dd/mm/yy', nextDate);

            if (disabledDates.indexOf(formattedNextDate) === -1) {
                return true; // Found an available check-out date
            }
        }

        return false; // No available check-out date found
    }
    function initializeDatepickers(disabledDates){
        // to get max date that's 1 year from now
        var currentDate = new Date();
        var maxDate = new Date(currentDate.getFullYear() + 1, currentDate.getMonth(), currentDate.getDate());

        // for check in datepicker
        $('#check-in').datepicker({
            dateFormat: 'dd/mm/yy',
            minDate: 0,
            maxDate: maxDate,
            beforeShowDay: function(date) {
                var formattedDate = $.datepicker.formatDate('dd/mm/yy', date);
                var isDisabled = (disabledDates.indexOf(formattedDate) !== -1);
                var cssClass = toolTip = '';
                if(discountDates.indexOf(formattedDate) !== -1){
                    cssClass = 'discount-date';
                    toolTip = "Terdapat diskaun pada hari tersebut";
                }else if(increaseDates.indexOf(formattedDate) !== -1){
                    cssClass = 'increase-date';
                    toolTip = "Terdapat penambahan harga pada hari tersebut";
                }
                    return [!isDisabled, cssClass, toolTip];
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
                        Swal.fire('Sila pastikan masa slot tempahan daftar masuk dan daftar keluar homestay adalah kosong.');
                        $('#check-in').datepicker("setDate", null);
                        $('#check-out').datepicker("setDate", null); // Clear the selected check-out date
                        $("#check-out").datepicker("option", "disabled", true);
                    }
            
                }
            }
        }); 
        function findNearestDisabledDate(checkinDate) {
            var checkinTimestamp = checkinDate.getTime(); // Convert checkinDate to timestamp for comparison

            var disabledDatesAfterCheckIn = disabledDates
                .filter(function(disabledDate) {
                    var parts = disabledDate.split('/');
                    var disabledTimestamp = new Date(parts[2], parts[1] - 1, parts[0]).getTime(); // Construct date for comparison
                    return disabledTimestamp >= checkinTimestamp;
                })
                .sort(function(a, b) {
                    var dateA = a.split('/').reverse().join('-'); // Reformat for proper sorting
                    var dateB = b.split('/').reverse().join('-');
                    return new Date(dateA) - new Date(dateB);
                });
            if (disabledDatesAfterCheckIn.length > 0) {
                return disabledDatesAfterCheckIn[0]; // Return the first disabled date after or on check-in date
            }
            
            return null; // If no disabled date is found after or on check-in date
        }
        $('#check-out').datepicker({
            dateFormat: 'dd/mm/yy',
            disabled: true,
            maxDate: maxDate,
            beforeShowDay: function(date) {
                var formattedDate = $.datepicker.formatDate('dd/mm/yy', date);
                var checkinDateObject = $('#check-in').datepicker('getDate');
                var isDisabled = (disabledDates.indexOf(formattedDate) !== -1 && checkinDateObject != null && formattedDate != findNearestDisabledDate(checkinDateObject));//even if the next day is disabled, still can checkout
                var cssClass = toolTip = '';
                if(isDisabled) {
                    toolTip = 'Terdapat tempahan untuk hari tersebut'; 
                }else if(discountDates.indexOf(formattedDate) !== -1){
                    cssClass = 'discount-date';
                    toolTip = "Terdapat diskaun pada hari tersebut";
                }else if(increaseDates.indexOf(formattedDate) !== -1){
                    cssClass = 'increase-date';
                    toolTip = "Terdapat penambahan harga pada hari tersebut";
                }

                return [!isDisabled,cssClass,toolTip];
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
                     $("#check-out").datepicker("option", "disabled", true);

                }
            }
        });


        // Function to check for disabled dates between check-in and check-out
        function checkDisabledDatesBetween() {
            var checkInDate = $('#check-in').datepicker('getDate');
            var checkOutDate = $('#check-out').datepicker('getDate');
            checkOutDate = moment($.datepicker.formatDate('dd/mm/yy', checkOutDate) ,'DD/MM/YYYY');
            var isDisabledFound = false;
            let firstNearestDateToCheckIn  = findNearestDisabledDate(checkInDate);
            if(firstNearestDateToCheckIn){
                firstNearestDateToCheckIn = moment(firstNearestDateToCheckIn.toString() ,'DD/MM/YYYY');
                if( checkOutDate.isSameOrBefore(firstNearestDateToCheckIn)){
                    isDisabledFound = false;
                }else{
                    // if checkoutDate is after the first disabled dates
                    isDisabledFound = true;
                }                
            }

            return isDisabledFound;
        }
    }
    function initializeCheckInOut(){        
        let roomId = $('#roomId').val();
        let roomNo = $('#book-room').val();

        $.ajax({
            url: "{{route('homestay.fetchUnavailableDates')}}",
            method: "GET",
            data: {
                roomId: roomId,
                roomNo: roomNo,
            },
            success: function(result){
                var disabledDates = result.disabledDates;
                if(typeof disabledDates != 'array'){
                    disabledDates = Object.values(disabledDates);
                }
                initializeDatepickers(disabledDates);
                let tempData = JSON.parse(sessionStorage.getItem('checkInOut'));
                if (tempData && tempData.current_url == window.location.href) {
                    // Set the datepicker dates
                    $('#check-in').datepicker("setDate", tempData.check_in);
                    $('.ui-datepicker-current-day').click();
                    $('#check-out').datepicker("setDate", tempData.check_out);
                    $('.ui-datepicker-current-day').click();

                    // Remove the data from session storage
                    sessionStorage.removeItem('checkInOut');
                }


            },
            error: function(result){
                console.log('Fetch Disabled Dates Error');
            }
        });
    }
    fetchDiscountOrIncrease();
    initializeCheckInOut();

    $('#check-in , #check-out').on('click focus',function(){
        // reset all price text
        $('.price-text').remove();
        $(".ui-datepicker-calendar .ui-state-default").each(function() {
            var currentDay = $(this).html() < 10 ? '0' + $(this).html()  :$(this).html();
            var currentMonth = (parseInt($(this).parent().attr('data-month'))+1) < 10 ?'0'+(parseInt($(this).parent().attr('data-month'))+1) :(parseInt($(this).parent().attr('data-month'))+1)
            var currentDate = currentDay + "/" + currentMonth +"/" + parseInt($(this).parent().attr('data-year'));
            // if the date is not disabled
            if(!$(this).parent().hasClass('ui-datepicker-unselectable')){
                //add custom text to date cell   
                let percentage = priceAfterDiscount = 0;  
                if(increaseMap.get(currentDate) != undefined){
                    // if has increase promotion
                    percentage = increaseMap.get(currentDate);
                    priceAfterDiscount = parseFloat($('#roomPrice').val()) + ( parseFloat($('#roomPrice').val()) * percentage /100);
                    $(this).after(`<div class="price-text">RM${Math.round(priceAfterDiscount)}</div>`);   
                }else if(discountMap.get(currentDate) != undefined){
                    // if has discount promotion
                    percentage = discountMap.get(currentDate);
                    priceAfterDiscount = parseFloat($('#roomPrice').val()) - ( parseFloat($('#roomPrice').val()) * percentage /100);
                    $(this).after(`<div class="price-text">RM${Math.round(priceAfterDiscount)}</div>`);                

                }else{
                    $(this).after(`<div class="price-text">RM${Math.round(parseFloat($('#roomPrice').val()))}</div>`);                
                }
            }else{
                $(this).after(`<div class="price-text">&nbsp</div>`);                
            }

        });
    })

    $('#form-book').on('submit',function(e){
       let isUserNotLoggedIn =  "{{Auth::guest()}}";
       let storedSessionData = sessionStorage.getItem('checkInOut');

        if($('#amount').val() == 0 || $('#check-in').val() == '' || $('#check-out').val() == ''){
            e.preventDefault();
            Swal.fire('Sila pilih masa daftar masuk dan daftar keluar sebelum membuat tempahan');
        }else if( isUserNotLoggedIn && !storedSessionData){
            // the the user is not logged in
            e.preventDefault();
                Swal.fire({
                    title: 'Sila <b>Daftar/Login</b> dahulu sebelum membuat sebarang tempahan',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Baik',
                    cancelButtonText: 'Kembali'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Store the current URL in the session
                        let checkInOut = {
                            check_in: $('#check-in').val(),
                            check_out: $('#check-out').val(),
                            current_url: window.location.href,
                        }
                        sessionStorage.setItem('checkInOut', JSON.stringify(checkInOut)); // Set redirect_url in sessionStorage
                        $('#form-book').submit();
                    }
                })
            }
    });

    // for review pagination
    function getMoreReviews(page){
        
        $.ajax({
            url: "{{route('homestay.getMoreReviews')}}" + "?page=" + page,
            method: 'GET',
            data:{
                roomId: $('#roomId').val(),
            },
            success: function(data){
                $('#user-review').html(data);
            },
            error: function(xhr, status, error) {
                var errorMessage = xhr.status + ': ' + xhr.statusText;
                console.log('Get More Review Error: ' + errorMessage);
            }
        });
    }
    $(document).on('click','.pagination a' ,function(e){
        e.preventDefault();
        // get the page number 
        var page = $(this).attr('href').split('page=')[1];
        getMoreReviews(page);
    });
    // for review images
    var reviewImagesSrc = [];
    var currentImageIndex = totalImages = 0;
    $(document).on('click','.img-review',function(){
        $('.img-modal-review-container img.img-modal-review').remove();
        // get all the images for a review
        var images = $(this).parent('.img-review-container').children('.img-review');
        reviewImagesSrc = images.map((i,img) =>{
            return img.src;
        });
        totalImages = images.length;
        currentImageIndex = $(this).attr('data-counter');
        $('.img-modal-review-container #btn-next-image').before(`
            <img src="${$(this).attr('src')}" class="img-modal-review">
        `);
        $('#modal-review-image').modal('show');
    });
    $('#btn-next-image').on('click',function(){
        currentImageIndex++;
        if(currentImageIndex >= totalImages){
            currentImageIndex = 0;
        }
        currentImageSrc = reviewImagesSrc[currentImageIndex];

        $('.img-modal-review').remove();
        $('.img-modal-review-container #btn-next-image').before(`
             <img src="${currentImageSrc}" class="img-modal-review">
        `);
    });
    $('#btn-previous-image').on('click',function(){
        currentImageIndex--;
        if(currentImageIndex < 0){
            currentImageIndex = totalImages - 1;
        }
        currentImageSrc = reviewImagesSrc[currentImageIndex];

        $('.img-modal-review').remove();
        $('.img-modal-review-container #btn-next-image').before(`
             <img src="${currentImageSrc}" class="img-modal-review">
        `);
    });
    $('#btn-close-review-image').on('click',function(){
        reviewImages = [];
        currentImageIndex =  totalImages = 0;
        $(this).parents('.modal').modal('hide');
    });
    $('.alert').delay(3000).fadeOut()

    

});


function copyReferralLink(){
        $.ajax({
        method: 'GET',
        url: "{{route('point.getReferralCode')}}",
        success: function(data) {
            var currentURL = window.location.href;
            var urlWithoutParams = currentURL.split('?')[0];
            console.log(data);
            copyToClipboard(urlWithoutParams+'?referral_code='+data.referral_code,true);

        },
        error: function (data) {
            var currentURL = window.location.href;
            var urlWithoutParams = currentURL.split('?')[0];
            copyToClipboard(urlWithoutParams,false);
        }
    });
    }


    function copyToClipboard(text,isReferralCode) {
    // Create a temporary input element
    var input = document.createElement('input');
    
    // Set its value to the text that needs to be copied
    input.style.position = 'absolute';
    input.style.left = '-9999px';
    input.value = text;
    
    
    // Append it to the body
    document.body.appendChild(input);
    
    // Select the text inside the input element
    input.select();
    
    // Execute the copy command
    document.execCommand('copy');
    
    // Remove the input element from the DOM
    document.body.removeChild(input);
    if(isReferralCode){
        alert('Url with your referral code copied');
      
    }else{
        alert('Url copied, login to get the referral code');
       
        
    }
   
   

// Set a timeout to hide the alert after 3 seconds

    
}

</script>
@endsection