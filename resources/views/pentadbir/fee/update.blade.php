@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Yuran</h4>
            {{-- <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Murid >> Tambah Murid</li>
            </ol> --}}
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
        <form method="post" action="{{ route('fees.update', $fee->feeid) }}" enctype="multipart/form-data">
            @method('PATCH')
            {{csrf_field()}}
            <div class="card-body">
                <div class="form-group">
                    <label>Nama Yuran</label>
                    <input type="text" name="name" class="form-control" placeholder="Nama Yuran"
                        value="{{ $fee->feename }}">
                </div>

                <div class="form-group">
                    <label>Nama Organisasi</label>
                    <select name="organization" id="organization" class="form-control">
                        <option value="">Pilih Organisasi</option>
                        @foreach($organization as $row)
                        @if($row->id == $fee->organization_id)
                        <option value="{{ $row->id }}" selected> {{ $row->nama }} </option>
                        @else
                        <option value="{{ $row->id }}">{{ $row->nama }}</option>
                        @endif
                        @endforeach
                    </select>
                </div>

                @if ($fee->type_org == 1 || $fee->type_org == 2)
                <div class="yearhide form-group">
                    <label>Tahun</label>
                    <select name="year" id="year" class="form-control">
                        <option value="" selected>Pilih Tahun</option>
                        @for ($i = 1; $i <= 5; $i++) @if ($getyear==$i) <option value="{{ $i }}" selected>Tahun {{ $i }}
                            </option>
                            @else
                            <option value="{{ $i }}">Tahun {{ $i }}</option>
                            @endif
                            @endfor
                    </select>
                </div>
                @elseif($fee->type_org == 3)
                <div class="yearhide form-group">
                    <label>Tingkatan</label>
                    <select name="year" id="year" class="form-control">
                        <option value="" selected>Pilih Tingkatan</option>
                        @for ($i = 1; $i <= 5; $i++) @if ($getyear==$i) <option value="{{ $i }}" selected>Tingkatan
                            {{ $i }}</option>
                            @else
                            <option value="{{ $i }}">Tingkatan {{ $i }}</option>
                            @endif
                            @endfor
                    </select>
                </div>
                @endif



                <div class="cbhide form-check-inline">
                    <label for='checkAll' style='margin-right: 22px;' class='form-check-label'> 
                        <input class='form-check-input' type='checkbox' id='checkedAll' name='all' value='' /> Semua Kelas
                    </label>
                    @foreach ($getallclass as $row)
                        @foreach ($getclass as $item)
                            @if ($item->cid == $row->cid)
                            <label for='cb_class' style='margin-right: 22px;' class='form-check-label'>
                                <input class='checkSingle form-check-input' type='checkbox' id='cb_class' checked name='cb_class[]'
                                    value='{{ $row->cid }}' /> {{ $row->classname }}
                            </label>
                            @else
                            <label for='cb_class' style='margin-right: 22px;' class='form-check-label'>
                                <input class='checkSingle form-check-input' type='checkbox' id='cb_classs' name='cb_class[]'
                                    value='{{ $row->cid }}' /> {{ $row->classname }}
                            </label>
                            @endif
                        @endforeach

                        {{-- @foreach($getclass as $item)
                            <label for='cb_class' style='margin-right: 22px;' class='form-check-label'>
                                <input class='checkSingle form-check-input' type='checkbox' id='cb_class' checked name='cb_class[]'
                                {{ $row->cid->contains($item->cid) ? 'checked' : '' }} value='{{ $row->cid }}' /> {{  $row->classname }}
                            </label>
                        @endforeach --}}

                    @endforeach
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
        
        // $('.yearhide').hide();

        // $('.cbhide').hide();

        $(document).on('change', '#checkedAll', function() {
            if(this.checked){
            $(".checkSingle").each(function(){
                this.checked=true;
            })              
            }else{
            $(".checkSingle").each(function(){
                this.checked=false;
            })              
            }
        });

        $(document).on('change', '.checkSingle', function() {
          
            // console.log('asdf');
            // $('#cb_class').not(this).prop('checked', this.checked);
            if ($(this).is(":checked")){
                var isAllChecked = 0;
                $(".checkSingle").each(function(){
                    if(!this.checked)
                    isAllChecked = 1;
                })              
                if(isAllChecked == 0){ 
                    $("#checkedAll").prop("checked", true); 
                }     
            }else {
                $("#checkedAll").prop("checked", false);
            }
            
        });

        $('#organization').change(function() {
           
            if($(this).val() != '')
            {
                var organizationid    = $("#organization option:selected").val();
                var _token            = $('input[name="_token"]').val();
                $.ajax({
                    url:"{{ route('fees.fetchYear') }}",
                    method:"POST",
                    data:{ oid:organizationid,
                            _token:_token },
                    success:function(result)
                    {
                        $('.yearhide').show();       
                        $('#year').empty();
                        
                        if(result.success.type_org == 1 || result.success.type_org == 2 ){

                            $("#year").append("<option value='' selected> Pilih Tahun</option>");
                            $("#year").append("<option value='1'>Tahun 1</option>");
                            $("#year").append("<option value='2'>Tahun 2</option>");
                            $("#year").append("<option value='3'>Tahun 3</option>");
                            $("#year").append("<option value='4'>Tahun 4</option>");
                            $("#year").append("<option value='5'>Tahun 5</option>");
                            $("#year").append("<option value='6'>Tahun 6</option>");

                        }else if(result.success.type_org == 3 ){
                            $("#year").append("<option value='' selected> Pilih Tingkatan</option>");
                            $("#year").append("<option value='1'>Tingkatan 1</option>");
                            $("#year").append("<option value='2'>Tingkatan 2</option>");
                            $("#year").append("<option value='3'>Tingkatan 3</option>");
                            $("#year").append("<option value='4'>Tingkatan 4</option>");
                            $("#year").append("<option value='5'>Tingkatan 5</option>");
                            $("#year").append("<option value='6'>Tingkatan 6</option>");
                        }

                        $('.cbhide').hide();
                    }

                })
            }
        });

        $('#year').change(function() {
           
           if($(this).val() != '')
           {
               var organizationid    = $("#organization option:selected").val();
               var year              = $("#year option:selected").val();
               var _token            = $('input[name="_token"]').val();
               $.ajax({
                   url:"{{ route('fees.fetchClass') }}",
                   method:"POST",
                   data:{ oid:organizationid,
                            year:year,
                           _token:_token },
                   success:function(result)
                   {
                       $('.cbhide').show();   
                       $('#cb_class').remove();
                       $(".cbhide label").remove();
                       $(".cbhide").append("<label for='checkAll' style='margin-right: 22px;' class='form-check-label'> <input class='form-check-input' type='checkbox' id='checkedAll' name='all' value=''/> Semua Kelas </label>");

                       // console.log(result.success.oid);
                       jQuery.each(result.success, function(key, value){

                           $(".cbhide").append("<label for='cb_class' style='margin-right: 22px;' class='form-check-label'> <input class='checkSingle form-check-input' type='checkbox' id='cb_class' name='cb_class[]' value='" + value.cid + "'/> " + value.cname + " </label>");
                          
                       });
                   }

               })
           }
       });
    });
</script>
@endsection