@extends('layouts.master')

@section('css')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <link rel="stylesheet" href="{{URL::asset('assets/homestay-assets/jquery-ui-datepicker.theme.min.css')}}">
  <link rel="stylesheet" href="{{URL::asset('assets/homestay-assets/jquery-ui-datepicker.structure.min.css')}}">
  <link rel="stylesheet" href="{{ URL::asset('assets/homestay-assets/style.css')}}">

@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18 page-title color-purple" ><span><a href="{{route('homestay.promotionPage')}}" class="color-dark-purple">Urus Promosi >> </a></span>Edit Promosi</h4>
        </div>
    </div>
</div>
<div class="promotion-container border-purple p-0">
    <div class="col-md-12 p-0">
        <div class="card card-primary mb-0">

        @if(count($errors) > 0)
      <div class="alert alert-danger">
        <ul>
          @foreach($errors->all() as $error)
          <li>{{$error}}</li>
          @endforeach
        </ul>
      </div>
      @endif
            
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
            <form method="post" action="{{route('homestay.updatePromotion')}}" enctype="multipart/form-data"
                class="form-validation" id="form-promotion">
                {{csrf_field()}}
                <input type="hidden" name="promotion_id" id="promotion_id" value="{{$promotion->promotionid}}">
                <input type="hidden" name="homestay_id" id="homestay_id" value="{{$promotion->roomid}}">
                <input type="hidden" name="homestay_price" id="homestay_price" value="{{$promotion->price}}">
                <div class="card-body">
                    <div class="mb-2">
                        <h3>{{$promotion->roomname}}</h3>
                    </div>
                    <div class="mb-2 text-center color-dark-purple" id="current-price">

                    </div>
                    <div class="mb-2">
                        <label for="promotion_name">Nama Promosi</label>
                        <input type="text" name="promotion_name" id="promotion_name" value="{{$promotion->promotionname}}" class="form-control" required>
                    </div>
                    <div class="mb-2 row">
                        <div class="col-md-6">
                            <label for="promotion_start">Hari Pertama</label>
                            <input type="text" name="promotion_start" id="promotion_start" value="{{date('d/m/Y', strtotime($promotion->datefrom)) }}" class="form-control" autocomplete="off" required>
                        </div>
                        <div class="col-md-6">
                            <label for="promotion_end">Hari Terakhir</label>
                            <input type="text" name="promotion_end" id="promotion_end" value="{{date('d/m/Y' , strtotime($promotion->dateto))}}" class="form-control" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="mb-2 row">
                        <div class="col-md-6">
                            <label>Jenis Promosi</label>
                            <div class="form-check">
                                <div>
                                    <input type="radio" name="promotion_type"  id="discount" value="discount" {{ $promotion->promotion_type == 'discount' ? 'checked' : '' }} class="form-check-input" required>
                                    <label for="discount"  class="form-check-label">Diskaun</label>                                    
                                </div>
                                <div>
                                    <input type="radio" name="promotion_type" id="increase" value="increase" {{ $promotion->promotion_type == 'increase' ? 'checked' : '' }} class="form-check-input" required>
                                    <label for="increase"  class="form-check-label">Naik Harga</label>                                    
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="promotion_percentage">Peratusan Promosi(%)</label>
                            <input type="text" name="promotion_percentage" id="promotion_percentage" value="{{$promotion->promotion_type == 'discount'? $promotion->discount : $promotion->increase}}" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-2 text-center color-dark-purple" id="actual-price">

                    </div>
                    <div class="form-group mb-0">
                        <div class="text-right">
                            <a type="button" href="{{route('homestay.promotionPage')}}"
                                class="btn btn-secondary waves-effect waves-light mr-1">
                                Kembali
                            </a>
                            <button type="submit" class="btn btn-primary waves-effect waves-light mr-1">
                                Simpan
                            </button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection


@section('script')
{{-- sweet alert --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ URL::asset('assets/libs/parsleyjs/parsleyjs.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/inputmask/inputmask.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/jquery-mask/jquery.mask.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

<script>

$(document).ready(function () {
    $('.navbar-header > div:first-child()').after(`
        <img src="{{URL('assets/homestay-assets/images/book-n-stay-logo(transparent).png')}}"  height="70px">
    `);
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });    
    function calculatePrice(){
        let oriPrice = Number.parseFloat($(`#homestay_price`).val());
        let price = 0;
        if($('input[name="promotion_type"]:checked').val() == "discount"){
            price = oriPrice - (oriPrice * Number.parseFloat($('#promotion_percentage').val())/100);
        }else if($('input[name="promotion_type"]:checked').val() == "increase"){
            price = oriPrice + (oriPrice * Number.parseFloat($('#promotion_percentage').val())/100);
        }
        $('#actual-price').empty();
        $('#actual-price').html(`
            <h5>Harga Semasa Promosi(per malam) = RM${price.toFixed(2)}</h5>
        `);            
    }
    function resetForm(){
        $('#promotion_name').val('');
        $('#promotion_percentage').prop('disabled',true);
        $('#promotion_percentage').val('');
        $('#promotion_name').val('');
        $('input[name="promotion_type"]').prop('checked',false);
        $('#promotion_start').val('');
        $('#promotion_end').val('');
        $('#promotion_start').datepicker('destroy');
        $('#promotion_end').datepicker('destroy');
        $('#actual-price').empty();
    }
    function initializeStartEnd(){
        let promotionId = $('#promotion_id').val();
        let homestayId = $('#homestay_id').val();
        const price =  Number.parseFloat($(`#homestay_price`).val());
        $('#current-price').empty();
        $('#current-price').html(`
            <h5>Harga Asal(per malam) = RM${price}</h5>
        `);    
        $.ajax({
            url: "{{route('homestay.fetchUnavailableEditPromotionDates')}}",
            method: "GET",
            data: {
                promotionId: promotionId,
                homestayId: homestayId,
            },
            success: function(result){
                const disabledDates = result.disabledDates;
                var startDate = $('#promotion_start').val();
                // for check in datepicker
                $('#promotion_start').datepicker({
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
                        selectedDateObject.setDate(selectedDateObject.getDate());
                        
                        // Format the new date as 'dd/mm/yy'
                        var newMinDate = $.datepicker.formatDate('dd/mm/yy', selectedDateObject);

                        // Set the newMinDate as the minimum date for #promotion-end datepicker
                        $("#promotion_end").datepicker("option", "minDate", newMinDate);

                        // if already selected a check in date and want to choose a different check in date
                        if($("#promotion_start").datepicker('getDate') != null &&  $('#promotion_end').datepicker('getDate') != null){
                            if(checkDisabledDatesBetween()){                                
                                Swal.fire('Sila pastikan tiada sebarang promosi antara tarikh mula dan akhir');
                                $('#promotion_start').datepicker("setDate", null); 
                                $('#promotion_end').datepicker("setDate", null); // Clear the selected check-out date
                            }
                        }
                    }
                });    
                $('#promotion_end').datepicker({
                    dateFormat: 'dd/mm/yy',
                    minDate: startDate,
                    beforeShowDay: function(date) {
                        var formattedDate = $.datepicker.formatDate('dd/mm/yy', date);
                        var isDisabled = (disabledDates.indexOf(formattedDate) !== -1);
                        return [!isDisabled];
                    },
                    onSelect: function(selectedDate) {
                        if(checkDisabledDatesBetween()){
                            Swal.fire('Sila pastikan tiada sebarang promosi antara tarikh mula dan akhir');
                            $('#promotion_start').datepicker("setDate", null); 
                            $('#promotion_end').datepicker("setDate", null); // Clear the selected check-out date
                        }
                    }
                });
                // Function to check for disabled dates between check-in and check-out
                function checkDisabledDatesBetween() {
                    var startDate = $('#promotion_start').datepicker('getDate');
                    var endDate = $('#promotion_end').datepicker('getDate');

                    var currentDate = new Date(startDate);
                    endDate = new Date(endDate);
                    var isDisabledFound = false;

                    while (currentDate <= endDate) {
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

    $('input[name="promotion_type"]').on('change',function(){
        if($(this).prop('checked') && !$('#promotion_percentage').prop('disabled')){
            calculatePrice();
        }
        $('#promotion_percentage').prop('disabled', false);
    });
    $('#promotion_percentage').on('blur',function(){
        if(isNaN($('#promotion_percentage').val())){
            Swal.fire('Sila masukkan nilai yang betul');
            return;
        }else if( $('#promotion_percentage').val() <= 0 || $('#promotion_percentage').val() > 100){
            Swal.fire('Sila masukkan nilai peratusan antara 0 - 100');
            return;
        }else{
            calculatePrice();
        }
    })
    $('#form-promotion').on('submit',function(e){
        if(isNaN($('#promotion_percentage').val())){
            Swal.fire('Sila masukkan nilai yang betul');
            e.preventDefault();
            return;
        }else if( $('#promotion_percentage').val() <= 0 || $('#promotion_percentage').val() > 100){
            Swal.fire('Sila masukkan nilai peratusan antara 0 - 100');
            e.preventDefault();
            return;
        }else{
            calculatePrice();
        }
    });
    initializeStartEnd(); 
    calculatePrice();
    $('.alert').delay(3000).fadeOut()
});

</script>
@endsection