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
            <form class="form-validation" method="POST" action="{{ route('fees.assignStudentsToFeeUpdate') }}"
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
                        <label>Kategori Yuran</label>
                        <input type="text" name="category" class="form-control" value="{{ $currentFee['fee_category'] }}"
                            readonly>
                    </div>


                    <div class="form-group">
                        <label>Nama Yuran</label>
                        <input type="hidden" name="fee_id" id="fee-id" value="{{ $currentFee['fee_id'] }}">
                        <input type="text" name="name" class="form-control" value="{{ $currentFee['fee_name'] }}" readonly>
                    </div>

                    <div class="form-group">
                        <label>Kelas</label>
                        <select name="classes" id="classes" class="form-control">

                        </select>
                    </div>


                    {{-- --------------------------Assign students to yuran-------------------------- --}}
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Senarai Pelajar</label>
                            <select name="students[]" id="students" multiple class="form-control">

                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="students_selected">Pelajar Dipilih Untuk Bayar Yuran</label>
                            <select name="students_selected[]" id="students-selected" multiple class="form-control">

                            </select>
                        </div>
                    </div>
                    {{-- --------------------------End of assigning students to yuran-------------------------- --}}

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
                $("#students-selected option").prop("selected", true);
                $("#students-selected option").prop("disabled", false);
            })

            // ************************** get all classes by current organization id ********************************
            $.ajax({
                url: "{{ route('class.getClassesDatatable') }}",
                method: "GET",
                data: {
                    oid: $("#organization-id").val(),
                    hasOrganization: true
                },
                success: function (result) {
                    var classes = $("#classes");

                    classes.empty();
                    classes.append("<option value='' selected disabled>Sila pilih kelas</option>")

                    result.data.forEach(classObj => {
                        classes.append("<option value='" + classObj.cid + "'>" + classObj.cnama + "</option>")
                    });
                }
            });

            // ************************** get all selected students for current fee based on class ********************************
            $("#classes").on("change", function () {
                var allStudentsList = $("#students");
                var selectedStudentsList = $("#students-selected");

                selectedStudentsList.empty();
                allStudentsList.empty();

                var selectedStudentsData = $.ajax({
                    url: "{{ route('fees.fetchOneFeeToManyStudentsJson') }}",
                    method: "GET",
                    data: {
                        oid: $("#organization-id").val(),
                        feeId: $("#fee-id").val(),
                        classId: $("#classes").val()
                    }
                });

                var allStudentsData = $.ajax({
                    url: "{{ route('fees.getstudentDatatable') }}",
                    method: "GET",
                    data: {
                        class_id: $("#classes").val(),
                        status: null
                    }
                });

                $.when(selectedStudentsData, allStudentsData).done(function (selectedStudentsData, allStudentsData) {
                    if (selectedStudentsData[0][0].students[0].student_id != null) {
                        selectedStudentsData[0][0].students.forEach(function (student) {
                            if (student.student_fee_status === "Paid") {
                                selectedStudentsList.append("<option disabled selected value='" + student.student_id + "'>" + student.student_name + "</option>");
                            } else {
                                selectedStudentsList.append("<option value='" + student.student_id + "'>" + student.student_name + "</option>")
                            }
                        });
                    }

                    var selectedStudentsId = $("#students-selected option").map(function () {
                        return parseInt($(this).val());
                    }).get();

                    allStudentsData[0].data.forEach(student => {
                        if (student.id != null && !selectedStudentsId.includes(student.id)) {
                            allStudentsList.append("<option value = '" + student.id + "'>" + student.nama + "</option>");
                        }
                    });
                })
            });

            // ************************** move students to selected list and vice versa ********************************
            $('#students').on('change', function () {
                // callback function to add students to pelajar dipilih section

                var studentsSelected = $('#students option:selected');
                var studentsSelectedList = $('#students-selected');

                studentsSelectedList.append(studentsSelected);
                studentsSelected.prop('selected', false);
            });

            $('#students-selected').on('change', function () {
                // callback function to add students back to original pelajar list section

                var studentsSelected = $('#students-selected option:selected');
                var studentsOriList = $('#students');

                studentsOriList.append(studentsSelected);
                studentsSelected.prop('selected', false);
            });
        });
    </script>
@endsection