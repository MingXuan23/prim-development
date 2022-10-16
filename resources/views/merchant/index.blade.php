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
            @if($oh_status[$row->id] == 1)
            <a data-org-id="{{ $row->id }}" class="order_modal list-group-item list-group-item-action flex-column">
            @else
            <a class="closed_modal list-group-item list-group-item-action flex-column" style="opacity: 50%">
            @endif
              <div class="d-flex" >
                  <img class="rounded img-fluid bg-dark" id="img-size" src="{{ URL('images/koperasi/default-item.png')}}">
                  <div class="flex-column ml-2">
                      <h4 class="merchant_name">
                        {!! 
                        $oh_status[$row->id] == 1 ? $row->nama : $row->nama." <label class='text-danger'>Closed</label>" 
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

{{-- Order Time Modal --}}
<div id="orderTimeModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Pilih Hari dan Masa Pengambilan</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">

          <div class="alert" id="popup" style="display: none"></div>

          <div class="row">

            <div class="col">
              <div class="form-group required">
                <label class="col">Hari</label>
                <div class="col" >
                  <select class="form-control" data-parsley-required-message="Sila pilih hari" id="pickup_day" required>
                    <option value="" selected>Pilih Hari</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="col">
              <div class="form-group required">
                <label class="col">Masa</label>
                <div class="col" id="pickup_time_div">
                  <input class="form-control" type="time" id="pickup_time" disabled required>
                </div>
              </div>
            </div>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-light mr-2" data-dismiss="modal">Tutup</button>
          <button type="button" class="btn btn-primary" id="order_submit">Teruskan</button>
        </div>

      </div>
  </div>
</div>
{{-- end Order Time Modal --}}

{{-- Order Exist Modal --}}
<div id="orderExistModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title"><i class="fas fa-info-circle"></i>  Anda Mempunyai Pesanan Yang Masih Aktif</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            Anda mempunyai pesanan yang <strong>aktif</strong> pada <strong id="exists_date_time"></strong> ataupun <strong>membatalkan</strong> pesanan tersebut dan membuat pesanan baharu?
          </div>
          <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn btn-danger" id="destroy_order">Batal Pesanan Lama</button>
            <button type="button" data-dismiss="modal" class="btn btn-primary" id="proceed_order">Teruskan</button>
          </div>
      </div>
  </div>
</div>
  {{-- end Order Exist Modal --}}


@endsection

@section('script')

<script>
    $(document).ready(function() {
      $('.closed_modal').click(function(){
          $('#merchantClosedModal').modal('show')
      })

      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      var org_id, path, order_id;

      $('.order_modal').click(function(e) {
        e.preventDefault();
        org_id = $(this).attr('data-org-id')
        
        $.ajax({
          url: "{{ route('merchant.fetchDay') }}",
          method: "POST",
          data: {o_id:org_id},
          success:function(result)
          {
            var pickup_day = $("#pickup_day")
            var pickup_time = $('#pickup_time_div')

            if(result.day_body != "")
            {
              pickup_time.html('<input class="form-control" type="time" id="pickup_time" disabled required>')
              pickup_day.empty()
              pickup_day.append("<option value='' disabled selected>Pilih Hari</option>")
              pickup_day.append(result.day_body)

              $('#orderTimeModal').modal('show')
            }
            else
            {
              path = result.path
              order_id = result.order_id
              
              $('#exists_date_time').empty().append(result.pickup_date)
              $('#orderExistModal').modal('show')
              
              // console.log(result)
              // window.location.href = result.path;
            }
          },
          error:function(result)
          {
            console.log(result.responseText)
          }
        })
      })

      var day;

      $('#pickup_day').change(function() {
        
        if(this.value != null)
        {
          day = this.value
          $.ajax({
            url: "{{ route('merchant.fetchTime') }}",
            method: "POST",
            data: { day:day, o_id:org_id },
            success:function(result){
              var pickup_time = $('#pickup_time_div')

              if(result.alert == "")
              {
                pickup_time.html(result.time_body)
              }
              else
              {
                pickup_time.html(result.alert)
              }
            },
            error:function(result){
              console.log(result.responseText)
            }
          })
        }
      })

      $('#order_submit').click(function() {
        if($('#pickup_time').val() == "" || $('#pickup_day').val() == null)
        {
          callAlert("Sila kemaskini ruangan yang tidak kosong")
        }
        else
        {
          var time = $('#pickup_time').val()
          var min = $('#pickup_time').attr('min')
          var max = $('#pickup_time').attr('max')

          $.ajax({
            url: "{{ route('merchant.storeOrderDate') }}",
            method: "POST",
            data: { day: day, time: time, min:min, max:max, org_id:org_id},
            success:function(result)
            {
              if(!result.alert)
              {
                window.location.href = result.path;
                $('#orderTimeModal').modal('hide')
              }
              else
              {
                callAlert(result.alert)
              }
            },
            error:function(result)
            {
              console.log(result.responseText)
            }
          })
        }
      })
      
      $('#proceed_order').click(function() {
        window.location.href = path;
      })

      $('#destroy_order').click(function(e) {
        e.preventDefault()
        $.ajax({
          url: "{{ route('merchant.destroyOldOrder') }}",
          method: "POST",
          data: { order_id:order_id },
          success:function(result){
            console.log(result)
          },
          error:function(result){
            console.log(result.responseText)
          }
        })
      })

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