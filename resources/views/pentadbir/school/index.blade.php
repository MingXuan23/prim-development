@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Sekolah</h4>
            <!-- <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Welcome to Veltrix Dashboard</li>
            </ol> -->
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            {{-- <div class="card-header">List Of Applications</div> --}}
            <div>
                {{-- route('sekolah.create')  --}}
                <a style="margin: 19px; float: right;" href="{{ route('school.create') }}" class="btn btn-primary"> <i class="fas fa-plus"></i> Tambah Sekolah</a>
            </div>

            <div class="card-body">

                @if(count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                        <li>{{$error}}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                @if(\Session::has('success'))
                <div class="alert alert-success">
                    <p>{{ \Session::get('success') }}</p>
                </div>
                @endif

                {{-- <div align="right">
                            <a href="{{route('admin.create')}}" class="btn btn-primary">Add</a>
                <br />
                <br />
            </div> --}}
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <tr style="text-align:center">
                        <th>Nama Sekolah</th>
                        <th>Kod</th>
                        <th>No Telefon</th>
                        <th>Email</th>
                        <th>Alamat</th>
                        <th>Action</th>
                    </tr>

                    @foreach($school as $row)
                    <tr>
                        <td>{{$row['nama']}}</td>
                        <td>{{$row['code']}}</td>
                        <td>{{$row['telno']}}</td>
                        <td>{{$row['email']}}</td>
                        <td>{{$row['address']}}</td>
                        <td>
                            <div class="d-flex justify-content-center">
                                <a href="{{ route('school.edit', $row['id']) }}" class="btn btn-primary m-1">Edit</a>
                            </div>
                        </td>

                    </tr>
                    @endforeach
                </table>
            </div>
        </div>
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