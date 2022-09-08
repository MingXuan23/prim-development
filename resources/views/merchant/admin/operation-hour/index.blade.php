@extends('layouts.master')

@section('css')

@endsection

@section('content')

<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Waktu Operasi</h4>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Hari</th>
                        <th>Buka/Tutup</th>
                        <th>Waktu Buka</th>
                        <th>Waktu Tutup</th>
                        <th>Action</th>
                    </tr>
                </thead>
                
                <tbody>
                    <tr>
                        <td>Isnin</td>
                        <td>
                            <span class="badge rounded-pill bg-success text-white">Buka</span>
                        </td>
                        <td>12:00 PM</td>
                        <td>11:30 PM</td>
                        <td>
                            <button id="edit-time" class="btn btn-primary">Edit</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Edit Operation Hours Modal --}}
<div id="editOperationHourModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @csrf
                <div class="row">
                    <div class="col">
                        <div class="form-group required">
                            <label class="col">Waktu Buka</label>
                            <div class="col">
                                <input class="form-control" type="time" id="open_hour" required>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group required">
                            <label class="col">Waktu Tutup</label>
                            <div class="col">
                                <input class="form-control" type="time" id="close_hour" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-light">Tutup</button>
                <button type="button" class="btn btn-primary">Kemaskini</button>
                <button type="button" id="check-order" class="btn btn-primary">Semak Pesanan</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')

<script>
    $(document).ready(function() {
        $('#edit-time').click(function() {
            $('#editOperationHourModal').modal('show')
        })

        $('#check-order').click(function() {
            window.location.href = "/admin-merchant/operation-hours/check-orders";
        })
    })
</script>

@endsection