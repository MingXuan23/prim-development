@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@include('layouts.datatable')

@endsection

@section('content')
<!-- 我想做login 然后去分辨学生还是老师 但是目前没有思绪 所以先用这个代替着-->
<!-- 目前是直接set student_id 和 teacher_id = 1-->
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Asrama</h4>
        </div>
    </div>
</div>

<!-- input -->
<!-- <form action="" method="get">
    @csrf
    <input type="text" name="role" id="role">
    <button class="btn btn-secondary" type="submit">Enter</button>
</form> -->
<form name="form" action="" method="get">
    <input type="text" name="subject" id="subject">
    <button type="submit">Enter</button>
</form>

<?php
if (isset($_GET['subject'])) {
    $role = $_GET['subject'];
} else {
    $role = NULL;
}
?>
@if($role != NULL)
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
            <div class="card-body">
                <div class="form-group">
                    <label>Nama Organisasi</label>
                    <select name="organization" id="organization" class="form-control">
                        <option value="" selected>Semua Organisasi</option>
                        @foreach($organization as $row)
                        <option value="{{ $row->id }}">{{ $row->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card">
            <div>
                <!-- {{-- route('sekolah.create')  --}} -->
                @if(str_contains($role, 's'))
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
                    <table id="activityTable" class="table table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr style="text-align:center">
                                <th> No. </th>
                                <th> Nama Pelajar </th>
                                <th> No IC Pelajar </th>
                                <th> Alasan </th>
                                <th> Dibenarkan Keluar </th>
                                <th> Dibenarkan Masuk </th>
                                @if(str_contains($role, 'student'))
                                <th> Status </th>
                                @endif
                                <th> Tarikh Keluar </th>
                                <th> Tarikh Sampai </th>
                                <th> Tarikh Masuk </th>
                                <th> Tarikh Sampai </th>
                                <th> Action </th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($asrama as $asrama)
                            <!-- 如果是学生 -->
                            @if(str_contains($role, 'student'))
                            <tr>
                                <td>{{$asrama->id}}</td>
                                <td>{{$asrama->nama}}</td>
                                <td>{{$asrama->icno}}</td>
                                <td>{{$asrama->reason}}</td>
                                <td>{{$asrama->start_date}}</td>
                                <td>{{$asrama->end_date}}</td>
                                @if($asrama->status == '0')
                                <td>Pending</td>
                                <td>{{$asrama->outing_time}}</td>
                                <td>{{$asrama->out_arrive_time}}</td>
                                <td>{{$asrama->in_time}}</td>
                                <td>{{$asrama->in_arrive_time}}</td>
                                <td>
                                    <form action="{{route('asrama.destroy', $asrama->id)}}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-secondary" type="submit">Delete</button>
                                    </form>
                                </td>
                                @elseif($asrama->status == '1')
                                <td>Approved</td>
                                <td>{{$asrama->outing_time}}</td>
                                <td>{{$asrama->out_arrive_time}}</td>
                                <td>{{$asrama->in_time}}</td>
                                <td>{{$asrama->in_arrive_time}}</td>
                                <td>
                                    @if($asrama->out_arrive_time == NULL)
                                    <form action="{{route('asrama.updateArriveTime', $asrama->id)}}" method="get">
                                        @csrf
                                        <button id="arrive_home" onclick="Arrive()" class="btn btn-primary" type="submit">
                                            Arrive
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            <!-- guard -->
                            @elseif(str_contains($role, 'guard'))
                            @if($asrama->status == '1')
                            <tr>
                                <td>{{$asrama->id}}</td>
                                <td>{{$asrama->nama}}</td>
                                <td>{{$asrama->icno}}</td>
                                <td>{{$asrama->reason}}</td>
                                <td>{{$asrama->start_date}}</td>
                                <td>{{$asrama->end_date}}</td>
                                <td>{{$asrama->outing_time}}</td>
                                <td>{{$asrama->out_arrive_time}}</td>
                                <td>{{$asrama->in_time}}</td>
                                <td>{{$asrama->in_arrive_time}}</td>
                                <td>
                                    @if($asrama->outing_time == NULL)
                                    <form action="{{route('asrama.updateOutTime', $asrama->id)}}" method="get">
                                        @csrf
                                        <button class="btn btn-primary" type="submit">Leave</button>
                                    </form>
                                    @elseif($asrama->in_time == NULL && $asrama->outing_time != NULL)
                                    <form action="{{route('asrama.updateInTime', $asrama->id)}}" method="get">
                                        @csrf
                                        <button class="btn btn-primary" type="submit">In</button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @endif
                            <!-- teacher -->
                            @else
                            @if($asrama->status == '0')
                            <tr>

                                <td>{{$asrama->id}}</td>
                                <td>{{$asrama->nama}}</td>
                                <td>{{$asrama->icno}}</td>
                                <td>{{$asrama->reason}}</td>
                                <td>{{$asrama->start_date}}</td>
                                <td>{{$asrama->end_date}}</td>
                                <td>{{$asrama->outing_time}}</td>
                                <td>{{$asrama->out_arrive_time}}</td>
                                <td>{{$asrama->in_time}}</td>
                                <td>{{$asrama->in_arrive_time}}</td>
                                <td>
                                    <form action="{{route('asrama.edit', $asrama->id)}}" method="get">
                                        @csrf
                                        <button class="btn btn-primary" type="submit">Approve</button>
                                    </form>
                                    <br />
                                    <form action="{{route('asrama.destroy', $asrama->id)}}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-secondary" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endif
                            @endif
                            </tr>
                            <!-- guard -->
                            @elseif(str_contains($role, 'guard'))
                            @if($asrama->status == '1')
                            <tr>
                                <td>{{$asrama->id}}</td>
                                <td>{{$asrama->nama}}</td>
                                <td>{{$asrama->icno}}</td>
                                <td>{{$asrama->reason}}</td>
                                <td>{{$asrama->start_date}}</td>
                                <td>{{$asrama->end_date}}</td>
                                <td>{{$asrama->outing_time}}</td>
                                <td>{{$asrama->out_arrive_time}}</td>
                                <td>{{$asrama->in_time}}</td>
                                <td>{{$asrama->in_arrive_time}}</td>
                                <td>
                                    <form action="{{route('asrama.updateOutTime', $asrama->id)}}" method="get">
                                        @csrf
                                        <button class="btn btn-primary" type="submit">Leave</button>
                                    </form>
                                </td>
                            </tr>
                            @endif
                            <!-- teacher -->
                            @else
                            @if($asrama->status == '0')
                            <tr>

                                <td>{{$asrama->id}}</td>
                                <td>{{$asrama->nama}}</td>
                                <td>{{$asrama->icno}}</td>
                                <td>{{$asrama->reason}}</td>
                                <td>{{$asrama->start_date}}</td>
                                <td>{{$asrama->end_date}}</td>
                                <td>{{$asrama->outing_time}}</td>
                                <td>{{$asrama->out_arrive_time}}</td>
                                <td>{{$asrama->in_time}}</td>
                                <td>{{$asrama->in_arrive_time}}</td>
                                <td>
                                    <form action="{{route('asrama.edit', $asrama->id)}}" method="get">
                                        @csrf
                                        <button class="btn btn-primary" type="submit">Approve</button>
                                    </form>
                                </td>
                                <td>
                                    <form action="{{route('asrama.destroy', $asrama->id)}}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-secondary" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endif
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
@section('script')
<script>
    function Arrive() {
        var arrive = document.getElementById('arrive_home');
        arrive.innerHTML = "Arrived";
        arrive.disabled = true;
    }
</script>
@endsection