@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">JAIM</h4>
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
                <a style="margin: 19px;" href="#" class="btn btn-primary" data-toggle="modal" data-target="#modelId"> <i
                        class="fas fa-plus"></i> Import</a>

                <a style="margin: 1px;" href=" {{ route('exportteacher') }}" class="btn btn-success"> <i
                        class="fas fa-plus"></i> Export</a>
                <a style="margin: 19px; float: right;" href="{{ route('jaim.create') }}" class="btn btn-primary"> <i
                        class="fas fa-plus"></i> Tambah Pegawai JAIM</a>
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

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <tr style="text-align:center">
                            <th>Nama Penuh</th>
                            <th>Nama Pengguna</th>
                            <th>Nombor Kad pengenalan</th>
                            <th>Email</th>
                            <th>Nombor Telefon</th>
                            <th>Status</th>
                            <th>Details</th>
                        </tr>

                        {{-- @foreach($listteacher as $row)
                        <tr>
                            <td>{{ $row->name }}</td>
                            <td>{{ $row->username }}</td>
                            <td>{{ $row->icno }}</td>
                            <td>{{ $row->email }}</td>
                            <td>{{ $row->telno }}</td>
                            @if($row->status =='1')
                            <td style="text-align: center">
                                <p class="btn btn-success m-1"> Aktif </p>
                            </td>
                            @else
                            <td style="text-align: center">
                                <p class="btn btn-danger m-1"> Tidak Aktif </p>
                            </td>
                            @endif
                            <td>
                                <div class="d-flex justify-content-center">
                                    <a href="{{ route('teacher.edit', $row->id) }}" class="btn btn-primary m-1">Edit</a>

                                    <form action="" method="POST">
                                        <input type="hidden" name="_method" value="DELETE">
                                        {{ csrf_field() }}
                                        <button class="btn btn-danger m-1"
                                            onclick="return confirm('Adakah anda pasti ?')">Buang</button>
                                    </form>
                                </div>
                            </td>

                        </tr>
                        @endforeach --}}
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