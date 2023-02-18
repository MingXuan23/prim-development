@extends('layouts.master')

@section('css')

<link rel="stylesheet" href="{{ URL::asset('assets/css/datatable.css')}}">
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
            <table class="table table-borderless responsive" id="cartTable" width="100%" cellspacing="0">
                <thead class="thead-dark">
                    <tr class="text-center">
                      <th style="width: 25%">Nama</th>
                      <th style="width: 15%">Kuantiti</th>
                      <th style="width: 25%">Pakej</th>
                      <th style="width: 20%">Harga Per Unit (RM)</th>
                      <th style="width: 15%">Jumlah (RM)</th>
                    </tr>
                </thead>
                
            </table>
          </div>

        </div>
      </div>

      
        <div class="card mb-4 border">
          <div class="card-body p-4">
            <div class="table-responsive">
              <table class="table table-borderless mb-0">
                  <tbody>
                    @if($response->note != null)<tr>
                      <th class="text-muted" scope="row">Nota:</th>
                      <td class="lead">{{ $response->note }}</td>
                    </tr>@endif
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
        <input type="hidden" name="order_id" id="order_id" value="{{ $cart->id }}">
        
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

    let cId = $('input#order_id').val()
    
    fetch_data(cId)

    function fetch_data(cId = '') {
        cartTable = $('#cartTable').DataTable({
            "searching": false,
            "bLengthChange": false,
            "bPaginate": false,
            "info": false,
            "orderable": false,
            "ordering": false,
            processing: true,
            serverSide: true,
            "language": {
                "zeroRecords": "Tiada Item Buat Masa Sekarang."
            },
            ajax: {
                url: "{{ route('merchant-reg.get-all-items') }}",
                data: {
                    id:cId,
                    type:'pay',
                    "_token": "{{ csrf_token() }}",
                },
                type: 'GET',
            },
            'columnDefs': [{
                "targets": [0, 1, 2, 3, 4], // your case first column
                "className": "align-middle text-center", 
            },
            { "responsivePriority": 1, "targets": 0 },
            { "responsivePriority": 2, "targets": 2 },
            { "responsivePriority": 3, "targets": 4 },
            ],
            columns: [{
                data: "name",
                name: 'name',
                orderable: false,
                searchable: false,
            }, {
                data: "quantity",
                name: 'quantity',
                orderable: false,
                searchable: false,
            }, {
                data: "full_quantity",
                name: 'full_quantity',
                orderable: false,
                searchable: false,
            },{
                data: 'price',
                name: 'price',
                orderable: false,
                searchable: false,
            },{
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
            },]
        });
      }

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