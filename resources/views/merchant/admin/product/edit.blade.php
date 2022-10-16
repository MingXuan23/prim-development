@extends('layouts.master')

@section('css')

<style>
#img-size
{
    width: 100px;
    height: 100px;
    object-fit: cover;
}

</style>

@endsection

@section('content')

<div class="row align-items-center">
  <div class="col-sm-6">
      <div class="page-title-box">
          <h4 class="font-size-18"></i>Kemaskini Produk Item</h4>
          <div class="form-group">
            <label><a href="{{ route('admin.merchant.product') }}" class="text-muted">Urus Produk</a> <i class="fas fa-angle-right"></i> <a href="{{ route('admin.merchant.product-item', $group->id) }}" class="text-muted">{{ $group->name }}</a> <i class="fas fa-angle-right"></i> Kemaskini - {{ $item->name }}</label>
          </div>
      </div>
  </div>
</div>

@if(Session::has('success'))
  <div class="alert alert-success">
    <p>{{ Session::get('success') }}</p>
  </div>
@endif

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        
        <h4>{{ $item->name }}</h4>

        <form action="{{ route('admin.merchant.update-item') }}" method="POST" enctype="multipart/form-data" class="form-validation">
          {{csrf_field()}}
          @method('PUT')
          <div class="row">
            <div class="col-xl-3">
                @if($item->image == null)
                <i>Tiada Imej</i>
                @else
                <img class="rounded img-fluid bg-dark" id="img-size" src="{{ URL($image_url.$item->image) }}">
                @endif
                <div class="form-group required custom-file" style="margin-top: 9px">
                  <label class="custom-file-label" for="image">Gambar Item</label>
                  <input class="custom-file-input" type="file" name="image" id="image">
                </div>
                {{-- <div class="form-group">
                  
                    <label>Ubah Gambar</label>
                    <div class="fallback">
                        <input name="image" type="file">
                    </div>
                </div> --}}
            </div>

            <div class="col-xl-9">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" name="name" value="{{ $item->name }}" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Harga (RM)</label>
                            <input type="number" name="price" value="{{ number_format($item->price, 2, '.', '') }}" oninput="validate(this)" min="0.01" step="0.01" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Penerangan</label>
                            <input type="text" name="desc" value="{{ $item->desc }}" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Kuantiti Per Slot</label>
                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" step="1" class="form-control" required>
                        </div>
                    </div>
                </div>
                
            </div>
          </div>

          <input type="hidden" name="id" value="{{ $item->id }}">
          <input type="hidden" name="image_url" value="{{ $image_url }}">

          <div class="text-right">
            <a type="button" 
              href="{{ route('admin.merchant.product-item', $item->product_group_id) }}"
              class="btn btn-secondary waves-effect waves-light mr-1">
              Kembali
            </a>

            <button type="submit" 
              class="btn btn-primary waves-effect waves-light">
              Kemaskini
            </button>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>

@endsection

@section('script')

<script>
  $(document).ready(function(){
    $('.alert-success').delay(2000).fadeOut()

    var validate = function(e) {
        var t = e.value;
        e.value = (t.indexOf(".") >= 0) ? (t.substr(0, t.indexOf(".")) + t.substr(t.indexOf("."), 3)) : t;
    }
        
  });
</script>

@endsection