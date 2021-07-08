@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
{{-- <p>Welcome to this beautiful admin panel.</p> --}}
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Tambah Butiran Yuran</h4>
            {{-- <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Murid >> Tambah Murid</li>
            </ol> --}}
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
        <form method="post" action="{{ route('details.store', ['id' => $getfees]) }}" enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="card-body">
                <div class="form-group">
                    <label>Nama Butiran</label>
                    <input type="text" name="name" class="form-control" placeholder="Nama Butiran">
                </div>
                <div class="form-group">
                    <label>Harga</label>
                    <input type="text" name="price" class="form-control" placeholder="Harga">
                </div>

                <div class="form-group">
                    <label>Kuantiti</label>
                    <input type="text" name="quantity" class="form-control" placeholder="Kuantiti">
                </div>

                <div class="form-group">
                    <label>Kategori</label>
                    <select name="cat" id="cat" class="form-control">
                        @foreach($cat as $row)
                        <option value="{{ $row->id }}">{{ $row->nama }}</option>
                        @endforeach
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

<script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script>
@endsection