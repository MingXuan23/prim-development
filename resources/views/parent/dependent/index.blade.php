@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Ibubapa/Penjaga</h4>
            <!-- <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Welcome to Veltrix Dashboard</li>
            </ol> -->
        </div>
    </div>
</div>
<div class="container">

    <div class="row d-flex justify-content-center">
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
                <form method="post" action="{{ route('parent.store')}}" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <div class="card-body">

                        <div class="form-group">
                            <label>Nama Sekolah</label>
                            <select name="school" id="school" class="form-control">
                                <option value="" disabled selected>Pilih Sekolah</option>
                                @foreach($school as $row)
                                <option value="{{ $row->id }}">{{ $row->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="dkelas" class="form-group">
                            <label> Kelas</label>
                            <select name="classes" id="classes" class="form-control">
                                <option value="0" disabled selected>Pilih Kelas</option>

                            </select>
                        </div>

                        <div id="dmurid" class="form-group">
                            <label> Murid</label>
                            <select name="student" id="student" class="form-control">
                                <option value="0" disabled selected>Pilih Murid</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Peranan</label>
                            <select name="roles" id="roles" class="form-control">
                                <option value="" disabled selected>Pilih Peranan</option>
                                @foreach($role as $row)
                                <option value="{{ $row->id }}">{{ $row->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="exampleCheck1">
                    <label class="form-check-label" for="exampleCheck1">Check me out</label>
                    </div> --}}
                    </div>
                    <!-- /.card-body -->

                    <div class="">
                        <button style="float: right" type="submit" class="btn btn-primary"><i class="fas fa-save"></i>
                            Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card">
                {{-- <div class="card-header">List Of Applications</div> --}}
                <div>
                    {{-- <a style="margin: 19px; float: right;" href="{{ route('acc.create') }}" class="btn
                    btn-primary"> <i class="fas fa-plus"></i> Pengesahan</a> --}}
                </div>

                <div class="card-body">

                    <table class="table table-bordered table-striped">
                        <tr style="text-align:center">
                            <th>Bil.</th>
                            <th>Nama Tanggungan</th>
                            <th>Nama Sekolah</th>
                            <th>Kelas</th>
                            <th>Peranan</th>
                            <th>Details</th>
                        </tr>

                        @foreach($list as $row)
                        <tr>
                            <td>{{ $loop->iteration }}.</td>
                            <td>{{ $row->studentname }}</td>
                            <td>{{ $row->nschool }}</td>
                            <td>{{ $row->classname }}</td>
                            <td>{{ $row->rolename }}</td>

                            <td>
                                <div class="d-flex justify-content-center">
                                    <a href="" class="btn btn-primary m-1">Edit</a>

                                    <form action="" method="POST">
                                        <input type="hidden" name="_method" value="DELETE">
                                        {{ csrf_field() }}
                                        <button class="btn btn-danger m-1"
                                            onclick="return confirm('Adakah anda pasti ?')">Delete</button>
                                    </form>
                                </div>
                            </td>

                        </tr>
                        @endforeach

                    </table>
                </div>
            </div>

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
        
                $('#school').change(function(){
        
                    // $('#kelas').val('');
                    // $('#murid').val('');
        
                    if($(this).val() != '')
                    {
                        // alert($(this).val();)
                        // alert($("#sekolah option:selected").val());
                        var schoolid    = $("#school option:selected").val();
                        var _token      = $('input[name="_token"]').val();
                        // console.log(schoolid);
        
                        $.ajax({
                            url:"{{ route('parent.fetchClass') }}",
                            method:"POST",
                            data:{ schid:schoolid,
                                    _token:_token },
                            success:function(result)
                            {
                                $('#classes').empty();
                                $("#classes").append("<option value='0'> Pilih Kelas</option>");
        
                                jQuery.each(result.success, function(key, value){
                                    // $('select[name="kelas"]').append('<option value="'+ key +'">'+value+'</option>');
                                    $("#classes").append("<option value='"+ value.cid +"'>" + value.cname + "</option>");
                                });
                            }
        
                        })
        
                    }
        
        
                });

                $('#classes').change(function(){

                    if($(this).val() != '')
                    {
                        var classid   = $("#classes option:selected").val();
                        var _token    = $('input[name="_token"]').val();
                    
                        console.log(classid);
                        $.ajax({
                            url:"{{ route('parent.fetchStd') }}",
                            method:"POST",
                            data:{ cid: classid,
                                    _token: _token },
                            success:function(result)
                            {
                                // $('#murid').val("0");
                                $('#student').empty();
                                $("#student").append("<option value='0'> Pilih Murid</option>");
                                
                                jQuery.each(result.success, function(key, value){

                                    $("#student").append("<option value='"+ value.sid +"'>" + value.namestd + "</option>");
                                
                                });

                            }

                        })

                    }

                });
        
                
            });
        
        
</script>
@endsection