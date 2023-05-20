@extends('layouts.master')

@section('css')

@include('layouts.datatable');

<style>
.noborder{
  border: none!important;
}

#info{
  margin-left: 0%
}
#banner-image{
  height: 400px;
  width:100%;
  object-fit:cover;
  display: flex;
  justify-content: center;
  align-items:center;
}
@media screen and (max-width:550px){
  #banner-image{
    display:none;
  }
}
</style>

@endsection

@section('content')

  <h2>Selamat datang ke Parcel Delivery System</h2>
  <img src="{{url('delivery-image/parcel-delivery.jpg')}}"  alt="..." id="banner-image" class="img-fluid">
  <hr class="ml-4 mr-4">
  
  
  <section class="row">
    <div class="col-md-8" id="info">
      <h2>  Sila masukan maklumat barang yang perlu dihantar</h2>
      <div class="row">
        <label class="col-sm-5 col-form-label ">Order Id : </label>
        <div class="col-12">
          <div class="input-group input-group-lg">
             <input type="text" class="form-control" aria-label="Item input">
          </div>
        </div>
      </div>
      <br>
      <div class="row">
        <label class="col-sm-4 col-form-label ">Alamat Pembeli :</label>
        <div class="col-12">
          <div class="input-group input-group-lg">
             <input type="text" class="form-control" aria-label="Item input">
          </div>
        </div>
      </div>
      <br>
      <div class="row">
        <label class="col-sm-3 col-form-label ">Berat Barang :</label>
        <div class="col-12 ">
        <select class="form-control form-control-lg" >
          <option value="0">Please choose the weight(kg)</option>
          <option value="1">less than 1 kg</option>
          <option value="2">between 1.1 kg to 5 kg</option>
          <option value="3">between 5.1 kg to 10 kg</option>
          <option value="4">between 10.1 kg to 50 kg</option>
        </select>
        </div>
      </div>
      <br>
      <div class="row">
        <label class="col-sm-3 col-form-label ">No Telefon Pembeli :</label>
        <div class="col-12">
          <div class="input-group input-group-lg">
             <input type="text" class="form-control" aria-label="Item input">
          </div>
        </div>
      </div>
      <br>
      <div class="row">
        <label class="col-sm-3 col-form-label ">Nota :</label>
        <div class="col-12">
          <div class="input-group input-group-lg">
             <input type="text" class="form-control" aria-label="Item input">
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4" id="price">
      <div class="table-responsive">
        <table class="table table-borderless responsive" id="priceTable" width="100%" cellspacing="0">
          <thead class="thead-dark">
            <tr class="text-center">
              <th style="width: 50%">Weigh(kg)</th>
              <th style="width: 50%">Price</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($price as $harga)
                  <tr>
                    <td>below {{$harga->weight_category}} kg</td>
                    <td>RM {{$harga->price}}</td>
                  </tr>
            @endforeach
          </tbody>
        </table>
     </div>  
    </div>
  </section>

                        
  
    

    {{-- <div class="row">
      <label class="col-sm-3 col-form-label ">Bayaran</label>
      <div class="col-sm-10">
        <p class="col col-form-label">Online-Banking</p>
      </div>
    </div> --}}
    <hr>

  <h5>Isi Pesanan</h5>
  <div class="table-responsive">
      <table class="table" id="dataTable" width="100%" cellspacing="0">
          <thead>
              <tr>
                  <th>Item</th>
                  <th>Harga Per Unit (RM)</th>
                  <th>Kuantiti</th>
                  <th>Jumlah (RM)</th>
              </tr>
          </thead>
          
          <tbody>
           
            
            <tr>
              <td class="noborder"></td>
              <td class="noborder"></td>
              <td class="table-dark noborder">Cas Organisasi</td>
              
            </tr>
            <tr>
              <td class="noborder"></td>
              <td class="noborder"></td>
              <td class="table-dark noborder">Jumlah Keseluruhan</td>
              <td class="table-dark noborder">RM  </td>
            </tr>
          </tbody>
      </table>
    </div>

    <div class="row">
      <div class="col d-flex justify-content-end">
        <a href="{{ url()->previous() }}" type="button" class="btn-lg btn-light mr-2">Kembali</a>
      </div>
    </div>

  </div>



@endsection

@section('script')

@endsection