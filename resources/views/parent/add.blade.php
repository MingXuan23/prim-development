@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />

<style>
    #name {
        text-transform: uppercase;
    }

    ::-webkit-input-placeholder {
        /* WebKit browsers */
        text-transform: none;
    }

    :-moz-placeholder {
        /* Mozilla Firefox 4 to 18 */
        text-transform: none;
    }

    ::-moz-placeholder {
        /* Mozilla Firefox 19+ */
        text-transform: none;
    }

    :-ms-input-placeholder {
        /* Internet Explorer 10+ */
        text-transform: none;
    }

    ::placeholder {
        /* Recent browsers */
        text-transform: none;
    }
</style>
@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Ibu Bapa/Penjaga</h4>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Ibu Bapa/Penjaga >> Tambah Ibu Bapa/Penjaga</li>
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
        <form method="post" action="{{ route('parent.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="card-body">

                <div class="form-group">
                    <label>Nama Penuh</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="Nama Penuh">
                </div>

                <div class="form-group">
                    <label>Nombor Kad Pengenalan</label>
                    <input type="text" id="icno" name="icno" class="form-control" placeholder="Nombor Kad Pengenalan">
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="text" id="email" name="email" class="form-control" placeholder="Email">
                </div>

                <div class="form-group">
                    <label>No Telefon</label>
                    <input type="text" id="telno" name="telno" class="form-control" placeholder="No Telefon" max="11">
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

@endsection


@section('script')
<!-- Peity chart-->
<script src="{{ URL::asset('assets/libs/peity/peity.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/chartist/chartist.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/jquery-mask/jquery.mask.min.js')}}"></script>
<script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script>
<script>
    $(document).ready(function() {
        $('#icno').mask('000000-00-0000');
        $('#telno').mask('+600000000000');

    });
</script>
@endsection