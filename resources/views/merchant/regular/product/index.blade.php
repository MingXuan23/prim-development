@extends('layouts.master')

@section('css')
    <style>
            /* variables */
            :root {
                --primary-bc: #ffffff;
                --secondary-bc: rgb(2, 122, 129);
                --hover-color:rgb(6, 225, 237);
            }
            /* to hide default search section */
            .search-wide{
                display: none!important;
            }
            .search-short{
                display: none!important;
            }
            /*end of to hide default search section*/
            /* new search */
            .search-container-input {
                margin-top:20px;
                position: relative;
                display: flex;
                align-items: center; /* Center vertically */
                justify-content: center; /* Align input and SVG to the left */
            }
            .input {
                width: 150px;
                padding: 10px 0px 10px 40px;
                border-radius: 9999px;
                border: solid 2px #5b626b;
                transition: all .2s ease-in-out;
                outline: none;
                opacity: 0.8;
            }
            .search-container-input svg {
                /* position: absolute; */
                top: 10px;
                position: inherit;
                left:-40px;
                /* right: 100px; */
                transform: translate(0, -50%);
            }
            .input:focus {
                opacity: 1;
                width: 250px;
            }
            /*end of new search  */
            .cart-btn {
                color:var(--secondary-bc);
                right: 10px;
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
                width:190px;
                height: 282px;
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
            .product-image{
                height: 190px!important;
                width: 190px!important;
            }
            .product-image img{
                object-fit: contain;
                max-width: 100%;
                width: auto;
            }
            .product-name{
                text-align: center;
                padding: 10px;
                font-size: 15px;
                color: #5b626b;
                text-overflow: ellipsis;
                white-space: nowrap;
                overflow: hidden;
            }
            .product-price{
                font-weight: bold;
                font-size: 16px;
                color: var(--secondary-bc);
                text-align: right;
                padding: 10px;   
            }
            nav .pagination{
                margin-top:20px;
                display: flex;
                justify-content: center;
            }
            .page-item.active .page-link{
                background-color:#5b626b!important;
                border-color: #5b626b!important;
                color:#ffffff!important;
            }
            .page-link {
                color:#5b626b!important;
            }
            @media screen and (max-width:800px){
                .product-container{
                    height:242px;
                    width: 150px;
                }
                .product-image{
                    height: 150px!important;
                    width: 150px!important;
                }
            }
            @media screen and (max-width:550px){
                .container{
                    margin-left:0!important;
                    margin-right: 0!important;
                }
                .product-container{
                    height:180px;
                    width: 100px;
                }
                .product-name{
                    font-size:12px;
                }
                .product-price{
                    font-size: 12px;
                }
                .product-image{
                    height: 100px!important;
                    width: 100px!important;
                }
            }
    </style>
@endsection

@section('content')
    <nav>
        <div class="search-container-input">
            <input type="text" placeholder="Search" id="search-input" name="text" autocomplete="off" class="input">
            <svg fill="#000000" width="20px" height="20px" viewBox="0 0 1920 1920" xmlns="http://www.w3.org/2000/svg">
              <path d="M790.588 1468.235c-373.722 0-677.647-303.924-677.647-677.647 0-373.722 303.925-677.647 677.647-677.647 373.723 0 677.647 303.925 677.647 677.647 0 373.723-303.924 677.647-677.647 677.647Zm596.781-160.715c120.396-138.692 193.807-319.285 193.807-516.932C1581.176 354.748 1226.428 0 790.588 0S0 354.748 0 790.588s354.748 790.588 790.588 790.588c197.647 0 378.24-73.411 516.932-193.807l516.028 516.142 79.963-79.963-516.142-516.028Z" fill-rule="evenodd"></path>
            </svg>
        </div>
        <a href="{{route('merchant.all-cart')}}" class="cart-btn"><i class="mdi mdi-cart fa-3x"></i><span class='notification' hidden></span></a>
    </nav>
    <section class="container products-container">
    @foreach ($products as $product)
            <a href="{{route('merchant-product.show',$product->id)}}" class="product-container" data-product-id="{{$product->id}}"> 
                <div class="product-image">
                    @if($product->image == null)
                        <img class="default-img" id="img-size"  src="{{ URL('merchant-image/default-item.jpeg')}}" alt="{{$product->name}}">
                    @else
                    <img class="rounded" id="img-size" src="{{ URL('merchant-image/product-item/'.$product->code.'/'.$product->image)}}" alt="{{$product->name}}">
                    @endif 
                </div>
                <div class="product-infos">
                    <div class="product-name">{{$product->name}}</div>
                    <div class="product-price">RM{{$product->price}}</div>
                </div>
            </a>
    @endforeach
    </section>
    {{-- for pagination --}}
        {{$products->links()}}
        <a href="{{route('merchant.testPay')}}" class="btn btn-primary">Test Pay</a>
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
    // Retrieve the containers that need to be filtered
    const containers = $('.product-container');

    // Add event listener to the search input field
    $('#search-input').on('keydown', function(e) {
       if(e.key === "Enter"){
        const inputValue = $(this).val().toLowerCase().trim(); // Retrieve the input value
        // Loop through the containers and filter based on the input value
        containers.each(function() {
            const containerName = $(this).find('.product-name').text().toLowerCase();
            if (containerName.includes(inputValue) || inputValue === '') {
                $(this).show(); // Display the container if there is a match or if the input value is empty
            } else {
                $(this).hide(); // Hide the container if there is no match
            }
        });
       } 
    });
})
</script>
@endsection