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
    <div class="card">
      {{-- <div class="card-header">List Of Applications</div> --}}
      <div>
        {{-- route('sekolah.create')  --}}
        <a style="margin: 19px; float: right;" href="{{ route('koperasi.createProduct') }}" class="btn btn-primary"> <i
            class="fas fa-plus"></i> Tambah Produk</a>
      </div>

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

        <div class="table-responsive">
          <table id="donationTable" class="table table-bordered table-striped dt-responsive"
            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
            <thead>
              <tr style="text-align:center">
                <th> No. </th>
                <th> Nama Produk </th>
                <th> Penerangan </th>
                <th> Gambar </th>
                <th> Kuantiti </th>
                <th> Harga </th>
                <th> Status </th>
                <th>Type</th>
                <th> Action </th>
              </tr>
            </thead>
            @foreach($product as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->desc }}</td>
                                        <td>
                                          <img src="{{ URL('koperasi-item/'.$item->image)  }}" width="80px">
                                        </td>
                                        <td>{{ $item->quantity_available }}</td>
                                        <td>{{ number_format($item->price,2)}}</td>
                                        <td>
                                         @if($item->status == 0) 
                                         <div class="d-flex justify-content-center"><span class="badge badge-danger">not aivalable</span></div>
                                         @else
                                         <div class="d-flex justify-content-center"><span class="badge badge-success">aivalable</span></div>
                                         @endif
                                        </td>
                                        <td>
                                          @if($item->product_group_id == 1)
                                          Barang sekolah
                                          @elseif($item->product_group_id == 2)
                                          Alat tulis
                                          @elseif($item->product_group_id == 3)
                                          Buku Kerja
                                          @elseif($item->product_group_id == 4)
                                          Makanan dan Minuman
                                          @endif
                                        </td>
                                        <td>
                                         <a href ="{{ route('koperasi.editProduct',$item->id) }}"> <button type="button" data-dismiss="modal" class="btn btn-primary" id="edit" name="edit">Edit</button></a>
                                         <a href="{{ route('koperasi.deleteProduct',$item->id) }}" 
                                         style="display:inline">
                                            <button type="submit" class="btn btn-danger m-1">Padam</button>
                                        </td>
                                    </tr>
                                @endforeach
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection


