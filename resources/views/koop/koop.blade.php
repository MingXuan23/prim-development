@extends('layouts.master')

@section('css')
{{-- <script src="{{ URL('assets/libs/bootstrap-touchspin-master/src/jquery.bootstrap-touchspin.css')}}"></script> --}}
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/libs/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/css/checkbox.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/css/accordion.css') }}" rel="stylesheet" type="text/css" />
<style>

.card-body
{
    width:auto;
    height: auto;
}

.card-title
{
    width:800px;
    height: auto;
}



.row-picture
{
    width:100%;
    height: 350px;
    position: relative;
    text-align: center;
     color: white;

}

.fill
{
   
    width:100%;
    height:100%;
}

.col-lg-4
{
  margin: auto;

  padding: 10px;
}

.top-left {
  position: absolute;
  top: 8px;
  left: 16px;
  color:white;
}

#img-size
{
  /* max-width: 100%; */
  height: 100px;
  width: 100px;
  object-fit: cover;
}

.shadow-bg{
  border-radius: 14px;
  box-shadow: 7px 6px 12px 5px rgba(0,0,0,0.10);
  
  
}
.default-img {
  background-color:rgb(61, 61, 61);
}

.cart-btn {
  position: relative;
}

.cart-btn .notification {
  position: absolute;
  top: -5px;
  right: -5px;
  padding: 5px 10px;
  border-radius: 50%;
  background: red;
  color: white;
}

#quantity-danger{
  display: none;
}

.center-danger{
  width: 100%;
  padding: 6px 10px;
  border-radius: 4px;
  border-style: solid;
  border-width: 1px;
  margin-top: 5px;
  margin-bottom: 5px;
  font-size: 12px;

  background-color: rgba(248, 215, 218, 1);
  border-color: rgba(220, 53, 69, 1);
  color: rgba(114, 28, 36,1);

  text-align: center;
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

@foreach($Sekolah as $sekolah)

<br>
<!-- <div class="row">
    <div class="row-picture">
    <img class="fill" src="{{$sekolah->organization_picture}}" alt="Card image">
    <h4 class="top-left">{{$sekolah->nama}}</h4>

    </div>
</div> -->
@if(Session::has('success'))
    <div class="alert alert-success">
    <p>{{ Session::get('success') }}</p>
    </div>
@elseif(Session::has('error'))
    <div class="alert alert-danger">
    <p>{{ Session::get('error') }}</p>
    </div>
@endif

<div class="row">
    <div class="col-12">
        <h4 class="my-3"></h4>
        <div class="card-group">
            <div class="card mb-4">
                @if($sekolah->organization_picture == NULL)
                
                <div class="col-lg-4">
                    <div class="card text-white bg-dark">
                        <div class="card-body">
                        <blockquote class="card-blockquote mb-0">
                        <img class="card-img-top img-fluid" src="{{ URL('images/koperasi/default-item.png')}}" alt="Card image cap">
                            </blockquote>
                            </div>
                    </div>
                </div>
                @else
                <img class="card-img-top img-fluid" src="{{$sekolah->organization_picture}}" alt="Card image cap" style="height: 300px;">
                @endif

                <!-- <img class="card-img-top img-fluid" src="{{ URL('images/koperasi/default-item.png')}}" alt="Card image cap" style="height: 300px;"> -->
                <div class="card-body">
                    <h4 class="card-title">{{$sekolah->nama}}</h4>
                    <p class="card-text">
                        <small class="text-muted"><i class="fas fa-map-marker-alt mr-2"></i> {{ $koperasi->address }}, {{ $koperasi->city }}, {{ $koperasi->state }}</small>
                        <small>
                            <div class="d-flex">
                                @if($koperasi->status != 0)
                                    <p class="mr-4"><b>Waktu Buka</b></p>
                                    <p>Hari ini {{ $k_open_hour }} - {{ $k_close_hour }}</p>
                                    @else
                                    p><b>Tutup pada hari ini</b></p>
                                @endif
                            </div>
                        </small>
                    </p>
                    <!-- <a href="{{route('koperasi.koopCart',$sekolah->id)}}" class=" btn btn-primary waves-effect waves-light"> -->
                    <a href="{{ route('koperasi.edit', $sekolah->id) }}" class=" btn btn-primary waves-effect waves-light">
                    <i class="fas fa-cart-arrow-down"></i> View Cart</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- <h2>Product Item</h4>
<br>
<div class="row">

@foreach($products as $product)

<div class="col-md-6 col-lg-6 col-xl-3"> -->
        
        <!-- Simple card -->
        <!-- <div class="card">
            @if($product->image == NULL)
    
                                            <div class="card text-white bg-dark">
                                                <div class="card-body">
                                                <blockquote class="card-blockquote mb-0">
                                                <img class="card-img-top img-fluid" src="{{ URL('images/koperasi/default-item.png')}}" alt="Card image cap">
                                                 </blockquote>
                                                 </div>
                                            </div>

            @else
            <img class="card-img-top img-fluid" src="{{$product->image}}" alt="Card image cap">
            @endif
            @if($product->status == 0) 
                                         <div class="d-flex justify-content-center"><span class="badge badge-danger">not aivalable</span></div>
                                         @else
                                         <div class="d-flex justify-content-center"><span class="badge badge-success">aivalable</span></div>
            @endif
            <div class="card-body">
                <h4 class="card-title">{{$product->name}}</h4>
                @if($product->desc == NULL)
                <p class="card-text"><br></p>
                @else
                <p class="card-text">{{$product->desc}}</p>
                @endif
                <p class="card-text">RM{{ number_format((double)$product->price, 2, '.', '') }}</p>

                <form action="{{ route('koperasi.store') }}" method="POST">
                    @csrf       
                    <div class="text-left">
                            @if($product->status != 0)  
                            <input type="number" name="item_quantity" value="1" min="1" step="1" class="form-group-sm" style="width:20%; height:70%" required>
                            <input type="hidden" id="item_id" name="item_id" value="{{ $product->id }}">
                            <input type="hidden" id="org_id" name="org_id" value="{{ $sekolah->id }}">
                            <button type="submit" class="btn btn-primary waves-effect waves-light">Kemaskini cart</button>
                            @else
                            @endif
                    </div>
                </form>
                
            </div>
        </div>

    </div>end col
@endforeach        
</div> -->
<div class="card-body pl-0 pr-0">
    <label for="DisplayParentName">
        <span style="font-size: 18px">{{ $childrenByParent[0]->parentName }}</span>
        <br> 
    </label>
    {{-- display item --}}
  
    @foreach($childrenByParent as $key=>$child)
    @if($key === 0)
    <!-- display the option to choose genaral product or all products  -->
        <div class="inputGroup">
            <input
                id="GeneralItem"
                class="childrenList"
                checked
                value="GeneralItem"
                onchange=""
                type="checkbox"
                 />

            <label for="GeneralItem">
                <span style="font-size: 18px">General Products/Items</span>
                <br>
            </label>
        </div>
        <div class="inputGroup">
            <input
                id="otherProduct"
                class="childrenList"
                value="AllItem"
                onchange=""
                type="checkbox" />

            <label for="otherProduct">
                <span style="font-size: 18px">All Products/Items</span>
                <br>
            </label>
        </div>
        
        
    @endif
    <!-- display the children with their class -->
    <div class="inputGroup">
        <input
            id="option-{{ $child->id }}-{{ $child->nama }}"
            class="childrenList"
            value="{{$child->className}}"
            onchange=""
            type="checkbox" />

        <label for="option-{{ $child->id }}-{{ $child->nama }}">
            <span style="font-size: 18px">{{ $child->nama}} ({{$child->className}})</span>
            <br>
            
        </label>
    </div>
    
    @endforeach   
</div>

<div class="card-body pl-0 pr-0" id="product-div">
        
        <div class="flash-message"></div>

        <div class="row">
                <div class="col">
                  <div class="card  p-2">
                  <label><span style="font-size: 18px;margin:10px 10px">Pilih Kategori</span></label>
                    <div class="d-flex">
                        
                        <select name="product_group" id="group_combobox" class="form-control">
                            <option value="AllItem" class="categoryGroup" selected>Semua Kategori </option>
                            @php
                            $groups = $products->pluck('groupId', 'groupName')->unique()->map(function ($groupId, $groupName) {
                                return [
                                    'groupId' => $groupId,
                                    'groupName' => $groupName,
                                ];
                            });
                            
                            @endphp
                            @foreach($groups as $group)
                                <option value="{{$group['groupId']}}" class="categoryGroup">{{$group['groupName']}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="product-container">
                    <!-- @foreach($products as $item)
                    <div class="row">
                        <div class="col">
                        <div class="card  p-2" >
                            <div class="d-flex">
                            <div class="d-flex justify-content-center align-items-start">
                                <div>
                                @if($item->image == null)
                                <img class="rounded img-fluid default-img" id="img-size"  src="{{ URL('images/koperasi/default-item.png')}}">
                                @else
                                <img class="rounded img-fluid " id="img-size" src="{{ URL('/koperasi-item/'.$item->image)}}">
                                @endif
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
                                    <p class="card-text"><b>RM</b> {{ $item->price }}</p>
                                    </div>
                                    <div class="ml-auto">
                                    @csrf
                                    @if($item->status != 0)
                                    <div class="button-cart-section">
                                        <button type="button" class="btn btn-success btn-item-modal" data-item-id="{{ $item->id }}" ><i class="mdi mdi-cart"></i></button>
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
                    
                    @endforeach -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
          

    </div>
<!--to show the product with the tingkatan category-->
<br>
<br>
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

    //to use ajax
$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
$(document).ready(function(){
    
    $.ajax({
               url: "{{ route('koperasi.fetchClassYear') }}",
               method: "GET",
               success: function(result) {
                            jQuery.each(result.datayear, function(key, value) {
                                $("#group_combobox").append("<option value='Tahun"+ value.year +"' class='tahunGroup'> Tahun " + value.year + "</option>");
                                // $(".cbhide").append(
                                //     "<label for='cb_year' style='margin-right: 22px;' class='form-check-label'> <input class='yearCheckbox' data-parsley-required-message='Sila pilih tahun' data-parsley-errors-container='.errorMessageCB' type='checkbox' id='cb_year' name='cb_year[]' value='" +
                                //     value.year + "'/> " +"Tahun " +value.year + " </label><br> <div class='errorMessageCB'></div>");
                                //     $(".cbhide").hide();
                            });  
                        }                  
                    });
                    
    $('.childrenList').change(function() {
        $('.childrenList').prop('checked', false);
        $(this).prop('checked', true);
        $('#product-div').show();
        const selectedGroup = document.querySelector('.childrenList:checked').value;
        if(selectedGroup!="AllItem" &&selectedGroup!="GeneralItem")
        {
            updateOption("tahun");
        }
        else{
            updateOption("category")
        }

        loadProductList(selectedGroup);
    });
    const selectedGroup = document.querySelector('.childrenList:checked').value;
    loadProductList(selectedGroup);

    
    
    $('#group_combobox').change(function(){
        
        var selectedGroup=$('#group_combobox').val();
        loadProductList(selectedGroup);
        
    });

    
});

function loadProductList(selectedGroup){
    //console.log(selectedGroup);
    $.ajax({
            url: '{{route("koperasi.productsListByGroup")}}', // Replace with your Laravel route
            type: 'POST', // Replace with GET or POST depending on your route
            data:{
                selectedGroup:selectedGroup,
                kooperasiId:{{$koperasi->id}}
            },
            success: function(response) {
                // Clear the current content of the container div
                $('#product-container').empty();

                // Loop through the new products data and append HTML for each product to the container
                if(response.products.length!=0){
                    console.log("exist");
                    $.each(response.products, function(index, item) {
                    var html = '<div class="row ">';
                    html += '<div class="col" >';
                    html += '<div class="card p-2" style="margin:0px 0px 10px">';
                    html += '<div class="d-flex shadow-bg" style="padding: 0px 0px 10px;">';
                    html += '<div class="d-flex justify-content-center align-items-start " >';
                    html += '<div>';

                    if (item.image == null) {
                        html += '<img class="rounded img-fluid default-img" id="img-size"  src="{{ URL("images/koperasi/default-item.png")}}">';
                    } else {
                        html += '<img class="rounded img-fluid" id="img-size" src="{{ URL("/koperasi-item") }}/' + item.image + '">';
                    }

                    html += '</div>';
                    html += '</div>';
                    html += '<div class="col">';
                    html += '<div class="d-flex align-items-start flex-column h-100">';
                    html += '<div>';
                    html += '<h4 class="mt-2">' + item.name + ' <span class="badge badge-light">' + '</span>';

                    if (item.status != 1) {
                        html += '<label class="text-danger">Kehabisan Stok</label>';
                    }

                    html += '</h4>';
                    html += '</div>';
                    html += '<div>';
                    if(item.desc !=null)
                    {
                        html += '<p class="card-text"><i>' + item.desc + '</i></p>';
                    }
                    
                    html += '</div>';
                    html += '<div class="mt-auto d-flex justify-content-between align-items-center w-100">';
                    html += '<div class="">';
                    html += '<p class="card-text"><b>RM</b> ' + item.price + '</p>';
                    html += '</div>';
                    html += '<div class="ml-auto">';

                    if (item.status != 0) {
                        html += '<div class="button-cart-section">';
                        html += '<button type="button" class="btn btn-success btn-item-modal" data-item-id="' + item.id + '"><i class="mdi mdi-cart"></i></button>';
                        html += '</div>';
                    }

                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';

                    // Append the HTML for the product to the container
                    $('#product-container').append(html);
                });
                initialiseCartButton();
                }
                else{
                    
                    var html = '<div class="row">';
                    html += '<div class="col">';
                    html += '<div class="card p-2" style="margin:0px 4px">';
                    html += '<div class="d-flex">';
                    html += '<div class="d-flex justify-content-center align-items-start">';
                    html += '<div>';

                    html+='<label for="DisplayParentName">';
                    html+='<br>';
                    html+='<span style="font-size: 24px;" class="center-danger">Tiada Produk</span>';
                    html+='<br></label>';

                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    console.log("run");
                    $('#product-container').append(html);

                }
                
            },
            error: function(xhr, status, error) {
                // Handle the error
                console.log(error+"error");
            }
        });
        
        
}
    
function updateOption(option){
    const category=$('.categoryGroup');
    const tahun=$('.tahunGroup');
    if(option=="category")
    {
        category.show();
        tahun.hide();
        
    }
    else if(option=="tahun"){
        category.hide();
        tahun.show();
    }
    
}

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
            $('#quantity-danger').addClass('center-danger').show()
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

function initialiseCartButton()
{
    console.log("runCart");
    //alert($('.btn-item-modal').length);
    $('.btn-item-modal').click(function(e) {
        console.log("runCart2");
      e.preventDefault();
      let modalTitle = $('.modal-title'), modalBody = $('.modal-body');
      modalTitle.empty();
      modalBody.empty();
      item_id = $(this).attr('data-item-id');
      org_id = {{$koperasi->id}};
      
      $.ajax({
        url: "{{ route('koperasi.fetchItemToModel',$koperasi->id) }}",
        method: "POST",
        data: {i_id:item_id, o_id:{{$koperasi->id}}},
        beforeSend:function() {
          $('#addToCartModal').modal('show')
          modalBody.append("<div class='text-center'><img src='{{ URL('images/koperasi/loading-ajax.gif')}}' style='width:40px;height:40px;'></div>")
        },
        success:function(result)
        {
          modalBody.empty()
          modalTitle.append(result.item.name)
          modalBody.append(result.body)
          
          quantityExceedHandler($("input[name='quantity_input']"), result.quantity)
        },
        error:function(result)
        {
          console.log(result.responseText)
        }
      })
    })

    $('.cart-add-btn').click(function(){
      let quantity = $("input[name='quantity_input']").val()
        console.log(item_id+ " "+org_id+" "+quantity );
      $.ajax({
        url: "{{ route('koperasi.storeInCart') }}",
        method: "POST",
        data: {
          i_id:item_id,
          o_id:org_id,
          qty:quantity,
        },
        beforeSend: function() {
          $(this).css('display', 'none')
          $('.loading').show()
        },
        success:function(result)
        { 
         
          $(this).show();
          $('.loading').hide();
          $('div.flash-message').empty();
          $('#addToCartModal').modal('hide');
          var message = "<div class='alert alert-success'>Add to cart successfully</div>";
            $('div.flash-message').show();
            $('div.flash-message').append(message);
            $('div.flash-message').delay(3000).fadeOut();
            notificationCounter(org_id);

            $("html, body").animate({ scrollTop: 0 }, "slow");

        },
        error:function(result)
        {
          console.log(result.responseText)
        }
      })
    });
}
</script>
@endsection