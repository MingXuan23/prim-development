@extends('layouts.master')

@section('css')
<link rel="stylesheet" href="{{ URL::asset('assets/homestay-assets/style.css')}}">
<style>
  img{
    object-fit: cover;
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
            @for($i = 0 ; $i < count($checkoutBookings); $i++)
              <div class="card tab-card my-3">
                  <div class="card-body row">
                    <a class="col-md-5" href="{{route('homestay.showRoom',['id' => $checkoutBookings[$i]->roomid, 'name' => $checkoutBookings[$i]->roomname])}}">
                      <img src="{{$checkoutImages[$i]}}" alt="{{$checkoutBookings[$i]->roomname}}" class="img-fluid">
                    </a>  
                    <div class="col-md-7">
                      <h4 class="color-purple">{{$checkoutBookings[$i]->roomname}}</h4>
                      <h5 class="color-dark-purple"><span><i class="fas fa-map-marker-alt"></i></span> {{$checkoutBookings[$i]->address}}, {{$checkoutBookings[$i]->area}}, {{$checkoutBookings[$i]->postcode}}, {{$checkoutBookings[$i]->district}}, {{$checkoutBookings[$i]->state}}</h5>
                      <h5 class="color-purple"><span class="color-dark-purple"><i class="fas fa-sign-in-alt"></i> Daftar Masuk: </span>{{date('d/m/Y',strtotime($checkoutBookings[$i]->checkin))}}, selepas {{date('H:i', strtotime($checkoutBookings[$i]->check_in_after))}}</h5>
                      <h5 class="color-purple"><span class="color-dark-purple"><i class="fas fa-sign-out-alt"></i> Daftar Keluar: </span>{{date('d/m/Y',strtotime($checkoutBookings[$i]->checkout))}}, sebelum {{date('H:i', strtotime($checkoutBookings[$i]->check_out_before))}}</h5>
                      <h5 class="color-purple"><span class="color-dark-purple"><i class="fas fa-money-check-alt"></i> Jumlah Dibayar: </span>RM{{$checkoutBookings[$i]->totalprice}}</h5>
                      <h5 class="color-purple"><span class="color-dark-purple"><i class="far fa-id-badge"></i> Nombor Telefon Organisasi: </span>{{$checkoutBookings[$i]->telno}}</h5>
                    </div>
                  </div>
              </div>
            @endfor
        </div>
        <div class="tab-pane fade" id="completed" role="tabpanel" aria-labelledby="completed-tab">
          @for($i = 0 ; $i < count($completedBookings); $i++)
            <div class="card tab-card my-3">
                <div class="card-body row">
                  <a class="col-md-5 d-flex align-items-center" href="{{route('homestay.showRoom',['id' => $completedBookings[$i]->roomid, 'name' => $completedBookings[$i]->roomname])}}">
                    <img src="{{$completedImages[$i]}}" alt="{{$completedBookings[$i]->roomname}}" class="img-fluid">
                  </a>  
                  <div class="col-md-7">
                    <h4 class="color-purple">{{$completedBookings[$i]->roomname}}</h4>
                    <h5 class="color-dark-purple"><span><i class="fas fa-map-marker-alt"></i></span> {{$completedBookings[$i]->address}}, {{$completedBookings[$i]->area}}, {{$completedBookings[$i]->postcode}}, {{$completedBookings[$i]->district}}, {{$completedBookings[$i]->state}}</h5>
                    <h5 class="color-purple"><span class="color-dark-purple"><i class="fas fa-sign-in-alt"></i> Daftar Masuk: </span>{{date('d/m/Y',strtotime($completedBookings[$i]->checkin))}}, selepas {{date('H:i', strtotime($completedBookings[$i]->check_in_after))}}</h5>
                    <h5 class="color-purple"><span class="color-dark-purple"><i class="fas fa-sign-out-alt"></i> Daftar Keluar: </span>{{date('d/m/Y',strtotime($completedBookings[$i]->checkout))}}, sebelum {{date('H:i', strtotime($completedBookings[$i]->check_out_before))}}</h5>
                    <h5 class="color-purple"><span class="color-dark-purple"><i class="fas fa-money-check-alt"></i> Jumlah Dibayar: </span>RM{{$completedBookings[$i]->totalprice}}</h5>
                    <h5 class="color-purple"><span class="color-dark-purple"><i class="far fa-id-badge"></i> Nombor Telefon Organisasi: </span>{{$completedBookings[$i]->telno}}</h5>
                  </div>
                </div>
            </div>
          @endfor
        </div>
        <div class="tab-pane fade" id="cancelled" role="tabpanel" aria-labelledby="cancelled-tab">
          @for($i = 0 ; $i < count($cancelledBookings); $i++)
            <div class="card tab-card my-3">
                <div class="card-body row">
                  <a class="col-md-5 d-flex align-items-center" href="{{route('homestay.showRoom',['id' => $cancelledBookings[$i]->roomid, 'name' => $cancelledBookings[$i]->roomname])}}">
                    <img src="{{$cancelledImages[$i]}}" alt="{{$cancelledBookings[$i]->roomname}}" class="img-fluid">
                  </a>  
                  <div class="col-md-7">
                    <h4 class="color-purple">{{$cancelledBookings[$i]->roomname}}</h4>
                    <h5 class="color-dark-purple"><span><i class="fas fa-map-marker-alt"></i></span> {{$cancelledBookings[$i]->address}}, {{$cancelledBookings[$i]->area}}, {{$cancelledBookings[$i]->postcode}}, {{$cancelledBookings[$i]->district}}, {{$cancelledBookings[$i]->state}}</h5>
                    <h5 class="color-purple"><span class="color-dark-purple"><i class="fas fa-sign-in-alt"></i> Daftar Masuk: </span>{{date('d/m/Y',strtotime($cancelledBookings[$i]->checkin))}}, selepas {{date('H:i', strtotime($cancelledBookings[$i]->check_in_after))}}</h5>
                    <h5 class="color-purple"><span class="color-dark-purple"><i class="fas fa-sign-out-alt"></i> Daftar Keluar: </span>{{date('d/m/Y',strtotime($cancelledBookings[$i]->checkout))}}, sebelum {{date('H:i', strtotime($cancelledBookings[$i]->check_out_before))}}</h5>
                    <h5 class="color-purple"><span class="color-dark-purple"><i class="fas fa-money-check-alt"></i> Jumlah Dibayar: </span>RM{{$cancelledBookings[$i]->totalprice}}</h5>
                    <h5 class="color-purple"><span class="color-dark-purple"><i class="far fa-id-badge"></i> Nombor Telefon Organisasi: </span>{{$cancelledBookings[$i]->telno}}</h5>
                  </div>
                </div>
            </div>
          @endfor
        </div>
      </div>
    </div>

@endsection

@section('scripts')

@endsection
