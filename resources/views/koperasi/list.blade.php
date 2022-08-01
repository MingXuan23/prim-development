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
        <h4 class="display-5">{{ $list_detail->nama }}</h4>
      </div>
    </div>

    <div class="row">
      <div class="col d-flex justify-content-center align-items-center text-center mb-3">
        @if($list_detail->status == 2)
        <span class="badge rounded-pill bg-warning">Sedang Diproses</span>
        @elseif($list_detail->status == 100)
        <span class="badge rounded-pill bg-danger text-white">Dibatalkan oleh Restoran</span>
        @elseif($list_detail->status == 200)
        <span class="badge rounded-pill bg-danger text-white">Dibatalkan oleh Anda</span>
        @elseif($list_detail->status == 3)
        <span class="badge rounded-pill bg-success text-white">Berjaya Diambil</span>
        @endif
      </div>
    </div>
    

    <hr>

    <div class="row">
      <label class="col-sm-3 col-form-label ">No Telefon dan E-mel Koperasi</label>
      <div class="col-sm-7">
        <p class="col col-form-label">{{ $list_detail->telno }} | {{ $list_detail->email }}</p>
      </div>
    </div>

    <div class="row">
      <label class="col-sm-3 col-form-label ">Alamat</label>
      <div class="col-sm-7">
        <p class="col col-form-label">
          {{ $list_detail->address }} {{ $list_detail->postcode }} {{ $list_detail->state }}
        </p>
      </div>
    </div>

    <div class="row">
      <label class="col-sm-3 col-form-label ">Sekolah</label>
      <div class="col-sm-7">
        <p class="col col-form-label">{{ $sekolah_name }}</p>
      </div>
    </div>

    @php($order_date = date_create($list_detail->updated_at))
    <div class="row">
      <label class="col-sm-3 col-form-label ">Tarikh dan Waktu Pesan</label>
      <div class="col-sm-7">
        <p class="col col-form-label">{{ date_format($order_date,"M D Y, h:i A") }}</p>
      </div>
    </div>

    @php($pickup_date = date_create($list_detail->pickup_date))
    @php($open_hour = date_create($allOpenDays->open_hour))
    @php($close_hour = date_create($allOpenDays->close_hour))
    <div class="row">
      <label class="col-sm-3 col-form-label ">Tarikh Pengambilan dan Waktu Buka</label>
      <div class="col-sm-7">
        <p class="col col-form-label">
          {{ date_format($pickup_date,"D, M d Y") }} | 
          {{ date_format($open_hour,'h:i A') }} - {{ date_format($close_hour,'h:i A') }}
        </p>
      </div>
    </div>

    <div class="row">
      <label class="col-sm-3 col-form-label ">Nota</label>
      <div class="col-sm-7">
        @if($list_detail->note != null)
          <p class="col col-form-label">{{ $list_detail->note }}</p>
        @else
          <p class="col col-form-label"><i>Tiada Nota</i></p>
        @endif
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
                <td>{{ number_format($row->price, 2, '.', '') }}</td>
                <td>{{ $row->quantity }}</td>
                <td>{{ number_format($totalPrice[$row->name], 2, '.', '') }}</td>
              </tr>
            @endforeach
              <tr>
                <td class="noborder"></td>
                <td class="noborder"></td>
                <td class="table-dark noborder">Jumlah Keseluruhan</td>
                <td class="table-dark noborder">RM {{ number_format($list_detail->total_price, 2, '.', '') }}</td>
              </tr>
          </tbody>
      </table>
    </div>

    <div class="row">
      <div class="col d-flex justify-content-end">
        <a href="{{ url()->previous() }}" type="button" class="btn btn-light mr-2">Kembali</a>
      </div>
    </div>

  </div>

</div>


@endsection

@section('script')


@endsection