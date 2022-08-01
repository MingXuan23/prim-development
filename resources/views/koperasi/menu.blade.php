@extends('layouts.master')

@section('css')

<style>
#has-bg-img{
  background-image: url("{{ URL('images/koperasi/Koperasi-default-background-2.jpg')}}");
  background-repeat: no-repeat;
  background-position: center;
  background-size: cover;
}

#shadow-bg{
  border-radius: 14px;
  box-shadow: 11px 10px 20px 8px rgba(0,0,0,0.10);
}

#img-size
{
  width: 200px;
  height: 200px;
  border-radius: 14px;
  object-fit: contain;
}
</style>

@endsection

@section('content')

<div class="row mt-4 ">
  <div class="col-12">
    <div class="card border border-dark">
      
      <div class="card-header text-white" id="has-bg-img">
        <div class="row justify-content-between">
          <h2>{{ $koperasi->nama }}</h2>
          <a href="{{ route('koperasi.edit', $koperasi->id) }}"><i class="mdi mdi-cart fa-3x"></i></a>
        </div>
        
        <p><i class="fas fa-school mr-2"></i> {{ $org->nama }}</p>
        <p><i class="fas fa-map-marker-alt mr-2"></i> {{ $koperasi->address }}, {{ $koperasi->city }}, {{ $koperasi->state }}</p>

        <div class="d-flex">
          @if($koperasi->status != 0)
          <p class="mr-4"><b>Waktu Buka</b></p>
          <p>Hari ini {{ $k_open_hour }} - {{ $k_close_hour }}</p>
          @else
          <p><b>Tutup pada hari ini</b></p>
          @endif
        </div>
      </div>
      
      <div class="card-body">

        @if(Session::has('success'))
          <div class="alert alert-success">
            <p>{{ Session::get('success') }}</p>
          </div>
        @elseif(Session::has('error'))
          <div class="alert alert-danger">
            <p>{{ Session::get('error') }}</p>
          </div>
        @endif
        
        @if(count($product_type) != 0 && count($product_item) != 0)
        @foreach($product_type as $type)
        <div class="d-flex justify-content-center">
          <h3 class="mb-4">{{ $type->type_name }}</h3>
        </div>

        <div class="row">

          @foreach($product_item as $product)
          @if($product->type_name == $type->type_name)
            @if($product->status != 0)
            <div class="col d-flex justify-content-center align-items-stretch">
            @else
            <div class="col d-flex justify-content-center align-items-stretch" style="opacity: 40%">
            @endif
            <!-- Simple card -->
              
              <div class="card" id="shadow-bg" style="width: 250px; ">
                <div class="row mt-4">
                  <div class="col d-flex justify-content-center align-items-center">
                    @if($product->image == null)
                    <img class="img-fluid" id="img-size" src="{{ URL('images/koperasi/default-item.png')}}" style="background-color:rgb(61, 61, 61)" alt="Card image cap">
                    @else
                    <img class="img-fluid" id="img-size" src="{{ URL('koperasi-item/'.$product->image)  }}" alt="Card image cap">
                    @endif
                  </div>
                </div>
                
                <div class="card-body">
                  <div class="card-title">
                    <div class="row justify-content-center align-self-center">
                      <h4 class="mt-2">{{ $product->name }}</h4>  
                    </div>
                  </div>
                  <div class="container">
                    <div class="row align-items-start" style="position: relative; min-height: 5vh;">
                      <div class="col">
                        @if($product->desc != null)
                        <p class="card-text"><i>{{ $product->desc }}</i></p>
                        @else
                        <p class="card-text"><i></i></p>
                        @endif
                      </div>
                    </div>
                  
                  
                    <div class="row align-items-end" style="position: absolute; bottom:3vh;">
                      <div class="col">
                        <p class="card-text"><b>RM</b> {{ number_format((double)$product->price, 2, '.', '') }}</p>
                      </div>
                    </div>
                  </div>

                </div>

                
                  <div class="card-footer bg-light mt-auto">
                    <form action="{{ route('koperasi.store') }}" method="POST">
                    @csrf
                        <div class="row justify-content-end">
                          <div class="text-right">
                            @if($product->status != 0)
                            <input type="number" name="item_quantity" value="1" min="1" step="1" class="form-group-sm" style="width:20%; height:70%" required>
                            <input type="hidden" id="item_id" name="item_id" value="{{ $product->id }}">
                            <input type="hidden" id="org_id" name="org_id" value="{{ $koperasi->id }}">
                            
                            <button type="submit" class="btn btn-primary waves-effect waves-light">Tambah</button>
                            @else
                            {{-- <p class="btn btn-primary waves-effect waves-light">Tambah</p> --}}
                            @endif
                          </div>
                        </div>
                    </form>
                </div>
                  
              </div>
                
            </div>
            @endif
            @endforeach
        </div>
        @endforeach
        @else
        <div class="d-flex justify-content-center">
            <h3>Koperasi Masih Belum Kemaskini Barang Tersedia</h3>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

{{-- quantityExceedModal --}}
{{-- <div class="modal fade" id="quantityExceedModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div> --}}

@endsection

@section('script')

<script>
  $(document).ready(function(){
    $('.alert-success').delay(2000).fadeOut();
    $('.alert-danger').delay(4000).fadeOut();
  });
</script>

@endsection