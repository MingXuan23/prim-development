@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Sekolah</h4>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Sekolah >> Edit Sekolah</li>
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
            {{-- {{ route('sekolah.update', $sekolah->organID) }} --}}
            <form method="post" action="{{ route('school.update', $school->id) }}" enctype="multipart/form-data">
                @method('PATCH')
                {{csrf_field()}}
                <div class="card-body">
                    <div class="form-group">
                        <label>Nama Sekolah</label>
                        <input type="text" name="name" class="form-control" placeholder="Nama Sekolah" value="{{$school->nama}}">
                    </div>
                    <div class="form-group">
                        <label>Kod Sekolah</label>
                        <input type="text" name="code" class="form-control" placeholder="Kod Sekolah" value="{{$school->code}}">
                    </div>
                    <div class="form-group">
                        <label>No Telefon</label>
                        <input type="text" name="telno" class="form-control" placeholder="No Telefon" value="{{$school->telno}}">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="text" name="email" class="form-control" placeholder="Email" value="{{$school->email}}">
                    </div>
                    <div class="row justify-content-center">
                        <div class="form-group col-md-8">
                            <label>Alamat</label>
                            <textarea name="address" class="form-control" rows="4" placeholder="Alamat">{{$school->address}}</textarea>
                        </div>
                        <div class="form-group col">
                            <label>Poskod</label>
                            <input type="text" name="postcode" class="form-control" placeholder="Poskod" value="{{$school->postcode}}">

                            <label>Negeri</label>
                            <input type="text" name="state" class="form-control" placeholder="Negeri" value="{{$school->state}}">
                        </div>

                    </div>

                    {{-- <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="exampleCheck1">
                        <label class="form-check-label" for="exampleCheck1">Check me out</label>
                        </div> --}}

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
@endsection