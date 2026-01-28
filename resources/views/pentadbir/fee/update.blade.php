@extends('layouts.master')

@section('css')
    <link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="page-title-box">
                @if ($fee->category == "Kategori A")
                    <h4 class="font-size-18">Ubah Butiran Yuran Kategori A</h4>
                @elseif ($fee->category == "Kategori B")
                    <h4 class="font-size-18">Ubah Butiran Yuran Kategori B</h4>
                @elseif ($fee->category == "Kategori C")
                    <h4 class="font-size-18">Ubah Butiran Yuran Kategori C</h4>
                @else
                    <h4 class="font-size-18">Ubah Butiran Yuran Kategori Berulang</h4>
                @endif
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
                        <label>Nama Organisasi</label>
                        <select name="organization" id="organization" class="form-control" disabled>
                            <option value="" disabled>Pilih Organisasi</option>
                            @foreach($organization as $row)
                                @if($row->id == $fee->organization_id)
                                    <option value="{{ $row->id }}" selected> {{ $row->nama }} </option>
                                @else
                                    <option value="{{ $row->id }}">{{ $row->nama }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Nama Yuran</label>
                        <input type="text" name="name" class="form-control" placeholder="Nama Yuran"
                            value="{{ $fee->feename }}" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label class="control-label required">Tempoh Aktif</label>

                            <div class="input-daterange input-group">
                                <input type="text" class="form-control" name="date_started" placeholder="Tarikh Awal"
                                    autocomplete="off" data-parsley-required-message="Sila masukkan tarikh awal"
                                    data-parsley-errors-container=".errorMessage" disabled
                                    value="{{ \Carbon\Carbon::parse($fee->start_date)->format('d/m/Y') }}" required />
                                <input type="text" id="end_date" class="form-control" name="date_end"
                                    placeholder="Tarikh Akhir" autocomplete="off"
                                    data-parsley-required-message="Sila masukkan tarikh akhir"
                                    data-parsley-errors-container=".errorMessage"
                                    value="{{ \Carbon\Carbon::parse($fee->end_date)->format('d/m/Y') }}" required />
                            </div>
                            <div class="errorMessage"></div>
                            <div class="errorMessage"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Penerangan</label>
                        <textarea name="description" class="form-control" placeholder="Penerangan" cols="30"
                            rows="5">{{ $fee->desc }}</textarea>
                    </div>

                    <div class="form-group mb-0">
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary waves-effect waves-light mr-1">
                                Simpan
                            </button>
                        </div>
                    </div>
                </div>


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
    <script src="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}" defer></script>


    <script>
        $(document).ready(function () {
            $('.yearhide').hide();

            $('.cbhide').hide();

            var today = new Date();

            $('#end_date').datepicker({
                toggleActive: true,
                startDate: today,
                todayHighlight: true,
                format: 'dd/mm/yyyy',
                orientation: 'bottom'
            });

            // trigger organization change because organization is sure to be selected at first
            $('#organization').trigger('change');

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

                // console.log('asdf');
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