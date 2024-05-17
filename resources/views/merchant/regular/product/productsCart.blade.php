@extends("layouts.master")
@section('css')
     {{-- bootstrap-icons --}}
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <style>
        :root {
                --primary-bc: #ffffff;
                --secondary-bc: rgb(2, 122, 129);
                --hover-color:rgb(6, 225, 237);
                --primary-color:#5b626b;
                --transition: all 0.3s linear;
            }
            .previous-nav{
                color:rgb(45, 173, 179);
                font: bold 22px "Roboto";

            }
            .main-content{
                color: var(--primary-color);
            }
            .product a{
                color: var(--primary-color)!important;
            }
            .carts-counter{
                font: 20px "Roboto" ;
            }
            .carts-counter b{
                color: var(--secondary-bc);
            }
            .product-container{
                border: 2px solid var(--primary-color);
                padding: 0.5em;
                margin:0.5em;
                margin-left: auto;
                margin-right: auto;
                width: 80%;
            }
            .product{
                display:flex;
                flex-direction: row;
                flex-wrap: wrap;
                border-bottom:2px solid #ccc;
                position: relative;
            }
            .product:last-child{
                border-bottom:none;
            }
            .product-image{
                width: 200px;
                height: 200px;
                border-radius: 14px;
            }
            .product-image img{
                max-width: 100%;
                max-height: 100%;
                width: 200px;
                height: 200px;
                object-fit: contain;
            }
            .product-details {
                margin-left: 2em;
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
                border: 0.15em solid #333547;
            }
            .quantity-input-disabled{
                text-align: center;
                width: 50px;
                border: 0.15em solid #333547;
            }
            /* to remove the arrow up and down for input type number */
            input::-webkit-outer-spin-button,
            input::-webkit-inner-spin-button {
                display: none;
            }
            .delete-btn i{
                position: absolute;
                right: 0;
                font-size: 18px;
            }
            .delete-btn i:hover{
                color:rgb(143, 143, 144);
                cursor: pointer;
            }
            .total-price{
                position: absolute;
                right: 0;
                bottom: 0;
                color: var(--secondary-bc);
                text-align: end;
            }
            .hide{
                display: none!important;
            }
            /* only show total once at the right bottom of the last product in cart */
            /* second last child */
            .product-container .product:nth-last-child(2) .total-price{
                display: block!important;
            }
            .product-container .checkout:last-child{
                display: flex!important;
            }
            /* checkout section at the bottom */
            .checkout-container{
                margin-top:20px;
                padding:10px;
                display:flex;
                justify-content: flex-end;
                align-items: center;
                gap:5px;
                border-top:2px solid #ccc;
                border-bottom:2px solid #ccc;
            }
            .checkout{
                display: flex;
                justify-content: flex-end;
                margin-top: 0.5em;
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
            /* for responsive */
            @media only screen and (max-width:647px){
                .product-image{
                    margin-left:auto;
                    margin-right:auto;
                }
                .total-price{
                    position: relative;
                    text-align: end;
                }
            }
            @media only screen and (max-width:600px){
                .delete-btn i{
                    top: 60%;
                    right: 0%;
                }
                .product-container{
                    width: 100%;
                }
                .page-content{
                    padding: 80px 0!important;
                }
                .product-details{
                    width: 100%;
                    margin-left: 0;
                }
                h1{
                    font-size: 1.5rem!important;
                }
                h2{
                    font-size: 1.2rem!important;
                }
                h3{
                    font-size: 1rem!important;
                }
                .previous-nav{
                    font-size: 18px;
                }
            }
    </style>
@endsection

@section('content')
    <div>
        <div class="previous-nav my-2">
            @if( !auth()->user()->hasRole('Guest'))
        <h1 class="font-size-18"> <a href="{{ route('merchant-product.index') }}" class="previous-nav"><span><i class="fas fa-angle-left"></i> </span>Senarai Produk</a></h1>
        @endif
        </div>
        <h1 class="title">Troli Membeli-Belah</h1>
        <h3 class="carts-counter"></h3>
    </div>

    @forelse ($organizations as $organization)
        <div>
            <a href="{{ route('merchant-reg.show', $organization->id) }}">
                <h3 style="color:#5b626b">{{$organization->nama}}</h3>
            </a>
        </div>
            <div class="product-container">
                @foreach($productInCart as $product)
                    @if($product->nama == $organization->nama)
                            <div class="product" >
                                <div class="product-image">
                                    <a href="{{route('merchant-product.show',$product->product_item_id)}}">
                                        @if($product->image == null)
                                            <img class="img-fluid mx-auto d-block" id="img-size"  src="{{ URL('merchant-image/default-item.jpeg')}}" alt="{{$product->name}}">
                                        @else
                                            <img class="rounded img-fluid " id="img-size" src="{{ URL('merchant-image/product-item/'.$product->code.'/'.$product->image)}}" alt="{{$product->name}}">
                                        @endif
                                    </a>
                                </div>
                                    @if($product->quantity_available > 0 && $product->status == 1)
                                        <div class="product-details">
                                            <a href="{{route('merchant-product.show',$product->product_item_id)}}"><h5>{{$product->name}}</h5></a>
                                            <h5 style="color: rgb(2, 122, 129);">RM{{$product->price}}/{{$product->collective_noun}}</h5>
                                            <div class  = "quantity-container" >
                                                <div class="quantity-input-container" data-product-order-id="{{$product->id}}"  data-pgng-order-id="{{$product->pgng_order_id}}">
                                                    <button id="button-minus"><i class="bi bi-dash-square"></i></button>
                                                    <input type="number" class="quantity-input" name = "quantity-input" value="{{$product->quantity}}" min="1" max="{{$product->quantity_available}}" data-org-id="{{$organization->id}}">
                                                    <button id="button-plus" ><i class="bi bi-plus-square"></i></button>
                                                    <h6 data-qty-available="{{$product->quantity_available}}" id="quantity-available">{{$product->quantity_available}} barang lagi</h6>
                                                </div>
                                            </div>
                                            <h5 class="total-price hide" data-pgng-order-id="{{$product->pgng_order_id}}" data-org-id="{{$organization->id}}"><!--Jumlah: RM{{$product->total_price}}--></h5>
                                            <div class="alert-message">
                                            {{-- alert message will be appended here --}}
                                            </div>
                                        </div>
                                    @elseif($product->status == 0)
                                        <div class="product-details" style="opacity: 0.3">
                                            <a href="{{route('merchant-product.show',$product->product_item_id)}}"><h5>{{$product->name}}</h5></a>
                                            <h5>RM{{$product->price}}/Unit</h5>
                                            <div class  = "quantity-container" >
                                                <div class="quantity-input-container" data-product-order-id="{{$product->id}}"  data-pgng-order-id="{{$product->pgng_order_id}}">
                                                    <button id="button-minus" disabled><i class="bi bi-dash-square"></i></button>
                                                    <input type="number" class="quantity-input-disabled" name = "quantity-input" value="{{$product->quantity}}" min="1" disabled>
                                                    <button id="button-plus" disabled><i class="bi bi-plus-square"></i></button>
                                                    <h6>Tidak Dijual Pada Masa Kini</h6>
                                                </div>
                                            </div>
                                            <h5 class="total-price hide" data-pgng-order-id="{{$product->pgng_order_id}}" data-org-id="{{$organization->id}}">Total: RM{{$product->total_price}}</h5>
                                            <div class="alert-message">
                                            {{-- alert message will be appended here --}}
                                            </div>
                                        </div>
                                    @else
                                        <div class="product-details" style="opacity: 0.3">
                                            <a href="{{route('merchant-product.show',$product->product_item_id)}}"><h5>{{$product->name}}</h5></a>
                                            <h5>RM{{$product->price}}/Unit</h5>
                                            <div class  = "quantity-container" >
                                                <div class="quantity-input-container" data-product-order-id="{{$product->id}}"  data-pgng-order-id="{{$product->pgng_order_id}}">
                                                    <button id="button-minus" disabled><i class="bi bi-dash-square"></i></button>
                                                    <input type="number" class="quantity-input-disabled" name = "quantity-input" value="{{$product->quantity}}" min="1" disabled>
                                                    <button id="button-plus" disabled><i class="bi bi-plus-square"></i></button>
                                                    <h6>Kehabisan Stok</h6>
                                                </div>
                                            </div>
                                            <h5 class="total-price hide" data-pgng-order-id="{{$product->pgng_order_id}}" data-org-id="{{$organization->id}}">Jumlah: RM{{$product->total_price}}</h5>
                                            <div class="alert-message">
                                            {{-- alert message will be appended here --}}
                                            </div>
                                        </div>
                                    @endif
                                <div class="delete-btn">
                                    <i class="delete bi bi-x-lg" data-cart-order-id="{{$product->id}}"></i>
                                </div>
                            </div>
                            @if($product->quantity_available > 0)
                                <div class="checkout hide" >
                                    {{-- <a class="fancy" href="{{ route('merchant.checkout', $organization->id) }}"> --}}
                                    <a class="fancy checkout-button" href="#">
                                        <span class="top-key"></span>
                                        <span class="text">Semak Keluar</span>
                                        <span class="bottom-key-1"></span>
                                        <span class="bottom-key-2"></span>
                                    </a>
                                </div>
                            @endif
                    @endif
                @endforeach
            </div>
        @empty
            <div style="text-align:center">
                <h2>Tiada Barang Dalam Troli Anda</h2>
                <h3>Marilah Membeli-belah Bersama Kami</h3>
            </div>
    @endforelse
        {{-- @if(count($productInCart)> 0)
        <div class="checkout-container">
            <div class="checkout-infos">
                <h3 class="carts-counter"></h3>
            </div>
            <div class="checkout">
                <a class="fancy" href="{{route('merchant.checkout')}}">
                    <span class="top-key"></span>
                    <span class="text">Semak Keluar</span>
                    <span class="bottom-key-1"></span>
                    <span class="bottom-key-2"></span>
                </a>
            </div>
        </div>

        @endif --}}
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
    <script>
    $(document).ready(function(){

        var isGuest = "{{ auth()->user()->hasRole('Guest') }}";
        // If the user is a guest, remove the href attribute from all anchor tags within the .product div
        if (isGuest) {
            $('.product a').removeAttr('href');
        }

        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
        });
             loadValidTotalPrice();
             //call this function to add item counter to the cart button
             notificationCounter();
        function notificationCounter(){
            let noty = $('.carts-counter')
            $.ajax({
                url: "{{ route('merchant.load-cart-counter') }}",
                method: "GET",
                success:function(result){
                if(result.counter != 0) {
                    noty.html("Jumlah("+result.counter+" item)<b>RM"+result.total+"</b>");
                }else {
                    noty.html("Jumlah(0 item)");
                }
                },
                error:function(result){
                    console.log(result.responseText)
                }
            })
        }
        // in order to get total price of products that are available to buy
        function loadValidTotalPrice() {
            $(".total-price").each(function (index, totalPrice) {
                $.ajax({
                    url: "{{route('merchant.get-actual-total-price')}}",
                    method: "GET",
                    data: {
                        pgng_order_id: $(totalPrice).attr('data-pgng-order-id'),
                        org_id: $(totalPrice).attr('data-org-id'),
                    },
                    success: function (result) {
                        let total = result.totalPrice;
                        total = parseFloat(total).toFixed(2);
                        if(parseFloat(result.fixed_charges) > 0){
                            let fixed_charges = parseFloat(result.fixed_charges).toFixed(2);

                            if(result.min_waive){
                                $(totalPrice).html("Jumlah: RM"+total + "<br>(+ Caj Servis:RM" + fixed_charges +")<br> <small>Caj Servis Boleh Digecualikan<br> Dengan Pembelian melebihi RM" + result.min_waive +"</small>");
                            }else{
                                $(totalPrice).html("Jumlah: RM"+total + "<br>(+ Caj Servis:RM" + fixed_charges +")" );
                            }
                        }
                        else{
                            $(totalPrice).html("Jumlah: RM"+total);
                        }
                    }
                });
            });
        }

        let order_cart_id = null
        $(document).on('click', '.delete', function(){
            order_cart_id = $(this).attr('data-cart-order-id');
            $('#deleteConfirmModal').modal('show')
        })
        // for delete product in cart
        $(document).on('click', '#delete_confirm_item', function(){
            $.ajax({
                url: "{{ route('merchant-reg.destroy-item') }}",
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
        })
        const plusButtons = document.querySelectorAll("#button-plus");
        const minusButtons = document.querySelectorAll("#button-minus");
        let debounceTimer;
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
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() =>{
                        updateInputQuantity(this,inputQuantity,productOrderId,pgngOrderId);
                    } , 500);
                }
            })
        })
        minusButtons.forEach(function(minusButton){
            minusButton.addEventListener("click",function(){
                let inputQuantity = parseInt(this.nextElementSibling.value);
                let qtyAvailable = parseInt(this.nextElementSibling.nextElementSibling.getAttribute("data-qty-available"));
                let productOrderId = this.parentElement.getAttribute("data-product-order-id");
                let pgngOrderId = this.parentElement.getAttribute("data-pgng-order-id");
                if(inputQuantity > 1){
                    inputQuantity--;
                    this.nextElementSibling.value = inputQuantity;
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() =>{
                        updateInputQuantity(this,inputQuantity,productOrderId,pgngOrderId);
                    } , 500);
                }
            })
            })
        //when key in quantity when want to buy in the input field
        $("input[name='quantity-input']").on('input',function(e){
            let qtyAvailable = parseInt(this.nextElementSibling.nextElementSibling.getAttribute("data-qty-available"));
                    let productOrderId = this.parentElement.getAttribute("data-product-order-id");
                    let pgngOrderId = this.parentElement.getAttribute("data-pgng-order-id");
                    const currentValue = parseInt($(this).val());
                    if ($(this).val() > qtyAvailable) {
                        // if the new value is out of bounds, prevent the default action
                        e.preventDefault();
                        // set the input value to the maximum allowed value
                        $(this).val(qtyAvailable);
                        clearTimeout(debounceTimer);
                        debounceTimer = setTimeout(() =>{
                            updateInputQuantity(this, $(this).val(),productOrderId,pgngOrderId);
                        } , 500);

                    }
                    else if($(this).val() < 1){
                        // if the new value is out of bounds, prevent the default action
                        e.preventDefault();
                        // set the input value to the maximum allowed value
                        $(this).val(1);
                    }
                    else{
                        clearTimeout(debounceTimer);
                        debounceTimer = setTimeout(() =>{
                            updateInputQuantity(this, $(this).val(),productOrderId,pgngOrderId);
                        } , 500);
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
                        $alertMessage.empty();
                        $alertMessage.append(message);
                        $alertMessage.delay(1000).fadeOut()

                        $totalPrice = $parent.parent().parent().children().children('.product-details').children('.total-price');
                        let total = result.totalPrice;
                        total = total.toFixed(2);
                        if(parseFloat(result.charges) > 0){
                            let fixed_charges = parseFloat(result.charges).toFixed(2);
                            if(result.min_waive){
                                $totalPrice.html("Jumlah: RM"+total + "<br>(+ Caj Servis:RM" + fixed_charges +")<br> <small>Caj Servis Boleh Digecualikan<br> Dengan Pembelian melebihi RM" + result.min_waive +"</small>");
                            }else{
                                $totalPrice.html("Jumlah: RM"+total + "<br>(+ Caj Servis:RM" + fixed_charges +")" );
                            }
                        }
                        else{
                            $totalPrice.html("Jumlah: RM"+total);
                        }
                        notificationCounter();
                },
                // error:function(result) {
                //     console.log(result.responseText)
                // }
           })
    }
    });

    // for validation before proceed to checkout page
    $('.checkout-button').each(function (index,checkButton) {
        $(checkButton).on('click', function (event) {
            var productContainer = $(checkButton).parents('.product-container');
            var quantityInputs = $(productContainer).find('.quantity-input');
            $(quantityInputs).each(function(index,quantityInput){
                var quantityValue = $(quantityInput).val();
                if (quantityValue > parseInt($(quantityInput).attr('max'))) {
                    event.preventDefault(); // Prevent default action
                    // Optionally, display an error message or take any other desired action
                    $alertMessage = $(quantityInput).parents('.quantity-container').siblings('.alert-message');
                    var message = "<div class='success alert-danger' style='padding: 5px;'>Value is greater than the maximum allowed.</div>"
                    $alertMessage.empty();
                    $alertMessage.append(message);
                    $alertMessage.fadeIn();
                    $alertMessage.delay(1000).fadeOut()
                } else {
                    // Update the href attribute to navigate to the desired route
                    var org_id = $(quantityInput).attr('data-org-id');
                    checkButton.href = org_id + "/checkout";
                }
            });
        });
    })
    </script>
@endsection
