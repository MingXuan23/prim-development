@extends('layouts.master')

@section('css')

@endsection

@section('content')
<h4 class="font-size-18">Update Produk</h4>

<div class="card">
  <div class="card-body">
      @if(count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                <li>{{$error}}</li>
                @endforeach
            </ul>
        </div>
        @endif

      <form action="{{ route('koperasi.updateProduct', $edit->id , $test->id) }}" method="POST" enctype="multipart/form-data">
        {!! csrf_field() !!}
        <label>* wajib diisi</label></br>
        <label>* Nama</label></br>
        <input type="text" name="nama" id="nama" class="form-control" placeholder="Masukkan Nama Produk"  value="{{$edit->name}}" required></br>
        <label>Penerangan</label></br>
        <input type="text" name="description" value="{{$edit->desc}}" id="description" class="form-control"placeholder="Penerangan Produk"></br>
        <label>* Harga</label></br>
        <input type="number" name="price" id="price" class="form-control"placeholder="Masukkan Harga Produk" step ="any" min="0" value="{{$edit->price}}" required></br>
        <label>* Kuantiti</label></br>
        <input type="number" name="quantity" id="quantity" class="form-control" min = "0" placeholder="Masukkan bilangan kuantiti Produk"  value="{{$edit->quantity_available}}" required></br>
        <label>* Jenis Produk</label></br>    
                              <div class="form-group required">
                                <select name="type" id="type" class="form-control"
                                    data-parsley-required-message="Sila masukkan jenis produk" required>
                                    <option value="{{ $edit->product_group_id }}">{{$test->type_name }}</option>
                                    @foreach($type as $row)
                                     <option value="{{ $row->id }}">{{ $row->name }}</option>
                                     @endforeach
                                </select>
                            </div>
          
        <label>* Status Produk</label></br>
      
             <div class="col">
                          <div class="form-group required">
                              <select name="status" class="form-control"
                                  data-parsley-required-message="Sila masukkan jenis produk" required>
                                  <option value="{{ $edit->status }}">Default</option>
                                  <option value="0">not available</option>
                                  <option value="1"> available</option>
                              </select>
                          </div>
      </div>
                     
         <label>Gambar Produk</label>
         <div class="fallback">
         <input type="file" name="images" id="image"></br></br>
        </div>

        <button type="submit" class="btn btn-primary waves-effect waves-light mr-1">Simpan</button>
    </form>
  
  </div>
</div>

@endsection