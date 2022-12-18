@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/css/required-asterick.css')}}" rel="stylesheet">
@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Warden dan Guard</h4>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Guru >> Tambah Warden dan Guard</li>
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
        <form method="get" action="{{ route('teacher.perananstore') }}" enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="card-body">

                <div class="form-group">
                    <label class="control-label required">Nama Organisasi</label>
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
                    <label class="control-label required">Nama Penuh</label>
                    <input type="text" name="name" class="form-control" placeholder="Nama Penuh">
                </div>

                <div class="form-group">
                    <label class="control-label required">Email</label>
                    <input type="text" name="email" class="form-control" placeholder="Email">
                </div>

                <div class="form-group">
                    <label class="control-label required">No Telefon</label>
                    <input type="text" id="telno" name="telno" class="form-control" placeholder="No Telefon" max="11">
                </div>

                <div class="form-group">
                    <label class="control-label required">Peranan</label>
                    <select id="peranan" name="peranan" class="form-control">
                        <option value="1">Warden</option>
                        <option value="2">Guard</option>
                    </select>
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
<script src="{{ URL::asset('assets/libs/jquery-mask/jquery.mask.min.js')}}"></script>
<script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script>

<script>
    $(document).ready(function() {
        // $('#icno').mask('000000-00-0000');
        $('#telno').mask('+600000000000');

    });
</script>

@endsection