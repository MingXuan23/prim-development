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
            <h4 class="font-size-18">Kemaskini Derma</h4>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Derma >> Kemaskini Derma</li>
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
        <form class="form-validation" method="post" action="{{ route('donation.update', $donation->id) }}" enctype="multipart/form-data" name="donation">
            {{csrf_field()}}
            {{ method_field('PATCH') }}
            <div class="card-body">
                <p class="card-title-desc">Bahagian bertanda * wajib diisi</p>
                
                <div class="form-group ">
                    <label class="control-label">Nama Derma</label>
                    <input type="text" name="nama" class="form-control" placeholder="Nama Penuh" 
                    data-parsley-required-message="Sila masukkan nama derma" value="{{ $donation->nama }}" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group col-md-6 required">
                        <label class="control-label">Tarikh Awal</label>
                        <div>
                            <div class="input-group">
                                <input type="text" name="date_started" class="form-control" placeholder="mm/dd/yyyy" id="start_date" 
                                placeholder="Tarikh Awal" autocomplete="off" data-parsley-required-message="Sila masukkan tarikh awal"
                                data-parsley-errors-container=".errorMessage" value="{{ \Carbon\Carbon::parse($donation->date_started)->format('d/m/Y') }}" required>
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                                </div>
                            <div class="errorMessage"></div>
                            </div><!-- input-group -->
                        </div>
                    </div>

                    <div class="form-group col-md-6 required">
                        <label class="control-label">Tarikh Awal</label>
                        <div>
                            <div class="input-group">
                                <input type="text" name="date_end" class="form-control" placeholder="mm/dd/yyyy" id="end_date" 
                                placeholder="Tarikh Awal" autocomplete="off" data-parsley-required-message="Sila masukkan tarikh awal"
                                data-parsley-errors-container=".errorMessage" value="{{ \Carbon\Carbon::parse($donation->date_end)->format('d/m/Y') }}" required>
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                                </div>
                            <div class="errorMessage"></div>
                            </div><!-- input-group -->
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Pembayar Cukai</label>
                        <input type="text" name="tax_payer" value="{{ $donation->tax_payer }}" class="form-control" placeholder="Masukkan Pembayar Cukai">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Jumlah Cukai (RM)</label>
                        <input class="form-control input-mask text-left"
                            data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'"
                            im-insert="true" style="text-align: right;" value="{{ $donation->total_tax }}" name="total_tax">
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
                        rows="5">{{ $donation->description }}</textarea>
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

        $('#start_date').datepicker({
            toggleActive: true,
            startDate: today,
            todayHighlight:true,
            format: 'dd/mm/yyyy',
            orientation: "bottom" 
        });

        $('#end_date').datepicker({
            toggleActive: true,
            startDate: today,
            todayHighlight:true,
            format: 'dd/mm/yyyy',
            orientation: "bottom" 
        });
        
    });
</script>
@endsection