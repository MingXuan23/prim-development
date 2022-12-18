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
            <h4 class="font-size-18">Tambah Permintaan</h4>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Asrama >> Tambah Permintaan</li>
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
            <form method="post" action="{{ route('dorm.store') }}" enctype="multipart/form-data">
                {{csrf_field()}}
                <div class="card-body">
                    <div class="form-group">
                        <label class="control-label required">Nama Organisasi</label>
                        <select name="organization" id="organization" class="form-control">
                        <option value="" selected disabled>Pilih Organisasi</option>
                            @foreach($organization as $row)
                                <option value="{{ $row->id }}">{{ $row->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="control-label required">Nama Pelajar</label>
                        <select name="name" id="name" class="form-control">
                            <option value="" selected disabled>Pilih Pelajar</option>
                            
                        </select>
                    </div>

                    
                    <div class="form-group">
                        <label class="control-label required">Kategori</label>
                        <select name="category" id="category" class="form-control">
                            <option value="" disabled selected>Pilih Kategori Keluar</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="control-label required">Tarikh Keluar</label>
                        <input onclick="this.showPicker()" class="form-control" id="start_date" name="start_date" type="date"
                                placeholder="Pilih Tarikh Keluar">
                    </div>

                    <div class="form-group">
                        <label class="control-label required">Alasan</label>
                        <textarea name="reason" class="form-control" placeholder="Alasan Keluar" max-length="50" cols="30"
                            rows="5"></textarea>
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
    $(document).ready(function() {
        // do ajax call function to get the category for user based on dorm value
        var start;
        var end;
        $("#organization").prop("selectedIndex", 1).trigger('change');
        fetchStudent($("#organization").val());

        $('#organization').change(function() {
            
            if($(this).val() != '')
            {
                var organizationid = $("#organization option:selected").val();
                var _token = $('input[name="_token"]').val();
                $.ajax({
                    url:"{{ route('dorm.fetchStudent') }}",
                    method:"GET",
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
        });

        function fetchStudent(organizationid = ''){
            var _token = $('input[name="_token"]').val();
            $.ajax({
                url:"{{ route('dorm.fetchStudent') }}",
                method:"GET",
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
        
        $("#name").prop("selectedIndex", 1).trigger('change');
        fetchCategory($("#name").val());

        $('#name').change(function() {
            
            if($(this).val() != '')
            {
                var studentid = $("#name option:selected").val();
                var _token = $('input[name="_token"]').val();
                $.ajax({
                    url:"{{ route('dorm.fetchCategory') }}",
                    method:"GET",
                    data:{ sid:studentid,
                            oid:$("#organization option:selected").val(),
                            _token:_token },
                    success:function(result)
                    {  
                        $('#category').empty();
                        $("#category").append("<option value='' disabled selected> Pilih Kategori Keluar</option>");
                        jQuery.each(result.success, function(key, value){
                            if(value.organization_id == $("#organization option:selected").val())
                            {
                                $("#category").append("<option value='"+ value.id + "‡" + value.day_before + "‡" + value.name +"'>" + value.fake_name + "</option>");
                            }
                        });
                        start = result.start;
                        end = result.end;
                        console.log("start : " + start);
                    }

                });
            }
        });

        function fetchCategory(studentid = ''){
            var _token = $('input[name="_token"]').val();
            $.ajax({
                url:"{{ route('dorm.fetchCategory') }}",
                method:"GET",
                data:{ sid:studentid,
                    oid:$("#organization option:selected").val(),
                        _token:_token },
                success:function(result)
                {
                    $('#category').empty();
                    $("#category").append("<option value='' disabled selected> Pilih Kategori Keluar </option>");
                    jQuery.each(result.success, function(key, value){
                        if(value.organization_id == $("#organization option:selected").val())
                        {
                            $("#category").append("<option value='"+ value.id + "‡" + value.day_before + "‡" + value.name +"'>" + value.fake_name + "</option>");
                        }
                    });
                    start = result.start;
                    end = result.end;
                }
            })
        }
       
        $("#category").change(function() {
            $("#start_date").prop('disabled', false);
            
            if ($("#organization option:selected").val != '') {
                var selectedCat = $("#category option:selected").val().split('‡');
                console.log(selectedCat[2]);
                if(selectedCat[2].toUpperCase() == "OUTINGS")
                {
                    var today = new Date();
                    
                    // var day = today.getDate() + parseInt(selectedCat[1]) + 1;
                    // var month = today.getMonth();
                    // var year = today.getFullYear();
                    // var d = new Date (year, month, day);

                    if(end >= today.toISOString().split('T')[0])
                    {
                        start_date.value = start_date.max = null;
                        start_date.min = start;
                        start_date.max = end;
                    }
                    else
                    {
                        alert("No outings date and time is available for selection");
                        $("#start_date").prop('disabled', true);
                    }
                }
                else if(selectedCat[1] != 0)
                {
                    start_date.value = start_date.max = null;
                    
                    var today = new Date();
                    
                    var day = today.getDate() + parseInt(selectedCat[1]) + 1;
                    var month = today.getMonth();
                    var year = today.getFullYear();

                    var d = new Date (year, month, day);
                    start_date.min = d.toISOString().split('T')[0];
                }
                else
                {
                    start_date.value = start_date.max = null;
                    start_date.min = new Date().toISOString().split("T")[0];
                }
            }
            else{
                start_date.value = start_date.max = null;
                start_date.min = new Date().toISOString().split("T")[0];
            }
        });
        
    });

</script>
@endsection

