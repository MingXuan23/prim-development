@extends('layouts.master')

@section('css')

<style>
#img-size
{
  width: 100px;
  height: 100px;
  object-fit: cover;
}

@media screen and (max-width: 768px) { 
  #img-size
  {
    width: 50px;
    height: 50px;
    object-fit: cover;
  }
  .arrow-icon{
    display: none;
  }
}
</style>

@endsection

@section('content')


<div class="row align-items-center">
  <div class="col-sm-6">
      <div class="page-title-box">
          <h4 class="font-size-18">Senarai Peniaga</h4>
      </div>
  </div>
  {{-- <div class="col-sm-6 d-flex justify-content-center align-items-center flex-row-reverse">
    <a href="{{ route('delivery.showAddress', $location->id) }}" type="button" class="btn btn-info ml-4">Edit Lokasi</a>
    <h4 class=" m-0">{{ $location->address }} , {{ $location->city }} , {{ $location->postcode }} {{ $location->state }}</h4>
  </div> --}}
</div>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="list-group">
            @foreach($merchant as $row)
              @if($oh_status[$row->id] == 1)
              <a href="{{ route('merchant.show', $row->id) }}" class="list-group-item list-group-item-action flex-column">
              @else
              <a id="closed_modal" class="list-group-item list-group-item-action flex-column" style="opacity: 50%">
              @endif
                <div class="d-flex" style="height: 100px">
                    <img class="rounded img-fluid bg-dark" id="img-size" src="{{ URL('images/koperasi/default-item.png')}}">
                    <div class="flex-column ml-2">
                        <h4 class="merchant_name" id="{{$oh_status[$row->id]}}">{{ $row->nama }}</h4>
                        <p class="m-0"><i class="fas fa-map-marker-alt mr-2"></i>
                             {{ $row->address }} , {{$row->city}} , {{$row->state}}
                        </p>
                        {{-- <p class="m-0"><i class="mdi mdi-bike-fast mr-2"></i>RM </p> --}}
                        {{-- <p class="m-0"><i class="mdi mdi-food mr-2"></i> </p>--}}
                    </div>
                    <div class="arrow-icon ml-auto justify-content-end align-self-center">
                        <h1><i class="fas fa-angle-right"></i></h1>
                    </div>
                </div>
              </a>
            @endforeach
          {{-- <div class="text-center">
            <p><i>Tiada Restoran di dalam Negeri anda</i></p>
          </div> --}}

          {{-- <div class="row mt-2 ">
            <div class="col d-flex justify-content-end">
              this is for links
            </div>
          </div> --}}
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Merchant Closed Modal --}}
<div id="merchantClosedModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-header">
              <h4 class="modal-title">Harap Maaf</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
              Peniaga Tutup Pada Waktu Ini
          </div>
          <div class="modal-footer">
              <button type="button" data-dismiss="modal" class="btn btn-primary">OK</button>
          </div>
      </div>
  </div>
</div>
  {{-- end Merchant Closed Modal --}}


@endsection

@section('script')

<script>
    $(document).ready(function() {
      
      if($('.merchant_name').attr('id') == 0){
        $('.merchant_name').append(' <label class="text-danger">Closed</label>')
      }

      $('#closed_modal').click(function(){
          $('#merchantClosedModal').modal('show')
      })
    })
</script>

@endsection