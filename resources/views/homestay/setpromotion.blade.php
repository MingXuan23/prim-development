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
            <h4 class="font-size-18 page-title color-purple" ><span><a href="{{route('homestay.promotionPage')}}" class="color-dark-purple">Urus Promosi >> </a></span>Tambah Promosi</h4>
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
            <form method="post" action="{{route('homestay.insertpromotion')}}" enctype="multipart/form-data"
                class="form-validation" id="form-promotion">
                {{csrf_field()}}
                <input type="hidden" name="org_id" value="{{$orgId}}">
                <div class="card-body">
                    @foreach($homestays as $homestay)
                        <input type="hidden" value={{$homestay->price}} id="{{$homestay->roomid}}_price">
                    @endforeach
                    <div class="mb-2">
                        <label for="homestay_id">Nama Homestay</label>
                        <select name="homestay_id" id="homestay_id" class="form-select" required>
                            <option selected disabled>Pilih Homestay</option>
                            <option value="all">Semua Homestay</option>
                            @foreach($homestays as $homestay)
                                <option value="{{$homestay->roomid}}">{{$homestay->roomname}}</option>                                
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2 text-center color-dark-purple" id="current-price">

                    </div>
                    <div class="mb-2">
                        <label for="promotion_name">Nama Promosi</label>
                        <input type="text" name="promotion_name" id="promotion_name" class="form-control" required>
                    </div>
                    <div class="mb-2 row">
                        <div class="col-md-6">
                            <label for="promotion_start">Hari Pertama</label>
                            <input type="text" name="promotion_start" id="promotion_start" class="form-control" autocomplete="off" required>
                        </div>
                        <div class="col-md-6">
                            <label for="promotion_end">Hari Terakhir</label>
                            <input type="text" name="promotion_end" id="promotion_end" class="form-control" autocomplete="off" disabled required>
                        </div>
                    </div>
                    <div class="mb-2 row">
                        <div class="col-md-6">
                            <label>Jenis Promosi</label>
                            <div class="form-check">
                                <div>
                                    <input type="radio" name="promotion_type" id="discount" value="discount" class="form-check-input" required>
                                    <label for="discount"  class="form-check-label">Diskaun</label>                                    
                                </div>
                                <div>
                                    <input type="radio" name="promotion_type" id="increase" value="increase" class="form-check-input" required>
                                    <label for="increase"  class="form-check-label">Naik Harga</label>                                    
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="promotion_percentage">Peratusan Promosi(%)</label>
                            <input type="text" name="promotion_percentage" id="promotion_percentage" class="form-control" required>
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
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });    
    function calculatePrice(){
        const homestayId = $('#homestay_id').val();
        if(homestayId != "all"){
            let oriPrice = Number.parseFloat($(`#${homestayId}_price`).val());
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
        }else{
            $('#actual-price').empty();
        }

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
        let roomId = $('#homestay_id').val();
        $.ajax({
            url: "{{route('homestay.fetchUnavailablePromotionDates')}}",
            method: "GET",
            data: {
                roomId: roomId,
            },
            success: function(result){
                const disabledDates = result.disabledDates;
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
                        $("#promotion_end").datepicker("option", "disabled", false);

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
                    disabled: true,
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
    $('#homestay_id').on('change',function(){
        const homestayId = $(this).val();
        if(homestayId != "all"){
            const price = $(`#${homestayId}_price`).val();
            $('#current-price').empty();
            $('#current-price').html(`
                <h5>Harga Asal(per malam) = RM${price}</h5>
            `);            
        }else{
            $('#current-price').empty();
        }

        resetForm();
        initializeStartEnd();
    });
    $('input[name="promotion_type"]').on('change',function(){
        if($(this).prop('checked') && !$('#promotion_percentage').prop('disabled')){
            calculatePrice();
        }
        $('#promotion_percentage').prop('disabled', false);
    });
    $('#promotion_percentage').on('blur',function(){
        if(isNaN($(this).val())){
            Swal.fire('Sila masukkan nilai yang betul');
            return;
        }else if( $(this).val() <= 0 || $(this).val() > 100){
            Swal.fire('Sila masukkan nilai peratusan antara 0 - 100');
            return;
        }else{
            calculatePrice();
        }
    })
    $("#homestay_id option:nth-child(3)").prop("selected", true);
    $('#homestay_id').trigger('change');
    $('.alert').delay(3000).fadeOut()
});

</script>
@endsection