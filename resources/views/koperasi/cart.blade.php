@extends('layouts.master')

@section('css')

@include('layouts.datatable')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
<style>
  .noborder{
    border: none!important;
  }

#img-size
{
  width: 100px;
  height: 100px;
  object-fit: cover;
}

.quantity-container{
    
    display: flex;
    flex-direction: row;
    align-items: center;
}
.quantity-container button{
    border: none;
    background-color: transparent;
}
.quantity-container i{
    font-size: 24px;
}
.quantity-input-container{
    display: flex;
    flex-direction: row;
    align-items: center;
    flex-wrap: wrap;
}
.quantity-input{
    text-align: center;
    width: 50px;
    border: 0.15em solid rgb(0, 0, 0);
}

/* to remove the arrow up and down for input type number */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
    display: none;
}
</style>

@endsection

@section('content')

<div class="row align-items-center">
  <div class="col-sm-6">
      <div class="page-title-box">
          <h4 class="font-size-18">Koperasi</h4>
          <ol class="breadcrumb mb-0">
              <li class="breadcrumb-item active">Koperasi >> Pesanan Anda</li>
          </ol>
      </div>
  </div>
</div>

<div class="container">
  <div class="row d-flex justify-content-center align-items-center">
    <div class="col">
      <div class="d-flex justify-content-center align-items-center">
        <span class="h2 mb-4">Senarai Pesanan</span>
      </div>

      <div class="card">
        <div class="card-body">

          @if(Session::has('success'))
          <div class="alert alert-success">
            <p>{{ Session::get('success') }}</p>
          </div>
          @endif

          <div class="table-responsive">
            <table class="table table-borderless" width="100%" cellspacing="0">
                <thead>
                    <tr class="text-center">
                      <th style="width: 20%">Gambar</th>
                      <th style="width: 30%">Nama Item</th>
                      <th style="width: 10%">Kuantiti</th>
                      <th style="width: 20%">Harga Satu Unit (RM)</th>
                      <th style="width: 20%">Action</th>
                    </tr>
                </thead>
                
                <tbody>
                  @if(count($cart_item) != 0 || $cart)
                  @foreach($cart_item as $row)
                    
                    <tr class="text-center">
                      <td>
                          @if($row->image == null)
                          <img src="{{ URL('images/koperasi/default-item.png')}}" class="rounded img-fluid" id="img-size" style="background-color: rgb(61, 61, 61)">
                          @else
                          <img src="{{ URL('koperasi-item/'.$row->image)}}" class="rounded img-fluid" id="img-size">
                          @endif
                      </td>
                      <td class="align-middle">{{ $row->name }}</td>
                      <td class="align-middle">{{ $row->quantity }}</td>
                      <td class="align-middle">{{ number_format($row->price, 2, '.', '') }}</td>
                      <td class="align-middle">
                        <!-- <form action="{{ route('koperasi.destroyItemCart', ['org_id' => $id, 'id' => $row->productOrderId]) }}" method="POST">
                          @csrf
                          @method('delete')
                          <button type="submit" class="btn btn-danger">Buang</button>
                        </form> -->
                        <div class  = "quantity-container" >
                                    <div class="quantity-input-container" data-product-order-id="{{$row->productOrderId}}"  data-pgng-order-id="{{$row->pgngId}}">
                                        <button id="button-minus"><i class="bi bi-dash-square"></i></button>
                                        <input type="number" class="quantity-input" name = "quantity-input" value="{{$row->quantity}}" min="1">
                                        <button id="button-plus" ><i class="bi bi-plus-square"></i></button>
                                        <h6 data-qty-available="{{$row->quantity_available}}" id="quantity-available">{{$row->quantity_available}} barang lagi</h6>
                                    </div>
                        </div>
                      </td>
                    </tr>
                  @endforeach
                  @else
                    <tr>
                      <td colspan="5" class="text-center"><i>Tiada Item Buat Masa Sekarang.</i></td>
                    </tr>
                  @endif
                </tbody>
            </table>
          </div>

        </div>
      </div>

      @if(count($cart_item) != 0 || $cart)
      <form action="{{ route('fpxIndex') }}" method="POST">
      <input type="hidden" name="desc" id="desc" value="Koperasi">
      <input type="hidden" name="cartId" id="cartId" value="{{ $cart->id }}">
      @else
      @endif
        @csrf
        <div class="card mb-4 border">
          <div class="card-body p-4">

            <div class="table-responsive">
              <table class="table table-borderless mb-0">
                
                  <tbody>
                      <tr>
                          <th class="text-muted" scope="row">Jumlah Keseluruhan:</th>
                          @if($cart)
                          <td class="lead" id="totalPrice">RM {{ number_format($cart->total_price, 2, '.', '') }}</td>
                          @else
                          <td class="lead" id="totalPrice">RM 0.00</td>
                          @endif
                      </tr>
                  </tbody>
                
              </table>
          </div>

          </div>
        </div>

        @if($cart)
        <div class="card mb-4 border">
          <div class="card-body p-4">
            <div class="row">
              <div class="col">
                <div class="form-group required">
                  {{-- <label for="example-date-input" class="col-sm-2">Tarikh Pengambilan</label>
                  <div class="col">
                      <input class="form-control" type="date" value="{{ $tomorrowDate}}" min="{{ $tomorrowDate }}" id="date_pick_up">
                  </div> --}}
                  <label class="col-sm-2">Hari Pengambilan</label>
                    <div class="col">
                        <select class="form-control" data-parsley-required-message="Sila pilih hari" id="pick_up_date" required>
                          <option value="" selected>Pilih Hari</option>
                          @foreach($allDay as $row)
                            @if($row->day == 1)
                              <option value="{{ $row->day }}">Isnin {{ $isPast[strval($row->day)] }}</option>
                            @elseif($row->day == 2)
                              <option value="{{ $row->day }}">Selasa {{ $isPast[strval($row->day)] }}</option>
                            @elseif($row->day == 3)
                              <option value="{{ $row->day }}">Rabu {{ $isPast[strval($row->day)] }}</option>
                            @elseif($row->day == 4)
                              <option value="{{ $row->day }}">Khamis {{ $isPast[strval($row->day)] }}</option>
                            @elseif($row->day == 5)
                              <option value="{{ $row->day }}">Jumaat {{ $isPast[strval($row->day)] }}</option>
                            @elseif($row->day == 6)
                              <option value="{{ $row->day }}">Sabtu {{ $isPast[strval($row->day)] }}</option>
                            @elseif($row->day == 0)
                              <option value="{{ $row->day }}">Ahad {{ $isPast[strval($row->day)] }}</option>
                            @endif
                          @endforeach
                        </select>
                        
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="card mb-4 border">
          <div class="card-body p-4">
            <div class="row">
              <div class="col">
              <div class="form-group">
                                <label>Pilih Bank</label>
                                <select name="bankid" id="bankid" class="form-control"
                                    data-parsley-required-message="Sila pilih bank" required>
                                    <option value="">Pilih bank</option>
                                </select>

                            </div>
              </div>
            </div>
          </div>
        </div>

        

        <div class="card mb-4 border">
          <div class="card-body p-4">
            <div class="row">
              <div class="col">
                <div class="form-group required">
                  <label class="col">Nota kepada Koperasi</label>
                  <div class="col">
                    <input type="text" name="note" class="form-control" placeholder="Optional">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        @endif
        
        <input type="hidden" name="week_status" id="week_status" value="">

        <div class="row mb-2">
          <div class="col d-flex justify-content-end">
            <a href="{{ route('koperasi.koopShop', $id) }}" type="button" class="btn btn-light mr-2">Kembali</a>
            @if($cart)
              <button type="submit" class="btn btn-primary">Bayar</button>
            @endif
          </div>
        </div>

      </form>

    </div>
  </div>
</div>
{{-- Delete Confirmation Modal --}}
    <div id="deleteConfirmModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <h4 class="modal-title"><i class="fas fa-info-circle"></i>  Buang Item</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                Anda pasti mahu buang item ini?
                </div>
                <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-light">Tidak</button>
                <button type="button" data-dismiss="modal" class="btn btn-danger" id="delete_confirm_item">Buang</button>
                </div>
            </div>
        </div>
    </div>
    {{-- end Delete Confirmation Modal --}}
@endsection


@section('script')
<script src="{{ URL::asset('assets/libs/inputmask/inputmask.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/jquery-mask/jquery.mask.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/parsleyjs/parsleyjs.min.js')}}"></script>

<script>
    $(document).ready(function () {
      $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
        });
        $(".input-mask").inputmask();
        $('.phone_no').mask('01000000000');
        $('.form-validation').parsley();
    });
    
    $('.alert').delay(2000).fadeOut();

    $('#pick_up_date').on('change', function() {
        $('#week_status').val(this.value);
        // console.log($('#week_status').val());
    });

    function checkBank() {
        var t = jQuery('#bankid').val();
        if (t === '' || t === null) {
            alert('Please select a bank');
            return false;
        }
    }
    
    $(document).ready(function() {
	    $('.form-validation').parsley();
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
    let order_cart_id = null;//update when the user minus until zero at minus button
    $(document).on('click', '#delete_confirm_item', function(){
            $.ajax({
                url: "{{ route('koperasi.destroyItemCart', ['org_id' => $id]) }}",
                method: "DELETE",
                data: {cart_id:order_cart_id, "_token": "{{ csrf_token() }}",},
                beforeSend:function() {
                    $('.loading').show()
                    $(this).hide()
                    $('.alert-success').empty()
                },
                success:function(result) {
                    $('.loading').hide()
                    $(this).show()
                    location.reload()
                },
                error:function(result) {
                    console.log(result.responseText)
                }
            })
        });

    const plusButtons = document.querySelectorAll("#button-plus");
    const minusButtons = document.querySelectorAll("#button-minus");
    // add click event to plus and minus buttons
    plusButtons.forEach(function(plusButton){
        plusButton.addEventListener("click",function(){
            let inputQuantity = parseInt(this.previousElementSibling.value);
            let qtyAvailable = parseInt(this.nextElementSibling.getAttribute("data-qty-available"));
            let productOrderId = this.parentElement.getAttribute("data-product-order-id");
            let pgngOrderId = this.parentElement.getAttribute("data-pgng-order-id");
            //console.log(inputQuantity+" "+qtyAvailable+" "+productOrderId+" "+pgngOrderId);

            if(inputQuantity < qtyAvailable){
                inputQuantity++;
                this.previousElementSibling.value = inputQuantity;
                console.log(this,inputQuantity,productOrderId,pgngOrderId);
                updateInputQuantity(this,inputQuantity,productOrderId,pgngOrderId);
            }
        })
    });
    minusButtons.forEach(function(minusButton){
        minusButton.addEventListener("click",function(){
            let inputQuantity = parseInt(this.nextElementSibling.value);
            let qtyAvailable = parseInt(this.nextElementSibling.nextElementSibling.nextElementSibling.getAttribute("data-qty-available"));
            let productOrderId = this.parentElement.getAttribute("data-product-order-id");
            let pgngOrderId = this.parentElement.getAttribute("data-pgng-order-id");
            // console.log(this.parentElement,inputQuantity);
            console.log(pgngOrderId,productOrderId);
            if(inputQuantity >= 1){
               if(inputQuantity==1){
                order_cart_id =pgngOrderId; //update this so that can pass to delte model
                $('#deleteConfirmModal').modal('show');
               }
               else{
                inputQuantity--;
                this.nextElementSibling.value = inputQuantity;
                console.log(this,inputQuantity,productOrderId,pgngOrderId);
                updateInputQuantity(this,inputQuantity,productOrderId,pgngOrderId);
               }
                
            }
        })
    }) ;

    function updateInputQuantity(plusButton,inputQuantity,productOrderId,pgngOrderId){
            $.ajax({
                url: "{{route('merchant.update-cart')}}",
                method: "PUT",
                dataType: 'json',
                data: {
                    qty: inputQuantity,
                    productOrderId: productOrderId,
                    pgngOrderId: pgngOrderId,
                },
                beforeSend: function() {
                        $('.loading').show();
                },
                success: function(result){
                        $('.loading').hide();
                        $parent = $(plusButton).parent().parent().parent();
                        $alertMessage = $parent.children('.alert-message');
                        var message = "<div class='success alert-success' style='padding: 5px;'>"+result.success+"</div>"
                        $alertMessage.append(message);
                        $alertMessage.delay(1000).fadeOut()
                        
                        $totalPrice = $('#totalPrice');
                        console.log(result.totalPrice);
                        $totalPrice.html("RM "+result.totalPrice);

                },
                // error:function(result) {
                //     console.log(result.responseText)
                // }
           })};
</script>
@endsection