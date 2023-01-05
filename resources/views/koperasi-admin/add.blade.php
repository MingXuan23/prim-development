@extends('layouts.master')

@section('css')

@endsection

@section('content')
<h4 class="font-size-18">Tambah Produk</h4>

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

      <form action="{{ route('koperasi.storeProduct') }}" method="POST" enctype="multipart/form-data">
        {!! csrf_field() !!}
        <label>* wajib diisi</label></br>
        <label>* Nama</label></br>
        <input type="text" name="nama" id="nama" class="form-control" placeholder="Masukkan Nama Produk"  value="" required></br>
        <label>Penerangan</label></br>
        <input type="text" name="description"  id="description" class="form-control"placeholder="Penerangan Produk"></br>
        <label>* Harga</label></br>
        <input type="number" name="price" id="price" value="" step="any"
        class="form-control" placeholder="0.00" required  min="0"></br>
        <label>* Kuantiti</label></br>
        <input type="number" name="quantity" id="quantity" class="form-control" placeholder="Masukkan bilangan kuantiti Produk"  value="" required></br>
        <label>* Jenis Produk</label></br>

        <div class="col">
                            <div class="form-group required">
                                <select name="type" id="type" class="form-control"
                                    data-parsley-required-message="Sila masukkan jenis produk" required>
                                    @foreach($type as $row)
                                     <option value="{{ $row->id }}">{{ $row->name }}</option>
                                     @endforeach
                                </select>
                            </div>
        </div>

        <label>* Status Produk</label></br>
      
        <div class="col">
                            <div class="form-group required">
                                <select name="status" class="form-control"
                                    data-parsley-required-message="Sila masukkan jenis produk" required>
                                    <option value="0">not available</option>
                                    <option value="1"> available</option>
                                </select>
                            </div>
        </div>

         <label>Gambar Produk</label>
         <div class="fallback">
         <input name="image" type="file" id='image'></br></br>
        </div>
        

        <button type="submit" class="btn btn-primary waves-effect waves-light mr-1">Simpan</button>
    </form>
  
  </div>
</div>

@endsection