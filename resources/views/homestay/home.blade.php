@extends('layouts.master')

@section('css')
    <link rel="stylesheet" href="{{ URL::asset('assets/homestay-assets/style.css')}}">
    <style>
        #banner-content {
            color: #ffffff;
            padding: 12px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            height: 100%;
            width: 50%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background-color:transparent!important;
        }
        footer {
            background-color: var(--primary-color) !important;
            color: white !important;
        }
        @media screen and (max-width: 500px){
            #banner-content {
                width: 80%;
            }
            #banner-content > * {
                padding: 8px;
                font-size: 20px !important;
            }
            #banner-container > img {
                object-position: 50% 70%;
            }
        }
    </style>
@endsection

@section('content')
    {{-- <div>
        <form action="{{route('homestay.testPayment')}}" method="post">
            @csrf
            <button type="submit">Test Payment</button>
        </form>
    </div> --}}
    <section aria-label="Banner" class="mb-3">
        <div id="banner-container">
            <img src="{{URL('assets/homestay-assets/images/home-banner-image(updated).jpg')}}" alt="Banner Image" id="banner-image">
            <div id="banner-content">
                <h2>Cari penginapan idaman anda dengan tawaran yang menarik</h2>
            </div>
        </div>
        <div aria-label="Search Input" >
            <form action="{{route('homestay.searchRoom')}}" method="get" enctype="multipart/form-data" class="d-flex justify-content-center align-items-center mt-3" id="form-search">
                @csrf
                <div class="search-container-input">
                    <input type="search" placeholder="Masukkan destinasi" id="search-room" name="searchRoom" autocomplete="off" class="input">
                    <svg id="search-icon" fill="#852aff" width="20px" height="20px" viewBox="0 0 1920 1920" xmlns="http://www.w3.org/2000/svg">
                      <path d="M790.588 1468.235c-373.722 0-677.647-303.924-677.647-677.647 0-373.722 303.925-677.647 677.647-677.647 373.723 0 677.647 303.925 677.647 677.647 0 373.723-303.924 677.647-677.647 677.647Zm596.781-160.715c120.396-138.692 193.807-319.285 193.807-516.932C1581.176 354.748 1226.428 0 790.588 0S0 354.748 0 790.588s354.748 790.588 790.588 790.588c197.647 0 378.24-73.411 516.932-193.807l516.028 516.142 79.963-79.963-516.142-516.028Z" fill-rule="evenodd"></path>
                    </svg>
                </div>
            </form>
        </div>
    </section>

    <section aria-label="Homestays or Rooms Listings" >
        <div class="home-thumbnails-container">
            @forelse($rooms as $room)
                <div class="home-thumbnail-container">
                    <a href="{{route('homestay.showRoom',['id' => $room->roomid, 'name' => $room->roomname])}}" target="_self">
                        @if($room->ongoingDiscount != null || $room->nearestFutureDiscount != null)
                            @if($room->ongoingDiscount != null)
                                <div class="home-thumbnail-discount">
                                    <span>-{{$room->ongoingDiscount}}%</span>, {{date('d/m',strtotime($room->ongoingDiscountStart))}}  {{$room->ongoingDiscountLast != $room->ongoingDiscountStart ? ' - '.date('d/m',strtotime($room->ongoingDiscountLast)) : ''}}
                                </div>
                            @else
                                <div class="home-thumbnail-nearest-discount">
                                    <span>-{{$room->nearestFutureDiscount}}%</span>, {{date('d/m',strtotime($room->nearestFutureDiscountStart))}}  {{$room->nearestFutureDiscountLast != $room->nearestFutureDiscountStart ? ' - '.date('d/m',strtotime($room->nearestFutureDiscountLast)) : ''}}
                                </div>
                            @endif
                        @endif
                        <img src="{{URL($room->homestayImage->first()->image_path)}}" alt="{{$room->roomname}}'s Image" class="home-thumbnail-img">
                        <div class="home-thumbnail-captions p-2">
                                <h4 class="color-purple">{{$room->roomname}}</h4>
                            <div class="color-dark-purple"><span><i class="fas fa-map-marker-alt"></i></span> {{$room->district}},{{$room->state}}</div>
                            <div class="d-flex justify-content-between align-items-center">
                                @if($room->overallRating > 0)
                                    <span class="rated">{{$room->overallRating}}&#9733</span>
                                @else
                                    <span></span>
                                @endif
                                @if($room->ongoingDiscount != null)
                                    <h5 class="color-purple text-right"><span class="price-before-discount">RM{{$room->price}}</span> RM{{number_format(($room->price - ($room->price * $room->ongoingDiscount / 100)) , 2 )}}/malam</h5>

                                @else
                                    <h5 class="color-purple text-right">RM{{$room->price}}/malam</h5>
                                @endif
                            </div>

                        </div>
                    </a>
                </div>

            @empty

            @endforelse
        </div>

    </section>
    {{ $rooms->appends(request()->query())->links() }}
    <div class="mb-5">
    </div>
@endsection


@section('script')
{{-- sweet alert --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
{{-- Typeahead Js for autocomplete in searching --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js"></script>
<script>


$(document).ready(function() {
    $('.navbar-header > div:first-child()').after(`
        <img src="{{URL('assets/homestay-assets/images/book-n-stay-logo(transparent).png')}}" height="70px">
    `);
    // for autocomplete search
    $('#search-room').typeahead({
        source:function(query,process){
            $.ajax({
                url: '{{route("homestay.autocompleteSearch")}}',
                method: 'GET',
                data:{
                    query: query,
                },
                success: function(result){
                    var searchData = [];
                    $(Object.values(result)).each(function(i,location){
                        searchData.push(location.toUpperCase());
                    });

                    process(searchData);
                },
                error: function(){
                    console.log('Autocomplete Search Faileds');
                }
            })
        },
        autoSelect: false,
    });


    $('#form-search').on('submit',function(e){
        if($('#search-room').val() == ''){
            e.preventDefault();
            Swal.fire('Sila masukkan nama homestay/bilik untuk memulakan pencarian');
        }
    });
    $(document).on('click','.typeahead',function(){
        $('#form-search').trigger('submit');
    })
    $('#search-icon').on('click', function(){
        $('#form-search').trigger('submit');
    });
});
</script>
@endsection
