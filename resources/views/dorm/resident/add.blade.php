@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
{{-- <p>Welcome to this beautiful admin panel.</p> --}}
<!-- use class_student -->
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Asrama</h4>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Asrama >> Tambah Pelajar</li>
            </ol>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">

            @if(count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                    <li>{{$error}}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <form method="get" action="{{ route('dorm.storeResident') }}" enctype="multipart/form-data">
                {{csrf_field()}}
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col-md-12 required">
                            <label>Nama Organisasi</label>
                            <select name="organization" id="organization" class="form-control">
                                <option value="" selected>Pilih Organisasi</option>
                                @foreach($organization as $row)
                                    <option value="{{ $row->id }}">{{ $row->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div id="dorm" class="form-group col-md-12">
                            <label> Nama Asrama</label>
                            <select name="dorm" id="dorm" class="form-control">
                                <option value="" disabled selected>Pilih Asrama</option>
                                @foreach($dormlist as $row)
                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label>Nama Penuh Murid</label>
                            <input type="text" name="name" class="form-control" placeholder="Nama Penuh Murid">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label>No. IC Murid</label>
                            <input type="text" name="icno" class="form-control" placeholder="Nombor Kad Pengenalan Murid">
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
                <!-- /.card-body -->
            </form>
        </div>
    </div>
</div>
@endsection


@section('script')
<!-- Peity chart-->
<script src="{{ URL::asset('assets/libs/peity/peity.min.js')}}"></script>

<!-- Plugin Js-->
<script src="{{ URL::asset('assets/libs/chartist/chartist.min.js')}}"></script>

<script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script>

<script>

</script>
@endsection