@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/css/required-asterick.css')}}" rel="stylesheet">
@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Asrama</h4>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Asrama >> Update Sebab Permintaan Keluar</li>
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

        <form method="get" action="{{ route('dorm.updateReasonOuting', $id) }}" enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="card-body">

                <div class="form-group">
                    <label class="control-label required">Nama Organisasi</label>
                    <select name="organization" id="organization" class="form-control">

                        <option value="">Pilih Organisasi</option>
                        @foreach($organization as $organizationRow)

                        @if($organizationRow->id == $reason->organization_id)
                        <option value="{{ $organizationRow->id }}" selected> {{ $organizationRow->nama }} </option>
                        @else
                        <option value="{{ $organizationRow->id }}">{{ $organizationRow->nama }}</option>

                        @endif

                        @endforeach

                    </select>
                </div>



                <div class="form-group">
                    <label class="control-label required">Kategori Sebab Permintaan Keluar</label>
                    <select id="optionReason" class="form-control" name="reason">
                        @foreach($reasonlist as $list)
                        @if($list->id == $reason->id)
                        <option value="{{$list->name}}" selected>{{$list->name}}</option>
                        @else
                        <option value="{{$list->name}}">{{$list->name}}</option>
                        @endif
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="control-label required">Nama Sebab Permintaan Keluar</label>
                    <input type="text" name="name" class="form-control" placeholder="Nama Sebab Permintaan Keluar" value="{{$reason->fake_name}}">
                </div>

                <div class="form-group">
                    <label>Diskripsi</label>
                    <input type="text" name="description" class="form-control" placeholder="Diskripsi" value="{{ $reason->description }}">
                </div>

                <div class="form-group">
                    <label class="control-label required">Limit Permintaan Keluar</label>
                    <input type="number" name="limit" class="form-control" placeholder="Limit Pelajar boleh Keluar untuk Sebab ini" value="{{$reason->limit}}">
                </div>

                <div class="form-group">
                    <label class="control-label required">Hari Sebelum Permintaan Keluar</label>
                    <input type="number" name="day" class="form-control" placeholder="Hanya Membenarkan Penjaga Memohon Beberapa Hari Sebelum Hari ingin Keluar. Letak 0 Jika Boleh Mohon Pada Hari yang Sama. " value="{{$reason->day_before}}">
                </div>

                <div class="form-group">
                    <label>Masa Limit Balik</label>
                    <input type="time" name="time" class="form-control" placeholder="Pelajar akan Diletakkan dalam Blacklist Jika Lewat Balik Asrama Selepas Masa Ini. " value="{{$reason->time_limit}}">
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

@endsection


@section('script')
<!-- Peity chart-->
<script src="{{ URL::asset('assets/libs/peity/peity.min.js')}}"></script>

<!-- Plugin Js-->
<script src="{{ URL::asset('assets/libs/chartist/chartist.min.js')}}"></script>

<script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script>


@endsection