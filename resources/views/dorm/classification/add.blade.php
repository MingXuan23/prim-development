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
                <li class="breadcrumb-item active">Asrama >> Tambah Sebab Permintaan Keluar</li>
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
        <form method="get" action="{{ route('dorm.storeReasonOuting') }}" enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="card-body">

                <div class="form-group">
                    <label class="control-label required">Nama Organisasi</label>
                    <select name="organization" id="organization" class="form-control">
                        <option value="" selected>Pilih Organisasi</option>
                        @foreach($organization as $row)
                        @if ($loop->first)
                        <option value="{{ $row->id }}" selected>{{ $row->nama }}</option>
                        @else
                        <option value="{{ $row->id }}">{{ $row->nama }}</option>

                        @endif
                        @endforeach
                    </select>
                </div>

                <!-- real name -->
                <div class="form-group">
                    <label class="control-label required">Kategori Sebab Permintaan Keluar</label>
                    <select id="optionReason" class="form-control" name="optionReason">
                        <option value=1>Outings</option>
                        <option value=2>Balik Wajib</option>
                        <option value=3>Balik Khas</option>
                        <option value=4>Balik Kecemasan</option>
                        <option value=5>Others...</option>
                    </select>
                </div>

                <!-- fake name -->
                <div class="form-group">
                    <label class="control-label required">Nama Sebab Permintaan Keluar</label>
                    <input type="text" name="name" class="form-control" placeholder="Nama Sebab Permintaan Keluar">
                </div>

                <div class="form-group">
                    <label>Diskripsi</label>
                    <input type="text" name="description" class="form-control" id="description">
                </div>

                <div class="form-group">
                    <label class="control-label required">Limit Keluar</label>
                    <input type="number" name="limit" class="form-control" placeholder="Limit Pelajar boleh Keluar untuk Sebab Ini pada Setiap Tahun. Letak 0 Jika Tiada Limit. ">
                </div>

                <div class="form-group">
                    <label class="control-label required">Hari Sebelum Permintaan Keluar</label>
                    <input type="number" name="day" class="form-control" placeholder="Hanya Membenarkan Penjaga Memohon Beberapa Hari Sebelum Hari ingin Keluar. Letak 0 Jika Boleh Mohon Pada Hari yang Sama.  ">
                </div>

                <div class="form-group">
                    <label>Masa Limit Balik</label>
                    <input type="time" name="time" class="form-control" placeholder="Pelajar akan Diletakkan dalam Blacklist Jika Lewat Balik Asrama Selepas Masa Ini. ">
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

<script>
    $(document).ready(function() {

        if ($("#optionReason").val() != "") {
            $("#optionReason").prop("selectedIndex", 0).trigger('change');
            // console.log("at beginning" + $("#optionReason").val());
            $("#description").val("Pelajar boleh keluar asrama pada masa yang tertentu tetapi perlu balik asrama sebelum waktu tertentu dalam hari yang sama. ");
        }

        $('#optionReason').change(function() {
            var optionReason = $("#optionReason option:selected").val();
            // console.log("at change" + $("#optionReason").val());
            // if is outing
            if ($("#optionReason").val() == 1) {
                $("#description").val("");
                $("#description").val("Pelajar boleh keluar asrama pada masa yang tertentu tetapi perlu balik asrama sebelum waktu tertentu dalam hari yang sama. ");
            }
            //if is balik wajib
            else if ($("#optionReason").val() == 2) {
                $("#description").val("");
                $("#description").val("Pelajar perlu keluar asrama pada masa cuti untuk mengosongkan bilik asrama. ");
            }
            //if is balik khas
            else if ($("#optionReason").val() == 3) {
                $("#description").val("");
                $("#description").val("Pelajar boleh keluar asrama untuk tempoh yang melebihi 1 hari. ");
            }
            //if is balik kecemasan
            else if ($("#optionReason").val() == 4) {
                $("#description").val("");
                $("#description").val("Pelajar perlu keluar sekolah pada masa kecemasan. ");
            }
            //if is others
            else if ($("#optionReason").val() == 5) {
                $("#description").val("");
            }

        });

        $(document).on('click', '.importBtn', function() {
            dorm_id = $(this).attr('id');
            $('#dorm').val(dorm_id);
            // dd($('#dorm').val(dorm_id));
        });

    });
</script>

@endsection