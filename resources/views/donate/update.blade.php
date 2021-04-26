@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
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
        <form method="post" action="{{ route('donate.update', $donation->id ) }}" enctype="multipart/form-data">
            @method('PATCH')
            {{csrf_field()}}
            <div class="card-body">

                <div class="form-group">
                    <label>Nama Derma</label>
                    <input type="text" name="nama" class="form-control" placeholder="Nama Derma" value="{{ $donation->nama }}">
                </div>

                <div class="form-group">
                    <label>Penerangan</label>
                    <textarea name="description" class="form-control" placeholder="Penerangan" cols="30"
                        rows="5">{{ $donation->description }}</textarea>
                </div>

                <div class="form-group">
                    <label>Tarikh Mula</label>

                    <div id="datepicker-start_date" class="input-group date" data-date-format="mm-dd-yyyy"
                    data-provide="datepicker">
                        <input class="form-control" id="start_date" name="start_date" type="date"
                        placeholder="Pilih Tarikh Mula" autocomplete="off"  value="{{ $donation->date_started }}">
                    </div>
                </div>

                <div class="form-group">
                    <label>Tarikh Berakhir</label>

                    <div id="datepicker-end_date" class="input-group date" data-date-format="mm-dd-yyyy"
                    data-provide="datepicker">
                        <input class="form-control" id="end_date" name="end_date" type="date"
                        placeholder="Pilih Tarikh Berakhir" autocomplete="off"  value="{{ $donation->date_end }}">
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
@endsection