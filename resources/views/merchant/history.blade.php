@extends('layouts.master')

@section('css')

@include('layouts.datatable');

@endsection

@section('content')

<div class="card mb-3">
    <div class="card-header">
      <i class="ti-email mr-2"></i>Sejarah Pesanan Anda</div>
    <div class="card-body">

      <div class="table-responsive">
        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
  
          <thead>
            <tr>
              <th style="width: 2%">No.</th>
              <th style="width: 15%">Peniaga</th>
              <th style="width: 10%">No Telefon Peniaga</th>
              <th style="width: 10%">Tarikh Pengambilan</th>
              <th style="width: 10%">Jumlah (RM)</th>
              <th style="width: 10%" class="text-center">Status</th>
            </tr>
          </thead>
  
          <tbody>
            @php($i = 0)
            @forelse($history as $row)
              @csrf
                <tr>
                  <td class="align-middle">{{ ++$i }}.</td>
                  <td class="align-middle">{{ $row->merchant_name }}</td>
                  <td class="align-middle">{{ $row->merchant_telno }}</td>
                  <td class="align-middle">{{ $pickup_date[$row->id] }}</td>
                  <td class="align-middle">
                    {{ $total_price[$row->id] }} |
                    <a href="{{ route('merchant.list', $row->id) }}">Lihat Pesanan</a>
                  </td>
                  <td class="align-middle text-center">
                    @if($row->status == 3)
                    <span class="badge rounded-pill bg-success text-white btn-block">Berjaya Diambil</span>
                    @elseif($row->status == 100 || $row->status == 200)
                    <span class="badge rounded-pill bg-danger text-white btn-block">Dibatalkan</span>
                    @endif
                  </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center"><i>Tiada Sejarah Rekod Pesanan.</i></td>
                </tr>
            @endforelse
              
          </tbody>
        </table>
      </div>
      <div class="row mt-2 ">
        <div class="col d-flex justify-content-end">
          {{ $history->links() }}
        </div>
      </div>
    </div>
    {{-- <div class="card-footer small text-muted"></div> --}}
</div>

@endsection

@section('script')

@endsection