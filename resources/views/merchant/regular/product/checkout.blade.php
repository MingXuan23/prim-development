@extends('layouts.master')

@section('css')
     {{-- bootstrap-icons --}}
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
     <link href="{{ URL::asset('assets/css/required-asterick.css')}}" rel="stylesheet">
     <link rel="stylesheet" href="{{ URL::asset('assets/css/datatable.css')}}">
     <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
     @include('layouts.datatable')
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
            /* for submit button */
            .submit-btn {
                border: none;
                background: none;
            }
            .submit-btn span {
                color:var(--primary-color);
                padding-bottom: 7px;
                font-family: Roboto, sans-serif;
                font-size: 17.5px;
                padding-right: 15px;
                text-transform: uppercase;
            }
            .submit-btn svg {
                transform: translateX(-8px);
                transition: all 0.3s ease;
            }
            .submit-btn:hover svg {
                transform: translateX(0);
            }
            .submit-btn:active svg {
                transform: scale(0.9);
            }
            .hover-underline-animation {
                position: relative!important;
                color:var(--primary-color);
                padding-bottom: 20px;
            }
            .hover-underline-animation:after {
                content: "";
                position: absolute!important;
                width: 100%;
                transform: scaleX(0);
                height: 2px;
                bottom: 0;
                left: 0;
                background-color: var(--primary-color);
                transform-origin: bottom right;
                transition: transform 0.25s ease-out;
            }
            .submit-btn:hover .hover-underline-animation:after {
                transform: scaleX(1);
                transform-origin: bottom left;
            }
            .form-control{
                border: 2px solid #5b626b6c!important;
            }
            .form-control:focus{
                outline: none;
                border: 2px solid #5b626b!important;
            }

            @media screen and (max-width: 500px){
                .page-content{
                    padding: 80px 0!important;
                }
                .order-procurement-container{
                    gap: 1rem;
                }
                .procurement-option-container{
                    width: 100%;
                    height: 120px;
                }
            }
    </style>
@endsection

@section('content')
    <h1>Semak Keluar</h1>
    <h3>Dapatkan Pesanan Anda</h3>
    <div class="order-procurement-container">
        <div class="procurement-option-container" onclick="selectThis(this,'GNG')" id="gng-option">
            <span class='selected' hidden><i class="bi bi-check2"></i></span>
            <h5>Get & Go</h5>
            <div>
                <h5><i class="bi bi-shop"></i> Tempah secara dalam talian, kemudian ambil di kedai fizikal.</h5>
            </div>
        </div>
        <div class="procurement-option-container"  onclick="selectThis(this,'Delivery')">
            <span class='selected' hidden><i class="bi bi-check2"></i></span>
            <h5>Standard Delivery</h5>
            <div>
                <h5><i class="bi bi-truck"></i> Penghantaran ke alamat anda</h5>
            </div>
        </div>
    </div>
    <div class="form-container">
        <section class="gng-section" hidden>
            <h3>Produk Dipesan</h3>
            <input type="hidden" name="cart_id" id="cart_id" value="@if($cart){{ $cart->id }}@endif">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless responsive" id="cartTable" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr class="text-center">
                                <th style="width: 45%">Nama</th>
                                <th style="width: 10%">Kuantiti</th>
                                <th style="width: 22.5%">Harga Per Unit (RM)</th>
                                <th style="width: 22.5%">Subtotal Barang (RM)</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            @if($cart)
        <div class="row">
            <div class="col-sm-6 mb-4">
            <div class="card  h-100 border">
                <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-borderless mb-0">
                        <tbody>
                        <tr>
                            <th class="text-muted" scope="row">Jumlah Pesanan:</th>
                            <td class="lead" style="color:rgb(2, 122, 129)">RM {{ number_format((double)($cart->total_price - $response->fixed_charges), 2, '.', '') }}</td>
                        </tr>
                        @if($response->fixed_charges != null  )
                            <tr>
                                <th class="text-muted" scope="row">Caj Servis:</th>
                                <td class="lead" style="color:rgb(2, 122, 129)">RM {{ number_format((double)$response->fixed_charges, 2, '.', '') }}</td>
                            </tr>
                        @else
                            <tr>
                                <th class="text-muted" scope="row">Caj Servis:</th>
                                <td class="lead" style="color:rgb(2, 122, 129)">RM 0</td>
                            </tr>
                        @endif
                        <tr>
                            <th class="text-muted" scope="row">Jumlah Keseluruhan:</th>
                            <td class="lead" style="color:rgb(2, 122, 129)">RM {{ number_format((double)$cart->total_price, 2, '.', '') }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                </div>
            </div>
            </div>

            <div class="col-sm-6">
            <form action="{{ route('directpayIndex') }}" method="POST" enctype="multipart/form-data" id="form-order">
                @csrf
                <div class="card mb-4 border">
                <div class="card-body p-4">

                    <input type="hidden" id="org_id" name="org_id" value="{{$cart->org_id}}">
                    <input type="hidden" name="amount" id="total_price">
                    <input type="hidden" name="desc" id="desc" value="Merchant">
                    <input type="hidden" name="order_id" id="order_id">

                    <div class="row">
                        <div class="col pickup-date-div">
                            <div class="form-group ">
                                <label><span><i class="fas fa-map-marker-alt"></i></span> Lokasi Pengambilan</label>
                                <p>{{$response->pickup_location}}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 pickup-date-div">
                            <div class="form-group required">
                                <label><span><i class="fas fa-calendar-alt"></i></span> Tarikh Pengambilan</label>
                                <input type="text" value="{{ $response->pickup_date }}" class="form-control" name="pickup_date" id="datepicker"  placeholder="Pilih tarikh" readonly required>
                            </div>
                        </div>
                        <div class="col-md-6 pickup-time-div" hidden>
                            <div class="form-group required">
                            <label><span><i class="fas fa-clock"></i></span> Masa Pengambilan</label>
                            <div class="timepicker-section">
                            <input type="time" value="{{ $response->pickup_time }}" class="form-control" name="pickup_time" id="timepicker" required>
                            <p class="time-range"></p>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-12">
                <div class="card mb-4 border">
                    <div class="card-body p-4">
                        <div class="form-group">
                            <div><span><i class="fas fa-sticky-note"></i></span> Nota kepada Peniaga</div>
                            <textarea class="form-control" name="note" placeholder="Optional">{{ $cart->note }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" name="order_type" id="hidden_order_type" value="Pick-Up">

        <div class="row mb-2">
            <div class="col d-flex justify-content-end">
            <a href="{{route('merchant.all-cart')}}" type="button" class="btn-lg btn-light mr-2" style="color:#5b626b;">KEMBALI</a>
            <button class="submit-btn" type="button">
                <span class="hover-underline-animation"> Membuat Pesanan</span>
                <svg viewBox="0 0 46 16" height="10" width="30" xmlns="http://www.w3.org/2000/svg" id="arrow-horizontal">
                    <path transform="translate(30)" d="M8,0,6.545,1.455l5.506,5.506H-30V9.039H12.052L6.545,14.545,8,16l8-8Z" data-name="Path 10" id="Path_10"></path>
                </svg>
            </button>
            </div>
        </div>

        </form>

    @else
      <div class="d-flex justify-content-center">
        <a href="{{route('merchant.all-cart')}}" type="button" class="btn-lg btn-light mr-2" style="color:#5b626b;">KEMBALI</a>
      </div>
    @endif

        </section>
        <section class="delivery-section" hidden>
            <h5 style="text-align:center ;margin-top: 20px;">Under Construction</h5>
        </section>
    </div>
  @endsection
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


@section('script')
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function(){

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            let org_id = $('input#org_id').val()
            let cId = $('input#cart_id').val()
            fetch_data(cId)

            function fetch_data(cId = '') {
                cartTable = $('#cartTable').DataTable({
                    "searching": true,
                    "bLengthChange": false,
                    "bPaginate": false,
                    "info": false,
                    "orderable": false,
                    "ordering": true,
                    processing: false,
                    serverSide: true,
                    "language": {
                        "zeroRecords": "Tiada Item Buat Masa Sekarang."
                    },
                    ajax: {
                        url: "{{ route('merchant.get-checkout-items') }}",
                        data: {
                            id:cId,
                            type:'cart',
                            "_token": "{{ csrf_token() }}",
                        },
                        type: 'GET',
                    },
                    'columnDefs': [{
                        "targets": [0, 1, 2, 3], // your case first column
                        "className": "align-middle text-center",
                    },
                    { "responsivePriority": 1, "targets": 0 },
                    { "responsivePriority": 2, "targets": 2 },
                    { "responsivePriority": 2, "targets": 3 },
                    ],
                    columns: [{
                        data: "name",
                        name: 'name',
                        orderable: true,
                        searchable: true,
                    }, {
                        data: "quantity",
                        name: 'quantity',
                        orderable: true,
                        searchable: true,
                    }, {
                        data: 'price',
                        name: 'price',
                        orderable: true,
                        searchable: true,
                    },{
                        data: "sub_total",
                        name: 'sub_total',
                        orderable: true,
                        searchable: true,
                    }, ]
                });
            }


            $('#datepicker').change(function() {
                dateOnChange()
            })

            function dateOnChange() {
                let date_val = $('#datepicker').val(), timePicker = $('#timepicker'), timeRange = $('.time-range')
                if(date_val != '') {
                    $('.pickup-time-div').removeAttr('hidden')
                    $.ajax({
                    url: '{{ route("merchant-reg.fetch-hours") }}',
                    method: 'POST',
                    data: {org_id:org_id, date:date_val, "_token": "{{ csrf_token() }}",},
                    beforeSend:function() {
                        timeRange.empty()
                    },
                    success:function(result) {
                        if(result.hour.open) {
                        timePicker.prop('disabled', false)
                        timePicker.attr('min', result.hour.min)
                        timePicker.attr('max', result.hour.max)
                        timeRange.append(result.hour.body)
                        } else {
                        timePicker.prop('disabled', true)
                        timeRange.append(result.hour.body)
                        }
                    },
                    error:function(result) {
                        console.log(result.responseText)
                    }
                    })
                } else {
                    $('.pickup-time-div').attr('hidden', true)
                }
            }

        var dates = []
        $(document).ready(function() {
            $.ajax({
                url: '{{ route("merchant-reg.disabled-dates") }}',
                method: 'post',
                data: {org_id:org_id, "_token": "{{ csrf_token() }}",},
                success:function(result) {
                $.each(result.dates, function(index, value) {
                    dates.push(value)
                })
                },
                error:function(result) {
                console.log(result.responseText)
                }
            })
        })


        $("#datepicker").datepicker({
            minDate: 0,
            maxDate: '+7d',
            dateFormat: 'mm/dd/yy',
            dayNamesMin: ['Ahd', 'Isn', 'Sel', 'Rab', 'Kha', 'Jum', 'Sab'],
            beforeShowDay: editDays
        })

        disabledDates = dates

        function editDays(date) {
        for (var i = 0; i < disabledDates.length; i++) {
            if (new Date(disabledDates[i]).toString() == date.toString()) {
                // if the date is equal with disabled dates(dates),then return false
            return [false];
            }
        }
        return [true];
        }
    // form submit
    $('#form-order').on('submit', function(e){
        $('#total_price').val('{{$cart->total_price}}');
        $('#order_id').val('{{$cart->id}}');
    });

    $('.submit-btn').on('click', function(e){
        if($('input[name="pickup_date"]').val() == ''){
            e.preventDefault();
            Swal.fire({
                title:"Sila Pilih Tarikh Pengambilan",
                icon: "warning",
            });
            return;

        }
        var form = document.getElementById('form-order');
        if(form.reportValidity()){
            Swal.fire({
            title: 'Adakah anda pasti?',
            text: "Anda akan dialihkan ke payment gateway",
            footer: "<small style='color:#f14343;' >Pesanan dalam sistem kami adalah tidak boleh dipulangkan wang, dan sebarang pemulangan perlu diuruskan antara pembeli dan penjual.</small>",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, teruskan dengan pembayaran!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#form-order').submit();
                }
            })
        }

    });
})
        const orgs_id = document.querySelectorAll('#org_id');
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
        selectThis(document.getElementById('gng-option') , 'GNG')
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
            {{-- // const datePickers = document.querySelectorAll('input[name = "pickup_date"]');
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
        // }) --}}

