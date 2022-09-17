@extends('layouts.master')

@section('css')

@endsection

@section('content')

<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Waktu Operasi <i class="fas fa-angle-right"></i> Semak Pesanan</h4>
        </div>
    </div>
</div>

@csrf

<div class="card">
    <div class="card-body">
        @if(Session::has('success'))
        <div class="alert alert-success" id="session">
            <p id="success">{{ Session::get('success') }}</p>
        </div>
        @elseif(Session::has('error'))
        <div class="alert alert-danger" id="session">
            <p id="success">{{ Session::get('error') }}</p>
        </div>
        @endif
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama Pelanggan</th>
                        <th>No Telefon</th>
                        <th>Tarikh dan Masa Pengambilan</th>
                        <th>Jumlah (RM)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                
                <tbody>
                    @php($i = 0)
                    @forelse($order as $row)
                    <tr>
                        <td>{{ ++$i }}.</td>
                        <td>{{ $row->name }}</td>
                        <td>{{ $row->telno }}</td>
                        <td>{{ $pickup_date[$row->id] }}</td>
                        <td>
                            {{ number_format($row->total_price, 2, '.', '') }} |
                            <a href="{{ route('admin.merchant.list', $row->id) }}">Lihat Pesanan</a>
                        </td>
                        <td class="btn-section">
                            <button data-order-id="{{ $row->id }}" class="btn-edit-order btn btn-primary">Ubah Hari dan Masa</button>
                            <button data-order-id="{{ $row->id }}" class="btn-cancel-order btn btn-danger">Buang</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center"><i>Tiada Pesanan - Waktu Operasi Boleh <b>Dikemaskini.</b></i></td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="row">
            <div class="col d-flex justify-content-end">
              <a href="{{ url()->previous() }}" type="button" class="btn-lg btn-light mr-2">Kembali</a>
              @if(count($order) == 0)
              <form action="{{ route('admin.merchant.update-new-hours') }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="hour_id" id="hour_id" value="{{ $hour_id }}">
                <input type="hidden" name="status" id="status" value="{{ $status }}">
                <input type="hidden" name="updated_open" id="updated_open" value="{{ $open }}">
                <input type="hidden" name="updated_close" id="updated_close" value="{{ $close }}">
                <button type="submit" class="btn-lg btn-primary">Kemaskini</button>
              </form>
              @else
              <button type="button" class="alert-order btn-lg btn-primary">Kemaskini</button>
              @endif
            </div>
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
  
            <div class="alert alert-danger" id="popup" style="display: none"></div>

            <div class="row">
  
              <div class="col">
                <div class="form-group required">
                  <label class="col">Tarikh & Masa Pengambilan Baru</label>
                  <div class="col" id="pickup_time_div">
                    <input 
                        class="form-control"
                        type="datetime-local"
                        name="date_time_pickup"
                        id="date_time_pickup"
                        min="{{ date_format(now(), "Y-m-d H:i") }}" 
                        value="{{ date_format(now(), "Y-m-d H:i") }}" 
                    >
                    <input type="hidden" name="day" id="day" value="{{ $day }}">
                    <input type="hidden" name="status" id="status" value="{{ $status }}">
                    <input type="hidden" name="updated_open" id="updated_open" value="{{ $open }}">
                    <input type="hidden" name="updated_close" id="updated_close" value="{{ $close }}">
                  </div>
                </div>
              </div>
            </div>
            
          </div>
          
          <div class="modal-footer">
            <button type="button" class="btn btn-light mr-2" data-dismiss="modal">Tutup</button>
            <button type="button" class="btn-change-date btn btn-primary">Kemaskini</button>
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
                <button type="button" class="btn btn-danger" id="delete_order">Buang</button>
            </div>
        </div>
    </div>
</div>

{{-- confirmation delete modal --}}
<div id="alertModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Perhatian</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                Anda masih mempunyai pesanan yang belum diuruskan lagi.
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-light">OK</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')

<script>
    $(document).ready(function() {
        $('#session').delay(2000).fadeOut()

        var order_id

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })

        $('.alert-order').click(function() {
            $('#alertModal').modal('show')
        })
        
        $('.btn-edit-order').click(function() {
            order_id = $(this).attr('data-order-id')
            $('#popup').hide()
            $('#editOrderDateTimeModal').modal('show')
        })

        $('.btn-change-date').click(function() {
            var date_time = $('#date_time_pickup').val()
            var day = $('input#day').val()
            var status = $('input#status').val()
            var open = $('input#updated_open').val()
            var close = $('input#updated_close').val()
            
            $.ajax({
                url: "{{ route('admin.merchant.change-datetime') }}",
                method: "POST",
                data: { o_id:order_id,
                        date_time:date_time,
                        day:day,
                        status:status,
                        open:open,
                        close:close, },
                beforeSend:function() {

                },
                success:function(result) {
                    console.log(result)
                    if(result.status == "error") {
                        $('#popup').show().empty().append(result.message)
                    }
                    else {
                        location.reload()
                    }
                },
                error:function(result) {
                    console.log(result.responseText)
                },
            })
        })

        $('.btn-cancel-order').click(function() {
            order_id = $(this).attr('data-order-id')
            $('#deleteConfirmationModal').modal('show')
        })

        $('#delete_order').click(function() {
            $.ajax({
                url: "{{ route('admin.merchant.destroy-order') }}",
                method: "DELETE",
                data: {o_id:order_id},
                beforeSend:function() {

                },
                success:function(result) {
                    location.reload()
                    // console.log(result)
                },
                error:function(result) {
                    console.log(result.responseText)
                }
            })
        })
    })
</script>

@endsection