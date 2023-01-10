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
          @elseif(Session::has('error'))
            <div class="alert alert-danger">
              <p>{{ Session::get('error') }}</p>
            </div>
          @endif

          <div class="table-responsive">
            <table class="table table-borderless" width="100%" cellspacing="0">
                <thead>
                    <tr class="text-center">
                      <th style="width: 25%">Nama</th>
                      <th style="width: 15%">Kuantiti</th>
                      <th style="width: 25%">Kuantiti Penuh</th>
                      <th style="width: 20%">Harga Satu Unit (RM)</th>
                      <th style="width: 15%">Jumlah (RM)</th>
                    </tr>
                </thead>
                
                <tbody>
                  @foreach($cart_item as $row)
                    <tr class="text-center">
                      <td class="align-middle">{{ $row->name }}</td>
                      <td class="align-middle">{{ $row->quantity }}</td>
                      <td class="align-middle">{{ $row->selling_quantity * $row->quantity }}</td>
                      <td class="align-middle">{{ $response->price[$row->id] }}</td>
                      <td class="align-middle">{{ $response->total_price[$row->id] }}</td>
                    </tr>
                  @endforeach
                </tbody>
            </table>
          </div>

        </div>
      </div>

      
        <div class="card mb-4 border">
          <div class="card-body p-4">
            <div class="table-responsive">
              <table class="table table-borderless mb-0">
                  <tbody>
                    <tr>
                      <th class="text-muted" scope="row">Tarikh Pengambilan:</th>
                      <td class="lead">{{ $response->pickup_date }}</td>
                    </tr>
                    <tr>
                      <th class="text-muted" scope="row">Jumlah Keseluruhan:</th>
                      <td class="lead">RM {{ $response->amount }}</td>
                    </tr>
                  </tbody> 
              </table>
            </div>
          </div>
        </div>
        
      <form action="{{ route('fpxIndex') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="card mb-4 border">
          <div class="card-body p-4">
            <div class="row">
              <div class="col">
                <div class="form-group">
                  <label>Pilih Bank</label>
                  <select name="bankid" id="bankid" class="form-control"
                      data-parsley-required-message="Sila pilih bank" required>
                      <option value="">Pilih bank</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        {{-- <input type="hidden" name="amount" id="total_price" value="{{ $cart->total_price }}"> --}}
        <input type="hidden" name="amount" id="total_price" value="2.00">
        <input type="hidden" name="desc" id="desc" value="Merchant">
        <input type="hidden" name="order_id" value="{{ $cart->id }}">
        
        <div class="row mb-2">
          <div class="col d-flex justify-content-end">
            <a href="{{ url()->previous() }}" type="button" class="btn-lg btn-light mr-2">Kembali</a>
            <button type="submit" class="btn-lg btn-primary">Bayar</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>


@endsection

@section('script')

<script>
  $(document).ready(function(){

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    var arr = [];
    
    $.ajax({
        type: 'GET',
        dataType: 'json',
        url: "/fpx/getBankList",
        success: function(data) {
            jQuery.each(data.data, function(key, value){
                arr.push(key);
            });
            for(var i = 0; i < arr.length; i++){
                arr.sort();
                $("#bankid").append("<option value='"+data.data[arr[i]].code+"'>"+data.data[arr[i]].nama+"</option>");
            }

        },
        error: function (data) {
            // console.log(data);
        }
    });

    $('.alert-success').delay(2000).fadeOut()
    $('.alert-danger').delay(4000).fadeOut()

  });
</script>

@endsection