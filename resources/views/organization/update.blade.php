@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Organisasi</h4>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Organisasi >> Edit Organisasi</li>
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
            {{-- {{ route('sekolah.store') }} --}}
            <form method="post" action="{{ route('organization.update', $org->id) }} " enctype="multipart/form-data">
                @method('PATCH')
                {{csrf_field()}}
                <div class="card-body">
                    <div class="form-group">
                        <label>Nama Organisasi</label>
                        <input type="text" name="nama" class="form-control" placeholder="Nama Organisasi" value="{{ $org->nama }}">
                    </div>
                    <div class="form-group">
                        <label>No Telefon</label>
                        <input type="text" name="telno" class="form-control" placeholder="No Telefon" value="{{ $org->telno }}">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="text" name="email" class="form-control" placeholder="Email" value="{{ $org->email }}">
                    </div>
                    <div class="row justify-content-center">
                        <div class="form-group col-md-8">
                            <label>Alamat</label>
                            <textarea name="address" class="form-control" rows="4" placeholder="Alamat"> {{ $org->address }} </textarea>
                        </div>
                        <div class="form-group col">
                            <label>Poskod</label>
                            <input type="text" name="postcode" class="form-control" placeholder="Poskod" value="{{ $org->postcode }}">

                            <label>Negeri</label>
                            <input type="text" name="state" class="form-control" placeholder="Negeri" value="{{ $org->state }}">
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
</div>
@endsection


@section('script')
<!-- Peity chart-->
<script src="{{ URL::asset('assets/libs/peity/peity.min.js')}}"></script>

<!-- Plugin Js-->
<script src="{{ URL::asset('assets/libs/chartist/chartist.min.js')}}"></script>

<script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script>
@endsection