@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@include('layouts.datepicker')

@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Tambah Permintaan</h4>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Asrama >> Tambah Permintaan</li>
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
        <form method="post" action="{{ route('asrama.store') }}">
            {{csrf_field()}}
            <div class="card-body">

                 <!-- 这里是放organization的 可以从activity的add拿-->

                <div class="form-group">
                    <label>Nama Pelajar</label>
                    <input type="text" name="name" class="form-control" placeholder="Nama Pelajar">
                </div>

                <div class="form-group">
                    <label>No IC Pelajar</label>
                    <input type="text" name="ic" class="form-control" placeholder="No IC Pelajar">
                </div>

                <div class="form-group">
                    <label>Alasan</label>
                    <textarea name="reason" class="form-control" placeholder="Alasan Keluar" cols="30"
                        rows="5"></textarea>
                </div>

                <div class="form-group">
                    <label>Tarikh Keluar</label>
                    <input onclick="this.showPicker()" class="form-control" id="start_date" name="start_date" type="date"
                            placeholder="Pilih Tarikh Keluar">
                    <!-- <div id="datepicker-start_date" class="input-group date" data-date-format="mm-dd-yyyy"
                        data-provide="datepicker">
                        <input class="form-control" id="start_date" name="start_date" type="text"
                            placeholder="Pilih Tarikh Keluar" autocomplete="off">
                        <div class="input-group-addon">
                            <i class="mdi mdi-calendar-today"></i>
                        </div>
                    </div> -->
                </div>

                <div class="form-group">
                    <label>Tarikh Masuk</label>
                    <input onclick="this.showPicker()" class="form-control" id="end_date" name="end_date" type="date"
                            placeholder="Pilih Tarikh Keluar">

                    <!-- <div id="datepicker-end_date" class="input-group date" data-date-format="mm-dd-yyyy"
                        data-provide="datepicker">
                        <input class="form-control" id="end_date" name="end_date" type="text"
                            placeholder="Pilih Tarikh Masuk" autocomplete="off">
                        <div class="input-group-addon">
                            <i class="mdi mdi-calendar-today"></i>
                        </div>
                    </div> -->
                </div>


                <div class="form-group mb-0">
                    <div>
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

<!-- @section('script')
<script>
    $(document).ready(function(){

        var today = new Date();

        var start = $("#datepicker-start_date").datepicker({
            changeMonth: true,
            changeYear: true,
            startDate: today,
            todayHighlight:true,
            format: 'dd-mm-yyyy'
        });

        $("#datepicker-end_date").datepicker({
            changeMonth: true,
            changeYear: true,
            startDate: today,
            todayHighlight:true,
            format: 'dd-mm-yyyy'
        });

        // console.log($("#start_date").val());
        
    });
</script>
@endsection -->