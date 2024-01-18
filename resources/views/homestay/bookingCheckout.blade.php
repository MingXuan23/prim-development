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
footer {
    background-color: var(--primary-color) !important;
    color: white !important;
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
  .payment-type{
    display: block;
    color: #391582cc;
    border: 2px solid #391582cc;
    transition: all 0.2s ease-in-out;
    cursor: pointer;
  }
  .payment-type:hover{
    border: 2px solid var(--secondary-color);
    color: var(--secondary-color);
  }
  .payment-type:hover > i {
    animation: expandContract 2s ease-in-out infinite;
  }
  @keyframes expandContract{
    50%{
      transform: translateX(2px)  translateY(-5px) scale(1.25);
    }
  }
input[type = 'radio']:checked + .payment-type{
    border: 2px solid var(--primary-color)!important;
    color: var(--primary-color)!important;
  }
  .payment-disabled-type{
    color: #3915827b;
    border: 2px solid #3915827b;
  }
  #container-payment-option{
    margin-right: 12px!important;
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
    .page-content{
        padding: 80px 0!important;
    }
    .container{
      padding-left: 0!important;
      padding-right: 0!important;
    }
    #container-payment-option{
      margin-right:0!important;
      margin-bottom: 6px!important;
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
 
        
      <form action="{{ route('directpayIndex') }}" method="POST" enctype="multipart/form-data" id="form-checkout">
        @csrf
        <input type="hidden" name="desc" id="desc" value="Homestay">
        <input type="hidden" name="bookingid" id = "bookingid" value="{{ $checkoutDetails['bookingId'] }}">
        <input type="hidden" name="amount" id="amount" value="{{ $checkoutDetails['totalPrice'] }}">
        <div class="row d-flex justify-content-center ">
          <div class="border-white col-md-6" id="container-payment-option">
            <div class="h3 mb-4">Pilih Cara Pembayaran</div>
            <input type="radio" name="paymentType" id="payment-full" value="full" checked hidden>
            <label for="payment-full" class="payment-type my-2 p-4 h3">Pembayaran Penuh &nbsp;<i class="fas fa-hand-holding-usd"></i>
            </label>
              @if($checkoutDetails['depositCharge'] > 0)
               <input type="radio" name="paymentType" id="payment-deposit" value="deposit" hidden>
               <label for="payment-deposit" class="my-2 payment-type p-4 h3">Pembayaran Deposit({{$checkoutDetails['depositCharge']}}%) <i class="fas fa-money-bill-alt"></i>
                <div class="h5">Baki Perlu Dibayar Sebelum Daftar Keluar</div>
              </label>
              @else
                <div for="payment-deposit" class="my-2 payment-disabled-type p-4 h3" disabled>Pembayaran Deposit Tidak Tersedia
                 </div>
              @endif
          </div>    
          <div class="border-white col-md-5" id="checkout-price-container">
            <h4>{{$checkoutDetails['homestay']->roomname}}</h4>
            <div class="row my-3">
              <div class="col-md-4">
                <img src="{{URL($checkoutDetails['homestayImage'])}}" alt="Image Thumbnail" class="img-fluid d-block mx-auto">
              </div>
              <div class="col-md-8 text-justify">
                <h6 class="color-dark-purple"><span><i class="fas fa-map-marker-alt"></i></span> {{$checkoutDetails['homestay']->address}}, {{$checkoutDetails['homestay']->area}}, {{$checkoutDetails['homestay']->postcode}}, {{$checkoutDetails['homestay']->district}}, {{$checkoutDetails['homestay']->state}}</h6>
                <h6><span class="color-dark-purple mb-1"><i class="fas fa-user-friends"></i> Bilangan Tetamu: </span>{{$checkoutDetails['homestay']->roompax}}</h6>
                @if($checkoutDetails['bookedRooms'] != null)
                <h6><span class="color-dark-purple mb-1"><i class="fas fa-door-closed"></i> Jumlah Unit: </span>{{$checkoutDetails['bookedRooms']}}</h6>
                @endif
                <h6><span class="color-dark-purple mb-1"><i class="fas fa-sign-in-alt"></i> Daftar Masuk: </span>{{$checkoutDetails['checkInDate']}}, selepas {{date('H:i', strtotime($checkoutDetails['homestay']->check_in_after))}}</h6>
                <h6><span class="color-dark-purple mb-1"><i class="fas fa-sign-out-alt"></i> Daftar Keluar: </span>{{$checkoutDetails['checkOutDate']}}, sebelum {{date('H:i', strtotime($checkoutDetails['homestay']->check_out_before))}}</h6>
              </div>
            </div>
            <h4 class="border-top pt-3">Butiran Harga</h4>
            <div class="d-flex justify-content-between align-items-center flex-wrap my-3">
              @if($checkoutDetails['bookedRooms'] != null)
                <h5 class="color-dark-purple">RM{{$checkoutDetails['homestay']->price}} x {{$checkoutDetails['nightCount']}} malam x {{$checkoutDetails['bookedRooms']}} Unit</h5>
                <h5 class="color-purple">RM{{number_format($checkoutDetails['homestay']->price * $checkoutDetails['nightCount']* $checkoutDetails['bookedRooms'],2) }} </h5>
              @else
                <h5 class="color-dark-purple">RM{{$checkoutDetails['homestay']->price}} x {{$checkoutDetails['nightCount']}} malam</h5>
                <h5 class="color-purple">RM{{number_format($checkoutDetails['homestay']->price * $checkoutDetails['nightCount'],2)}} </h5>
              @endif
            </div>
            @if($checkoutDetails['discountAmount'] > 0)
              <div class="d-flex justify-content-between align-items-center flex-wrap my-3">
                <h5 class="color-dark-purple">Diskaun({{$checkoutDetails['discountDates']}})</h5>
                <h5 class="color-purple">-RM{{number_format($checkoutDetails['discountAmount'],2)}}</h5>
              </div>
            @endif 
            @if($checkoutDetails['increaseAmount'] > 0)
              <div class="d-flex justify-content-between align-items-center flex-wrap my-3">
                <h5 class="color-dark-purple">Penambahan Harga({{$checkoutDetails['increaseDates']}})</h5>
                <h5 class="color-purple">+RM{{number_format($checkoutDetails['increaseAmount'],2)}}</h5>
              </div>
            @endif 
            <div class="d-flex justify-content-between align-items-center mt-3">
              <h5 class="color-dark-purple">Jumlah Harga:</h5>
              <h5 class="color-purple">RM{{$checkoutDetails['totalPrice']}}</h5>
            </div>
          </div>        
        </div>

      {{-- <div class="row d-flex justify-content-center mb-3">
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
         --}}


        <div class="row mb-2 p-0">
          <div class="col d-flex justify-content-end my-1">
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
    $('.navbar-header > div:first-child()').after(`
        <img src="{{URL('assets/homestay-assets/images/book-n-stay-logo(transparent).png')}}" height="70px">
    `);
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    var arr = [];
    

    // add border bottom before amount to pay
    $('#checkout-price-container > div:nth-last-child(2)').addClass('border-bottom')

    $('button[type="submit"]').on('click', function(e){
      e.preventDefault();
        Swal.fire({
          title: 'Adakah anda pasti?',
          text: "Anda akan dialihkan ke payment gateway",
          footer: "<small style='color:#f14343;' >Tempahan dalam sistem kami adalah tidak boleh dipulangkan wang, dan sebarang pemulangan perlu diuruskan antara tuan rumah dan tetamu.</small>",
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Ya, teruskan dengan pembayaran!'
        }).then((result) => {
          if (result.isConfirmed) {
            $('#form-checkout').submit();
          }
        })        
    })

    $('.alert-success').delay(2000).fadeOut()
    $('.alert-danger').delay(4000).fadeOut()

    // for payment types
    $('input[name="paymentType"]').on('change', function(){
      if($(this).val() == 'deposit'){
        var depositCharge = {{$checkoutDetails['depositCharge']}};
        var totalPrice  = "{{$checkoutDetails['totalPrice']}}";

        totalPrice = totalPrice.replace(/,/g , '');
        totalPrice = parseFloat(totalPrice);

        var depositAmount = totalPrice * depositCharge / 100;
        var balanceAmount = totalPrice - depositAmount;
        $('#checkout-price-container ').append(`
          <div class="price-deposit d-flex justify-content-between align-items-center mt-3">
                <h5 class="color-dark-purple">Deposit Perlu Dibayar(${depositCharge}%)</h5>
                <h5 class="color-purple">RM${depositAmount.toFixed(2)}</h5>
          </div>
          <div class="price-deposit d-flex justify-content-between align-items-center mt-3" >
                <h5 class="color-dark-purple">Baki</h5>
                <h5 class="color-purple">RM${balanceAmount.toFixed(2)}</h5>
          </div>
        `);
      }else{
        $('#checkout-price-container > div.price-deposit').remove();
      }
    });
  });
</script>

@endsection