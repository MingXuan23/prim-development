@extends('layouts.master')

@section('css')
<link rel="stylesheet" href="{{ URL::asset('assets/homestay-assets/style.css')}}">
<style>
  #btn-add-image,#image-previews > img{
    width: 100px!important;
    height: 75px!important;
  }
  .img-thumbnail{
    padding: 0.15rem!important;
  }
  @media screen and (max-width: 700px){
    #booking-button-group{
      position: relative;
    }
  }
</style>
@endsection

@section('content')
    {{-- bootstrap 5 tabs --}}
    <div class="card mt-3 p-5 border-white" id="tab-container">
      <h3 class="color-purple text-center">Tempahan Anda</h3>    
      <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
          <a class="nav-link active " id="checkout-tab" data-toggle="tab" href="#checkout" role="tab" aria-controls="checkout" aria-selected="true">Daftar Keluar</a>
        </li>
        <li class="nav-item">
          <a class="nav-link " id="completed-tab" data-toggle="tab" href="#completed" role="tab" aria-controls="completed" aria-selected="false">Selesai</a>
        </li>
        <li class="nav-item">
          <a class="nav-link " id="cancelled-tab" data-toggle="tab" href="#cancelled" role="tab" aria-controls="cancelled" aria-selected="false">Batal</a>
        </li>
      </ul>
      <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="checkout" role="tabpanel" aria-labelledby="checkout-tab">
          @if(Session::has('success'))
          <div class="alert alert-success">
              <p>{{ Session::get('success') }}</p>
            </div>
          @elseif(Session::has('error'))
            <div class="alert alert-danger">
              <p>{{ Session::get('error') }}</p>
            </div>
          @endif
          <div class="card tab-card my-3" style="{{count($checkoutBookings) == 0 ? 'display:block' : 'display:none'}}">
            <div class="card-body text-center">
              Tiada tempahan yang telah dibuat untuk masa kini
            </div>
          </div>
          <form action="{{route('directpayIndex')}}" method="post" id="form-pay-balance">
            @csrf
            <input type="hidden" name="desc" id="desc" value="Homestay">
            <input type="hidden" name="bookingid" id = "bookingid">
            <input type="hidden" name="amount" id="amount">
            <input type="hidden" name="paymentType" id="paymentType">

          </form>
            @for($i = 0 ; $i < count($checkoutBookings); $i++)
              <div class="card tab-card my-3">
                  <div class="card-body row">
                    <a class="col-md-5" href="{{route('homestay.showRoom',['id' => $checkoutBookings[$i]->roomid, 'name' => $checkoutBookings[$i]->roomname])}}">
                      <img src="{{URL($checkoutImages[$i])}}" alt="{{$checkoutBookings[$i]->roomname}}" >
                    </a>  
                    <div id="booking-details-container" class="col-md-7">
                      <h4 class="color-purple">{{$checkoutBookings[$i]->roomname}}</h4>
                      <h6 class="color-dark-purple"><span><i class="fas fa-map-marker-alt"></i></span>&nbsp;&nbsp;&nbsp;{{$checkoutBookings[$i]->address}}, {{$checkoutBookings[$i]->area}}, {{$checkoutBookings[$i]->postcode}}, {{$checkoutBookings[$i]->district}}, {{$checkoutBookings[$i]->state}}</h6>
                      <h6 class="color-purple"><span class="color-dark-purple"><i class="fas fa-sign-in-alt"></i>&nbsp;&nbsp;Daftar Masuk: </span>{{date('d/m/Y',strtotime($checkoutBookings[$i]->checkin))}}, selepas {{date('H:i', strtotime($checkoutBookings[$i]->check_in_after))}}</h6>
                      <h6 class="color-purple"><span class="color-dark-purple"><i class="fas fa-sign-out-alt"></i>&nbsp;&nbsp;Daftar Keluar: </span>{{date('d/m/Y',strtotime($checkoutBookings[$i]->checkout))}}, sebelum {{date('H:i', strtotime($checkoutBookings[$i]->check_out_before))}}</h6>
                      @if($checkoutBookings[$i]->status == 'Deposited')
                        <h6 class="color-purple"><span class="color-dark-purple"><i class="fas fa-money-bill-alt"></i>&nbsp;Jumlah Dibayar(Deposit): </span>RM{{$checkoutBookings[$i]->deposit_amount}}</h6>    
                        <h6 class="color-purple"><span class="color-dark-purple"><i class="fas fa-money-check-alt"></i>&nbsp;Baki Perlu Dibayar: </span>RM{{$checkoutBookings[$i]->totalprice - $checkoutBookings[$i]->deposit_amount}}</h6>                        
                      @else
                        <h6 class="color-purple"><span class="color-dark-purple"><i class="fas fa-money-check-alt"></i>&nbsp;Jumlah Dibayar: </span>RM{{$checkoutBookings[$i]->totalprice}}</h6>
                      @endif
                      <h6 class="color-purple"><span class="color-dark-purple"><i class="far fa-id-badge"></i>&nbsp;&nbsp;&nbsp;Nombor Telefon Organisasi: </span>{{$checkoutBookings[$i]->telno}}</h6>    
                      @if($checkoutBookings[$i]->booked_rooms != null)
                      <h6 class="color-purple"><span class="color-dark-purple"><i class="fas fa-door-closed"></i>&nbsp;Jumlah Unit Ditempah: </span>{{$checkoutBookings[$i]->booked_rooms}}</h6>   
                      @endif
                      <div id="booking-button-group" class="d-flex justify-content-end align-items-center flex-wrap gap-3">
                        @if($checkoutBookings[$i]->status == 'Deposited')
                          <button class="btn-purple mx-2 btn-pay-balance" data-booking-id="{{$checkoutBookings[$i]->bookingid}}" data-amount="{{$checkoutBookings[$i]->totalprice - $checkoutBookings[$i]->deposit_amount}}">Bayar Baki</button>
                        @else
                          <button class="btn-purple mx-2 btn-checkout-room" data-booking-id="{{$checkoutBookings[$i]->bookingid}}">Daftar Keluar</button>
                        @endif

                        <a href="{{route('homestay.bookingDetails',$checkoutBookings[$i]->bookingid)}}" class="btn-dark-purple mx-2 btn-detail">Butiran</a>
                        <button class="btn btn-danger mx-2 btn-cancel-booking" data-booking-id="{{$checkoutBookings[$i]->bookingid}}">Batal</button>
                      </div>
                    </div>

                  </div>
              </div>
            @endfor
        </div>
        <div class="tab-pane fade" id="completed" role="tabpanel" aria-labelledby="completed-tab">
          <div class="card tab-card my-3" style="{{count($completedBookings) == 0 ? 'display:block' : 'display:none'}}">
            <div class="card-body text-center">
              Tiada tempahan yang telah selesai 
            </div>
          </div>
          @for($i = 0 ; $i < count($completedBookings); $i++)
            <div class="card tab-card my-3">
                <div class="card-body row">
                  <a class="col-md-5 d-flex align-items-center" href="{{route('homestay.showRoom',['id' => $completedBookings[$i]->roomid, 'name' => $completedBookings[$i]->roomname])}}">
                    <img src="{{URL($completedImages[$i])}}" alt="{{$completedBookings[$i]->roomname}}" class="img-fluid">
                  </a>  
                  <div id="booking-details-container" class="col-md-7">
                    <h4 class="color-purple">{{$completedBookings[$i]->roomname}}</h4>
                    <h6 class="color-dark-purple"><span><i class="fas fa-map-marker-alt"></i></span>&nbsp;&nbsp;&nbsp;&nbsp;{{$completedBookings[$i]->address}}, {{$completedBookings[$i]->area}}, {{$completedBookings[$i]->postcode}}, {{$completedBookings[$i]->district}}, {{$completedBookings[$i]->state}}</h6>
                    <h6 class="color-purple"><span class="color-dark-purple"><i class="fas fa-sign-in-alt"></i>&nbsp;&nbsp;Daftar Masuk: </span>{{date('d/m/Y',strtotime($completedBookings[$i]->checkin))}}, selepas {{date('H:i', strtotime($completedBookings[$i]->check_in_after))}}</h6>
                    <h6 class="color-purple"><span class="color-dark-purple"><i class="fas fa-sign-out-alt"></i>&nbsp;&nbsp;Daftar Keluar: </span>{{date('d/m/Y',strtotime($completedBookings[$i]->checkout))}}, sebelum {{date('H:i', strtotime($completedBookings[$i]->check_out_before))}}</h6>
                    <h6 class="color-purple"><span class="color-dark-purple"><i class="fas fa-money-check-alt"></i>&nbsp;Jumlah Dibayar: </span>RM{{$completedBookings[$i]->totalprice}}</h6>
                    <h6 class="color-purple"><span class="color-dark-purple"><i class="far fa-id-badge"></i>&nbsp;&nbsp;&nbsp;Nombor Telefon Organisasi: </span>{{$completedBookings[$i]->telno}}</h6>
                    <div id="booking-button-group" class="d-flex justify-content-end align-items-center flex-wrap gap-3">
                      {{-- if user haven't leave a review--}}
                      @if($completedBookings[$i]->review_star == null)
                        <button class="btn-purple mx-2 btn-review" data-booking-id="{{$completedBookings[$i]->bookingid}}">Nilai</button>
                      @else
                        {{-- for booking that already have been reviewed --}}
                        <h6 class="color-purple">Dinilai oleh anda: </h6>
                        <div class="rating">
                          @for($j = 0; $j < $completedBookings[$i]->review_star; $j++)
                            <span>&#9733</span>
                          @endfor
                        </div>
                      @endif
                      <a href="{{route('homestay.bookingDetails',$completedBookings[$i]->bookingid)}}" class="btn-dark-purple mx-2 btn-detail">Butiran</a>
                    </div>
                  </div>
                </div>
            </div>
          @endfor
        </div>
        <div class="tab-pane fade" id="cancelled" role="tabpanel" aria-labelledby="cancelled-tab">
          <div class="card tab-card my-3" style="{{count($cancelledBookings) == 0 ? 'display:block' : 'display:none'}}">
            <div class="card-body text-center">
              Tiada tempahan yang telah dibatal
            </div>
          </div>
          @for($i = 0 ; $i < count($cancelledBookings); $i++)
            <div class="card tab-card my-3">
                <div class="card-body row">
                  <a class="col-md-5 d-flex align-items-center" href="{{route('homestay.showRoom',['id' => $cancelledBookings[$i]->roomid, 'name' => $cancelledBookings[$i]->roomname])}}">
                    <img src="{{URL($cancelledImages[$i])}}" alt="{{$cancelledBookings[$i]->roomname}}" class="img-fluid">
                  </a>  
                  <div id="booking-details-container" class="col-md-7">
                    <h4 class="color-purple">{{$cancelledBookings[$i]->roomname}}</h4>
                    <h6 class="color-dark-purple"><span><i class="fas fa-map-marker-alt"></i></span>&nbsp;&nbsp;&nbsp;&nbsp;{{$cancelledBookings[$i]->address}}, {{$cancelledBookings[$i]->area}}, {{$cancelledBookings[$i]->postcode}}, {{$cancelledBookings[$i]->district}}, {{$cancelledBookings[$i]->state}}</h6>
                    <h6 class="color-purple"><span class="color-dark-purple"><i class="fas fa-sign-in-alt"></i>&nbsp;&nbsp;Daftar Masuk: </span>{{date('d/m/Y',strtotime($cancelledBookings[$i]->checkin))}}, selepas {{date('H:i', strtotime($cancelledBookings[$i]->check_in_after))}}</h6>
                    <h6 class="color-purple"><span class="color-dark-purple"><i class="fas fa-sign-out-alt"></i>&nbsp;&nbsp;Daftar Keluar: </span>{{date('d/m/Y',strtotime($cancelledBookings[$i]->checkout))}}, sebelum {{date('H:i', strtotime($cancelledBookings[$i]->check_out_before))}}</h6>
                    <h6 class="color-purple"><span class="color-dark-purple"><i class="fas fa-money-check-alt"></i>&nbsp;Jumlah Dibayar: </span>RM{{$cancelledBookings[$i]->totalprice}}</h6>
                    <h6 class="color-purple"><span class="color-dark-purple"><i class="far fa-id-badge"></i>&nbsp;&nbsp;&nbsp;Nombor Telefon Organisasi: </span>{{$cancelledBookings[$i]->telno}}</h6>
                    <div id="booking-button-group" class="d-flex justify-content-end align-items-center flex-wrap gap-3">
                      <a href="{{route('homestay.bookingDetails',$cancelledBookings[$i]->bookingid)}}" class="btn-dark-purple mx-2 btn-detail">Butiran</a>
                    </div>
                  </div>
                </div>
            </div>
          @endfor
        </div>
      </div>
    </div>
    {{--  Review Modal Dialog--}}
    
    <div class="modal fade" id="modal-review" tabindex="-1" role="dialog" aria-labelledby="Review Modal" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
              <div class="modal-content">
                  <div class="text-right cross" id="btn-close"> <i class="fa fa-times mr-2"></i> </div>
                  <div class="card-body">
                    <div id="rating-message">
                    </div> 
                    <img src="{{URL('assets/homestay-assets/images/review-icon.png')}}" height="100" width="100" class="d-block mx-auto">
                    <form action="{{route('homestay.addReview')}}" method="POST" enctype="multipart/form-data" id="form-review">
                      @csrf
                      <input type="hidden" value="" id="booking_id" name="booking_id">
                        <div class="comment-box text-center">
                        <h4 class="color-dark-purple">Beri Nilaian Anda</h4>
                        <div class="rating"> 
                          <input type="radio" name="rating" value="5" id="5" ><label for="5">☆</label> 
                          <input type="radio" name="rating" value="4" id="4" ><label for="4">☆</label> 
                          <input type="radio" name="rating" value="3" id="3" ><label for="3">☆</label> 
                          <input type="radio" name="rating" value="2" id="2" ><label for="2">☆</label> 
                          <input type="radio" name="rating" value="1" id="1" ><label for="1">☆</label> 
                        </div>
                        <div class="comment-area"> <textarea name="review_comment" class="form-control" placeholder="Beri pandangan tentang penginapan anda.(tidak wajib)" rows="4"></textarea> </div>
                        {{-- for review images --}}
                        <div class="mt-2" >
                          <div class="mb-1">Upload gambar-gambar ulasan untuk penginapan ini jika ingin<br><b>(Maximum 4 gambar)</b></div>
                          <div class="d-flex flex-wrap justify-content-center">
                            <div id="image-previews" class="d-flex justify-content-center align-items-center gap-1 flex-wrap">                          
                            </div>                           
                              <button id="btn-add-image" type="button"><i class="fas fa-images"></i></button>
                              <input type="file"  name="review_images[]" id="review_images" accept=".jpg,.jpeg,.png" multiple hidden>                               
                          </div>
                      
                        </div>


                        <div class="text-center mt-2"> <button type="submit" class="btn-purple send px-5">Hantar Nilaian &nbsp;<i class="fas fa-long-arrow-alt-right ml-1"></i></button>
                        </div>
                    </form>

                  </div>
              </div>
          </div>
      </div>
  </div>
@endsection

@section('script')
  {{-- sweet alert --}}
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
  <script>
    $(document).ready(function(){
      $('.navbar-header > div:first-child()').after(`
        <img src="{{URL('assets/homestay-assets/images/book-n-stay-logo(transparent).png')}}"  height="70px">
    `);
      // for redirecting tab
      const switchTab =JSON.parse(localStorage.getItem('switchTab'));
      if(switchTab == 'completedTab'){
        $('#completed-tab').tab('show');
        localStorage.removeItem('switchTab');
      }else if( switchTab == 'cancelledTab'){
        $('#cancelled-tab').tab('show');
        localStorage.removeItem('switchTab');        
      }

      $('.btn-checkout-room').on('click',function(){
        const bookingId = $(this).attr('data-booking-id');
          Swal.fire({
            title: 'Adakah anda pasti?',
            text: "Anda mahu selesaikan tempahan ini?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, daftar daripada homestay ini!'
          }).then((result) => {
            if (result.isConfirmed) {
              const bookingId = $(this).attr('data-booking-id');
              $.ajax({
                url: "{{route('homestay.checkoutHomestay')}}",
                method: 'POST',
                dataType: 'json',
                data: {
                  bookingId: bookingId,
                  '_token': '{{csrf_token()}}',
                },
                success: function(result){
                  console.log(result.success);
                  // set flag to redirect to completed tab after reloading page
                  localStorage.setItem('switchTab', JSON.stringify('completedTab'));
                  window.location.reload();
                },
                error:function(){
                  console.log('Checkout Room Failed');
                }
              })
              Swal.fire(
                'Tempahan Selesai!',
                'Kamu telah didaftar keluar daripada homestay ini',
                'success'
              )
            }
          })
      });
      $('.btn-pay-balance').on('click',function(){
        const bookingId = $(this).attr('data-booking-id');
        const payAmount = $(this).attr('data-amount');
          Swal.fire({
            title: 'Adakah anda pasti?',
            text: `Anda mahu bayar baki RM${parseFloat(payAmount).toFixed(2)}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, teruskan dengan pembayaran baki!'
          }).then((result) => {
            if (result.isConfirmed) {
                $('#bookingid').val(bookingId);
                $('#amount').val(payAmount);
                $('#paymentType').val('balance');

                $('#form-pay-balance').submit();
            }
          })
      });
      // for review 
      let bookingId = 0;
      $('.btn-review').on('click',function(){
        bookingId = $(this).attr('data-booking-id');
        $('#booking_id').val(bookingId);
        $('#modal-review').modal('show');
      })

      $('#btn-close').on('click',function(){
        $('#booking_id').val('');
        $('input[name = "rating"]').prop('checked', false);
        $('#review_comment').val('');
        $('#review_images').val('');
        $('#modal-review').modal('hide');
      })

      $('#form-review').on('submit',function(e){
        if($('input[name = "rating"]:checked').length == 0){
          e.preventDefault();
          $('#rating-message').html(`
            <div class="alert alert-danger text-center">Sila pilih jumlah bintang untuk diberi</div>
          `);
          $('.alert').fadeOut(4000);
        }else{
          localStorage.setItem('switchTab', JSON.stringify('completedTab'));
        }
      });
     $('.alert').delay(3000).fadeOut()
    }); 
    // for review images
    $('#btn-add-image').on('click', function(){
      $('#review_images').click();
    });

    $('#review_images').on('change',function(){
      $('#image-previews').empty();
            const imageFiles = $(this).prop('files');
            if(imageFiles.length > 4){
                $('#rating-message').html(`
                    <div id="alert" class="alert alert-danger text-center">
                        Hanya dibenarkan muat naik maximum 4 gambar sahaja
                    </div>
                `);
                $('#alert').fadeOut(6000);
                $(this).val('');
                return;
            }else{
                for(let i = 0;i < imageFiles.length;i++){
                    if(imageFiles[i].type.includes('jpg')||imageFiles[i].type.includes('jpeg')||imageFiles[i].type.includes('png')){
                        const image = new Image();
                        image.src = URL.createObjectURL(imageFiles[i]);
                        image.onload = function () {
                            // Display the image preview
                            $('#image-previews').append(`
                                <img src="${image.src}" class="img-thumbnail img-new" id="img-preview">
                            `);
                            } 
                        // }                 
                    }else{
                        $('#rating-message').html(`
                            <div id="alert" class="alert alert-danger text-center">
                                Hanya dibenarkan muat naik gambar sahaja
                            </div>
                        `);
                        $('#alert').fadeOut(6000);
                        $(this).val('');
                        return;
                    }
                }
            }
    });

    // for booking cancellation
    $('.btn-cancel-booking').on('click',function(){
      Swal.fire({
        title: 'Adakah anda pasti?',
        text: "Pemulangan duit tempahan tidak akan diproses oleh sistem ini dan anda perlu menghubungi organisasi tersebut sendiri untuk mendapatkan pemulangan",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, batalkan tempahan ini!'
      }).then((result) => {
        if (result.isConfirmed) {
          const bookingId = $(this).attr('data-booking-id');
          $.ajax({
            url: "{{route('homestay.cancelBooking')}}",
            method: 'POST',
            dataType: 'json',
            data: {
              bookingId: bookingId,
              '_token': '{{csrf_token()}}',
            },
            success: function(result){
              console.log(result.success);
              // set flag to redirect to completed tab after reloading page
              localStorage.setItem('switchTab', JSON.stringify('cancelledTab'));
              window.location.reload();
            },
            error:function(){
              console.log('Cancel Booking Failed');
            }
          })
          Swal.fire(
            'Tempahan dibatalkan!',
            'Tempahan telah berjaya dibatalkan',
            'success'
          )
        }
      })
    });
  </script>
    
@endsection
