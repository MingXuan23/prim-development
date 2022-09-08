@extends('layouts.master')

@section('css')

@endsection

@section('content')

<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Waktu Operasi > Semak Pesanan</h4>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama Pelanggan</th>
                        <th>No Telefon</th>
                        <th>Jumlah (RM)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                
                <tbody>
                    <tr>
                        <td>1.</td>
                        <td>Ismail Bin Mail</td>
                        <td>+601111715744</td>
                        <td>
                            12.00 |
                            <a href="#">Lihat Pesanan</a>
                        </td>
                        <td>
                            <button id="edit-order" class="btn btn-primary">Ubah Hari dan Masa</button>
                            <button id="cancel-order" class="btn btn-danger">Buang</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Edit Operation Hours Modal --}}
<div id="editOrderDateTimeModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
              <h4 class="modal-title">Ubah Hari dan Masa Pengambilan</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
  
            <div class="alert" id="popup" style="display: none"></div>
  
            <div class="row">
  
              <div class="col">
                <div class="form-group required">
                  <label class="col">Hari</label>
                  <div class="col" >
                    <select class="form-control" data-parsley-required-message="Sila pilih hari" id="pickup_day" required>
                      <option value="" selected>Pilih Hari</option>
                    </select>
                  </div>
                </div>
              </div>
  
              <div class="col">
                <div class="form-group required">
                  <label class="col">Masa</label>
                  <div class="col" id="pickup_time_div">
                    <input class="form-control" type="time" id="pickup_time" disabled required>
                  </div>
                </div>
              </div>
            </div>
            
          </div>
          
          <div class="modal-footer">
            <button type="button" class="btn btn-light mr-2" data-dismiss="modal">Tutup</button>
            <button type="button" class="btn btn-primary" id="order_submit">Kemaskini</button>
          </div>
  
        </div>
    </div>
</div>

{{-- confirmation delete modal --}}
<div id="deleteConfirmationModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Batalkan Pesanan</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                Adakah anda pasti?
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-light">Kembali</button>
                <button type="button" class="btn btn-danger" id="delete_order">Confirm</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')

<script>
    $(document).ready(function() {
        $('#edit-order').click(function() {
            $('#editOrderDateTimeModal').modal('show')
        })

        $('#cancel-order').click(function() {
            $('#deleteConfirmationModal').modal('show')
        })
    })
</script>

@endsection