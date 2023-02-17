@extends('layouts.master')

@section('css')

@endsection

@section('content')
<h4 class="font-size-18">Tambah Produk Type</h4>

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

      <form action="{{route('koperasi.storeType')}}" method="POST" enctype="multipart/form-data">
        {!! csrf_field() !!}
        <label>* wajib diisi</label></br>
        <label>* Nama Produk</label></br>
        <input type="text" name="name" id="name" class="form-control" placeholder="Masukkan Nama Produk"  value="" required></br>

        <button type="submit" class="btn btn-primary waves-effect waves-light mr-1">Simpan</button>
    </form>
  
  </div>
</div>
@endsection