@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css') }}" rel="stylesheet" type="text/css" />
<link href={{ URL::asset("assets/libs/select2/select2.min.css") }} rel="stylesheet" type="text/css">
<link href={{ URL::asset("assets/libs/spectrum-colorpicker2/spectrum.min.css") }} rel="stylesheet" type="text/css">
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

        <form method="post" action="{{ route('fees.store') }}" enctype="multipart/form-data" class="outer-repeater">
            {{ csrf_field() }}
            <div class="card-body" data-repeater-list="outer-group" class="outer">
                <div data-repeater-item class="outer">
                    <div class="form-group">
                        <label>Nama Yuran</label>
                        <input type="text" name="name" class="form-control" placeholder="Nama Yuran">
                    </div>

                    <div class="form-group">
                        <label>Nama Organisasi</label>
                        <select name="organization" id="organizationdd" class="form-control">
                            <option value="" selected>Pilih Organisasi</option>
                            @foreach ($organization as $row)
                            <option value="{{ $row->id }}">{{ $row->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- <div class="form-group categoryhide">
                        <label>Kategori Yuran</label>
                        <select name="category" id="category" class="select2 form-control select2-multiple"
                            multiple="multiple" multiple data-placeholder="Pilih Kategori">
                        </select>
                    </div> --}}


                    <div class="inner-repeater mb-4 form-group categoryhide">
                        <div data-repeater-list="inner-group" class="inner mb-3">
                            <label class="form-label">Kategori Yuran</label>
                            <div data-repeater-item class="inner mb-3 row">
                                <div class="cat col-md-10 col-sm-8">
                                    <select name="category" id="category" class="cat form-control">
                                        <option value="" selected>Pilih Kategori</option>
                                    </select>
                                </div>
                                <div class="col-md-2 col-sm-4">
                                    <div class="d-grid">
                                        <input data-repeater-delete type="button"
                                            class="btn btn-primary inner mt-2 w-100 mt-sm-0" value="Delete" />
                                    </div>
                                </div>

                                <div class="cbhidecategory form-check-inline pb-3 pt-3">

                                </div>
                            </div>
                        </div>

                        <input data-repeater-create type="button" id="btnAdd" class="btn btn-success inner"
                            value="Tambah Kategori" />
                    </div>
                    {{-- 
                    <div class="categoryhide form-group">
                        <label>Kategori Yuran</label>
                        <div>
                            <select name="category" id="category" class="form-control">
                                <option value="" selected>Pilih Organisasi</option>
                            </select>
    
                            <div class="cbhidecategory form-check-inline pb-3 pt-3">
    
                            </div>
                        </div>
                        

                        <button type="button" id="btnAdd" class="btn btn-primary repeater-add-btn">Tambah
                            Kategori</button>

                    </div> --}}

                    <div class="yearhide form-group">
                        <label>Tahun</label>
                        <select name="year" id="year" class="form-control">
                            <option value="" selected>Pilih Tahun</option>
                        </select>
                    </div>

                    <div class="cbhide form-check-inline pb-3 pt-3">

                    </div>

                    <div class="form-group mb-0">
                        <div class="text-lg-right">
                            <button type="submit" class="btn btn-primary waves-effect waves-light mr-1">
                                Simpan
                            </button>
                        </div>
                    </div>
                    </>
                </div>
                <!-- /.card-body -->



        </form>
    </div>
</div>
@endsection


@section('script')
<!-- Peity chart-->
<script src="{{ URL::asset('assets/libs/peity/peity.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}" defer></script>
<script src="{{ URL::asset('assets/libs/chartist/chartist.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/jquery-repeater/jquery-repeater.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/select2/select2.min.js')}}"></script>
<script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script>
<script src="{{ URL::asset('assets/js/pages/form-repeater.int.js')}}"></script>
<script src="{{ URL::asset('assets/js/pages/form-advanced.init.js')}}"></script>
<script src="{{ URL::asset('assets/libs/spectrum-colorpicker2/spectrum.min.js')}}"></script>

<script>
    $(document).ready(function(){
        
        $('#btnAdd').hide();
        $('.categoryhide').hide();
        $('.cbhidecategory').hide();
        $('.yearhide').hide();
        $('.cbhide').hide();


        $('#btnAdd').click(function(){  
           $('select[name=name=outer-group[0][inner-group][1][category]]').append("<select></select><option value='2'> ss </option></select>");
        });  


        // ************************** checkbox category ********************************

        $(document).on('change', '#checkedAllCategory', function() {
            if (this.checked) {
                $(".checkSingleCategory").each(function() {
                    this.checked = true;
                })
            } else {
                $(".checkSingleCategory").each(function() {
                    this.checked = false;
                })
            }
        });

        // ************************** checkbox category ********************************

        $(document).on('change', '.checkSingleCategory', function() {
            // console.log('asdf');
            // $('#cb_class').not(this).prop('checked', this.checked);
            if ($(this).is(":checked")) {
                var isAllChecked = 0;
                $(".checkSingleCategory").each(function() {
                    if (!this.checked)
                        isAllChecked = 1;
                })
                if (isAllChecked == 0) {
                    $("#checkedAllCategory").prop("checked", true);
                }
            } else {
                $("#checkedAllCategory").prop("checked", false);
            }
        });

        // ************************** checkbox class ********************************

        $(document).on('change', '#checkedAll', function() {
            if (this.checked) {
                $(".checkSingle").each(function() {
                    this.checked = true;
                })
            } else {
                $(".checkSingle").each(function() {
                    this.checked = false;
                })
            }
        });

        // ************************** checkbox class ********************************

        $(document).on('change', '.checkSingle', function() {
            // console.log('asdf');
            // $('#cb_class').not(this).prop('checked', this.checked);
            if ($(this).is(":checked")) {
                var isAllChecked = 0;
                $(".checkSingle").each(function() {
                    if (!this.checked)
                        isAllChecked = 1;
                })
                if (isAllChecked == 0) {
                    $("#checkedAll").prop("checked", true);
                }
            } else {
                $("#checkedAll").prop("checked", false);
            }
        });

        $('#organizationdd').change(function() {
            if ($(this).val() != '') {
                var organizationid = $("#organizationdd option:selected").val();
                var _token = $('input[name="_token"]').val();
                $.ajax({
                    url: "{{ route('fees.fetchYear') }}",
                    method: "POST",
                    data: {
                        oid: organizationid,
                        _token: _token
                    },
                    success: function(result) {
                        $('.categoryhide').show();
                        $('#category').empty();
                        $('.yearhide').show();
                        $('#year').empty();
                        if(result.category){
                            $("#category").append("<option value='' selected> Pilih Kategori</option>");
                            
                            jQuery.each(result.category, function(key, value) {
                                $("#category").append("<option value='" +
                                value.id + "'> " + value.nama + "</option>");
                            });
                            if (result.success.type_org == 1 || result.success.type_org == 2) {
                                $("#year").append("<option value='' selected> Pilih Tahun</option>");
                                $("#year").append("<option value='1'>Tahun 1</option>");
                                $("#year").append("<option value='2'>Tahun 2</option>");
                                $("#year").append("<option value='3'>Tahun 3</option>");
                                $("#year").append("<option value='4'>Tahun 4</option>");
                                $("#year").append("<option value='5'>Tahun 5</option>");
                                $("#year").append("<option value='6'>Tahun 6</option>");
                            } else if (result.success.type_org == 3) {
                                $("#year").append("<option value='' selected> Pilih Tingkatan</option>");
                                $("#year").append("<option value='1'>Tingkatan 1</option>");
                                $("#year").append("<option value='2'>Tingkatan 2</option>");
                                $("#year").append("<option value='3'>Tingkatan 3</option>");
                                $("#year").append("<option value='4'>Tingkatan 4</option>");
                                $("#year").append("<option value='5'>Tingkatan 5</option>");
                                $("#year").append("<option value='6'>Tingkatan 6</option>");
                            }
                        }
                        
                        
                        $('.cbhidecategory').hide();
                        $('.cbhide').hide();
                    }
                })
            }
        });

        $('#category').change(function() {


            if ($(this).val() != '') {
                var categoryid = $("#category option:selected").val();

                var _token = $('input[name="_token"]').val();
                $.ajax({
                    url: "{{ route('category.getDetails') }}",
                    method: "POST",
                    data: {
                        cid: categoryid,
                        _token: _token
                    },
                    success: function(result) {

                        $('#btnAdd').show();

                        $('.cbhidecategory').show();
                        $('#cb_details').remove();
                        $(".cbhidecategory label").remove();
                        $(".cbhidecategory").append(
                            "<label for='checkAll' style='margin-right: 22px;' class='form-check-label'> <input class='form-check-input' type='checkbox' id='checkedAllCategory' name='all' value=''/> Semua Butiran </label>"
                        );

                        jQuery.each(result.categorylist, function(key, value) {
                            $(".cbhidecategory").append(
                                "<label for='cb_details' style='margin-right: 22px;' class='form-check-label'> <input class='checkSingleCategory form-check-input' type='checkbox' id='cb_details' name='cb_details[]' value='" +
                                value.id + "'/> " + value.nama + " </label>");
                        });
                    }
                })
            }
        });

        $('#year').change(function() {
            if ($(this).val() != '') {
                var organizationid = $("#organizationdd option:selected").val();
                var year = $("#year option:selected").val();
                var _token = $('input[name="_token"]').val();
                $.ajax({
                    url: "{{ route('fees.fetchClass') }}",
                    method: "POST",
                    data: {
                        oid: organizationid,
                        year: year,
                        _token: _token
                    },
                    success: function(result) {
                        $('.cbhide').show();
                        $('#cb_class').remove();
                        $(".cbhide label").remove();
                        $(".cbhide").append(
                            "<label for='checkAll' style='margin-right: 22px;' class='form-check-label'> <input class='form-check-input' type='checkbox' id='checkedAll' name='all' value=''/> Semua Kelas </label>"
                        );
                        // console.log(result.success.oid);
                        jQuery.each(result.success, function(key, value) {
                            $(".cbhide").append(
                                "<label for='cb_class' style='margin-right: 22px;' class='form-check-label'> <input class='checkSingle form-check-input' type='checkbox' id='cb_class' name='cb_class[]' value='" +
                                value.cid + "'/> " + value.cname + " </label>");
                        });
                    }
                })
            }
        });
        

    });

        
</script>
@endsection