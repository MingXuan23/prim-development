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
    text-align:center;
}
.quantity-container button{
    border: none;
    background-color: transparent;
    align-items: center;
}
.quantity-container i{
    font-size: 24px;
}
.quantity-input-container{
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: center; /* Add this line to center the elements horizontally */
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
        @if(isset($updateMessage)&&$updateMessage!="")
          <div class="alert alert-danger">
              {!! nl2br($updateMessage) !!}
          </div>
        @endif

          @if(Session::has('success'))
          <div class="alert alert-success">
            <p>{{ Session::get('success') }}</p>
          </div>
          @endif
          

          <div class="table-responsive">
            <table class="table table-borderless" width="100%" cellspacing="0">
                <thead>
                    <tr class="text-center">
                      <th style="width: 20%" class="d-none d-md-table-cell">Gambar</th>
                      <th style="width: 25%">Nama Item</th>
                      <!-- <th style="width: 10%">Kuantiti</th> -->
                      <th style="width: 15%">Harga Satu Unit (RM)</th>
                      <th style="width: 40%">Kuantiti</th>
                    </tr>
                </thead>
                
                <tbody>
                  @if(count($cart_item) != 0 || $cart)
                  @foreach($cart_item as $row)
                    
                    <tr class="text-center" >
                      <td class="d-none d-md-table-cell">
                          @if($row->image == null)
                          <img src="{{ URL('images/koperasi/default-item.png')}}" class="rounded img-fluid" id="img-size" style="background-color: rgb(61, 61, 61)">
                          @else
                          <img src="{{ URL('koperasi-item/'.$row->image)}}" class="rounded img-fluid" id="img-size">
                          @endif
                      </td>
                      <td class="align-middle">{{ $row->name }}</td>
                      <!-- <td class="align-middle">{{ $row->quantity }}</td> -->
                      <td class="align-middle">{{ number_format($row->price, 2, '.', '') }}</td>
                      <td class="align-middle">
                        <!-- <form action="{{ route('koperasi.destroyItemCart', ['org_id' => $id, 'id' => $row->productOrderId]) }}" method="POST">
                          @csrf
                          @method('delete')
                          <button type="submit" class="btn btn-danger">Buang</button>
                        </form> -->
                        <div class = "quantity-container " >
                                    <div class="quantity-input-container "style="width: 100%" data-product-order-id="{{$row->productOrderId}}"  data-pgng-order-id="{{$row->pgngId}}" >
                                        <button id="button-minus" ><i class="bi bi-dash-square"></i></button>
                                        <input type="number" class="quantity-input" name = "quantity-input" value="{{$row->quantity}}" min="1" required>
                                        <button id="button-plus" ><i class="bi bi-plus-square"></i></button>
                                        <h6 data-qty-available="{{$row->quantity_available}}" id="quantity-available">{{$row->quantity_available}} barang dalam stock</h6>
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
      <form action="{{ route('directpayIndex') }}" method="POST" onsubmit="return validateCart(event)">
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
                      <th class="text-muted" scope="row" style="font-size:16px">Jumlah Harga Produk:</th>
                          @if($cart)
                          <td class="lead" id="total">RM {{ number_format($cart->total_price-$charge, 2, '.', '')}}</td>
                          @else
                          <td class="lead" id="total">RM 0.00</td>
                          @endif
                          </tr>
                      <tr>
                      <th class="text-muted" scope="row" style="font-size:16px">Caj yang dikenakan:</th>
                          @if($cart)
                          <td class="lead" id="charge" charge="{{$charge}}">RM {{ number_format($charge, 2, '.', '') }}</td>
                          @else
                          <td class="lead" id="charge" charge="0">RM 0.00</td>
                          @endif
                          </tr>
                          <tr>
                        <th class="text-muted" scope="row" style="font-size:20px">Jumlah Keseluruhan:</th>
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
        @if($allDay[0]->date_selection_enable ==1)
        <div class="card mb-4 border">
          <div class="card-body p-4">
            <div class="row">
              <div class="col">
               
                <div class="form-group required">

                  
                  <label class="col-sm-2">Hari Pengambilan</label>
                    <div class="col">
                        <select class="form-control" data-parsley-required-message="Sila pilih hari" id="pick_up_date" required>
                          <option value="" selected>Pilih Hari</option>
                          @foreach($allDay->where('status',1) as $row)
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
        @else
          <select class="form-control d-none" data-parsley-required-message="Sila pilih hari" id="pick_up_date" required >
                  <option value="-1" selected></option>
          </select>
          @endif
        <!-- <div class="card mb-4 border">
          <div class="card-body p-4">
            <div class="row">
              <div class="col">
              <div class="form-group">
             
                                <label>Pilih Bank</label>
                                <select name="bankid" id="bankid" class="form-control"
                                    data-parsley-required-message="Sila pilih bank" required>
                                    <option value=""> Pilih bank</option>
                                </select>

                            </div>
              </div>
            </div>
          </div>
        </div> -->

        

        <div class="card mb-4 border">
          <div class="card-body p-4">
            <div class="row">
              <div class="col">
                <div class="form-group required">
                  <label class="col">Nota kepada Koperasi</label>
                  <div class="col">
                    <input type="text" name="note" class="form-control" id="noteTxt" placeholder="Optional" data-parsley-required-message="Sila isi nota">
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
              <button type="submit" class="btn btn-primary" >Bayar</button>
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
        var inputElement = document.getElementById('noteTxt');
        
        const pholder = "{{$allDay[0]->note_requirement}}";
        if(pholder){
          // Check if pholder is null or empty, if not, use the original placeholder
          inputElement.placeholder = pholder ? pholder : 'Optional';
          inputElement.required = true;
          $('#week_status').val(-1);
        }
       
    });
    
    $('.alert').delay(12000).fadeOut();

    $('#pick_up_date').on('change', function() {
        $('#week_status').val(this.value);
        // console.log($('#week_status').val());
    });

    // function checkBank() {
    //     var t = jQuery('#bankid').val();
    //     if (t === '' || t === null) {
    //         alert('Please select a bank');
    //         return false;
    //     }
    // }
    
    $(document).ready(function() {
	    $('.form-validation').parsley();
    });

    var arr = [];

    // $.ajax({
    //     type: 'GET',
    //     dataType: 'json',
    //     url: "/fpx/getBankList",
    //     success: function(data) {
    //         jQuery.each(data.data, function(key, value){
    //             arr.push(key);
    //         });
    //         for(var i = 0; i < arr.length; i++){
    //             arr.sort();
    //             $("#bankid").append("<option value='"+data.data[arr[i]].code+"'>"+data.data[arr[i]].nama+"</option>");
    //         }

    //     },
    //     error: function (data) {
    //         // console.log(data);
    //     }
    // });
    let order_cart_id = null;//update when the user minus until zero at minus button
    let productOrderInCartId=null;
    $(document).on('click', '#delete_confirm_item', function(){
      console.log(order_cart_id);
            $.ajax({
                url: "{{ route('koperasi.destroyItemCart', ['org_id' => $id]) }}",
                method: "DELETE",
                data: {cart_id:order_cart_id,productOrderInCartId:productOrderInCartId},
                beforeSend:function() {
                    $('.loading').show()
                    $(this).hide()
                    $('.alert-success').empty()
                },
                success:function(result) {
                  //console.log(result.data);
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
            console.log("now qty"+inputQuantity);
            let qtyAvailable = parseInt(this.nextElementSibling.getAttribute("data-qty-available"));
            let productOrderId = this.parentElement.getAttribute("data-product-order-id");
            let pgngOrderId = this.parentElement.getAttribute("data-pgng-order-id");
            //console.log(inputQuantity+" "+qtyAvailable+" "+productOrderId+" "+pgngOrderId);

            if(inputQuantity < qtyAvailable){
                inputQuantity++;
                this.previousElementSibling.value = inputQuantity;
                //console.log(this,inputQuantity,productOrderId,pgngOrderId);
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
            //console.log(pgngOrderId,productOrderId);
            if(inputQuantity >= 1){
               if(inputQuantity==1){
                order_cart_id =pgngOrderId;
                productOrderInCartId=productOrderId;//update this so that can pass to delte model
                $('#deleteConfirmModal').modal('show');
               }
               else{
                inputQuantity--;
                this.nextElementSibling.value = inputQuantity;
                //console.log(this,inputQuantity,productOrderId,pgngOrderId);
                updateInputQuantity(this,inputQuantity,productOrderId,pgngOrderId);
               }
                
            }
        })
    }) ;

    $("input[type='number']").on('keyup', function(e) {
      var input =this;
      let productOrderId = this.parentElement.getAttribute("data-product-order-id");
      let pgngOrderId = this.parentElement.getAttribute("data-pgng-order-id");

      if ( $(this).val()===''){
        setTimeout(function() {
          if($(input).val()===''){
            input.value=1;
            updateInputQuantity(input, 1, productOrderId, pgngOrderId);
            console.log("success");
          }  
        }, 2000);      
    }
    else if ($(this).val()==='0'){
      $(this).val(1);
      updateInputQuantity(this, 1, productOrderId, pgngOrderId);
    }
      
      // Rest of your code goes here
      // ...
    });

    $("input[name='quantity-input']").on('input', function(e) {
    let qtyAvailable = parseInt(this.nextElementSibling.nextElementSibling.getAttribute("data-qty-available"));
    let productOrderId = this.parentElement.getAttribute("data-product-order-id");
    let pgngOrderId = this.parentElement.getAttribute("data-pgng-order-id");
    
    //const currentValue = parseInt($(this).val());
    const newValue = parseInt($(this).val());
    //console.log(newValue);
    if (newValue < 1) {
        // if the new value is less than 1, set it to 1
        e.preventDefault();
        $(this).val('');
        $(this).val(1);
        updateInputQuantity(this, 1, productOrderId, pgngOrderId);
    } else if (newValue > qtyAvailable) {
        // if the new value is greater than qtyAvailable, set it to qtyAvailable
        e.preventDefault();
        $(this).val('');
        $(this).val(qtyAvailable);
        updateInputQuantity(this, qtyAvailable, productOrderId, pgngOrderId);
    } else {
        // otherwise, update the input value with the new value
        //console.log(newValue);
        updateInputQuantity(this, newValue, productOrderId, pgngOrderId);
    }
    
});
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

                        let charge= parseFloat(result.charges);
                        $('#charge').attr('charge', charge);
                        $('#charge').text('RM ' + charge.toFixed(2));
                        totalBeforeCharge=parseFloat(result.totalPrice)-charge;

                         
                        $('#total').html("RM "+totalBeforeCharge.toFixed(2));
                        $totalPrice = $('#totalPrice');
                        //console.log(result.totalPrice+" "+totalBeforeCharge+" "+charge );
                        $totalPrice.html("RM "+result.totalPrice.toFixed(2));

                },
                // error:function(result) {
                //     console.log(result.responseText)
                // }
           })};

    function validateCart(event) {
    // Perform your validation logic here
    // If the validation fails, prevent the form from submitting
    event.preventDefault();
    @if (isset($cart))
    let pgngOrderId = {{$cart->id}};
    @endif
    

     $.ajax({
            url: "{{route('koperasi.checkCart')}}",
            method: "POST",
            data: {
                pgngOrderId: pgngOrderId,
            },
            success:function (result){
              if (parseInt(result.insufficientQuantity) === 1) {
                event.preventDefault();
                alert("Some items in cart have not sufficient quantity in stock now");
                location.reload();
            } else if (parseInt(result.insufficientQuantity) === 0) {
                 console.log("no err");
                 event.target.submit();
            }
            return false;
            }
        });
  };

</script>
@endsection