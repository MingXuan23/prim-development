@extends('layouts.master')

@section('css')
     {{-- bootstrap-icons --}}
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    
     <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
   <style>
         :root {
                --primary-bc: #ffffff;
                --secondary-bc: rgb(2, 122, 129);
                --hover-color:rgb(6, 225, 237);
                --primary-color:#5b626b;
                --transition: all 0.3s linear;
            }
            .main-content{
                color: var(--primary-color);
            }
            .order-procurement-container {
                margin-top: 2rem;
                display: flex;
                flex-direction: row;
                flex-wrap: wrap;
                justify-content:center;
                gap:3rem;
                width: 100%;
            }
            .procurement-option-container{
                border: 2px solid rgb(218, 218, 218);
                padding: 0.5rem;
                width: 40%;
                position: relative;
            }
            .procurement-option-container:hover{
                border: 2px solid var(--primary-color)!important;
            }
            .procurement-option-container .selected{
                position: absolute;
                top: -16px;
                right: -16px;
                padding: 5px 10px;
                border-radius: 50%;
                background: var(--primary-color);
                color: white;
                font-size: 16px;
            }
            .gng-section{
                margin-top: 20px;
                margin-left: auto;
                margin-right: auto;
            }
            .shop-order-container{
                border-top: 1px solid #ccc;
            }
            .shop-order-details:last-child{
                border-bottom:  1px solid #ccc!important;

            }
            .product-image{
                height: 40px;
                width:40px;
            }
            .hide{
                display: none;
            }
            /* if there are multiple items ordered from a store display total price beside the last item */
            .shop-order-container .shop-order-details:last-child .order-total-price{
                display: block;
            }
            .order-summary{
                float:right;
            }
            
    </style>
@endsection

@section('content')
    <h1>Semak Keluar</h1>
    <h3>Dapatkan Pesanan Anda</h3>
    <div class="order-procurement-container">
        <div class="procurement-option-container" onclick="selectThis(this,'GNG')">
            <span class='selected' hidden><i class="bi bi-check2"></i></span>
            <h5>Get & Go</h5>
            <div>
                <h5><i class="bi bi-shop"></i> Reserve online, then collect at the physical store</h5>
            </div>
        </div>
        <div class="procurement-option-container"  onclick="selectThis(this,'Delivery')">
            <span class='selected' hidden><i class="bi bi-check2"></i></span>
            <h5>Standard Delivery</h5>
            <div>
                <h5><i class="bi bi-truck"></i> Deliver straight to your address</h5>
            </div>
        </div>
    </div>
    <div class="form-container">
        <section class="gng-section" hidden> 
            <h3>Produk Dipesan</h3>

            {{-- <table class="table table-borderless responsive" style="text-align: center">
                <thead class="thead-dark">
                <tr style="background-color">
                    <th></th>
                    <th>Nama Barang</th>
                    <th>Harga Seunit</th>
                    <th>Amaun</th>
                    <th>Subtotal Barang</th>
                </tr>
                </thead>
            @foreach($organizations as $organization)
                <tbody class="shop-order-container">
                    <tr >
                        <th>{{$organization->nama}}</th>
                    </tr> 
                    <div>
                        @foreach($products as $product)
                            @if($organization->nama == $product->nama)
                                <tr class="shop-order-details">
                                        {{-- @if($product->image == null) --}}
                                        {{-- <td><img class="img-fluid mx-auto d-block product-image" id="img-size"  src="{{ URL('merchant-image/default-item.jpeg')}}"></td> --}}
                                        {{-- @else
                                        <img class="rounded img-fluid " id="img-size" src="{{ URL('merchant-image/product-item/'.$product->code.'/'.$product->image)}}">
                                        @endif --}}
                                        {{-- <td>{{$product->name}}</td>
                                        <td>RM{{$product->price}}</td>
                                        <td>{{$product->quantity}}</td>
                                        <td class="order-total-price hide">
                                            RM{{$product->total_price}}
                                            @if($organization->fixed_charges != null)
                                                (Cas Servis: RM{{$organization->fixed_charges}})
                                            @endif
                                        </td>
                                </tr>    
                                @endif
                        @endforeach
                </tbody>
                    <form action="" method="POST" enctype="multipart/form-data">
                        @csrf
                        <tr style = "border-bottom: 1px solid #ccc;" >
                            <td></td>
                            <input type="hidden" id="org_id" value="{{ $organization->id }}">
                            <input type="hidden" name="order_type" id="hidden_order_type" value="Pick-Up">
                            <td>
                                <label>Mesej:</label>
                                <textarea class="form-control" name="note" placeholder="Optional(Tinggalkan mesej kepada penjual)" spellcheck="false" rows="2" cols="40"></textarea>
                            </td>
                            <td class="pickup-date form-group required">
                                    <label>Tarikh Pengambilan</label>
                                    <input type="text" class="form-control" name="pickup_date" id="datepicker-{{ $organization->id }}" data-org-id="{{ $organization->id }}"placeholder="Pilih tarikh" readonly required>
                            </td>
                            <td class="pickup-time form-group required">
                                    <label>Masa Pengambilan</label>
                                      <input type="time" class="form-control" name="pickup_time" id="timepicker-{{ $organization->id }}"  data-org-id="{{ $organization->id }}" required hidden>
                                      <p id="time-range-{{ $organization->id }}"class="time-range"></p>
                            </td>
                        </tr>
                    </form>    
            @endforeach
        </table> 
            <div class="order-summary">
                <h3>Jumlah Bayaran: RM{{$cartTotalPrice}}</h3>
            </div> --}}
        </section>
        <section class="delivery-section" hidden>
            <h5>delivery</h5>
        </section>
    </div>
@endsection

@section('script')
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script>
        $(document).ready(function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        })    
            const orgs_id = document.querySelectorAll('#org_id');
            // const datePickers = document.querySelectorAll('input[name = "pickup_date"]');
            // //add event listener to each of the date pickers
            // $.each(datePickers, function(index, datePicker){
            //     //console.log(datePicker);
            //     var currentIndex = index;
            //     $.ajax({
            //         url: '{{ route("merchant-reg.disabled-dates") }}',
            //         method: 'post',
            //         data: {
            //             org_id:orgs_id[index].value, 
            //             "_token": "{{ csrf_token() }}",
            //         },
            //         success:function(result) {
            //             var dates = [];
            //             $.each(result.dates, function(index, value) {
            //                 dates.push(value)
            //             });
            //             $(datePicker).datepicker({
            //                 minDate: 1,
            //                 maxDate: '+2m',
            //                 dateFormat: 'mm/dd/yy',
            //                 dayNamesMin: ['Ahd', 'Isn', 'Sel', 'Rab', 'Kha', 'Jum', 'Sab'],
            //                 beforeShowDay: function(date) {
            //                     for (var i = 0; i < dates.length; i++) {
            //                         if (new Date(dates[i]).toString() == date.toString()) {
            //                         // if the date is equal with disabled dates(dates),then return false  
            //                         return [false];
            //                         }
            //                     }
            //                     return [true];
            //                 },
            //                 onSelect: function(dateText) {
            //                     dateOnChange(this);
            //                 }
            //             });
            //         },
            //         error:function(result) {
            //             console.log(result.responseText)
            //         }
            //     }) 
            // })
            // $.each(datePickers, function(index, datePicker){
            //     $(datePicker).on('change', function(e) {
            //                 console.log(e);
            //                 dateOnChange(e.target);
            //     });
            // })

        //     function dateOnChange(currentTarget) {
        //             let currentDatePicker = currentTarget;
        //             let date_val = $(currentDatePicker).val(), timePicker = $(currentDatePicker).parent().next().children('input[name = "pickup_time"]'), timeRange = timePicker.next();
        //             let org_id = $(currentDatePicker).attr('data-org-id');
        //             if(date_val != '') {
        //                 timePicker.removeAttr('hidden')
        //                 $.ajax({
        //                     url: '{{ route("merchant-reg.fetch-hours") }}',
        //                     method: 'POST',
        //                     data: {org_id:org_id, date:date_val, "_token": "{{ csrf_token() }}",},
        //                     beforeSend:function() {
        //                         timeRange.empty()
        //                     },
        //                     success:function(result) {
        //                         if(result.hour.open) {
        //                             timePicker.prop('disabled', false)
        //                             timePicker.attr('min', result.hour.min)
        //                             timePicker.attr('max', result.hour.max)
        //                             timeRange.append(result.hour.body)
        //                         } else {
        //                             timePicker.prop('disabled', true)
        //                             timeRange.append(result.hour.body)
        //                         }
        //                     },
        //                     error:function(result) {
        //                         console.log(result.responseText)
        //                     }
        //                 })
        //             } else {
        //                 timePicker.attr('hidden', true)
        //             }
        //         }
        // })
        
        function selectThis(element,  option){
            $orderOption = $(element);
            $orderOption.children('.selected').attr('hidden',false);
            $orderOption.css({"border": "2px solid var(--primary-color)"});

            // reset previous option
            $previousOption = $(element).siblings();
            $previousOption.children('.selected').attr('hidden',true);
            $previousOption.css({"border": "2px solid rgb(218, 218, 218)"});

            // Get & Go option
            if(option == 'GNG'){
                loadGNG();
            }else{//Delivery option
                loadDelivery();
            }
        }
        function loadGNG(){
            $('.form-container').children().attr('hidden', true);//hide all children of the container
            $gngSection = $('.gng-section').attr('hidden',false);
        }
        function loadDelivery(){
            $('.form-container').children().attr('hidden', true);//hide all children of the container
            $deliverySection =  $('.delivery-section').attr('hidden',false);
        }
    </script>
@endsection