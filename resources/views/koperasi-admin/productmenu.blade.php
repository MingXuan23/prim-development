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
    <div class="card-body">
      {{-- <div class="card-header">List Of Applications</div> --}}
      <div>
        <h4>Pergi ke halaman jenis produk</h4>
        <a  href="{{route('koperasi.addtype')}}" class="btn btn-primary"> <i
            class="fas fa-plus"></i> Tambah jenis produk</a>
      </div>
      <div>
      <h4>To go to produk page</h4>
        <a  href="{{route('koperasi.indexAdmin')}}" class="btn btn-primary"> <i
            class="fas fa-plus"></i> Tambah produk</a>
      </div>
<br><br>
      <div class="table-responsive">
          <table id="donationTable" class="table table-bordered table-striped dt-responsive"
            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
            <thead>
              <tr style="text-align:center">
                <th> No. </th>
                <th> Nama jenis produk </th>
                <th> Action </th>
              </tr>
            </thead>
            @foreach($group as $group)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $group->name }}</td>
                                        <td> <a href="{{ route('koperasi.deleteType',$group->id) }}" style="display:inline"> <button type="submit" class="btn btn-danger m-1">Padam</button></td>
                                    </tr>
            @endforeach
@endsection
