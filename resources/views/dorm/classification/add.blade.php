@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Asrama</h4>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Asrama >> Tambah Sebab Permintaan Keluar</li>
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
        <form method="get" action="{{ route('dorm.storeReasonOuting') }}" enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="card-body">

                <div class="form-group">
                    <label>Nama Organisasi</label>
                    <select name="organization" id="organization" class="form-control">
                        <option value="" selected>Pilih Organisasi</option>
                        @foreach($organization as $row)
                        @if ($loop->first)
                        <option value="{{ $row->id }}" selected>{{ $row->nama }}</option>
                        @else
                        <option value="{{ $row->id }}">{{ $row->nama }}</option>

                        @endif
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Nama Sebab Permintaan Keluar</label>
                    <input type="text" name="reason" class="form-control" placeholder="Nama Sebab Permintaan Keluar">
                </div>

                <div class="form-group">
                    <label>Diskripsi</label>
                    <input type="text" name="description" class="form-control" placeholder="Diskripsi Sebab Permintaan">
                </div>

                <div class="form-group">
                    <label>Limit Keluar</label>
                    <input type="number" name="limit" class="form-control" placeholder="Limit Pelajar boleh Keluar untuk Sebab ini">
                </div>

                <div class="form-group mb-0">
                    <div>
                        <button type="submit" class="btn btn-primary waves-effect waves-light mr-1">
                            Simpan
                        </button>
                    </div>
                </div>
            </div>
            <!-- /.card-body -->

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