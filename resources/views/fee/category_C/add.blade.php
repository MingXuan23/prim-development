@extends('layouts.master')

@section('css')
    <link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    {{-- <p>Welcome to this beautiful admin panel.</p> --}}
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="page-title-box">
                <h4 class="font-size-18">Tambah Butiran Kategori C</h4>
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
            <form class="form-validation" method="POST" action="{{ route('fees.storeC') }}" enctype="multipart/form-data">

                @csrf
                <div class="card-body">

                    <div class="form-group">
                        <label class="control-label">Nama Organisasi</label>
                        <select name="organization" id="organization" class="form-control"
                            data-parsley-required-message="Sila pilih organisasi" required>
                            <option value="" disabled selected>Pilih Organisasi</option>
                            @foreach($organization as $row)
                                <option value="{{ $row->id }}">{{ $row->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Nama Butiran</label>
                        <input type="text" name="name" class="form-control"
                            data-parsley-required-message="Sila masukkan nama butiran" required placeholder="Nama Butiran">
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Harga (RM)</label>
                            <input class="form-control input-mask text-left"
                                data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'"
                                im-insert="true" name="price" data-parsley-required-message="Sila masukkan harga"
                                data-parsley-errors-container=".errorMessagePrice" required>
                            <i>*Harga per kuantiti</i>
                            <div class="errorMessagePrice"></div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Kuantiti</label>
                            <input type="text" name="quantity" class="form-control" placeholder="Kuantiti"
                                data-parsley-required-message="Sila masukkan kuantiti" required>


                        </div>

                    </div>


                    <div class="form-row">
                        <div class="form-group col-md-12 required">
                            <label class="control-label">Tempoh Aktif</label>

                            <div class="input-daterange input-group" id="date">
                                <input type="text" class="form-control" name="date_started" placeholder="Tarikh Awal"
                                    autocomplete="off" data-parsley-required-message="Sila masukkan tarikh awal"
                                    data-parsley-errors-container=".errorMessage" required />
                                <input type="text" class="form-control" name="date_end" placeholder="Tarikh Akhir"
                                    autocomplete="off" data-parsley-required-message="Sila masukkan tarikh akhir"
                                    data-parsley-errors-container=".errorMessage" required />
                            </div>
                            <div class="errorMessage"></div>
                            <div class="errorMessage"></div>
                        </div>
                    </div>


                    <div class="form-group">
                        <label>Tahap</label>
                        <select name="level" id="level" class="form-control"
                            data-parsley-required-message="Sila pilih tahap"
                            data-parsley-required-message="Sila pilih tahap" required>
                            <option value="" disabled selected>Pilih Tahap</option>
                            <option value="All_Level">Semua Tahap</option>
                            <option value="1">Tahap 1</option>
                            <option value="2">Tahap 2</option>
                        </select>
                    </div>

                    <div class="yearhide form-group">
                        <label>Tahun</label>
                        <select name="year" id="year" class="form-control">
                            <option value="" disabled selected>Pilih Tahun</option>
                        </select>
                    </div>

                    <div class="cbhide row px-4 py-3">

                    </div>

                    <div class="genderhide form-group">
                        <label>Jantina</label>
                        <div class="radio">
                            <label class="radio-inline pl-2"><input type="radio" name="gender" value="L"> Lelaki </label>
                            <label class="radio-inline pl-2"><input type="radio" name="gender" value="P"> Perempuan </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Penerangan</label>
                        <textarea name="description" class="form-control" placeholder="Penerangan" cols="30"
                            rows="5"></textarea>
                    </div>

                    <div class="form-group mb-0">
                        <div class="text-right">
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
    <script src="{{ URL::asset('assets/libs/dropzone/dropzone.min.js')}}"></script>
    <script src="{{ URL::asset('assets/libs/chartist/chartist.min.js')}}"></script>
    <script src="{{ URL::asset('assets/libs/parsleyjs/parsleyjs.min.js')}}"></script>
    <script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script>
    <script src="{{ URL::asset('assets/libs/inputmask/inputmask.min.js')}}"></script>
    <script src="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}" defer></script>

    <script>
        $(document).ready(function () {

            $('.form-validation').parsley();
            $(".input-mask").inputmask();

            var today = new Date();
            $('.yearhide').hide();
            $('.cbhide').hide();

            $('#date').datepicker({
                toggleActive: true,
                startDate: today,
                todayHighlight: true,
                format: 'dd/mm/yyyy',
                orientation: 'bottom'
            });

            // ************************** organization on change ********************************

            $('#organization').change(function () {
                var organizationid = $("#organization option:selected").val();
                $('.yearhide').hide();
                $("#level").prop("selectedIndex", 1).trigger('change');

            });

            if ($("#organization").val() != "") {
                $("#organization").prop("selectedIndex", 1).trigger('change');
            }


            // ************************** checkbox class ********************************

            $(document).on('change', '#checkedAll', function () {
                if (this.checked) {
                    $(".checkSingle").each(function () {
                        this.checked = true;
                    })
                } else {
                    $(".checkSingle").each(function () {
                        this.checked = false;
                    })
                }
            });

            // ************************** checkbox class ********************************

            $(document).on('change', '.checkSingle', function () {
                console.log('asdf');
                // $('#cb_class').not(this).prop('checked', this.checked);
                if ($(this).is(":checked")) {
                    var isAllChecked = 0;
                    $(".checkSingle").each(function () {
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

            // ************************** retrieve class year ********************************
            $('#level').change(function () {
                if ($(this).val() != '') {
                    var level = $("#level option:selected").val();
                    var oid = $("#organization option:selected").val();
                    var _token = $('input[name="_token"]').val();

                    if (level == "All_Level") {
                        $('.yearhide').hide();
                        $('.cbhide').hide();
                        $('#cb_class').remove();
                        $(".cbhide label").remove();
                        $('#year').empty();

                    } else {
                        $.ajax({
                            url: "{{ route('fees.fetchClassYear') }}",
                            method: "GET",
                            data: {
                                level: level,
                                oid: oid,
                                _token: _token
                            },
                            success: function (result) {

                                $('.yearhide').show();
                                $('#year').empty();
                                $("#year").append("<option value='All_Year' selected> Semua Tahun</option>");
                                jQuery.each(result.datayear, function (key, value) {
                                    $("#year").append("<option value='" + value.year + "'> Tahun " + value.year + "</option>");
                                });
                            }
                        })
                    }
                }
            });

            // ************************** retrieve class ********************************
            $('#year').change(function () {
                if ($(this).val() != '') {
                    var year = $("#year option:selected").val();
                    var oid = $("#organization option:selected").val();
                    var _token = $('input[name="_token"]').val();

                    $.ajax({
                        url: "{{ route('fees.fetchClass') }}",
                        method: "POST",
                        data: {
                            year: year,
                            oid: oid,
                            _token: _token
                        },
                        success: function (result) {

                            if (year == "All_Year") {
                                $('.cbhide').hide();
                                $('#cb_class').remove();
                                $(".cbhide label").remove();
                            } else {
                                $('.cbhide').show();
                                $('#cb_class').remove();
                                $(".cbhide div").remove();
                                $(".cbhide").append(
                                    "<div class='col-md-2 col-sm-6 mb-2'><label for='checkAll' style='margin-right: 22px;' class='form-check-label'> <input class='form-check-input' type='checkbox' id='checkedAll' name='all_classes' value=''/> Semua Kelas </label></div>"
                                );

                                jQuery.each(result.success, function (key, value) {
                                    $(".cbhide").append(
                                        "<div class='col-md-2 col-sm-6 mb-2'><label for='cb_class' style='margin-right: 22px;' class='form-check-label'> <input class='checkSingle form-check-input' data-parsley-required-message='Sila pilih kelas' data-parsley-errors-container='.errorMessageCB' type='checkbox' id='cb_class' name='cb_class[]' value='" +
                                        value.cid + "'/> " + value.cname + " </label><br> <div class='errorMessageCB'></div></div>");
                                });

                                $("#cb_class").attr('required', '');
                            }

                        }
                    })
                }
            });



        });
    </script>
@endsection