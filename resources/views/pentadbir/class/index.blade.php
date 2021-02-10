@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Kelas</h4>
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
                <a style="margin: 19px;" href="#" class="btn btn-primary" data-toggle="modal" data-target="#modelId"> <i class="fas fa-plus"></i> Import</a>
                <a style="margin: 1px;" href="{{ route('exportclass') }}" class="btn btn-success"> <i class="fas fa-plus"></i> Export</a>
                {{-- href="{{ route('kelas.create') }}" {{ route('exportkelas') }}--}}
                <a style="margin: 19px; float: right;" href="{{ route('class.create') }}" class="btn btn-primary"> <i class="fas fa-plus"></i> Tambah Kelas</a>
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
                        <th>Bil.</th>
                        <th>Nama Kelas</th>
                        <th>Bilangan Pelajar</th>
                        <th>Tahap</th>
                        <th>Details</th>
                    </tr>

                    @foreach($listclass as $row)
                    <tr>
                        <td style="text-align:center">{{$loop->iteration}}.</td>
                        <td>{{$row->nama}}</td>
                        <td>{{$row->levelid}}</td>
                        <td>{{$row->levelid}}</td>
                        <td>
                            <div class="d-flex justify-content-center">
                                {{-- {{ route('kelas.edit', $row['kelasID']) }} --}}
                                <a href="{{ route('class.edit', $row->id) }}" class="btn btn-primary m-1">Edit</a>
                                {{-- {{ route('kelas.destroy', $row['kelasID']) }} --}}
                                <form action="" method="POST">
                                    <input type="hidden" name="_method" value="DELETE">
                                    {{ csrf_field() }}
                                    <button class="btn btn-danger m-1" onclick="return confirm('Adakah anda pasti ?')">Delete Class</button>
                                </form>
                            </div>
                        </td>

                    </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Kelas</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                {{-- {{ route('importkelas')}} --}}
                <form action="{{ route('importclass') }}" method="post" enctype="multipart/form-data">
                    <div class="modal-body">

                        {{ csrf_field() }}
                        {{-- <div class="form-group">
                                            <label>Nama Kelas</label>
                                            <select name="tahap" id="tahap" class="form-control">
                                                <option value="1">Tahap 1</option>
                                            </select>
                                        </div> --}}
                        <div class="form-group">
                            <input type="file" name="file" required>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Import</button>
                        </div>
                    </div>

                </form>
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