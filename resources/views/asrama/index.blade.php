@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@include('layouts.datatable')

@endsection

@section('content')
<!-- 我想做login 然后去分辨学生还是老师 但是目前没有思绪 所以先用这个代替着-->
<!-- 目前是直接set student_id 和 teacher_id = 1-->
<!-- 目前action用不到了 -->
<?php
    $role = "t";
?>
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Asrama</h4>
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

            {{csrf_field()}}
            <!-- 这里是放organization的 可以从activity的index拿-->

        </div>
    </div>

    <div class="col-md-12">
        <div class="card">
            <div>
                <!-- {{-- route('sekolah.create')  --}} -->
                @if($role=="s")
                <a style="margin: 19px; float: right;" href="{{ route('asrama.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Permintaan</a>
                @endif
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
                    <table id="activityTable" class="table table-bordered table-striped dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <!-- 如果是学生 -->
                        @foreach($asrama as $asrama)
                        @if($role == "s")
                        <thead>
                            <tr style="text-align:center">
                                <th> No. </th>
                                <th> Nama Pelajar </th>
                                <th> No IC Pelajar </th>
                                <th> Alasan </th>
                                <th> Tarikh Keluar </th>
                                <th> Tarikh Masuk </th>
                                <th> Status </th>
                                <th> Action </th>
                            </tr>
                        </thead>

                        <tbody>
                            @if($asrama->status == '0')
                            <tr>
                                <td>{{$asrama->id}}</td>
                                <td>{{$asrama->nama}}</td>
                                <td>{{$asrama->icno}}</td>
                                <td>{{$asrama->reason}}</td>
                                <td>{{$asrama->start_date}}</td>
                                <td>{{$asrama->end_date}}</td>
                                <td>Pending</td>
                                <td>
                                    <form action="{{route('asrama.destroy', $asrama->id)}}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit">Decline</button>
                                    </form>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                        <!-- 其他 -->
                        @else
                        <thead>
                            <tr style="text-align:center">
                                <th> No. </th>
                                <th> Nama Pelajar </th>
                                <th> No IC Pelajar </th>
                                <th> Alasan </th>
                                <th> Tarikh Keluar </th>
                                <th> Tarikh Masuk </th>
                                <th colspan="2"> Action </th>
                            </tr>
                        </thead>

                        <tbody>
                            @if($asrama->status == '0')
                            <tr>
                                <td>{{$asrama->id}}</td>
                                <td>{{$asrama->nama}}</td>
                                <td>{{$asrama->icno}}</td>
                                <td>{{$asrama->reason}}</td>
                                <td>{{$asrama->start_date}}</td>
                                <td>{{$asrama->end_date}}</td>
                                <td><form action="{{route('asrama.edit', $asrama->id)}}" method="get">
                                        @csrf
                                        <button type="submit">Approve</button>
                                    </form>
                                </td>
                                <td>
                                    <form action="{{route('asrama.destroy', $asrama->id)}}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit">Decline</button>
                                    </form>
                                </td>
                            </tr>
                            @endif
                            
                        </tbody>
                        @endif
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection