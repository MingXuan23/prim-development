@extends('layouts.master')

@section('css')
        <link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Kategori</h4>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active"> Tambah Kategori</li>
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
            <form method="post" action="{{ route('category.store') }}" enctype="multipart/form-data">
                {{csrf_field()}}
                <div class="card-body">
                    
                    <div class="form-group">
                        <label>Nama Kategori</label>
                        <input type="text" name="name" class="form-control" placeholder="Nama Kategori">
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
@endsection