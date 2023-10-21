@extends('layouts.master')

@section('css')

<link rel="stylesheet" href="{{ URL::asset('assets/css/datatable.css')}}">
@include('layouts.datatable')
<link rel="stylesheet" href="{{ URL::asset('assets/homestay-assets/style.css')}}">

<style>
:root {
    --transition: all 0.3s linear;
}
.main-content{
    color: var(--primary-color);
}


.loading {
  width: 35px;
  height: 35px;
  display:none;
}
/* for submit button */
.submit-btn {
      border: none;
      background: none;
  }
  .submit-btn span {
      color:var(--primary-color);
      padding: 8px;
      font-family: Roboto, sans-serif;
      font-size: 17.5px;
      text-transform: uppercase;
  }
  .submit-btn svg {
      transform: translateX(-8px);
      transition: all 0.3s ease;
  }
  .submit-btn:hover svg {
      transform: translateX(0);
  }
  .submit-btn:active svg {
      transform: scale(0.9);
  }
  .hover-underline-animation {
      position: relative;
      color:var(--primary-color);
      padding-bottom: 20px;
  }
  .hover-underline-animation:after {
      content: "";
      position: absolute;
      width: 100%;
      transform: scaleX(0);
      height: 2px;
      bottom: 0;
      left: 0;
      background-color: var(--primary-color);
      transform-origin: bottom right;
      transition: transform 0.25s ease-out;
  }
  .submit-btn:hover .hover-underline-animation:after {
      transform: scaleX(1);
      transform-origin: bottom left;
  }

  @media screen and (max-width: 500px){
    .card{
      width: 100%!important;
      padding: 6px!important;
    }
    .container-fluid{
      padding-left: 0!important;
      padding-right: 0!important;
    }
    .border-white{
      margin: 0!important;
    }
  }
</style>

@endsection

@section('content')

<div class="container">
  <div class="row d-flex justify-content-center align-items-center">
    <div class="col">
      <div class="d-flex justify-content-center align-items-center">
        <span class="h2 m-4 color-purple">Tempahan Homestay</span>
      </div>

      <div class="card">
        <div class="card-body border-purple">

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
 
        
      <form action="{{ route('fpxIndex') }}" method="POST" enctype="multipart/form-data" id="form-checkout">
        @csrf
        <input type="hidden" name="desc" id="desc" value="Homestay">
        <input type="hidden" name="bookingid" id = "bookingid" value="{{ $checkoutDetails['bookingId'] }}">
        <input type="hidden" name="amount" id="amount" value="{{ $checkoutDetails['totalPrice'] }}">
        <div class="row d-flex justify-content-center">
          <div class="border-white col-md-6 mb-2 mx-2 mr-2">
              <h4>{{$checkoutDetails['homestay']->roomname}}</h4>
              <div class="row">
                <div class="col-md-4">
                  <img src="{{$checkoutDetails['homestayImage']}}" alt="Image Thumbnail" class="img-fluid d-block mx-auto">
                </div>
                <div class="col-md-8">
                  <h6 class="color-dark-purple mb-1"><span><i class="fas fa-map-marker-alt"></i></span> {{$checkoutDetails['homestay']->address}}, {{$checkoutDetails['homestay']->area}}, {{$checkoutDetails['homestay']->postcode}}, {{$checkoutDetails['homestay']->district}}, {{$checkoutDetails['homestay']->state}}</h6>
                  <h6><span class="color-dark-purple mb-1">Room Pax: </span>{{$checkoutDetails['homestay']->roompax}}</h6>
                  <h6><span class="color-dark-purple mb-1">Daftar Masuk: </span>{{$checkoutDetails['checkInDate']}}, selepas {{date('H:i', strtotime($checkoutDetails['homestay']->check_in_after))}}</h6>
                  <h6><span class="color-dark-purple mb-1">Daftar Keluar: </span>{{$checkoutDetails['checkOutDate']}}, sebelum {{date('H:i', strtotime($checkoutDetails['homestay']->check_out_before))}}</h6>
                </div>
              </div>
          </div>    
          <div class="border-white col-md-5 mb-2" id="checkout-price-container">
            <h4>Butiran Harga</h4>
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-1">
              <h5 class="color-dark-purple">RM{{$checkoutDetails['homestay']->price}} x {{$checkoutDetails['nightCount']}} malam</h5>
              <h5 class="color-purple">RM{{number_format($checkoutDetails['homestay']->price * $checkoutDetails['nightCount'],2)}}</h5>
            </div>
            @if($checkoutDetails['discountAmount'] > 0)
              <div class="d-flex justify-content-between align-items-center flex-wrap mb-1">
                <h5 class="color-dark-purple">Diskaun({{$checkoutDetails['discountDates']}})</h5>
                <h5 class="color-purple">-RM{{number_format($checkoutDetails['discountAmount'],2)}}</h5>
              </div>
            @endif 
            @if($checkoutDetails['increaseAmount'] > 0)
              <div class="d-flex justify-content-between align-items-center flex-wrap mb-1">
                <h5 class="color-dark-purple">Penambahan Harga({{$checkoutDetails['increaseDates']}})</h5>
                <h5 class="color-purple">+RM{{number_format($checkoutDetails['increaseAmount'],2)}}</h5>
              </div>
            @endif 
            <div class="d-flex justify-content-between align-items-center mb-1">
              <h5 class="color-dark-purple">Jumlah Harga:</h5>
              <h5 class="color-purple">RM{{$checkoutDetails['totalPrice']}}</h5>
            </div>
          </div>        
        </div>

      <div class="row d-flex justify-content-center mb-2">
        <div class="col-md-6 border-white">
          <div class="form-group">
            <div id="invalid-bank-message">
            </div>
            <label class="color-dark-purple">Online Banking</label>
            <select name="bankid" id="bankid" class="form-control"
                data-parsley-required-message="Sila pilih bank" required>
                <option value="" disabled selected>Pilih bank</option>
            </select>
          </div>          
        </div>
      </div>
        
        <div class="row mb-2">
          <div class="col d-flex justify-content-end">
            <a href="{{ url()->previous() }}" type="button" class="btn-lg btn-light mr-2" style="color:#391582;">KEMBALI</a>
            <button class="submit-btn" type="submit">
              <span class="hover-underline-animation">Bayar</span>
          </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>


@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  $(document).ready(function(){

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    var arr = [];
    
    $.ajax({
        type: 'GET',
        dataType: 'json',
        url: "/fpx/getBankList",
        success: function(data) {
            jQuery.each(data.data, function(key, value){
                arr.push(key);
            });
            for(var i = 0; i < arr.length; i++){
                arr.sort();
                $("#bankid").append("<option value='"+data.data[arr[i]].code+"'>"+data.data[arr[i]].nama+"</option>");
            }

        },
        error: function (data) {
            // console.log(data);
        }
    });

    // add border bottom before amount to pay
    $('#checkout-price-container > div:nth-last-child(2)').addClass('border-bottom')

    $('button[type="submit"]').on('click', function(e){
      e.preventDefault();
      if($('#bankid').val() != null){
        Swal.fire({
          title: 'Are you sure?',
          text: "You will be redirected to website of the selected online banking",
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, proceed with payment!'
        }).then((result) => {
          if (result.isConfirmed) {
            $('#form-checkout').submit();
          }
        })        
      }else{
        $('#invalid-bank-message').html(`
          <div class="alert alert-danger text-center">Sila Pilih Bank</div>
        `);
      }
    })

    $('.alert-success').delay(2000).fadeOut()
    $('.alert-danger').delay(4000).fadeOut()
  });
</script>

@endsection