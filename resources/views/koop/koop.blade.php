@extends('layouts.master')

@section('css')
<style>
.card-body
{
    width:auto;
    height: 230px;
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
    background: ;
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


</style>
@endsection

@section('content')

@foreach($sekolah as $sekolah)
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
<h2>Product Item</h4>
<br>
<div class="row">
@foreach($product as $product)
<div class="col-md-6 col-lg-6 col-xl-3">
        
        <!-- Simple card -->
        <div class="card">
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
                @if($product->status == 0) 
                
                @else
                <!-- <a href="" class=" btn btn-primary waves-effect waves-light">
                <i class="fas fa-cart-plus"></i> Add to Cart</a> -->
                <form action="{{ route('koperasi.store') }}" method="POST">
                    @csrf          
                    <div class="text-left">
                            @if($product->status != 0)
                            <input type="number" name="item_quantity" value="1" min="1" step="1" class="form-group-sm" style="width:20%; height:70%" required>
                            <input type="hidden" id="item_id" name="item_id" value="{{ $product->id }}">
                            <input type="hidden" id="org_id" name="org_id" value="{{ $sekolah->id }}">
                            
                            <button type="submit" class="btn btn-primary waves-effect waves-light">Tambah</button>
                            @else
                            {{-- <p class="btn btn-primary waves-effect waves-light">Tambah</p> --}}
                            @endif
                    </div>
                @endif
                
            </div>
        </div>

    </div><!-- end col -->
@endforeach
</div>
@endsection