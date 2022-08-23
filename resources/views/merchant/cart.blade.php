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
                  {{-- @if(count($cart_item) != 0 || $cart)
                  @foreach($cart_item as $row) --}}
                    
                    <tr class="text-center">
                      <td>
                          <img src="{{ URL('images/koperasi/default-item.png')}}" class="rounded img-fluid" id="img-size" style="background-color: rgb(61, 61, 61)">
                          {{-- <img src="{{ URL('koperasi-item/'.$row->image)}}" class="rounded img-fluid" id="img-size"> --}}
                      </td>
                      <td class="align-middle"></td>
                      <td class="align-middle"></td>
                      <td class="align-middle"></td>
                      <td class="align-middle">
                        <form action="#" method="POST">
                          @csrf
                          @method('delete')
                          <button type="submit" class="btn btn-danger">Buang</button>
                        </form>
                      </td>
                    </tr>
                  
                    <tr>
                      <td colspan="5" class="text-center"><i>Tiada Item Buat Masa Sekarang.</i></td>
                    </tr>
                  
                </tbody>
            </table>
          </div>

        </div>
      </div>

      
      <form action="#" method="POST">
        @csrf
        @method('PUT')
        <div class="card mb-4 border">
          <div class="card-body p-4">

            <div class="table-responsive">
              <table class="table table-borderless mb-0">
                
                  <tbody>
                      <tr>
                          <th class="text-muted" scope="row">Jumlah Keseluruhan:</th>
                          
                          <td class="lead">RM </td>
                          
                          <td class="lead">RM 0.00</td>
                          
                      </tr>
                  </tbody>
                
              </table>
          </div>

          </div>
        </div>

        
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
        
        <input type="hidden" name="week_status" id="week_status" value="">

        <div class="row mb-2">
          <div class="col d-flex justify-content-end">
            <a href="#" type="button" class="btn btn-light mr-2">Kembali</a>
              <button type="submit" class="btn btn-primary">Bayar</button>
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
  });
</script>

@endsection