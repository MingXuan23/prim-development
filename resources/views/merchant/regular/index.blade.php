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

@csrf

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="list-group">
          @if(Session::has('success'))
          <div class="alert alert-success">
            <p>{{ Session::get('success') }}</p>
          </div>
          @endif

          @forelse($merchant as $row)
            @if($row->status == 1)
            <a href="{{ route('merchant.regular.show', $row->id) }}" class="order_modal list-group-item list-group-item-action flex-column">
            @else
            <a class="closed-modal list-group-item list-group-item-action flex-column" style="opacity: 50%">
            @endif
              <div class="d-flex" >
                  <img class="rounded img-fluid bg-dark" id="img-size" src="
                  {!! $row->organization_picture != null ? 
                    URL('organization_picture/'.$row->organization_picture) : 
                    URL('images/koperasi/default-item.png')
                  !!}">
                  <div class="flex-column ml-2">
                      <h4 class="merchant_name">
                        {!! 
                        $row->status == 1 ? $row->nama : $row->nama." <label class='text-danger'>Closed</label>" 
                        !!}
                      </h4>
                      <div class="d-flex">
                        <div class="justify-content-center align-items-center mr-2">
                          <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <p class="m-0">{{ $row->address }} , {{$row->city}} , {{$row->state}}</p>
                      </div>
                  </div>
                  <div class="arrow-icon ml-auto justify-content-end align-self-center">
                      <h1><i class="fas fa-angle-right"></i></h1>
                  </div>
              </div>
            </a>
            @empty
            <div class="text-center">
              <p><i>Tiada Peniaga buat masa sekarang</i></p>
            </div>
          @endforelse
          {{-- <div class="test">
          </div>
          <a href="" class="order_modal list-group-item list-group-item-action flex-column" hidden>
            <div class="d-flex">
                <img class="rounded img-fluid bg-dark" id="img-size" src="">
                <div class="flex-column ml-2">
                    <h4 class="merchant_name"></h4>
                    <div class="d-flex">
                      <div class="justify-content-center align-items-center mr-2">
                        <i class="fas fa-map-marker-alt"></i>
                      </div>
                      <p class="m-0"></p>
                    </div>
                </div>
                <div class="arrow-icon ml-auto justify-content-end align-self-center">
                    <h1><i class="fas fa-angle-right"></i></h1>
                </div>
            </div>
          </a> --}}
          
          <div class="row mt-2 ">
            <div class="col d-flex justify-content-end">
              {{ $merchant->links() }}
            </div>
          </div>
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

      

      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      var state

      

      // fetch_merchant()

      $('.closed-modal').click(function(){
          $('#merchantClosedModal').modal('show')
      })

      // function test(){
      //   alert('test')
      // }

      // function fetch_merchant(state='') {
      //   $.ajax({
      //     url: "{{ route('merchant.fetch-merchant') }}",
      //     method: "GET",
      //     data: state,
      //     beforeSend:function() {

      //     },
      //     success:function(result) {
      //       for(var i = 0; i < result.count; i++) {
      //         var isClose = ""
      //         var route = "/merchant/regular/"+result.merchant[i].id
      //         var pic_url = "/"+result.merchant[i].picture
      //         if(result.merchant[i].status != 1) {
      //           route = ""
      //         }
      //         $('.test').append('<a href="'+route+'" class=" order-modal list-group-item list-group-item-action flex-column" disabled><div class="d-flex"><img class="rounded img-fluid bg-dark" id="img-size" src="'+pic_url+'"><div class="flex-column ml-2"><h4 class="merchant_name">'+result.merchant[i].nama+'</h4><div class="d-flex"><div class="justify-content-center align-items-center mr-2"><i class="fas fa-map-marker-alt"></i></div><p class="m-0">'+result.merchant[i].address+' , '+result.merchant[i].city+' , '+result.merchant[i].state+'</p></div></div><div class="arrow-icon ml-auto justify-content-end align-self-center"><h1><i class="fas fa-angle-right"></i></h1></div></div></a>')
      //       }
      //       console.log(result)
      //     },
      //     error:function(result) {
      //       console.log(result.responseText)
      //     }
      //   })
      // }

      // $('.order_modal').click(function(){
        
      // })

      function callAlert(message)
      {
        const popup = $('#popup')
        popup.empty()
        popup.addClass('alert-danger')
        popup.css('display', '')
        popup.append('<p>'+message+'</p>')
        popup.delay(3000).fadeOut()
      }

      $('.alert-success').delay(2000).fadeOut()

    })
</script>

@endsection