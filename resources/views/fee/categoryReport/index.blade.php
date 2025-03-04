@extends('layouts.master')
@include('layouts.datatable')
@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
<style>
    .errorMessage{
        color:red;
    }
</style>
@endsection

@section('content')

<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Laporan Jenis Yuran</h4>
        </div>
    </div>
</div>

<div class="row">
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
            
            @if(\Session::has('success'))
            <div class="alert alert-success">
                <p>{{ \Session::get('success') }}</p>
            </div>
            @endif
            
            <div class="flash-message"></div>
            
            <div class="card-body">

                <div class="form-group">
                    <label>Organisasi</label>
                    <select name="organization" id="organization" class="form-control">
                        <option value="" selected>Pilih Organisasi</option>
                        @foreach($organization as $row)
                        <option value="{{ $row->id }}">{{ $row->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="dkelas" class="form-group">
                    <label> Kelas </label>
                    <select name="classes" id="classes" class="form-control">
                        <option value="0" disabled selected>Pilih Kelas</option>
                    </select>
                </div>

                <div id="yuran" class="form-group">
                    <label> Tahun </label>
                    <select name="fee_year" id="fee_year" class="form-control">
                        <option value="0" disabled selected>Pilih Tahun</option>
                    </select>
                </div>

                <div id="yuran" class="form-group">
                    <label> Yuran </label>
                    <select name="fees" id="fees" class="form-control">
                        <option value="0" disabled selected>Pilih Yuran</option>
                    </select>
                </div>

            </div>

            <div class="col-md-12">
                <a style="float: right; margin: 0px 0px 10px 10px" class="btn btn-primary"  data-toggle="modal" data-target="#modalJumlahBayaran"><i class="fas fa-plus"></i> Export Jumlah Bayaran Ibu/Bapa</a>
                <a style="float: right; margin: 0px 0px 10px 10px" class="btn btn-success"  data-toggle="modal" data-target="#modalByYuran"><i class="fas fa-plus"></i> Export Yuran</a>
            </div>

            <div class="col-md-12">
                <div class="card">
                    <div class="table-responsive">
                        <table id="yuranTable" class="table table-bordered table-striped dt-responsive wrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr style="text-align:center">
                                    <th>No</th>
                                    <th>Nama Murid</th>
                                    <th>Jantina</th>
                                    <th>Status Pembayaran</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- modal export yuran --}}
<div class="modal fade" id="modalByYuran" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Export Yuran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="{{ route('exportAllYuranStatus') }}" method="post">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label>Organisasi</label>
                        <select name="organExport" id="organExport" class="form-control" >
                            <option value="" disabled selected>Pilih Organisasi</option>
                            @foreach($organization as $row)
                                <option value="{{ $row->id }}">{{ $row->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="yuran" class="form-group">
                        <label> Tahun </label>
                        <select name="fee_year" id="fee_year_export" class="form-control">
                            <option value="0" disabled selected>Pilih Tahun</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nama Yuran</label>
                        <select name="yuranExport" id="yuranExport" class="form-control">

                        </select>
                    </div>
                    <div class="modal-footer">
                        <button id="buttonExport" type="submit" class="btn btn-primary">Export</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalJumlahBayaran" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true" >
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Export Jumlah Bayaran Ibu/Bapa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="{{ route('exportJumlahBayaranIbuBapa') }}" method="post" onsubmit="remindMessage()" id="exportJumlahBayaranIbuBapaForm">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label>Organisasi</label>
                        <select name="organExport1" id="organExport1" class="form-control">
                            <option value="" disabled selected>Pilih Organisasi</option>
                            @foreach($organization as $row)
                                <option value="{{ $row->id }}">{{ $row->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nama Kelas</label>
                        <select name="yuranExport1" id="yuranExport1" class="form-control" required>

                        </select>
                    </div>

                    <div class="form-row">
                    <div class="form-group col-md-12 required">
                        <label class="control-label">Tempoh Transaksi</label>

                        <div class="input-daterange input-group" id="date">
                            <input type="text" class="form-control" id="date_started" name="date_started" placeholder="Tarikh Awal"
                                autocomplete="off" data-parsley-required-message="Sila masukkan tarikh awal"
                                data-parsley-errors-container=".errorMessage" required />
                            <input type="text" class="form-control"  id="date_end" name="date_end" placeholder="Tarikh Akhir"
                                autocomplete="off" data-parsley-required-message="Sila masukkan tarikh akhir"
                                data-parsley-errors-container=".errorMessage" required />
                        </div>
                        <div class="errorMessage"></div>
                    </div>
                </div>
                    <div class="modal-footer">
                        <button id="buttonExport" type="submit" class="btn btn-primary" >Export</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection


@section('script')
<!-- Peity chart-->
<script src="{{ URL::asset('assets/libs/peity/peity.min.js')}}"></script>

<!-- Plugin Js-->
<script src="{{ URL::asset('assets/libs/chartist/chartist.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/parsleyjs/parsleyjs.min.js')}}"></script>
<script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script>
<script src="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}" defer></script>

<script>
    $(document).ready(function(){

        function validateDateRange() {
            var startDate = $('#date_started').datepicker('getDate');
            var endDate = $('#date_end').datepicker('getDate');

            // Check if both dates are selected and validate the date range
            if (startDate && endDate) {
                if (endDate < startDate) {
                    // Clear end date and display error message
                    $('#date_end').val('');
                    $('.errorMessage').text('Tarikh Akhir mesti selepas Tarikh Awal');
                } else {
                    // Clear error message
                    $('.errorMessage').text('');
                }
            }
        }

       
        $('#modalJumlahBayaran').on('shown.bs.modal', function () {

            $('#date .form-control').datepicker({
                toggleActive: true,
                todayHighlight:true,
                format: 'yyyy-mm-dd',
                autoclose: true,
                container: '#modalJumlahBayaran .modal-body'
            });

            $('#date_started, #date_end').off('change').on('change', function() {
                // Call validateDateRange function when either datepicker changes
                validateDateRange();
            });


        });

       


        function fetchClass(organizationid = '', yuranId = '', year=''){
            var _token = $('input[name="_token"]').val();
            var fee_year = year ?? $('#fee_year').val();
            $.ajax({
                url:"{{ route('fees.fetchYuranByOrganId') }}",
                method:"POST",
                data:{ oid:organizationid,
                        _token:_token,
                        fee_year: fee_year},
                success:function(result)
                {
                    $(yuranId).empty();
                    $(yuranId).append("<option value='0' selected>Semua Yuran</option>");
                    jQuery.each(result.success, function(key, value){
                        $(yuranId).append("<option value='"+ value.id +"'>" + value.name + "</option>");
                    });
                }
            })
        }

        function fetchYear(organizationid = ''){
            var _token = $('input[name="_token"]').val();
            var fee_year = $('#fee_year').val();
            $.ajax({
                url:"{{ route('fees.fetchYuranByOrganId') }}",
                method:"POST",
                data:{ oid:organizationid,
                        _token:_token,
                        fee_year :fee_year },
                success:function(result)
                {
                    $(yuranId).empty();
                    $(yuranId).append("<option value='0' selected>Semua Yuran</option>");
                    jQuery.each(result.success, function(key, value){
                        $(yuranId).append("<option value='"+ value.id +"'>" + value.name + "</option>");
                    });
                }
            })
        }

        $('#organExport').change(function() {
            var organizationid    = $("#organExport").val();
            var _token            = $('input[name="_token"]').val();
            fetch_data_year(organizationid)
            fetchClass(organizationid, '#yuranExport',$('#fee_year_export').val());
        });

       $('#fee_year_export').change(function(){
        var organizationid    = $("#organExport").val();
            fetchClass(organizationid, '#yuranExport',$('#fee_year_export').val());
        
       })

        $('#organExport1').change(function() {
            var organizationid    = $("#organExport1").val();
            var _token            = $('input[name="_token"]').val();
            fetch_data(organizationid, '#yuranExport1');
        });
        
        if($("#organization").val() == ""){
            $("#organization").prop("selectedIndex", 1).trigger('change');
            fetch_data($("#organization").val(), '#classes');
        }

        function fetch_data(oid = '', classId = ''){ 
            var _token = $('input[name="_token"]').val();
                $.ajax({
                    url:"{{ route('fees.fetchClassForCateYuran') }}",
                    method:"POST",
                    data:{ oid:oid,
                            _token:_token },
                    success:function(result)
                    {
                        $(classId).empty();
                        $(classId).append("<option value='0'> Semua Kelas</option>");    
                        jQuery.each(result.success, function(key, value){
                            $(classId).append("<option value='"+ value.cid +"'>" + value.cname + "</option>");
                        });

                        $('#fee_year').empty();
                        jQuery.each(result.years, function(key, value){
                            $('#fee_year').append("<option value='"+ value.year +"'>Tahun " + value.year + "</option>");
                        });
                    }   
                })    
        }

        function fetch_data_year(oid = ''){ 
            var _token = $('input[name="_token"]').val();
                $.ajax({
                    url:"{{ route('fees.fetchClassForCateYuran') }}",
                    method:"POST",
                    data:{ oid:oid,
                            _token:_token },
                    success:function(result)
                    {
                       
                        $('#fee_year_export').empty();
                        jQuery.each(result.years, function(key, value){
                            $('#fee_year_export').append("<option value='"+ value.year +"'>Tahun " + value.year + "</option>");
                        });
                    }   
                })    
        }

        $('#classes').change(function(){
            if($(this).val() != '')
            {
                var classid   = $("#classes option:selected").val();
                var _token    = $('input[name="_token"]').val();
                var fee_year = $("#fee_year").val();
                //console.log(classid);
                $.ajax({
                    url:"{{ route('fees.fetchYuran') }}",
                    method:"POST",
                    data:{ 
                        classid: classid,
                        oid : $("#organization").val(),
                        _token: _token,
                        fee_year:fee_year 
                    },
                    success:function(result)
                    {
                        $('#fees').empty();
                        $("#fees").append("<option value='0'>Pilih Yuran</option>");
                        
                        jQuery.each(result.success, function(key, value){
                            $("#fees").append("<option value='"+ value.id +"'>" + value.name + "</option>");
                        });
                    }
                })
            }
        });

        $('#fee_year').change(function(){
            $('#classes').trigger('change');
        });

        $('#organization').change(function(){
            if($(this).val() != '')
            {
                fetch_data($("#organization").val(), "#classes");
            }
        });
        
        $('#fees').change(function(){
            if($(this).val() != 0){
                $('#yuranTable').DataTable().destroy();

                var yuranTable = $('#yuranTable').DataTable({
                    ordering: true,
                    processing: true,
                    serverSide: true,
                        ajax: {
                            url: "{{ route('fees.debtDatatable') }}",
                            type: 'GET',
                            data: {
                                feeid: $("#fees").val(),
                                classid: $("#classes").val(),
                                orgId:$("#organization").val()
                            }
                        },
                        'columnDefs': [{
                              "targets": [0, 1, 2, 3], // your case first column
                              "className": "text-center",
                              "width": "2%"
                          }],
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
                            name: "nama",
                            "width": "20%"
                        },
                        {
                            data: "gender",
                            name: "gender",
                            "width": "10%"
                        },{
                            data: "status",
                            name: "status",
                            "width": "10%"
                        }]
                  });
            }
        });

        // csrf token for ajax
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.alert').delay(3000).fadeOut();
    });

    function remindMessage(){
        //document.querySelectorAll('#buttonExport').forEach(button => button.disabled = true);
        if( $('#yuranExport1').val()==0)
            alert("To download all data may take more time,dont refresh the page");
    }
</script>
@endsection