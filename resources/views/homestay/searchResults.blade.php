@extends('layouts.master')

@section('css')
    <link rel="stylesheet" href="{{ URL::asset('assets/homestay-assets/style.css')}}">
@endsection

@section('content')

    <section aria-label="Banner" class="mb-3">
        <div aria-label="Search Input" >
            <form action="{{route('homestay.searchRoom')}}" method="get" enctype="multipart/form-data" class="d-flex justify-content-center align-items-center mt-3" id="form-search">
                @csrf            
                <div class="search-container-input">
                    <input type="search" placeholder="Cari homestay" id="search-room" name="searchRoom" autocomplete="off" class="input">
                    <svg id="search-icon" fill="#852aff" width="20px" height="20px" viewBox="0 0 1920 1920" xmlns="http://www.w3.org/2000/svg">
                      <path d="M790.588 1468.235c-373.722 0-677.647-303.924-677.647-677.647 0-373.722 303.925-677.647 677.647-677.647 373.723 0 677.647 303.925 677.647 677.647 0 373.723-303.924 677.647-677.647 677.647Zm596.781-160.715c120.396-138.692 193.807-319.285 193.807-516.932C1581.176 354.748 1226.428 0 790.588 0S0 354.748 0 790.588s354.748 790.588 790.588 790.588c197.647 0 378.24-73.411 516.932-193.807l516.028 516.142 79.963-79.963-516.142-516.028Z" fill-rule="evenodd"></path>
                    </svg>
                </div>
            </form>
        </div>
        <h5 class="color-purple">{{count($rooms)}} Penginapan Telah Dijumpa</h5>
    </section>
 
    <section aria-label="Homestays or Rooms Listings" >
        <div class="home-thumbnails-container">
            @for($i = 0; $i < count($rooms); $i++)
                <div class="home-thumbnail-container">
                    <a href="{{route('homestay.showRoom',['id' => $rooms[$i]->roomid, 'name' => $rooms[$i]->roomname])}}" target="_blank">
                        <img src="{{$roomImage[$i]}}" alt="{{$rooms[$i]->roomname}}'s Image" class="home-thumbnail-img">                      
                        <div class="home-thumbnail-captions p-2">
                            <h4 class="color-purple">{{$rooms[$i]->roomname}}</h4>
                            <div class="color-dark-purple"><span><i class="fas fa-map-marker-alt"></i></span> {{$rooms[$i]->district}},{{$rooms[$i]->state}}</div>
                            <h5 class="color-purple text-right">RM{{$rooms[$i]->price}}/malam</h5>
                        </div>                      
                    </a>
                </div>
            @endfor           
        </div>
    </section>
    {{$rooms->withQueryString()->links()}}
@endsection


@section('script')
{{-- sweet alert --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>


$(document).ready(function() {    
    $('#form-search').on('submit',function(e){
        if($('#search-room').val() == ''){
            e.preventDefault();
            Swal.fire('Sila masukkan nama homestay untuk memulakan pencarian');
        }
    });  
    $('#search-icon').on('click', function(){
        $('#form-search').trigger('submit');
    });
});
</script>
@endsection