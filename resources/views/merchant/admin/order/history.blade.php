@extends('layouts.master')

@section('css')

@endsection

@section('content')

<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Urus Pesanan > Sejarah Pesanan</h4>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col">
        <select class="form-control" data-parsley-required-message="Sila pilih hari" id="pickup_day" required>
            <option value="" selected>Semua Pesanan</option>
        </select>
    </div>
    <div class="col">
        <button class="btn btn-primary">Filter</button>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header">
      <i class="ti-email mr-2"></i>Sejarah Pesanan</div>
    <div class="card-body">
      @if(Session::has('success'))
        <div class="alert alert-success">
          <p>{{ Session::get('success') }}</p>
        </div>
      @endif
      <div class="table-responsive">
        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
  
          <thead>
            <tr>
              <th style="width: 2%">No.</th>
              <th style="width: 15%">Pelanggan</th>
              <th style="width: 10%">No Telefon</th>
              <th style="width: 10%">Tarikh Pengambilan</th>
              <th style="width: 10%">Jumlah (RM)</th>
              <th style="width: 10%" class="text-center">Status</th>
            </tr>
          </thead>
  
          <tbody>
              @csrf
                <tr>
                    <td class="align-middle">1.</td>
                    <td class="align-middle">Bob</td>
                    <td class="align-middle">+601112715744</td>
                    <td class="align-middle">10/7/2022 12:31 PM</td>
                    <td class="align-middle">
                        11.00 | 
                        <a href="#">Lihat Pesanan</a>
                    </td>
                    <td class="align-middle text-center">
                        <span class="badge rounded-pill bg-success text-white btn-block">Berjaya Diambil</span>
                        {{-- <span class="badge rounded-pill bg-danger text-white btn-block">Dibatalkan</span> --}}
                    </td>
                </tr>
                <tr>
                    <td colspan="6" class="text-center"><i>Tiada Pesanan Buat Masa Sekarang.</i></td>
                </tr>
              
          </tbody>
        </table>
      </div>
      {{-- <div class="row mt-2 ">
        <div class="col d-flex justify-content-end">
          {{ $order->links() }}
        </div>
      </div> --}}
    </div>
</div>

@endsection

@section('script')

@endsection