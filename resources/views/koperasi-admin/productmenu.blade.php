@extends('layouts.master')
@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ URL::asset('assets/css/datatable.css')}}">
@include('layouts.datatable')

@endsection


@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="page-title-box">
      <h4 class="font-size-18">{{ $koperasi->nama }}</h4>
      <!-- <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Welcome to Veltrix Dashboard</li>
            </ol> -->
    </div>
  <div class="col-md-12">
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
@if(\Session::has('success'))
<div class="alert alert-success">
  <p>{{ \Session::get('success') }}</p>
</div>
@endif

<div class="flash-message"></div>
    <div class="card">
      {{-- <div class="card-header">List Of Applications</div> --}}
      <div>
        <h4>To go to produk type page</h4>
        <a style="margin: 19px; float: left;" href="{{route('koperasi.addtype')}}" class="btn btn-primary"> <i
            class="fas fa-plus"></i> Produk Type</a>
      </div>
      <div>
      <h4>To go to produk page</h4>
        <a style="margin: 19px; float: left;" href="{{route('koperasi.indexAdmin')}}" class="btn btn-primary"> <i
            class="fas fa-plus"></i> Produk</a>
      </div>
@endsection
