@extends('layouts.master')

@section('css')
    <style>
            /* variables */
            :root {
                --primary-bc: #ffffff;
                --secondary-bc: rgb(2, 122, 129);
                --hover-color:rgb(6, 225, 237);
            }
            .cart-btn {
                color:var(--secondary-bc);
                right: 5px;
                position: fixed;
                transition-property: transform;
                transition-timing-function: ease;
                transition-duration: 0.3s;
                /* position: relative; */
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
            .cart-btn:hover{
                color:var(--hover-color);
                transform: translateX(-3px);
            }
            .products-container {
                margin-top: 30px;
                display:flex;
                flex-direction: row;
                flex-wrap: wrap;
                align-items: center;
                justify-content: center;
                gap: 15px;
            }
            .product-container{
                width:200px;
                height: 300px;
                background-color: var(--primary-bc);
                border-radius: 0.5rem;
                box-shadow: 0 2px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 10px 0 rgba(0, 0, 0, 0.19);
                transition-property: transform;
                transition-timing-function: ease;
                transition-duration: 0.2s;
            }
            .product-container:hover{
                transform: translateY(-5px);
            }
            .product-image img{
                border-radius: 0.5rem;
                object-fit: contain;
            }
            .product-name{
                text-align: center;
                padding: 10px;
                font-size: 16px;
                color: rgb(0, 0, 0);
            }
            .product-price{
                font-weight: bold;
                font-size: 16px;
                color: var(--secondary-bc);
                text-align: right;
                padding: 10px;   
            }
    </style>
@endsection

@section('content')
    <nav>
        <a href="{{route('merchant.all-cart')}}" class="cart-btn"><i class="mdi mdi-cart fa-3x"></i><span class='notification' hidden></span></a>
    </nav>
    <section class="container products-container">
    @foreach ($products as $product)
            <a href="{{route('merchant-product.show',$product->id)}}" class="product-container"> 
                <div class="product-image">
                {{-- @if($product->image == null) --}}
                    <img class="img-fluid default-img" id="img-size"  src="{{ URL('merchant-image/default-item.jpeg')}}">
                {{-- @else
                <img class="rounded img-fluid " id="img-size" src="{{ URL('merchant-image/product-item/'.$product->code.'/'.$product->image)}}">
                @endif --}}
                </div>
                <div class="product-infos">
                    <div class="product-name">{{$product->name}}</div>
                    <div class="product-price">RM{{$product->price}}</div>
                </div>
            </a>
    @endforeach
    </section>

@endsection

@section('script')
<script>
$(document).ready(function(){
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
            } else {
                noty.attr('hidden', true)
            }
            },
            error:function(result){
                console.log(result.responseText)
            }
        })
    }
})
</script>
@endsection