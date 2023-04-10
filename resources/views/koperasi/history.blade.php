@extends('layouts.master')

@section('css')

@include('layouts.datatable');

@endsection

@section('content')

<div class="card mb-3">
  <div class="card-header">
    <i class="ti-clipboard mr-2"></i>Sejarah Pesanan Anda</div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">

        <thead>
          <tr>
            <th style="width: 2%">No.</th>
            <th style="width: 15%">Nama Koperasi</th>
            <th style="width: 10%">No Telefon Koperasi</th>
            <th style="width: 10%">Tarikh dan Waktu Pesan</th>
            <th style="width: 10%">Tarikh Pengambilan</th>
            <th style="width: 15%">Nota</th>
            <th style="width: 10%">Jumlah (RM)</th>
            <th style="width: 10%">Status</th>
          </tr>
        </thead>

        <tbody>
          @php($i = 1)
          @if(count($order) != 0)
            @foreach($order as $row)
            @php($date = date_create($row->updated_at))
            @php($pickup = date_create($row->pickup_date))
            <tr>
              <td class="align-middle">{{ $i }}.</td>
              <td class="align-middle">{{ $row->koop_name }}</td>
              <td class="align-middle">{{ $row->koop_telno }}</td>
              <td class="align-middle">{{ date_format($date,"M D Y, h:m:s A") }}</td>
              <td class="align-middle">{{ date_format($pickup,"D, M d Y") }}</td>
              <td class="align-middle">
                @if($row->note != null)
                {{ $row->note }}
                @else
                <i>Tiada Nota</i>
                @endif  
              </td>           
              <td class="align-middle">
                {{ number_format($row->total_price, 2, '.', '') }} | 
                <a href="{{ route('koperasi.list', $row->id) }}">Lihat Pesanan</a>
              </td>
              <td>
                @if($row->status == 3)
                <span class="badge rounded-pill bg-success text-white btn-block">Berjaya Diambil</span>
                @elseif($row->status == 100 || $row->status == 200)
                <span class="badge rounded-pill bg-danger text-white btn-block">Dibatalkan</span>
                @endif
              </td>
            </tr>
            @php($i++)
            @endforeach
          @else
          <tr>
              <td colspan="8" class="text-center"><i>Tiada Sejarah Rekod Pesanan.</i></td>
          </tr>
          @endif
        </tbody>
      </table>
    </div>
    <div class="row mt-2 ">
      <div class="col d-flex justify-content-end">
        {{ $order->links() }}
      </div>
    </div>
  </div>
  <div class="card-footer small text-muted"></div>
</div>

@endsection

@section('script')

@endsection