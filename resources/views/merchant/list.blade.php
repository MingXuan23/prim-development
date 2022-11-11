@extends('layouts.master')

@section('css')

@include('layouts.datatable');

<style>
.noborder{
  border: none!important;
}

#img-size
{
  width: 15%;
  object-fit: cover;
}
</style>

@endsection

@section('content')

<div class="card">
  {{-- <div class="card-header">
      <h2 class="d-inline-block">Senarai Pesanan</h2>
  </div> --}}
  <div class="container mt-3" >
    <div class="row" style="background: #333547; border-radius: 20px">
      <div class="col mx-auto">
        <div class="text-center">
          <img src="{{ URL::asset('assets/images/logo/prim-transparent.png') }}" class="" id="img-size" alt="" />
        </div>
      </div>
    </div>
    {{-- <div class="row">
      <div class="col mx-auto">
        <div class="text-center">
          <h4>Receipt</h4>
        </div>
      </div>
    </div> --}}
  </div>
  <hr class="ml-4 mr-4">
  <div class="card-body">

    <div class="row">
      <div class="col d-flex justify-content-center align-items-center text-center mb-3">
        <h4 class="display-5">{{ $list->nama }}</h4>
      </div>
    </div>

    <div class="row">
      <div class="col d-flex justify-content-center align-items-center text-center mb-3">
        @if($list->status == 'Paid')
        <span class="badge rounded-pill bg-info text-white">Selesai Dibayar</span>
        @elseif($list->status == 'Cancel by merchant')
        <span class="badge rounded-pill bg-danger text-white">Dibatalkan oleh Restoran</span>
        @elseif($list->status == 'Cancel by user')
        <span class="badge rounded-pill bg-danger text-white">Dibatalkan oleh Anda</span>
        @elseif($list->status == 'Picked-Up')
        <span class="badge rounded-pill bg-success text-white">Berjaya Diambil</span>
        @endif
      </div>
    </div>

    <hr>

    <div class="row">
      <label class="col-sm-3 col-form-label ">No Telefon dan E-mel Peniaga</label>
      <div class="col-sm-7">
        <p class="col col-form-label">{{ $list->telno }} | {{ $list->email }}</p>
      </div>
    </div>

    <div class="row">
      <label class="col-sm-3 col-form-label ">Alamat</label>
      <div class="col-sm-7">
        <p class="col col-form-label">
          {{ $list->address }} {{ $list->postcode }} {{ $list->state }}
        </p>
      </div>
    </div>

    <div class="row">
      <label class="col-sm-3 col-form-label ">Tarikh dan Waktu Pesan</label>
      <div class="col-sm-7">
        <p class="col col-form-label">{{ $order_date }}</p>
      </div>
    </div>

    <div class="row">
      <label class="col-sm-3 col-form-label ">Tarikh Pengambilan</label>
      <div class="col-sm-7">
        <p class="col col-form-label">{{ $pickup_date }}</p>
      </div>
    </div>

    <div class="row">
      <label class="col-sm-3 col-form-label ">Nota</label>
      <div class="col-sm-7">
        <p class="col col-form-label">{!! $list->note ?: "<i>Tiada Nota</i>" !!}</p>
      </div>
    </div>

    {{-- <div class="row">
      <label class="col-sm-3 col-form-label ">Bayaran</label>
      <div class="col-sm-10">
        <p class="col col-form-label">Online-Banking</p>
      </div>
    </div> --}}
    <hr>

  <h5>Isi Pesanan</h5>
  <div class="table-responsive">
      <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
          <thead>
              <tr>
                  <th>Item</th>
                  <th>Harga Per Unit (RM)</th>
                  <th>Kuantiti</th>
                  <th>Jumlah (RM)</th>
              </tr>
          </thead>
          
          <tbody>
            @foreach($item as $row)
              <tr>
                <td>{{ $row->name }}</td>
                <td>{{ $price[$row->id] }}</td>
                <td>{{ $row->quantity * $row->selling_quantity }}</td>
                <td>{{ $total_price[$row->id] }}</td>
              </tr>
            @endforeach
              <tr>
                <td class="noborder"></td>
                <td class="noborder"></td>
                <td class="table-dark noborder">Jumlah Keseluruhan</td>
                <td class="table-dark noborder">RM {{ $total_order_price }}</td>
              </tr>
          </tbody>
      </table>
    </div>

    <div class="row">
      <div class="col d-flex justify-content-end">
        <a href="{{ url()->previous() }}" type="button" class="btn-lg btn-light mr-2">Kembali</a>
      </div>
    </div>

  </div>

</div>


@endsection

@section('script')

@endsection