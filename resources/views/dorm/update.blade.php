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
                <li class="breadcrumb-item active">Asrama >> Edit Permintaan</li>
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
        <form method="post" action="{{ route('dorm.update', $studentouting->id) }}" enctype="multipart/form-data">
            @method('PUT') 
            {{csrf_field()}}
            <div class="card-body">

                <div class="form-group">
                    <label>Nama Organisasi</label>
                    <select name="organization" id="organization" class="form-control">
                        @foreach($organization as $row)
                        @if($row->id == $studentouting->oid)
                        <option value="{{ $row->id }}" selected> {{ $row->nama }} </option>
                        @endif
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Nama Pelajar</label>
                    <input type="text" name="name" class="form-control" placeholder="Nama Penuh" value="{{$studentouting->nama}}" readonly>
                </div>

                <div class="form-group">
                    <label>Email Pelajar</label>
                    <input type="text" name="email" class="form-control" placeholder="Email" value="{{ $studentouting->email }}" readonly>
                </div>

                <div class="form-group">
                    <label>Kategori</label>
                    <select name="category" id="category" class="form-control">
                        @foreach($category as $row)  
                            @if($row->name == $studentouting->categoryname)
                            <option value="{{ $row->id }}" selected>{{ $row->name }}</option>
                            @else
                            <option value="{{ $row->id }}">{{ $row->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Alasan</label>
                    <textarea name="reason" class="form-control" placeholder="Alasan Keluar" max-length="50" cols="30"
                        rows="5">{{ $studentouting->reason }}</textarea>
                </div>

                <div class="form-group">
                    <label>Tarikh Keluar</label>
                    <input onclick="this.showPicker()" class="form-control" id="start_date" name="start_date" type="date"
                            placeholder="Pilih Tarikh Keluar" value="{{$studentouting->apply_date_time}}">
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