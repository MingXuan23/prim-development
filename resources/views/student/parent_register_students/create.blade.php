@extends('layouts.master')

@section('css')
    <link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @if (session("success"))
        <div class="alert alert-success" role="alert">
            <p class="text-center">{{ session("success") }}</p>
        </div>
    @endif

    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="page-title-box">
                <h4 class="font-size-18">Murid</h4>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item active">Yuran >> Daftar Pelajar</li>
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
            <form method="post" action="{{ route('student.parentRegisterStudents.store') }}" enctype="multipart/form-data">
                {{csrf_field()}}
                <div class="card-body">

                    <div class="form-row">
                        <div class="form-group col-md-6 required">
                            <label>Nama Organisasi</label>
                            <select name="organization" id="organization" class="form-control">
                                <option value="" selected disabled>Pilih Organisasi</option>
                                @foreach($organizations as $row)
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
                        <div class="form-group col-md-6">
                            <label>Nama Penuh Pelajar</label>
                            <input type="text" id="name" name="name" class="form-control" placeholder="Nama Penuh Murid">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Nombor Kad Pengenalan</label>
                            <input type="text" id="icno" name="icno" class="form-control"
                                placeholder="Nombor Kad Pengenalan">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Email Pelajar&nbsp(optional)</label>
                            <input type="email" id="email" name="email" class="form-control" placeholder="Email Pelajar">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Jantina</label>
                            <div class="radio">
                                <label class="radio-inline pl-2">
                                    <input type="radio" name="gender" class="gender" value="L"> Lelaki
                                </label>
                                <label class="radio-inline pl-2">
                                    <input type="radio" name="gender" class="gender" value="P"> Perempuan
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-row mb-4">
                        <div>
                            <button type="button" onclick="addStudent()"
                                class="btn btn-primary waves-effect waves-light mr-1">
                                Tambah Pelajar
                            </button>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="table-responsive">
                                    <table id="students-table" class="table table-bordered table-striped dt-responsive wrap"
                                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead>
                                            <tr style="text-align:center">
                                                <th>Nama</th>
                                                <th>Emel</th>
                                                <th>No. Kad Pengenalan</th>
                                                <th>Jantina</th>
                                                <th>Kelas</th>
                                                <th>Organisasi</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>

                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-row mt-2">
                        <div>
                            <button type="submit" disabled id="daftar-btn"
                                class="btn btn-primary waves-effect waves-light mr-1">
                                Daftar
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
        let currentRowIndex = 1;

        // function to add students
        function addStudent() {
            const studentNameInput = $('#name');
            const studentIcnoInput = $('#icno');
            const studentGenderInput = $('.gender:checked');
            const studentEmailInput = $("#email")
            const organizationInput = $('#organization');
            const studentClassInput = $('#classes');

            // checks if the email and icno is a duplicate of the already inserted student
            let isDuplicate = false;

            const studentsTable = $("#students-table tbody");

            const inputArray = [studentNameInput, studentEmailInput, studentIcnoInput, studentGenderInput, studentClassInput, organizationInput];
            const hiddenInputNamesArray = ["names[]", "emails[]", "icnos[]", "genders[]", "class_ids[]", "org_ids[]"];

            // check if all inputs are not empty
            if (inputArray.some(inp => inp !== studentEmailInput && (inp.val() == null || inp.val().trim() === ""))) {
                return alert("Sila isikan semua maklumat yang diperlukan.");
            }

            // check if the icnos are the same
            $(".student-icno").map((_, icno) => {
                if (studentIcnoInput.val() == icno.innerText) {
                    isDuplicate = true;
                    alert("No. kad pengenalan tersebut sudah digunakan.");
                }
            });

            // check if the emails are the same
            $(".student-email").map((_, email) => {
                if (studentEmailInput.val() == email.innerText) {
                    isDuplicate = true;
                    alert("Emel tersebut sudah digunakan.");
                }
            });

            if (isDuplicate) return;

            studentsTable.append("<tr id='" + currentRowIndex + "'></tr>");
            const studentTableRow = $("#" + currentRowIndex);

            // add student's info into table
            studentTableRow.append("<td class='text-center'>" + studentNameInput.val() + "</td>");
            studentTableRow.append("<td class='text-center student-email'>" + (studentEmailInput.val().trim() !== "" ? studentEmailInput.val() : "-") + "</td>");
            studentTableRow.append("<td class='text-center student-icno'>" + studentIcnoInput.val() + "</td>");
            studentTableRow.append("<td class='text-center'>" + studentGenderInput.val() + "</td>");
            studentTableRow.append("<td class='text-center'>" + $("#classes option:selected").text() + "</td>");
            studentTableRow.append("<td class='text-center'>" + $("#organization option:selected").text() + "</td>");

            // add remove button to remove student
            studentTableRow.append("<td class='text-center'>" +
                "<button type='button' onclick=\"removeStudent('" + currentRowIndex + "')\" class='btn btn-danger waves-effect waves-light mr-1'>Keluarkan</button>" +
                "</td>");

            hiddenInputNamesArray.forEach((inpName, index) => {
                studentTableRow.append("<input type='hidden' name='" + inpName + "' value='" + inputArray[index].val() + "' />");
            });

            // clear the inputs
            studentNameInput.val('');
            studentIcnoInput.val('');
            $(".gender").prop("checked", false);
            $("#classes").prop("selectedIndex", 0)
            studentEmailInput.val('');

            currentRowIndex++;

            // disable the organization select to ensure that users only select one organization
            // $("#organization").prop("disabled", true);

            // check if there are students in the table, if not disable the button
            if (studentsTable.children().length == 0) {
                $("#daftar-btn").prop("disabled", true);
            } else {
                $("#daftar-btn").prop("disabled", false);
            }
        }

        function removeStudent(studentIcno) {
            const individualStudentInfo = $("#" + studentIcno);

            individualStudentInfo.remove();

            // check if the list is empty, if yes, enable the selection of organization
            if ($("#students-table tbody").children().length == 0) {
                // $("#organization").prop("disabled", false);
                $("#daftar-btn").prop("disabled", true);
            }
        }

        $(document).ready(function () {
            $('#icno').mask('000000-00-0000');
            $('#parent_icno').mask('000000-00-0000');
            $('#parent_phone').mask('000000000000');

            fetchClass($("#organization").val());

            // re-enable the organization select
            // $("form").on("submit", function () {
            //     $("#organization").prop("disabled", false);
            // });

            $('#organization').change(function () {
                if ($(this).val() != '') {
                    var organizationid = $("#organization option:selected").val();
                    var _token = $('input[name="_token"]').val();
                    $.ajax({
                        url: "{{ route('parent.fetchClass') }}",
                        method: "POST",
                        data: {
                            oid: organizationid,
                            _token: _token
                        },
                        success: function (result) {

                            $('#classes').empty();
                            $("#classes").append("<option value='' disabled selected> Pilih Kelas</option>");
                            jQuery.each(result.success, function (key, value) {
                                $("#classes").append("<option value='" + value.cid + "'>" + value.cname + "</option>");
                            });
                        }

                    })
                }
            });

            function fetchClass(organizationid = '') {
                var _token = $('input[name="_token"]').val();
                $.ajax({
                    url: "{{ route('student.fetchClass') }}",
                    method: "POST",
                    data: {
                        oid: organizationid,
                        _token: _token
                    },
                    success: function (result) {
                        $('#classes').empty();
                        $("#classes").append("<option value='' disabled selected> Pilih Kelas</option>");
                        jQuery.each(result.success, function (key, value) {
                            $("#classes").append("<option value='" + value.cid + "'>" + value.cname + "</option>");
                        });
                    }
                })
            }
        });
    </script>
@endsection