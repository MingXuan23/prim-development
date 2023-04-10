@extends('layouts.master')

@section('css')
    <link href="{{ URL::asset('assets/libs/dropzone/dropzone.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{ URL::asset('assets/css/required-asterick.css')}}" rel="stylesheet">
    <link href="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet">
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

      <form action="{{ route('koperasi.storeProduct) }}" method="POST" enctype="multipart/form-data">
        {!! csrf_field() !!}
        <label>* wajib diisi</label></br>
        <label>* Nama</label></br>
        <input type="text" name="nama" id="nama" class="form-control" placeholder="Masukkan Nama Produk"  value="" required></br>
        <label>Penerangan</label></br>
        <input type="text" name="description" id="description" class="form-control"placeholder="Penerangan Produk"></br>
        <label>* Harga</label></br>
        <input type="text" name="price" id="price" class="form-control"placeholder="Masukkan Harga Produk"  value="" required></br>
        <label>* Kuantiti</label></br>
        <input type="number" name="quantity" id="quantity" class="form-control" placeholder="Masukkan bilangan kuantiti Produk"  value="" required></br>
       
         <label>Gambar Produk</label>
         <div class="fallback">
         <input name="images" type="file" id="images"></br></br>
        </div>
                      
      

        <button type="submit" class="btn btn-primary waves-effect waves-light mr-1">Simpan</button>
    </form>
  
  </div>
</div>
@endsection