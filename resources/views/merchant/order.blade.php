@extends('layouts.master')

@section('css')

@include('layouts.datatable');

@endsection

@section('content')

<div class="card mb-3">
    <div class="card-header">
      <i class="ti-email mr-2"></i>Pesanan Untuk Diambil</div>
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
              <th style="width: 15%">Peniaga</th>
              <th style="width: 10%">No Telefon Peniaga</th>
              <th style="width: 10%">Tarikh Pengambilan</th>
              <th style="width: 15%">Nota</th>
              <th style="width: 10%">Jumlah (RM)</th>
              <th style="width: 10%" class="text-center">Status</th>
              <th style="width: 15%" class="text-center">Action</th>
            </tr>
          </thead>
  
          <tbody>
            @php($i = 0)
            @forelse($order as $row)
              @csrf
                <tr>
                  <td class="align-middle">{{ ++$i }}.</td>
                  <td class="align-middle">{{ $row->merchant_name }}</td>
                  <td class="align-middle">{{ $row->merchant_telno }}</td>
                  <td class="align-middle">{{ $pickup_date[$row->id] }}</td>
                  <td class="align-middle">{!! $row->note ?: "<i>Tiada Nota</i>" !!}</td>
                  <td class="align-middle">
                    {{ $total_price[$row->id] }} |
                    <a href="{{ route('merchant.list', $row->id) }}">Lihat Pesanan</a>
                  </td>
                  <td class="align-middle text-center">
                    @if($row->status == 2)
                    <span class="badge rounded-pill bg-success text-white">Berjaya dibayar</span>
                    @else
                    <span class="badge rounded-pill bg-danger text-white">Tidak Diambil</span>
                    @endif
                  </td>
                  <td class="align-middle">
                    <div class="text-center">
                      <button type="button" class="btn btn-danger" data-order-id="{{ $row->id }}"><i class="fas fa-trash-alt"></i></button>
                    </div>
                  </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center"><i>Tiada Pesanan Buat Masa Sekarang.</i></td>
                </tr>
            @endforelse
              
          </tbody>
        </table>
      </div>
      <div class="row mt-2 ">
        <div class="col d-flex justify-content-end">
          {{ $order->links() }}
        </div>
      </div>
    </div>
    {{-- <div class="card-footer small text-muted"></div> --}}
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
{{-- end confirmation delete modal --}}

@endsection

@section('script')

<script src="{{ URL::asset('assets/libs/peity/peity.min.js')}}"></script>

<script>
  $(document).ready(function(){
    var o_id

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $(document).on('click', '.btn-danger', function(){
        o_id = $(this).attr('data-order-id')
        $('#deleteConfirmationModal').modal('show')
    });

    $('#delete_order').click(function() {
          $.ajax({
            url: "/merchant/order/" + o_id,
            method: 'DELETE',   
            data: {
                "_token": "{{ csrf_token() }}",
                oID: o_id,
            },
            
            success: function(result) {
                setTimeout(function() {
                    $('#deleteConfirmationModal').modal('hide')
                }, 2000)

                $('div.flash-message').html(result);

                location.reload();
            },
            error: function (result) {
                console.log(result.responseText)
            }
          })
        });

    $('.alert').delay(2000).fadeOut()

    
  });
</script>

@endsection