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

</style>

@endsection

@section('content')

<div class="row align-items-center">
  <div class="col-sm-6">
      <div class="page-title-box">
          <h4 class="font-size-18">Koperasi</h4>
          <ol class="breadcrumb mb-0">
              <li class="breadcrumb-item active">Koperasi >> Pesanan Anda</li>
          </ol>
      </div>
  </div>
</div>

<div class="container">
  <div class="row d-flex justify-content-center align-items-center">
    <div class="col">
      <div class="d-flex justify-content-center align-items-center">
        <span class="h2 mb-4">Senarai Pesanan</span>
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
                      <th style="width: 20%">Gambar</th>
                      <th style="width: 30%">Nama Item</th>
                      <th style="width: 20%">Kuantiti</th>
                      <th style="width: 30%">Harga Satu Unit (RM)</th>
                      <th style="width: 10%">Action</th>
                    </tr>
                </thead>
                
                <tbody>
                  @if(count($cart_item) != 0 || $cart)
                  @foreach($cart_item as $row)
                    
                    <tr class="text-center">
                      <td>
                          @if($row->image == null)
                          <img src="{{ URL('images/koperasi/default-item.png')}}" class="rounded img-fluid" id="img-size" style="background-color: rgb(61, 61, 61)">
                          @else
                          <img src="{{ URL('koperasi-item/'.$row->image)}}" class="rounded img-fluid" id="img-size">
                          @endif
                      </td>
                      <td class="align-middle">{{ $row->name }}</td>
                      <td class="align-middle">{{ $row->quantity }}</td>
                      <td class="align-middle">{{ number_format($row->price, 2, '.', '') }}</td>
                      <td class="align-middle">
                        <form action="{{ route('koperasi.destroyItemCart', ['org_id' => $id, 'id' => $row->id]) }}" method="POST">
                          @csrf
                          @method('delete')
                          <button type="submit" class="btn btn-danger">Buang</button>
                        </form>
                      </td>
                    </tr>
                  @endforeach
                  @else
                    <tr>
                      <td colspan="5" class="text-center"><i>Tiada Item Buat Masa Sekarang.</i></td>
                    </tr>
                  @endif
                </tbody>
            </table>
          </div>

        </div>
      </div>

      @if(count($cart_item) != 0 || $cart)
      <form action="{{ route('koperasi.update', $cart->id) }}" method="POST">
      @else
      @endif
        @csrf
        @method('PUT')
        <div class="card mb-4 border">
          <div class="card-body p-4">

            <div class="table-responsive">
              <table class="table table-borderless mb-0">
                
                  <tbody>
                      <tr>
                          <th class="text-muted" scope="row">Jumlah Keseluruhan:</th>
                          @if($cart)
                          <td class="lead">RM {{ number_format($cart->total_price, 2, '.', '') }}</td>
                          @else
                          <td class="lead">RM 0.00</td>
                          @endif
                      </tr>
                  </tbody>
                
              </table>
          </div>

          </div>
        </div>

        @if($cart)
        <div class="card mb-4 border">
          <div class="card-body p-4">
            <div class="row">
              <div class="col">
                <div class="form-group required">
                  {{-- <label for="example-date-input" class="col-sm-2">Tarikh Pengambilan</label>
                  <div class="col">
                      <input class="form-control" type="date" value="{{ $tomorrowDate}}" min="{{ $tomorrowDate }}" id="date_pick_up">
                  </div> --}}
                  <label class="col-sm-2">Hari Pengambilan</label>
                    <div class="col">
                        <select class="form-control" data-parsley-required-message="Sila pilih hari" id="pick_up_date" required>
                          <option value="" selected>Pilih Hari</option>
                          @foreach($allDay as $row)
                            @if($row->day == 1)
                              <option value="{{ $row->day }}">Isnin {{ $isPast[strval($row->day)] }}</option>
                            @elseif($row->day == 2)
                              <option value="{{ $row->day }}">Selasa {{ $isPast[strval($row->day)] }}</option>
                            @elseif($row->day == 3)
                              <option value="{{ $row->day }}">Rabu {{ $isPast[strval($row->day)] }}</option>
                            @elseif($row->day == 4)
                              <option value="{{ $row->day }}">Khamis {{ $isPast[strval($row->day)] }}</option>
                            @elseif($row->day == 5)
                              <option value="{{ $row->day }}">Jumaat {{ $isPast[strval($row->day)] }}</option>
                            @elseif($row->day == 6)
                              <option value="{{ $row->day }}">Sabtu {{ $isPast[strval($row->day)] }}</option>
                            @elseif($row->day == 0)
                              <option value="{{ $row->day }}">Ahad {{ $isPast[strval($row->day)] }}</option>
                            @endif
                          @endforeach
                        </select>
                        
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>

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
        
        <input type="hidden" name="week_status" id="week_status" value="">

        <div class="row mb-2">
          <div class="col d-flex justify-content-end">
            <a href="{{ route('koperasi.koopShop', $id) }}" type="button" class="btn btn-light mr-2">Kembali</a>
            @if($cart)
              <button type="submit" class="btn btn-primary">Bayar</button>
            @endif
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
    $('.alert').delay(2000).fadeOut();

    $('#pick_up_date').on('change', function() {
        $('#week_status').val(this.value);
        // console.log($('#week_status').val());
    });
  });
</script>

@endsection