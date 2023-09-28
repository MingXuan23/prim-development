@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
<style>
.heading{
    float:left;
    margin-bottom:6px;
}

#addAllNewStudent,#transferAllStudent{
    float:right;
    margin-bottom:6px;
}
</style>
@include('layouts.datatable')

@endsection

@section('content')
<!-- {{-- <p>Welcome to this beautiful admin panel.</p> --}} -->
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Pelajar</h4>
            <!-- <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Welcome to Veltrix Dashboard</li>
            </ol> -->
            @if(count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                        <li>{{$error}}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                @if(\Session::has('success'))
                <div class="alert alert-success">
                    <p>{{ \Session::get('success') }}</p>
                </div>
                @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <div id="head">
                        <h4 class="heading">Pelajar Baharu </h4>
                        @if(count($newStudents)>0)
                        <button id="addAllNewStudent" class="btn btn-primary align-right">Tambah Semua Pelajar Baharu</button>
                        @endif
                    </div>
                    <table id="studentTable" class="table table-bordered table-striped dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr style="text-align:center">
                                <th>No</th>
                                <th>Nama Penuh</th>
                                {{-- <th>Nombor Kad pengenalan</th> --}}
                                <th>Kelas</th>
                                <th>Nama Penjaga</th>
                                <th>Tel No Penjaga</th>
                                <th>Email Penjaga</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($newStudents as $row)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{$row->studentName}}</td>
                                <td>{{$row->gender}}</td>
                                <td>{{$row->parentName}}</td>
                                <td>{{$row->parentTelno}}</td>
                                <td>{{$row->parentEmail}}</td>
                                <td><button class="btn btn-primary addNewStudent" studentNo="{{$loop->iteration - 1}}">Tambah</button></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                <div id="head">
                        <h4 class="heading">Pelajar Yang Dalam Kelas Lain</h4>
                        @if(count($differentClassStudents)>0)
                        <button id="transferAllStudent" class="btn btn-primary align-right">Pindah Semua Pelajar</button>
                        @endif
                    </div>
                    <table id="studentTable" class="table table-bordered table-striped dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr style="text-align:center">
                                <th>No</th>
                                <th>Nama Penuh</th>
                                {{-- <th>Nombor Kad pengenalan</th> --}}
                                <th>Kelas</th>
                                <th>Nama Penjaga</th>
                                <th>Tel No Penjaga</th>
                                <th>Email Penjaga</th>
                                <th>Kelas</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($differentClassStudents as $row)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{$row->studentName}}</td>
                                <td>{{$row->gender}}</td>
                                <td>{{$row->parentName}}</td>
                                <td>{{$row->parentTelno}}</td>
                                <td>{{$row->parentEmail}}</td>
                                <td>{{$row->oldClassName}}</td>
                                <td><button class="btn btn-primary transferStudent" studentNo="{{$loop->iteration - 1}}">Pindah</button></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <h4>Pelajar Yang Dalam Kelas Sama</h4>
                    <table id="studentTable" class="table table-bordered table-striped dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr style="text-align:center">
                                <th>No</th>
                                <th>Nama Penuh</th>
                                {{-- <th>Nombor Kad pengenalan</th> --}}
                                <th>Kelas</th>
                                <th>Nama Penjaga</th>
                                <th>Tel No Penjaga</th>
                                <th>Email Penjaga</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sameClassStudents as $row)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{$row->studentName}}</td>
                                <td>{{$row->gender}}</td>
                                <td>{{$row->parentName}}</td>
                                <td>{{$row->parentTelno}}</td>
                                <td>{{$row->parentEmail}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <h4>Pelajar Yang Di Sekolah Lain</h4>
                    <!-- @if(count($differentOrgStudents)>0)
                        <button id="transferOutsideStudent" class="btn btn-primary align-right">Pindah Semua Pelajar</button>
                    @endif -->
                    <table id="studentTable" class="table table-bordered table-striped dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr style="text-align:center">
                                <th>Nama Penuh</th>
                                {{-- <th>Nombor Kad pengenalan</th> --}}
                                <th>Kelas</th>
                                <th>Nama Penjaga</th>
                                <th>Tel No Penjaga</th>
                                <th>Email Penjaga</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($differentOrgStudents as $row)
                            <tr>
                                <td>{{$row->studentName}}</td>
                                <td>{{$row->gender}}</td>
                                <td>{{$row->parentName}}</td>
                                <td>{{$row->parentTelno}}</td>
                                <td>{{$row->parentEmail}}</td>
                            </tr>
                            @endforeach
                        </tbody>
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
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var number=0;
    $(document).ready(function() {
        $('.addNewStudent').on('click', function() {
            document.querySelectorAll('button').forEach(button => button.disabled = true);
            const studentNo = $(this).attr('studentNo');
            const newStudents = @json($newStudents);

            var student =JSON.stringify(newStudents[studentNo]);
            number=0;
            addStudent(student,1);
            location.reload();
        });

        $('.transferStudent').on('click', function() {
            document.querySelectorAll('button').forEach(button => button.disabled = true);
            const studentNo = $(this).attr('studentNo');
            const difStudents = @json($differentClassStudents);
            
            var student =JSON.stringify(difStudents[studentNo]);
            number=0;
            //console.log(student);
            transferStudent(student,1);
            //location.reload();
        });

        $('#addAllNewStudent').on('click', function() {
            document.querySelectorAll('button').forEach(button => button.disabled = true);
            const newStudents = @json($newStudents);
            newStudents.forEach(function(Student) {
                // Access properties of the student object

                var student =JSON.stringify(Student);
                number=0;
                addStudent(student,newStudents.length);
               
            });
            //location.reload();
        });

        $('#transferAllStudent').on('click', function() {
            document.querySelectorAll('button').forEach(button => button.disabled = true);
            const difStudents = @json($differentClassStudents);
            difStudents.forEach(function(Student) {
                // Access properties of the student object

                var student =JSON.stringify(Student);
                number=0;
                transferStudent(student,difStudents.length);
               
            });
            //location.reload();
        });

        $('#transferOutsideStudent').on('click', function() {
            document.querySelectorAll('button').forEach(button => button.disabled = true);
            const difStudents = @json($differentOrgStudents);
            difStudents.forEach(function(Student) {
                // Access properties of the student object

                var student =JSON.stringify(Student);
                number=0;
                transferStudent(student,difStudents.length);
               
            });
            //location.reload();
        });
    });

    function addStudent(student,n){
        $.ajax({
                type: 'GET',
                url: '{{ route("student.compareAddNewStudent") }}',
                data: {
                    student: student,
                },
                success:function(response){
                    //console.log(JSON.stringify(response.data));
                    number++;
                    //console.log("success",n)
                    if(n===number)
                        location.reload();
                }
            });    
    }

    function transferStudent(student,n){
        $.ajax({
                type: 'GET',
                url: '{{ route("student.compareTransferStudent") }}',
                data: {
                    student: student,
                },
                success:function(response){
                    //console.log(JSON.stringify(response.data));
                    // console.log(response);
                    number++;
                    //console.log("success",n)
                    if(n===number)
                        location.reload();
                }
            });    
    }
</script>

@endsection