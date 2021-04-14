@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@include('layouts.datepicker')

@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Tambah Aktiviti</h4>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Aktiviti >> Tambah Aktiviti</li>
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
        <form method="post" action="{{ route('activity.store') }}" enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="card-body">

                <div class="form-group">
                    <label>Nama Organisasi</label>
                    <select name="organization" id="organization" class="form-control">
                        <option value="" disabled selected>Pilih Organisasi</option>
                        {{-- @foreach($organization as $row)
                        <option value="{{ $row->id }}">{{ $row->nama }}</option>
                        @endforeach --}}
                    </select>
                </div>

                <div class="form-group">
                    <label>Nama Derma</label>
                    <input type="text" name="name" class="form-control" placeholder="Nama Aktiviti">
                </div>

                <div class="form-group">
                    <label>Penerangan</label>
                    <textarea name="description" class="form-control" placeholder="Penerangan" cols="30"
                        rows="5"></textarea>
                </div>

                <div class="form-group">
                    <label>Gambar</label>
                    <textarea name="description" class="form-control" placeholder="Penerangan" cols="30"
                        rows="5"></textarea>
                </div>

                <div class="form-group">
                    <label>Tarikh Mula</label>

                    <div id="datepicker-start_date" class="input-group date" data-date-format="mm-dd-yyyy"
                        data-provide="datepicker">
                        <input class="form-control" id="start_date" name="start_date" type="text"
                            placeholder="Pilih Tarikh Mula" autocomplete="off">
                        <div class="input-group-addon">
                            <i class="mdi mdi-calendar-today"></i>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Tarikh Berakhir</label>

                    <div id="datepicker-end_date" class="input-group date" data-date-format="mm-dd-yyyy"
                        data-provide="datepicker">
                        <input class="form-control" id="end_date" name="end_date" type="text"
                            placeholder="Pilih Tarikh Berakhir" autocomplete="off">
                        <div class="input-group-addon">
                            <i class="mdi mdi-calendar-today"></i>
                        </div>
                    </div>
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


@section('script')
<!-- Peity chart-->
<script src="{{ URL::asset('assets/libs/peity/peity.min.js')}}"></script>
<!-- Plugin Js-->
<script src="{{ URL::asset('assets/libs/chartist/chartist.min.js')}}"></script>
<script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script>
<script src="{{ URL::asset('assets/libs/peity/peity.min.js')}}"></script>

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
@endsection