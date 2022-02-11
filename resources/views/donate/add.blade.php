@extends('layouts.master')

@section('css')
    <link href="{{ URL::asset('assets/libs/dropzone/dropzone.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/css/required-asterick.css')}}" rel="stylesheet">
    <link href="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Tambah Derma</h4>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Derma >> Tambah Derma</li>
            </ol>
        </div>
    </div>
</div>
<div class="row">
    <div class="card col-md-12">
        @if(count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                <li>{{$error}}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <form class="form-validation" method="post" action="{{ route('donation.store') }}" enctype="multipart/form-data" name="donation">
            {{csrf_field()}}
            <div class="card-body">
                <p class="card-title-desc">Bahagian bertanda * wajib diisi</p>
                <div class="form-group">
                    <label>Nama Organisasi</label>
                    <label class="control-label">Nama Organisasi</label>
                        <select name="organization" id="organization" class="form-control"
                        data-parsley-required-message="Sila masukkan nama organisasi" required>
                            <option value="" disabled selected>Pilih Organisasi</option>
                            @foreach($organization as $row)
                            <option value="{{ $row->id }}">{{ $row->nama }}</option>
                            @endforeach
                        </select>  
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6 required">
                        <label class="control-label">Nama Derma</label>
                        <input type="text" name="nama" class="form-control" placeholder="Nama Penuh" 
                        data-parsley-required-message="Sila masukkan nama derma" value="" required>
                    </div>
                    <div class="form-group required col-md-6">
                        <label class="control-label">Jenis Derma</label>
                        <select name="donation_type" id="donation_type" class="form-control"
                        data-parsley-required-message="Sila masukkan nama organisasi" required>
                            <option value="" disabled selected>Pilih Jenis Derma</option>
                            @foreach($donation_type as $row)
                            <option value="{{ $row->id }}">{{ $row->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group col-md-12 required">
                    <label class="control-label">Tempoh Sah Derma</label>
                    
                        <div class="input-daterange input-group" id="date">
                            <input type="text" class="form-control" name="date_started" placeholder="Tarikh Awal" autocomplete="off" 
                            data-parsley-required-message="Sila masukkan tarikh awal"
                            data-parsley-errors-container=".errorMessage" required/>
                            <input type="text" class="form-control" name="date_end" placeholder="Tarikh Akhir" autocomplete="off"
                            data-parsley-required-message="Sila masukkan tarikh akhir"
                            data-parsley-errors-container=".errorMessage" required/>
                        </div>
                        <div class="errorMessage"></div>
                        <div class="errorMessage"></div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Pembayar Cukai</label>
                        <input type="text" name="tax_payer" class="form-control" placeholder="Masukkan Pembayar Cukai">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Jumlah Cukai (RM)</label>
                        <input class="form-control input-mask text-left"
                            data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'"
                            im-insert="true" style="text-align: right;" name="total_tax">
                    </div>
                </div>

                <div class="form-group">
                    <label>Poster Derma</label>
                    <form action="#" class="dropzone">
                        <div class="fallback">
                            <input name="donation_poster" type="file">
                        </div>
                    </form>
                </div>
                
                <div class="form-group">
                    <label>Penerangan</label>
                    <textarea name="description" class="form-control" placeholder="Penerangan" cols="30"
                        rows="5"></textarea>
                </div>

                <div class="form-group mb-0">
                    <div class="text-right">
                        <button type="submit" class="btn btn-primary waves-effect waves-light mr-1">
                            Simpan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection


@section('script')
<!-- Plugin Js-->
<script src="{{ URL::asset('assets/libs/inputmask/inputmask.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/dropzone/dropzone.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/parsleyjs/parsleyjs.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}" defer></script>

<script>
    $(document).ready(function(){

        $('.form-validation').parsley();
        $(".input-mask").inputmask();

        var today = new Date();

        $('#date').datepicker({
            toggleActive: true,
            startDate: today,
            todayHighlight:true,
            format: 'dd/mm/yyyy',
            orientation: 'bottom'
        });
        
    });
</script>
@endsection