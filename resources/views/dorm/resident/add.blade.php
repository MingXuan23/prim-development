@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/css/required-asterick.css')}}" rel="stylesheet">
@endsection

@section('content')
{{-- <p>Welcome to this beautiful admin panel.</p> --}}
<!-- use class_student -->
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Asrama</h4>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Asrama >> Tambah Pelajar</li>
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
            <form method="get" action="{{ route('dorm.storeResident') }}" enctype="multipart/form-data">
                {{csrf_field()}}
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label class="control-label required">Nama Organisasi</label>
                            <select name="organization" id="organization" class="form-control">
                                <option value="" selected>Pilih Organisasi</option>
                                @foreach($organization as $row)
                                    <option value="{{ $row->id }}" selected>{{ $row->nama }}</option>           
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label class="control-label required"> Nama Asrama</label>
                            <select name="dorm" id="dorm" class="form-control">
                                <option value="" disabled selected>Pilih Asrama</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label class="control-label required">Nama Penuh Murid</label>
                            <select name="name" id="name" class="form-control">
                                <option value="" selected disabled>Pilih Pelajar</option>
                                
                            </select>
                        </div>
                    </div>

                    <!-- <div class="form-row">
                        <div class="form-group col-md-12">
                            <label>Email Pelajar</label>
                            <input type="text" id="email" name="email" class="form-control" placeholder="Email Pelajar">
                        </div>
                    </div> -->
                    
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
    $(document).ready(function(){
        
        $("#organization").prop("selectedIndex", 1).trigger('change');
        $("#name").prop("selectedIndex", 0);
        fetchDorm($("#organization").val());

        $('#organization').change(function() {
            
            if($(this).val() != '')
            {
                var organizationid    = $("#organization option:selected").val();
                var _token            = $('input[name="_token"]').val();
                $.ajax({
                    url:"{{ route('dorm.fetchDorm') }}",
                    method:"POST",
                    data:{ oid:organizationid,
                            _token:_token },
                    success:function(result)
                    {  
                        $('#dorm').empty();
                        "<option value='' disabled selected>Pilih Asrama</option>"
                        $("#dorm").append("<option value='' disabled selected> Pilih Asrama</option>");
                        jQuery.each(result.success, function(key, value){
                            console.log(value.id);
                            $("#dorm").append("<option value='"+ value.id +"'>" + value.name + "</option>");
                        });
                    }

                });
            }
        });

        $('#dorm').change(function(){
            if($(this).val() != '')
            {
                var organizationid    = $("#organization option:selected").val();
                var _token            = $('input[name="_token"]').val();
                $.ajax({
                    url:"{{ route('dorm.schoolStudent') }}",
                    method:"POST",
                    data:{ oid:organizationid,
                            _token:_token },
                    success:function(result)
                    {  
                        $('#name').empty();
                        $("#name").append("<option value='' disabled selected> Pilih Pelajar</option>");
                        jQuery.each(result.success, function(key, value){
                            $("#name").append("<option value='"+ value.id + "'>" + value.nama + "</option>");
                        });
                    }

                });
            }
        })

        function schoolStudent(organizationid = ''){
            var _token = $('input[name="_token"]').val();
            $.ajax({
                url:"{{ route('dorm.schoolStudent') }}",
                method:"POST",
                data:{ oid:organizationid,
                        _token:_token },
                success:function(result)
                {
                    $('#name').empty();
                    $("#name").append("<option value='' disabled selected> Pilih Pelajar</option>");
                    jQuery.each(result.success, function(key, value){
                        $("#name").append("<option value='"+ value.id + "'>" + value.nama + "</option>");
                    });
                }
            })
        }
        

        function fetchDorm(organizationid = ''){
            var _token = $('input[name="_token"]').val();
            $.ajax({
                url:"{{ route('dorm.fetchDorm') }}",
                method:"POST",
                data:{ oid:organizationid,
                        _token:_token },
                success:function(result)
                {
                    $('#dorm').empty();
                    $("#dorm").append("<option value='' disabled selected> Pilih Asrama </option>");
                    jQuery.each(result.success, function(key, value){
                        $("#dorm").append("<option value='"+ value.id +"'>" + value.name + "</option>");
                    });
                }
            })
        }
    });
</script>
@endsection