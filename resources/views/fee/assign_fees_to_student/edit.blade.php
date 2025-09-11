@extends('layouts.master')

@section('css')
    <link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    {{-- <p>Welcome to this beautiful admin panel.</p> --}}
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="page-title-box">
                <h4 class="font-size-18">Ubah Suai Yuran Pelajar</h4>
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
            <form class="form-validation" method="POST" action="{{ route('fees.assignFeesToStudentUpdate') }}"
                enctype="multipart/form-data">

                @csrf
                <div class="card-body">

                    <div class="form-group">
                        <label class="control-label">Nama Organisasi</label>
                        {{-- pass parameters when submitting form --}}
                        <input type="hidden" id="organization-id" name="oid" value="{{ $organization->id }}">
                        <input type="text" class="form-control" value="{{ $organization->nama }}" readonly>
                    </div>


                    <div class="form-group">
                        <label>Nama Pelajar</label>
                        {{-- pass parameters when submitting form --}}
                        <input type="hidden" id="student-id" name="student_id"
                            value="{{ $currentStudentFeesData["student_id"] }}">
                        <input type="text" name="name" class="form-control"
                            value="{{ $currentStudentFeesData["student_name"] }}" readonly>
                    </div>


                    <div class="form-group">
                        <label>Jantina</label>
                        <input type="text" name="name" class="form-control"
                            value="{{ $currentStudentFeesData['gender'] == 'L' ? 'Lelaki' : 'Perempuan' }}" readonly>
                    </div>


                    <div class="form-group">
                        <label>Kelas</label>
                        {{-- pass parameters when submitting form --}}
                        <input type="hidden" name="class_id" value="{{ $class->id }}">
                        <input type="text" name="name" class="form-control" value="{{ $class->nama }}" readonly>
                    </div>


                    {{-- --------------------------Assign yuran to students-------------------------- --}}
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Senarai Yuran</label>
                            <select name="all_fees[]" id="all-fees" multiple class="form-control">

                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="students_selected">Yuran Dipilih Untuk Pelajar</label>
                            <select name="fees_selected[]" id="fees-selected" multiple class="form-control">
                                @if (isset($currentStudentFeesData["fees"][0]["fee_id"]))
                                    @foreach ($currentStudentFeesData["fees"] as $fee)
                                        {{-- only able to change for debt fees --}}
                                        <option value="{{ $fee["fee_id"] }}" {{ $fee["fee_status"] === 'Paid' ? 'disabled' : '' }}>
                                            {{ $fee["fee_category"] . " - " . $fee["fee_name"] }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    {{-- --------------------------End of assigning yuran to students-------------------------- --}}

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

            // ************************** set all the options in the selected fees to be selected ********************************
            $("form").on("submit", function () {
                $("#fees-selected option").prop("selected", true);
            })

            // ************************** get all fees from the current organization ********************************
            $.ajax({
                url: "{{ route('fees.fetchAllFeesDatatableByOrg') }}",
                method: "GET",
                data: {
                    oid: $("#organization-id").val(),
                    routeName: ""
                },
                success: function (result) {
                    var allFees = $("#all-fees");
                    var selectedFeeIds = $("#fees-selected option").map(function () {
                        return parseInt($(this).val());
                    }).get();

                    result.data.forEach(fee => {
                        // only able to change for debt fees
                        if (fee.fee_id != null && !selectedFeeIds.includes(fee.fee_id) && fee.fee_category != "Kategori A") {
                            allFees.append("<option value = '" + fee.fee_id + "'>" + fee.fee_category + " - " + fee.fee_name + "</option>");
                        }
                    });
                }
            });

            // ************************** move yuran to selected list and vice versa ********************************
            $('#all-fees').on('change', function () {
                // callback function to add fees to the selected fees section

                var feesSelected = $('#all-fees option:selected');
                var feesSelectedList = $('#fees-selected');

                feesSelectedList.append(feesSelected);
                feesSelected.prop('selected', false);
            });

            $('#fees-selected').on('change', function () {
                // callback function to add selected fees back to original fees list section

                var feesSelected = $('#fees-selected option:selected');
                var feesOriList = $('#all-fees');

                feesOriList.append(feesSelected);
                feesSelected.prop('selected', false);
            });
        });
    </script>
@endsection