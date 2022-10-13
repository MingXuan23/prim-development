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
      koperasi >> pengesahan
      <!-- <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Welcome to Veltrix Dashboard</li>
            </ol> -->
    </div>
  <div class="col-md-12">
    <div class="card">
      {{-- <div class="card-header">List Of Applications</div> --}}


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
              <th style="width: 2%">No.</th>
              <th style="width: 15%">Nama Pelanggan</th>
              <th style="width: 10%">No Telefon Pelanggan</th>
              <th style="width: 10%">Tarikh dan Waktu Pesan</th>
              <th style="width: 10%">Tarikh Pengambilan</th>
              <th style="width: 15%">Nota</th>
              <th style="width: 10%">Jumlah (RM)</th>
              <th style="width: 10%">Status</th>
              <th style="width: 15%">Action</th>
              </tr>
            </thead>
            <tbody>
            @foreach($customer as $customer)
              <tr>


              <td>{{ $loop->iteration }}</td>
                <td>
                  {{$customer->name}}
                </td>
                
                <td>
                {{$customer->telno}}
                </td>

                <td>
                 {{$customer->updated_at}}
                </td>

                <td>
                {{$customer->pickup_date}}
                </td>

                <td>
                {{$customer->note}}
                </td>

                <td>
                {{number_format($customer->total_price,2)}}
                </td>

                <td>
                @if($customer->status == 2)
                    <span class="badge rounded-pill bg-warning ">Sedang Diproses</span>
                @elseif($customer->status == 4)
                    <span class="badge rounded-pill bg-danger text-white">Tidak Diambil</span>
                @elseif($customer->status == 1)
                <span class="badge rounded-pill bg-warning ">In cart</span>
                @else
                <div class="d-flex justify-content-center"><span class="badge badge-success">Telah dibayar</span></div>
                @endif
                </td>

                <td class="allign-middle">
                <div class="">
                <a href="{{ route('koperasi.storeConfirm',$customer->id) }}" style="display:inline">
                <button style="margin: 4px" type="submit" class="btn btn-primary">Telah Diambil</button>
                <a href="{{ route('koperasi.notConfirm',$customer->id) }}" style="display:inline">
                <button style="margin: 4px" type="submit" class="btn btn-danger m1">Tidak Diambil</button>
                </div>
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