@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/css/required-asterick.css')}}" rel="stylesheet">
@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Peranan</h4>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Guru >> Edit Peranan</li>
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
        <form method="post" action="{{ route('teacher.perananupdate', $teacher->uid) }}" enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="card-body">

                <div class="form-group">
                    <label class="control-label required">Nama Penuh</label>
                    <input type="text" name="name" class="form-control" placeholder="Nama Penuh" value="{{$teacher->tcname}}">
                </div>

                <div class="form-group">
                    <label class="control-label required">Nama Organisasi</label>
                    <select name="organization" id="organization" class="form-control">
                        <option value="">Pilih Organisasi</option>
                        @foreach($organization as $row)
                        @if($row->id == $teacher->organization_id)
                        <option value="{{ $row->id }}" selected> {{ $row->nama }} </option>
                        @else
                        <option value="{{ $row->id }}">{{ $row->nama }}</option>
                        @endif
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="control-label required">Email</label>
                    <input type="text" name="email" class="form-control" placeholder="Email" value="{{ $teacher->email }}">
                </div>

                <div class="form-group">
                    <label class="control-label required">No Telefon</label>
                    <input type="text" id="telno" name="telno" class="form-control" placeholder="No Telefon" max="11" value="{{$teacher->telno}}">
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
<script src="{{ URL::asset('assets/libs/jquery-mask/jquery.mask.min.js')}}"></script>

<script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script>

<script>
    $(document).ready(function() {
        $('#telno').mask('+600000000000');
    });
</script>
@endsection