@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
{{-- <p>Welcome to this beautiful admin panel.</p> --}}
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Outing</h4>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Outing >> Tambah Tarikh Outing</li>
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
            <form method="get" action="{{ route('dorm.storeOuting') }}" enctype="multipart/form-data">
                {{csrf_field()}}
                <div class="card-body">
                    <div class="form-group">
                        <label>Nama Organisasi</label>
                        <select name="organization" id="organization" class="form-control">
                            @foreach($organization as $row)
                                @if ($loop->first)
                                <option value="{{ $row->id }}" selected>{{ $row->nama }}</option>
                                @else
                                <option value="{{ $row->id }}">{{ $row->nama }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Tarikh dan Masa Keluar</label>
                        <input onclick="this.showPicker()" class="form-control" id="start_date" name="start_date" type="datetime-local"
                                placeholder="Pilih Tarikh Keluar">
                    </div>

                    <div class="form-group">
                        <label>Tarikh dan Masa Masuk</label>
                        <input onclick="this.showPicker()" onfocus="setMinDate();" class="form-control" id="end_date" name="end_date" type="datetime-local"
                                placeholder="Pilih Tarikh Masuk">
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
</div>
@endsection


@section('script')
<!-- Peity chart-->
<script src="{{ URL::asset('assets/libs/peity/peity.min.js')}}"></script>

<!-- Plugin Js-->
<script src="{{ URL::asset('assets/libs/chartist/chartist.min.js')}}"></script>

<script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script>

<script>
    // $(document).ready(function() {

    //     if($("#organization").val() != ""){
    //         $("#organization").prop("selectedIndex", 0).trigger('change');
    //         fectchTeacher($("#organization").val());
    //     }

    //     $('#organization').change(function() {
    //         organizationid    = $("#organization").val();
    //         _token            = $('input[name="_token"]').val();
    //         fectchTeacher(organizationid);
    //     });

    //     function fectchTeacher(organizationid = ''){
    //         var _token = $('input[name="_token"]').val();
    //         $.ajax({
    //             url:"{{ route('class.fetchTeacher') }}",
    //             method:"POST",
    //             data:{ oid:organizationid,
    //                     _token:_token },
    //             success:function(result)
    //             {
    //                 $('#classTeacher').empty();
    //                 $("#classTeacher").append("<option value='' disabled selected> Pilih Guru</option>");
    //                 jQuery.each(result.success, function(key, value){
    //                     $("#classTeacher").append("<option value='"+ value.id +"'>" + value.name + "</option>");
    //                 });
    //             }
    //         })
    //     }

    //     // csrf token for ajax
    //     $.ajaxSetup({
    //             headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         }
    //     });
    // });
</script>
@endsection