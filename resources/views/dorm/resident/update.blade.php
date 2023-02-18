@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/css/required-asterick.css')}}" rel="stylesheet">
@endsection

@section('content')
{{-- <p>Welcome to this beautiful admin panel.</p> --}}
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Asrama</h4>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Asrama >> Edit Pelajar</li>
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

        <form method="post" action="{{ route('dorm.updateResident', $resident->id) }}" enctype="multipart/form-data">
            <!-- 注意这个 -->
            
            {{csrf_field()}}
            @method('GET')
            <div class="card-body">

                <div class="form-group">
                    <label class="control-label required">Nama Organisasi</label>
                    <select name="organization" id="organization" class="form-control">
                        <option value="" selected>Pilih Organisasi</option>
                        @foreach($organization as $row)
                            @if($row->id == $resident->organization_id)
                            <option value="{{ $row->id }}" selected> {{ $row->nama }} </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="control-label required"> Nama Asrama</label>
                    <select name="dorm" id="dorm" class="form-control">
                        <option value="" disabled selected>Pilih Asrama</option>
                        @foreach($dormlist as $row)
                        @if($row->id == $resident->dorm_id)
                        <option value="{{ $row->id }}" selected> {{ $row->name }} </option>
                        @else
                        <option value="{{ $row->id }}">{{ $row->name }}</option>
                        @endif
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="control-label required">Nama Penuh Pelajar</label>
                    <input type="text" name="name" class="form-control" placeholder="Nama Penuh"
                        value="{{ $resident->studentname }}" readonly>
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
    $(document).ready(function(){

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
                        $("#dorm").append("<option value='' disabled selected> Pilih Asrama</option>");
                        jQuery.each(result.success, function(key, value){
                            $("#dorm").append("<option value='"+ value.id +"'>" + value.name + "</option>");
                        });
                    }

                })
            }
        });
    });
</script>
@endsection