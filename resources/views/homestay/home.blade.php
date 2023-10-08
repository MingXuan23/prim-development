@extends('layouts.master')

@section('css')
    <link rel="stylesheet" href="{{ URL::asset('assets/homestay-assets/style.css')}}">
@endsection

@section('content')

    <section aria-label="Banner" class="mb-3">
        <div id="banner-container">
            <img src="homestay-image/home-banner-image.jpeg" alt="Banner Image" id="banner-image">
            <div id="banner-content">
                <h2>Cari penginapan idaman anda dengan tawaran yang menarik</h2>
            </div>
        </div>
    </section>

    <section aria-label="Homestays or Rooms Listings" >
        <div class="home-thumbnails-container">
            @foreach($rooms as $room)
                <div class="home-thumbnail-container">
                    <a href="{{route('homestay.showRoom',['id' => $room->roomid, 'name' => $room->roomname])}}" target="_blank">
                        <img src="{{$room->image_path}}" alt="{{$room->roomname}}'s Image" class="home-thumbnail-img">                      <div class="home-thumbnail-captions p-2">
                            <h4 class="color-purple">{{$room->roomname}}</h4>
                            <div class="color-dark-purple"><span><i class="fas fa-map-marker-alt"></i></span> {{$room->district}},{{$room->state}}</div>
                            <h5 class="color-purple text-right">RM{{$room->price}}/malam</h5>
                        </div>                      
                    </a>
                </div>
            @endforeach            
        </div>

    </section>
@endsection


@section('script')
{{-- sweet alert --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>


$(document).ready(function() {    

});
</script>
@endsection