@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@include('layouts.datatable')

@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Carian Laporan</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            {{-- <div class="card-header">List Of Applications</div> --}}
            {{-- <div>
                <a style="margin: 19px; float: right;" id="btn-download" class="btn btn-primary"> <i
                        class="fas fa-download"></i> Muat Turun PDF</a>
            </div> --}}

            <div class="card-body">
                {{csrf_field()}}
                <div class="card-body">

                    <div class="form-group">
                        <label>Nama Organisasi</label>
                        <select name="organization" id="organization" class="form-control">
                            <option value="" selected disabled>Pilih Organisasi</option>
                            @foreach($organization as $row)
                            <option value="{{ $row->id }}">{{ $row->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="dkelas" class="form-group">
                        <label> Kelas</label>
                        <select name="classes" id="classes" class="form-control">
                            <option value="" disabled selected>Pilih Kelas</option>

                        </select>
                    </div>
                </div>
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

                <div class="table-responsive">
                    <table id="studentTable" class="table table-bordered table-striped dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr style="text-align:center">
                                <th> No. </th>
                                <th>Nama Penuh</th>
                                <th>Jantina</th>
                                <th>Tarikh Daftar</th>
                                <th>Senarai Yuran</th>
                                <th>Status Yuran</th>
                            </tr>
                        </thead>
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
        
        var studentTable;

        if($("#organization").val() != ""){
            $("#organization").prop("selectedIndex", 1).trigger('change');
            fetchClass($("#organization").val());
        }

        $('#studentTable').on('draw.dt', function() {
            $('[data-toggle="tooltip"]').tooltip();
        })
        
        // fetch_data();
        // alert($("#organization").val());

            function fetch_data(cid = '') {
                //console.log($("#organization").val());
                studentTable = $('#studentTable').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                            url: "{{ route('fees.getStudentSwastaDatatableFees') }}",
                            data: {
                                classid: cid,
                                orgId : $("#organization").val(),
                                hasOrganization: true
                            },
                            type: 'GET',

                        },
                        'columnDefs': [{
                            "targets": [0], // your case first column
                            "className": "text-center",
                            "width": "2%"
                        },{
                            "targets": [2,3,4,5], // your case first column
                            "className": "text-center",
                        },],
                        order: [
                            [1, 'asc']
                        ],
                        columns: [{
                            "data": null,
                            searchable: false,
                            "sortable": false,
                            render: function (data, type, row, meta) {
                                return meta.row + meta.settings._iDisplayStart + 1;
                            }
                        }, {
                            data: "nama",
                            name: 'nama'
                        }, {
                            data: "gender",
                            name: 'gender'
                        }, {
                            data: 'cs_startdate',
                            name: 'cs_startdate',
                            searchable: false,
                            render: function(data, type, full) {
                                if (data) {
                                    var formattedDate = new Date(data);
                                    var day = formattedDate.getDate();
                                    var month = formattedDate.getMonth() + 1; // Months are zero-based
                                    var year = formattedDate.getFullYear();

                                    // Ensure leading zeros for day and month if needed
                                    if (day < 10) {
                                        day = '0' + day;
                                    }
                                    if (month < 10) {
                                        month = '0' + month;
                                    }

                                    return day + '/' + month + '/' + year;
                                } else {
                                    return '';
                                }
                            }
                        }, {
                            data: 'yuran',
                            name: 'yuran',
                            orderable: false,
                            searchable: false
                        }, {
                            data: 'status',
                            name: 'status',
                            orderable: false,
                            searchable: false
                        },]
                });
            }

            $('#organization').change(function() {
               
                var organizationid    = $("#organization").val();
                var _token            = $('input[name="_token"]').val();

                fetchClass(organizationid);
                
            });

            function fetchClass(organizationid = ''){
                var _token            = $('input[name="_token"]').val();
                $.ajax({
                    url:"{{ route('student.fetchClass') }}",
                    method:"POST",
                    data:{ oid:organizationid,
                            _token:_token },
                    success:function(result)
                    {
                        $('#classes').empty();
                        $("#classes").append("<option value='' disabled selected> Pilih Kelas</option>");
                        jQuery.each(result.success, function(key, value){
                            // $('select[name="kelas"]').append('<option value="'+ key +'">'+value+'</option>');
                            $("#classes").append("<option value='"+ value.cid +"'>" + value.cname + "</option>");
                        });
                    }
                })
            }

            function downloadPDF(cid = ''){
                $.ajax({
                    url:"{{ route('fees.generatePDFByClass') }}",
                    method:"GET",
                    data:{ 
                        class_id:cid,
                    },
                    success:function(result)
                    {
                        
                    }
                })
            }

            $('#classes').change(function() {
                var organizationid    = $("#organization option:selected").val();

                var classid    = $("#classes option:selected").val();
                if(classid){
                    $('#studentTable').DataTable().destroy();
                    fetch_data( classid);
                    downloadPDF(classid);
                }
                // console.log(organizationid);
            });

            // csrf token for ajax
            $.ajaxSetup({
                    headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

                $('.alert').delay(3000).fadeOut();

        });
        
        
</script>

@endsection