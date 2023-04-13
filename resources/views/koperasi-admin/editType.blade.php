@extends('layouts.master')

@section('css')

@endsection

@section('content')

@if(count($errors) > 0)
  <div class="alert alert-danger">
      <ul>
          @foreach($errors->all() as $error)
          <li>{{$error}}</li>
          @endforeach
      </ul>
  </div>
@endif

@if(Session::has('success'))
  <div class="alert alert-success">
    <p id="success">{{ \Session::get('success') }}</p>
  </div>
@endif

<h4 class="font-size-18">Ubah Produk Type</h4>

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

      <form action="{{route('koperasi.updateType',$edit->id)}}" method="POST" enctype="multipart/form-data">
        {!! csrf_field() !!}
        <label>* Nama Produk</label></br>
        <input type="text" name="name" id="name" class="form-control" placeholder="Masukkan Nama Produk"  value="{{$edit->name}}" required></br>
        <input type="hidden" name="id" id="id" class="form-control" value="{{$edit->id}}">
        <button type="submit" class="btn btn-primary waves-effect waves-light mr-1">Simpan</button>
        <a  href="{{route('koperasi.return',4)}}" class="btn btn-danger">Return</a>
    </form>
  
  </div>
</div>

@endsection