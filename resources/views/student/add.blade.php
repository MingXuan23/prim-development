@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
{{-- <p>Welcome to this beautiful admin panel.</p> --}}
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Murid</h4>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Murid >> Tambah Murid</li>
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
        <form method="post" action="{{ route('student.store') }}" enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="card-body">

                <div class="form-row">
                    <div class="form-group col-md-6 required">
                        <label>Nama Organisasi</label>
                        <select name="organization" id="organization" class="form-control">
                            <option value="" selected>Pilih Organisasi</option>
                            @foreach($organization as $row)
                            <option value="{{ $row->id }}">{{ $row->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="dkelas" class="form-group col-md-6">
                        <label> Nama Kelas</label>
                        <select name="classes" id="classes" class="form-control">
                            <option value="" disabled selected>Pilih Kelas</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-12">
                        <label>Nama Penuh Murid</label>
                        <input type="text" name="name" class="form-control" placeholder="Nama Penuh Murid">
                    </div>
                    {{-- <div class="form-group col-md-6">
                        <label>Nombor Kad Pengenalan</label>
                        <input type="text" id="icno" name="icno" class="form-control" placeholder="Nombor Kad Pengenalan">
                    </div> --}}
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Email Pelajar&nbsp(optional)</label>
                        <input type="text" name="email" class="form-control" placeholder="Email Pelajar">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Jantina</label>
                        <div class="radio">
                            <label class="radio-inline pl-2"><input type="radio" name="gender" value="L"> Lelaki </label>
                            <label class="radio-inline pl-2"><input type="radio" name="gender" value="P"> Perempuan </label>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-12">
                        <label for="">Nama Penjaga</label>
                        <input type="text" id="parent_name" name="parent_name" class="form-control" placeholder="Nama Penuh">
                    </div>

                    {{-- <div class="form-group col-md-6">
                        <label for="">Nombor Kad Pengenalan Penjaga</label>
                        <input type="text" id="parent_icno" name="parent_icno" class="form-control" placeholder="Nombor Kad Pengenalan">
                    </div> --}}
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Email Penjaga</label>
                        <input type="text" id="parent_email" name="parent_email" class="form-control" placeholder="Email">
                    </div>

                    <div class="form-group col-md-6">
                        <label>No Telefon Penjaga</label>
                        <input type="text" id="parent_phone" name="parent_phone" class="form-control" placeholder="No Telefon" max="11">        
                    </div>
                </div>

                {{-- <div class="form-check">
                <input type="checkbox" class="form-check-input" id="exampleCheck1">
                <label class="form-check-label" for="exampleCheck1">Check me out</label>
                </div> --}}
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
<script src="{{ URL::asset('assets/libs/jquery-mask/jquery.mask.min.js')}}"></script>
<script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script>

<script>
    $(document).ready(function() {
        $('#icno').mask('000000-00-0000');
        $('#parent_icno').mask('000000-00-0000');
        $('#parent_phone').mask('+600000000000');
    });

    $(document).ready(function(){
        
        $("#organization").prop("selectedIndex", 1).trigger('change');
        fetchClass($("#organization").val());

            $('#organization').change(function() {
               
                if($(this).val() != '')
                {
                    var organizationid    = $("#organization option:selected").val();
                    var _token            = $('input[name="_token"]').val();
                    $.ajax({
                        url:"{{ route('parent.fetchClass') }}",
                        method:"POST",
                        data:{ oid:organizationid,
                                _token:_token },
                        success:function(result)
                        {
                             
                            $('#classes').empty();
                            $("#classes").append("<option value='' disabled selected> Pilih Kelas</option>");
                            jQuery.each(result.success, function(key, value){
                                // $('select[name="kelas"]').append('<option value="'+ key +'">'+value+'</option>');

                                $("#classes").append("<option value='"+ value.cid +"'>" + value.cname + "</option>");
                            });
                        }

                    })
                }
            });

            function fetchClass(organizationid = ''){
                var _token            = $('input[name="_token"]').val();
                $.ajax({
                    url:"{{ route('student.fetchClass') }}",
                    method:"POST",
                    data:{ oid:organizationid,
                            _token:_token },
                    success:function(result)
                    {
                        $('#classes').empty();
                        $("#classes").append("<option value='' disabled selected> Pilih Kelas</option>");
                        jQuery.each(result.success, function(key, value){
                            // $('select[name="kelas"]').append('<option value="'+ key +'">'+value+'</option>');
                            $("#classes").append("<option value='"+ value.cid +"'>" + value.cname + "</option>");
                        });
                    }
                })
            }
        });
</script>
@endsection