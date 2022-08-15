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
                <li class="breadcrumb-item active">Asrama >> Edit Asrama</li>
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

        <form method="get" action="{{ route('dorm.updateDorm', $dorm->id) }}" enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="card-body">

                <div class="form-group">
                    <label>Nama Organisasi</label>
                    <select name="organization" id="organization" class="form-control">

                        <option value="">Pilih Organisasi</option>
                        @foreach($organization as $organizationRow)

                        @if($organizationRow->id == $dorm->organization_id)
                        <option value="{{ $organizationRow->id }}" selected> {{ $organizationRow->nama }} </option>
                        @else
                        <option value="{{ $organizationRow->id }}">{{ $organizationRow->nama }}</option>

                        @endif

                        @endforeach

                    </select>
                </div>

                <div class="form-group">
                    <label>Nama Asrama</label>
                    <input type="text" name="name" class="form-control" placeholder="Nama Asrama" value="{{$dorm->name}}">
                </div>

                <div class="form-group">
                    <label>Kapasiti</label>
                    <input type="number" name="capacity" class="form-control" placeholder="Kapasiti" value="{{ $dorm->accommodate_no }}">
                </div>

                <div class="form-group">
                    <label>Bilangan Pelajar Dalam</label>
                    <input type="number" name="studentno" class="form-control" disabled value="{{$dorm_student_inside}}">
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