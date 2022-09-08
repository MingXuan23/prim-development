@extends('layouts.master')

@section('css')

@endsection

@section('content')

<div class="row align-items-center">
    <div class="col">
        <div class="page-title-box">
            <h4 class="font-size-18">Urus Pesanan</h4>
        </div>
    </div>
    <div class="d-flex justify-content-end mr-3">
        <a href="{{ route('admin.merchant.history') }}" class="btn btn-primary">Sejarah Pesanan</a>
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
      <i class="ti-email mr-2"></i>Pesanan Anda</div>
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
              <th style="width: 15%">Nota</th>
              <th style="width: 10%">Jumlah (RM)</th>
              <th style="width: 10%" class="text-center">Status</th>
              <th style="width: 15%" class="text-center">Action</th>
            </tr>
          </thead>
  
          <tbody>
              @csrf
                <tr>
                    <td class="align-middle">1.</td>
                    <td class="align-middle">Ismail Bin Mail</td>
                    <td class="align-middle">+601111715744</td>
                    <td class="align-middle">10/7/2022 12:30 PM</td>
                    <td class="align-middle"><i>Tiada Nota</i></td>
                    <td class="align-middle">
                        12.00 | 
                        <a href="#">Lihat Pesanan</a>
                    </td>
                    <td class="align-middle text-center">
                        <span class="badge rounded-pill bg-success text-white">Berjaya dibayar</span>
                        {{-- <span class="badge rounded-pill bg-danger text-white">Tidak Diambil</span> --}}
                    </td>
                    <td class="align-middle">
                        <div class="text-center">
                            <button type="button" id="done_pickup" class="btn btn-primary">Berjaya Diambil</button>
                            <button type="button" id="cancel_order" class="btn btn-danger" data-order-id="">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="8" class="text-center"><i>Tiada Pesanan Buat Masa Sekarang.</i></td>
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

{{-- confirmation modal --}}
<div id="confirmationModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-header">
              <h4 class="modal-title"></h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
              Adakah anda pasti?
          </div>
          <div class="modal-footer">
              <button type="button" data-dismiss="modal" class="btn btn-light">Kembali</button>
              <button type="button" class="btn">Confirm</button>
          </div>
      </div>
  </div>
</div>

@endsection

@section('script')

<script>
    $('#done_pickup').click(function() {
        $('#confirmationModal').modal('show')
    })

    $('#cancel_order').click(function() {
        $('#confirmationModal').modal('show')
    })
</script>

@endsection