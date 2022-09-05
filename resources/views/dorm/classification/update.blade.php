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
                <li class="breadcrumb-item active">Asrama >> Update Sebab Permintaan Keluar</li>
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

        <form method="get" action="{{ route('dorm.updateReasonOuting', $id) }}" enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="card-body">

                <div class="form-group">
                    <label>Nama Organisasi</label>
                    <select name="organization" id="organization" class="form-control">

                        <option value="">Pilih Organisasi</option>
                        @foreach($organization as $organizationRow)

                        @if($organizationRow->id == $reason->organization_id)
                        <option value="{{ $organizationRow->id }}" selected> {{ $organizationRow->nama }} </option>
                        @else
                        <option value="{{ $organizationRow->id }}">{{ $organizationRow->nama }}</option>

                        @endif

                        @endforeach

                    </select>
                </div>

                <div class="form-group">
                    <label>Nama Sebab Permintaan Keluar</label>
                    <input type="text" name="name" class="form-control" placeholder="Nama Sebab Permintaan Keluar" value="{{$reason->name}}">
                </div>

                <div class="form-group">
                    <label>Diskripsi</label>
                    <input type="text" name="description" class="form-control" placeholder="Diskripsi" value="{{ $reason->description }}">
                </div>

                <div class="form-group">
                    <label>Limit Permintaan Keluar</label>
                    <input type="number" name="limit" class="form-control" placeholder="Limit Pelajar boleh Keluar untuk Sebab ini" value="{{$reason->limit}}">
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