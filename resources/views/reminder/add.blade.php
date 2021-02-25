@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
{{-- @include('layouts.datepicker') --}}

@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Derma</h4>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Derma >> Peringatan Derma</li>
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
            {{-- {{ route('sekolah.store') }} --}}
            <form method="post" action="{{ route('reminder.store') }} " enctype="multipart/form-data">
                {{csrf_field()}}
                <div class="card-body">
                    <div class="form-group">
                        <label>Peringatan</label>
                            <select type="text" name="recurrence" id="recurrence" class="form-control" placeholder="Peringatan">
                            <option value="" disabled selected>Pilih Peringatan</option>
                            <option value="daily">Harian</option>
                            <option value="weekly">Mingguan</option>
                            <option value="monthly">Bulanan</option>
                        </select>
                    </div>
                    <div class="form-group" id="date-form" style="display: none">
                        <label class="control-label">Tarikh</label>
                        <div id="datepicker-date" class="input-group date" data-date-format="mm-dd-yyyy"
                            data-provide="datepicker">
                            <input class="form-control" id="date" name="date" type="date"
                                placeholder="Pilih Tarikh" autocomplete="off">
                        </div>
                    </div>
                    <div class="form-group" id="time-form" style="display: none">
                        <label>Masa</label>
                        <div id="datepicker-time" class="input-group date">
                            <input class="form-control" id="time" name="time" type="time"
                                placeholder="Pilih Masa" autocomplete="off">
                        </div>
                    </div>
                    <div class="form-group" id="day-form" style="display: none">
                        <label>Hari</label>
                            <select type="text" name="" class="form-control" placeholder="Peringatan">
                            <option value="" disabled selected>Pilih Hari</option>
                            <option value="Isnin">Isnin</option>
                            <option value="Selesa">Selesa</option>
                            <option value="Rabu">Rabu</option>
                            <option value="Khamis">Khamis</option>
                            <option value="Jumaat">Jumaat</option>
                            <option value="Sabtu">Sabtu</option>
                            <option value="Ahad">Ahad</option>

                        </select>
                    </div>
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
</div>
@endsection


@section('script')
<!-- Peity chart-->
<script src="{{ URL::asset('assets/libs/peity/peity.min.js')}}"></script>

<!-- Plugin Js-->
<script src="{{ URL::asset('assets/libs/chartist/chartist.min.js')}}"></script>

<script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script>

<script>
    $(document).ready(function(){
        $("#recurrence").change(function() {
            if ($(this).val() == "daily") {
                $('#time-form').show();
                $('#day-form').hide();
                $('#date-form').hide();
            } else if ($(this).val() == "weekly") {
                $('#day-form').show();
                $('#time-form').show();
                $('#date-form').hide();
            } else if ($(this).val() == "monthly") {
                $('#date-form').show();
                $('#time-form').show();
                $('#day-form').hide();
            }
        });
    });
</script>
@endsection