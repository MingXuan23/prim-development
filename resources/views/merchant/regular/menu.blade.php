@extends('layouts.master')

@section('css')

{{-- <script src="{{ URL('assets/libs/bootstrap-touchspin-master/src/jquery.bootstrap-touchspin.css')}}"></script> --}}

<style>

#has-bg-img{
  background-image: url("{{ URL('images/koperasi/Koperasi-default-background-2.jpg')}}");
  background-repeat: no-repeat;
  background-position: center;
  background-size: cover;
}

#shadow-bg{
  border-radius: 14px;
  box-shadow: 11px 10px 20px 8px rgba(0,0,0,0.10);
}

#img-size
{
  max-width: 100%;
  height: auto;
  width: 100px;
  border-radius: 14px;
  object-fit: contain;
  background-color:rgb(61, 61, 61)
}

.nav-link{
  color: black;
}

.nav-link:hover{
  color:rgb(98, 97, 97);
}

.modal {
  text-align: center;
}

.modal-dialog {
  display: inline-block;
  text-align: left;
  vertical-align: middle;
}

.loading {
  width: 35px;
  height: 35px;
  display:none;
}
</style>

@endsection

@section('content')

<div class="row align-items-center">
  <div class="col-sm-6">
      <div class="page-title-box">
          <h4 class="font-size-18"><a href="{{ route('merchant.regular.index') }}" class="text-muted">Senarai Peniaga</a> <i class="fas fa-angle-right"></i> {{ $merchant->nama }}</h4>
      </div>
  </div>
</div>

<div class="row mt-4 ">
  <div class="col-12">
    <div class="card border border-dark">
      
      <div class="card-header text-white" id="has-bg-img">
        <div class="row justify-content-between">
          <h2>{{ $merchant->nama }}</h2>
          <a href="{{ route('merchant.regular.cart', $merchant->id) }}"><i class="mdi mdi-cart fa-3x"></i></a>
        </div>
        
        <p><i class="fas fa-map-marker-alt mr-2"></i> {{ $merchant->address }}, {{ $merchant->city }}, {{ $merchant->state }}</p>

        {{-- <div class="d-flex">
          @if($oh->status != 0)
          <p class="mr-4"><b>Waktu Buka</b></p>
          <p>Hari ini {{ $open_hour }} - {{ $close_hour }}</p>
          @else
          <p><b>Tutup pada hari ini</b></p>
          @endif
        </div> --}}
      </div>

      <div class="m-2">
        <nav class="nav">
          @foreach($product_group as $row)
          <a class="nav-item nav-link" id="{{ $row->id }}" href="#{{ $row->name }}">
            {{ $row->name }}
          </a>
          @endforeach
        </nav>
        <hr>
      </div>
      
      <div class="card-body">
        
        <div class="flash-message"></div>

        @foreach($product_group as $group)
          <div class="d-flex justify-content-start" id="{{ $group->name }}">
            <h3 class="mb-4 ml-2">{{ $group->name }}</h3>
          </div>

          @foreach($product_item as $item)
            @if($item->product_group_id == $group->id)
              <div class="row">
                <div class="col">
                  <div class="card border p-2" id="shadow-bg">
                    <div class="d-flex">
                      <div class="d-flex justify-content-center align-items-start">
                        <div>
                          <img class="img-fluid" id="img-size" src="{{ URL('images/koperasi/default-item.png')}}">
                        </div>
                      </div>
                      <div class="col">
                        <div class="d-flex align-items-start flex-column h-100" >
                          <div>
                            <h4 class="mt-2">{{ $item->name }} <span class="badge badge-light">{{ $item->selling_quantity }} {{ $item->collective_noun }}</span>
                              @if($item->status != 1) <label class="text-danger">Kehabisan Stok</label> @endif
                            </h4> 
                          </div>
                          <div>
                            <p class="card-text"><i>{{ $item->desc }}</i></p>
                          </div>
                          <div class="mt-auto d-flex justify-content-between align-items-center w-100">
                            <div class="">
                              <p class="card-text"><b>RM</b> {{ $price[$item->id] }}</p>
                            </div>
                            <div class="ml-auto">
                              @csrf
                              @if($item->status != 0)
                              <div class="button-cart-section">
                                <button type="button" class="btn btn-success btn-item-modal" data-item-id="{{ $item->id }}" data-org-id="{{ $merchant->id }}"><i class="mdi mdi-cart"></i></button>
                              </div>
                              @endif
                            </div>
                            
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            @endif
          @endforeach
        
        @endforeach
    </div>
  </div>
</div>

{{-- addToCartModal --}}
<div class="modal fade" id="addToCartModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="alert alert-danger" id="item_alert"></div>
      </div>
      <div class="modal-footer justify-content-center">
        <img class="loading" src="{{ URL('images/koperasi/loading-ajax.gif')}}">
        <button type="button" class="cart-add-btn btn btn-primary btn-block">Tambah</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('script')

<script src="{{ URL('assets/libs/bootstrap-touchspin-master/src/jquery.bootstrap-touchspin.js')}}"></script>

<script>
  $(document).ready(function(){
    var item_id, org_id;
    
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $('.btn-item-modal').click(function(e) {
      e.preventDefault()
      $('.modal-title').empty()
      $('.modal-body').empty()
      item_id = $(this).attr('data-item-id')
      org_id = $(this).attr('data-org-id')
      
      $.ajax({
        url: "{{ route('merchant.regular.fetch-item') }}",
        method: "POST",
        data: {i_id:item_id, o_id:org_id},
        beforeSend:function() {
          $('#addToCartModal').modal('show')
          $('.modal-body').append("<div class='text-center'><img src='{{ URL('images/koperasi/loading-ajax.gif')}}' style='width:40px;height:40px;'></div>")
        },
        success:function(result)
        {
          $('.modal-body').empty()
          $('.modal-title').append(result.item.name)
          $('.modal-body').append(result.body)
          
          quantityExceedHandler($("input[name='quantity_input']"), result.quantity)
        },
        error:function(result)
        {
          console.log(result.responseText)
        }
      })
    })

    $('.cart-add-btn').click(function(){
      var quantity = $("input[name='quantity_input']").val()

      $.ajax({
        url: "{{ route('merchant.regular.store-item') }}",
        method: "POST",
        data: {
          i_id:item_id,
          o_id:org_id,
          qty:quantity,
        },
        beforeSend: function() {
          $('.cart-add-btn').css('display', 'none')
          $('.loading').show()
        },
        success:function(result)
        { 
          console.log(result)
          $('.cart-add-btn').show()
          $('.loading').hide()
          $('div.flash-message').empty()
          
          if(result.alert == '')
          {
            $('#addToCartModal').modal('hide')

            var message = "<div class='alert alert-success'>"+result.success+"</div>"
            $('div.flash-message').show()
            $('div.flash-message').append(message)
            $('div.flash-message').delay(3000).fadeOut()
            $("html, body").animate({ scrollTop: 0 }, "slow");
          }
          else
          {
            if(result.alert == 'restart') {
              location.reload()
            } else {
              $('#addToCartModal').modal('hide')

              var message = "<div class='alert alert-danger'>"+result.alert+"</div>"
              $('div.flash-message').show()
              $('div.flash-message').append(message)
              $('div.flash-message').delay(3000).fadeOut()
              $("html, body").animate({ scrollTop: 0 }, "slow");
            }
          }
        },
        error:function(result)
        {
          console.log(result.responseText)
        }
      })
    })

    function quantityExceedHandler(i_Quantity, maxQuantity)
    {
      i_Quantity.TouchSpin({
        min: 1,
        max: maxQuantity,
        stepinterval: 50,
      });

      var tmp = true;
      
      i_Quantity.on('keypress', function (event) {
        var regex = new RegExp("^[0-9]+$");
        var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
        
        i_Quantity.on('keyup', function (event) {
          if(this.value > maxQuantity) {
            if (event.cancelable) event.preventDefault();
            tmp = false;
            $(this).val(this.value.slice(0, -1))
            return tmp;
          }
          else
          {
            tmp = true;
            return tmp;
          }
        })  
        if (!regex.test(key) || tmp == false) {
          if (event.cancelable) event.preventDefault();
          return false;
        }
      });
    }

    $('.alert-success').delay(2000).fadeOut()
    $('.alert-danger').delay(4000).fadeOut()
    

    
  });
</script>

@endsection