@extends('layouts.master')

@section('css')

@endsection

@section('content')

<div class="row align-items-center">
  <div class="col-sm-6">
      <div class="page-title-box">
          <h4 class="font-size-18">Koperasi</h4>
          <ol class="breadcrumb mb-0">
              <li class="breadcrumb-item active">Koperasi >> Pilih Koperasi</li>
          </ol>
      </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="card card-primary">
      <div class="card-body">

        <div class="row">
          <div class="col">
            <div class="form-group required">
              <label class="control-label">Sekolah Anak Anda</label>
              <select name="sekolah_org" id="sekolah_org" class="form-control"
                data-parsley-required-message="Sila pilih Sekolah" required>
                <option value="" disabled selected>Pilih Sekolah</option>

              </select>
            </div>
          </div>
        </div>


     <div class="row">
        @foreach($sekolah as $sekolah)

                            <div class="col-md-6 col-lg-6 col-xl-3">
        
                                <!-- Simple card -->
                                <div class="card bg-secondary">
                                    <img class="card-img-top img-fluid" src="{{$sekolah->organization_picture}}" alt="Card image cap">
                                    <div class="card-body">
                                        <h4 class="card-title">{{$sekolah->nama}}</h4>
                                        <p class="card-text">Some quick example text to build on the card title and make
                                            up the bulk of the card's content.</p>
                                        <a href="{{route('koperasi.koopShop',$sekolah->id)}}" class="btn btn-primary waves-effect waves-light">Pesan</a>
                                    </div>
                                </div>
        
                            </div><!-- end col -->
        @endforeach
     </div>
</div>

@endsection