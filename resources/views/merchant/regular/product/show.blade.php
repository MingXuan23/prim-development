@extends('layouts.master')

@section('css')
    {{-- bootstrap-icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <style>

            /* variables */
            :root {
                --primary-bc: #ffffff;
                --secondary-bc: rgb(2, 122, 129);
                --hover-color:rgb(6, 225, 237);
                --primary-color:#5b626b;
                --transition: all 0.3s linear;
            }
            body{
                overflow: visible !important;
            }
            .main-content{
                color: var(--primary-color);
            }
            /* css */
            .previous-nav{
                color:rgb(45, 173, 179);
                font: bold 19px "Roboto";
                
            }
            .cart-btn {
                z-index: 999;
                color:var(--secondary-bc);
                right: 10px;
                position: fixed;
                transition-property: transform;
                transition-timing-function: ease;
                transition-duration: 0.3s;
            }
            .cart-btn:hover{
                color:var(--hover-color);
                transform: translateX(-3px);
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
            .left-container{
                width:65%;
                float:left;
                border-radius: 0.2rem;
                border: 1px solid var(--secondary-bc);
                box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 6px 10px 0 rgba(0, 0, 0, 0.19);
                margin-bottom: 30px;
            }
            .product-image{
                display: flex;
                justify-content: center;
            }
            .product-image img{
                object-fit: contain;
                height: 450px; 
                width: 450px;
                margin-top:30px;
                padding: 10px;
            }
            .right-container{
               height: 100%!important;
               border-top:2px solid #ccc;
               border-bottom:2px solid #ccc;
               display: flex;
               flex-direction: column;
               flex-wrap: wrap;
               gap:15px;
            }
            .right-container >*{
                margin-left:10px; 
            }
            .product-price{
                color: var(--secondary-bc);
                
            }
            .product-details-title{
                margin-top:30px;
                display: flex;
                padding:30px;
                border-bottom:2px solid #ccc;
                justify-content: space-between;
            }
            .product-details-title:first-child{
                border-top:2px solid #ccc;
            }
            
            .product-details-title i{
                align-self: center;
                transition: var(--transition);
                font-size: 20px;
            }
            .product-details-title:hover{
                cursor: pointer;
            }
            .product-details-content{
                text-align: justify;
                white-space: pre-wrap;
                overflow: hidden;
                display: none;
                height: auto;
            }
            .product-details-content p {
                padding: 20px;
            }
            .rotate{
                transform: rotate(180deg);
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
                border: 0.15em solid #5b626b;
            }
            
            /* to remove the arrow up and down for input type number */
            input::-webkit-outer-spin-button,
            input::-webkit-inner-spin-button {
                display: none;
            }
            .fancy {
                background-color: transparent;
                border: 2px solid #5b626b;
                border-radius: 0;
                box-sizing: border-box;
                color: #fff;
                cursor: pointer;
                display: inline-block;
                /* float: right; */
                font-weight: 700;
                letter-spacing: 0.05em;
                margin: 0;
                outline: none;
                overflow: visible;
                padding: 1.25em 2em;
                position: relative;
                text-align: center;
                text-decoration: none;
                text-transform: none;
                transition: all 0.3s ease-in-out;
                user-select: none;
                font-size: 13px;
            }

            .fancy::before {
                content: " ";
                width: 1.5625rem;
                height: 2px;
                background: #5b626b;
                top: 50%;
                left: 1.5em;
                position: absolute;
                transform: translateY(-50%);
                transform-origin: center;
                transition: background 0.3s linear, width 0.3s linear;
            }

            .fancy .text {
                font-size: 1.125em;
                line-height: 1.33333em;
                padding-left: 2em;
                display: block;
                text-align: left;
                transition: all 0.3s ease-in-out;
                text-transform: uppercase;
                text-decoration: none;
                color: #5b626b;
            }

            .fancy .top-key {
                height: 2px;
                width: 1.5625rem;
                top: -2px;
                left: 0.625rem;
                position: absolute;
                background: #e8e8e8;
                transition: width 0.5s ease-out, left 0.3s ease-out;
            }

            .fancy .bottom-key-1 {
                height: 2px;
                width: 1.5625rem;
                right: 1.875rem;
                bottom: -2px;
                position: absolute;
                background: #e8e8e8;
                transition: width 0.5s ease-out, right 0.3s ease-out;
            }

            .fancy .bottom-key-2 {
                height: 2px;
                width: 0.625rem;
                right: 0.625rem;
                bottom: -2px;
                position: absolute;
                background: #e8e8e8;
                transition: width 0.5s ease-out, right 0.3s ease-out;
            }

            .fancy:hover {
                color: white;
                background: #333547;
            }

            .fancy:hover::before {
                width: 0.9375rem;
                background: white;
            }

            .fancy:hover .text {
                color: white;
                padding-left: 1.5em;
            }

            .fancy:hover .top-key {
                left: -2px;
                width: 0px;
            }

            .fancy:hover .bottom-key-1,
            .fancy:hover .bottom-key-2 {
                right: 0;
                width: 0;
            }
            .product-merchant-container{
                display:flex;
                flex-direction: row;
                margin-bottom: 10px;
            }
            .product-merchant-container img{
                width: 78px;
                height:78px
            }
            .product-merchant-right{
                flex-direction: column;
                margin-left:20px;
            }
            .product-merchant-right a{ 
                border: 2px solid #5b626b;
               font-weight: bold;
            } 
            .product-merchant-right a:hover{
                background-color: var(--primary-color);
                
            }
            .btn{
                 color: var(--primary-color)!important;
            }
            .btn:hover{
                    color:var(--primary-bc)!important;
            }
           @media only screen and (max-width:600px){
             .google-map{
                height:300px;
             }
           }
    </style>
@endsection

@section('content')
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="page-title-box">
                <h4 class="font-size-18"><a href="{{ route('merchant-product.index') }}" class="previous-nav">Senarai Produk</a>  <i class="fas fa-angle-right"></i>  {{$product->pg_name}} <i class="fas fa-angle-right"></i>  {{$product->name}}</h4>
            </div>
        </div>
        <a href="{{route('merchant.all-cart')}}" class="cart-btn"><i class="mdi mdi-cart fa-3x"></i><span class='notification' hidden></span></a>
    </div>
    <div class="row">
        <section class="col-md-8 left-container">
            <div class="product-image"> 
                @if($product->image == null)
                    <img class="img-fluid mx-auto d-block" id="img-size"  src="{{ URL('merchant-image/default-item.jpeg')}}" alt="{{$product->name}}">
                @else
                    <img class="rounded img-fluid " id="img-size" src="{{ URL('merchant-image/product-item/'.$product->code.'/'.$product->image)}}" alt="{{$product->name}}">
                @endif
            </div>
            <div class="product-details">
                <div class="product-details-title">
                    <h4>Description</h3>
                    <i class="fas fa-angle-down arrow-icon"></i>
                </div>
                <div class="product-details-content">
                    <p>{{$product->desc}}</p>
                </div>
                <div class="product-details-title mt-0" >
                    <h4>Location</h3>
                    <i class="fas fa-angle-down arrow-icon"></i>
                </div>
                <div class="product-details-content">
                    <iframe
                    width="100%"
                    height="400"
                    frameborder="0" style="border:0"
                    src="https://www.google.com/maps?q={{ urlencode($address) }}&output=embed" class="google-map">
                </iframe>
                </div>
            </div>
        </section>
        <section class="col-md-4 right-container">
            <div>
                <h2 class="product-name">{{$product->name}}</h2>
                <h3 class="product-price">RM{{$product->price}}/{{$product->collective_noun}}</h3>
            </div>
            <div class="quantity-container">
                <h4>Kuantiti</h4>
                <div class="quantity-input-container">
                    <button id="button-minus"><i class="bi bi-dash-square"></i></button>
                    <input type="number" class="quantity-input" name = "quantity-input" value="1" min="1">
                    <button id="button-plus"><i class="bi bi-plus-square"></i></button>
                    <h6 data-qty-available="{{$product->quantity_available}}" id="quantity-available">{{$product->quantity_available}} barang lagi</h6>
                </div>
            </div>
            <div class="add-to-cart" data-item-id="{{$product->id}}" data-org-id = "{{$product->organization_id}}">
                 <a class="fancy">
                    <span class="top-key"></span>
                    <span class="text">Tambah Dalam Troli</span>
                    <span class="bottom-key-1"></span>
                    <span class="bottom-key-2"></span>
                  </a>
            </div>
            <div class="alert-message" >
                {{-- alert message will be appended here --}}
            </div>
            <div class="product-merchant-container">
                <a href="{{ route('merchant-reg.show', $product->organization_id) }}"><img class="rounded-circle bg-dark" src="{{URL("images/koperasi/default-item.png")}}">
                </a>
                <div class="product-merchant-right">
                    <h5>{{$product->org_name}} </h5>
                    <h6><i class="bi bi-geo-alt">{{$product->city}}, {{$product->district}}</i></h6>
                   <a class="btn" href="{{ route('merchant-reg.show', $product->organization_id)}}">Layari Kedai</a>
                </div>
                
            </div>
        </section>
     </div>   
@endsection

@section('script')
    <script>
        $(document).ready(function(){
            let qtyAvailable = $("#quantity-available").attr("data-qty-available");

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
                //call this function to add item counter to the cart button
            notificationCounter();

            function notificationCounter(){
                let noty = $('.notification')
                $.ajax({
                    url: "{{ route('merchant.load-cart-counter') }}",
                    method: "GET",
                    beforeSend:function(){
                        noty.empty()
                    },
                    success:function(result){
                    if(result.counter != 0) {
                        noty.attr('hidden', false)
                        noty.append(result.counter)
                    }else {
                        noty.attr('hidden', true)
                    }
                    },
                    error:function(result){
                        console.log(result.responseText)
                    }
                })
            }
            //for slide down details section
            const productDetailsTitle = $(".product-details-title");
            productDetailsTitle.click(function(){
                var clickedItem = $(this);//get the clicked product details title
                var contentToShow = clickedItem.next();//get the content to be revealed which is neighbouring to the title
                contentToShow.slideToggle(400);
                clickedItem.find("i").toggleClass('rotate');//find() uses to find the child of the element
            })

            //for input quantity section
            let inputValue = $("input[ name = 'quantity-input']").val();
            $("#button-plus").click(()=>{
                if(inputValue < qtyAvailable){
                    inputValue++;
                    $("input[ name = 'quantity-input']").val(inputValue);//updated the value in input field
                }
            })
            $("#button-minus").click(()=>{
                if(inputValue > 1){
                    inputValue--;
                    $("input[ name = 'quantity-input']").val(inputValue);//updated the value in input field
                }
            })
            //when key in quantity when want to buy in the input field
            $("input[name='quantity-input']").off('keypress').on('keypress',function(e){
                const currentValue = parseInt($(this).val());
                if (e.key === "Backspace" || e.key === "Delete" || e.key === "ArrowLeft" || e.key === "ArrowRight") {
                    // allow navigation and deletion keys
                    return;
                }
                if (isNaN(currentValue)) {
                    // if the current value is not a number, reset it to 1
                    $(this).val(1);
                }
                console.log($(this).val() +" "+ e.key);
                const newValue = parseInt($(this).val() + e.key);
                if (newValue < 1 || newValue > qtyAvailable) {
                    // if the new value is out of bounds, prevent the default action
                    e.preventDefault();
                    // set the input value to the maximum allowed value
                    $(this).val(qtyAvailable);
                }else{
                    inputValue = newValue;
                }
            });
            
            //for add to cart section
            let item_id = $(".add-to-cart").attr("data-item-id");
            let org_id = $(".add-to-cart").attr("data-org-id");
            
            $(".add-to-cart a").click(()=>{
                $.ajax({
                    url: "{{route('merchant-reg.store-item')}}",
                    method: "POST",
                    data: {
                        i_id:item_id,
                        o_id:org_id,
                        qty:inputValue,
                    },
                    beforeSend: function() {
                        $('.loading').show();
                    },
                    success: function(result){
                        $('.loading').hide();
                        if(result.alert == '')
                        {
                            var message = "<div class='success alert-success' style='padding: 5px;'>"+result.success+"</div>"
                            $('div.alert-message').append(message)
                            $('div.alert-message').delay(3000).fadeOut()
                        }
                        else
                        {
                            if(result.alert == 'restart') {
                                location.reload();
                            } else {
                                var message = "<div class='alert alert-danger'>"+result.alert+"</div>"
                                $('div.alert-message').append(message)
                                $('div.alert-message').delay(3000).fadeOut()
                            }
                        }
                        //reset the quantity input field
                        $("input[name='quantity-input']").val(1);
                        notificationCounter();
                    },
                    error:function(result){
                        console.log(result.responseText);
                    }
                })
            })
        })
    </script>
@endsection