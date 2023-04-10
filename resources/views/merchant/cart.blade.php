@extends('layouts.master')

@section('css')

@include('layouts.datatable')

<style>
.noborder{
  border: none!important;
}

#img-size
{
  width: 100px;
  height: 100px;
  object-fit: cover;
}

.loading {
  width: 35px;
  height: 35px;
  display:none;
}

</style>

@endsection

@section('content')

<div class="container">
  <div class="row d-flex justify-content-center align-items-center">
    <div class="col">
      <div class="d-flex justify-content-center align-items-center">
        <span class="h2 m-4">Senarai Pesanan</span>
      </div>

      <div class="card">
        <div class="card-body">

          @if(Session::has('success'))
            <div class="alert alert-success">
              <p>{{ Session::get('success') }}</p>
            </div>
          @endif

          <div class="table-responsive">
            <table class="table table-borderless" width="100%" cellspacing="0">
                <thead>
                    <tr class="text-center">
                      <th style="width: 25%">Nama Item</th>
                      <th style="width: 20%">Kuantiti</th>
                      <th style="width: 30%">Harga Satu Unit (RM)</th>
                      <th style="width: 25%">Action</th>
                    </tr>
                </thead>
                
                <tbody>
                  @forelse($cart_item as $row)
                    <tr class="text-center">
                      <td class="align-middle">{{ $row->name }}</td>
                      <td class="align-middle">{{ $row->quantity }}</td>
                      <td class="align-middle">{{ number_format($row->price , 2, '.', '') }}</td>
                      <td class="align-middle">
                          <button type="button" data-cart-order-id="{{ $row->id }}" class="delete-item btn btn-danger"><i class="fas fa-trash-alt"></i></button>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="4" class="text-center"><i>Tiada Item Buat Masa Sekarang.</i></td>
                    </tr>
                  @endforelse
                </tbody>
            </table>
          </div>

        </div>
      </div>

      @if(count($cart_item) != 0)
        <div class="card mb-4 border">
          <div class="card-body p-4">
            <div class="table-responsive">
              <table class="table table-borderless mb-0">
                  <tbody>
                    <tr>
                      <th class="text-muted" scope="row">Jumlah Keseluruhan:</th>
                      <td class="lead">RM {{ number_format($cart->total_price , 2, '.', '') }}</td>
                    </tr>

                    <tr>
                      <th class="text-muted" scope="row">Waktu Pengambilan:</th>
                      <td class="lead">{{ $pickup_date }}</td>
                    </tr>
                  </tbody>
              </table>
            </div>
          </div>
        </div>

      <form action="{{ route('merchant.storeOrderPayment', ['o_id' => $id, 'p_id' => $cart->id]) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card mb-4 border">
          <div class="card-body p-4">
            <div class="row">
              <div class="col">
                <div class="form-group required">
                  <label class="col">Nota kepada Koperasi</label>
                  <div class="col">
                    <input type="text" name="note" class="form-control" placeholder="Optional">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        @endif
        <div class="row mb-2">
          <div class="col d-flex justify-content-end">
            <a href="{{ route('merchant.show', $id) }}" type="button" class="btn-lg btn-light mr-2">Kembali</a>
            @if(count($cart_item) != 0)<button type="submit" class="btn-lg btn-primary">Bayar</button>@endif
          </div>
        </div>
      </form>

    </div>
  </div>
</div>

{{-- Delete Confirmation Modal --}}
<div id="deleteConfirmModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title"><i class="fas fa-info-circle"></i>  Buang Item</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            Anda pasti mahu buang item ini?
          </div>
          <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn btn-light">Tidak</button>
            <img class="loading" src="{{ URL('images/koperasi/loading-ajax.gif')}}">
            <button type="button" data-dismiss="modal" class="btn btn-danger" id="delete_confirm_item">Buang</button>
          </div>
      </div>
  </div>
</div>
  {{-- end Delete Confirmation Modal --}}



@endsection

@section('script')

<script>
  $(document).ready(function(){

    var order_cart_id = null

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $('.delete-item').click(function() {
      order_cart_id = $(this).attr('data-cart-order-id')
      $('#deleteConfirmModal').modal('show')
    })

    $('#delete_confirm_item').click(function() {
      $.ajax({
        url: "{{ route('merchant.destroyItem') }}",
        method: "DELETE",
        data: {oc_id:order_cart_id},
        beforeSend:function() {
          $('.loading').show()
          $(this).hide()
          $('.alert-success').empty()
        },
        success:function(result) {
          $('.loading').hide()
          $(this).show()
          
          location.reload()
        },
        error:function(result) {
          console.log(result.responseText)
        }
      })
    })

  });
</script>

@endsection