@extends('layouts.master')
@include('layouts.datatable')
@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Laporan Jenis Yuran</h4>
        </div>
    </div>
</div>
<div class="container">

    <div class="row d-flex justify-content-center">
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
                        <label> Yuran </label>
                        <select name="fees" id="fees" class="form-control">
                            <option value="0" disabled selected>Pilih Yuran</option>
                        </select>
                    </div>

                </div>
            </div>
        </div>

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
@endsection


@section('script')
<!-- Peity chart-->
<script src="{{ URL::asset('assets/libs/peity/peity.min.js')}}"></script>

<!-- Plugin Js-->
<script src="{{ URL::asset('assets/libs/chartist/chartist.min.js')}}"></script>

<script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script>

<script>
    $(document).ready(function(){
        
        if($("#organization").val() != ""){
            $("#organization").prop("selectedIndex", 1).trigger('change');
            fetch_data($("#organization").val());
        }

        function fetch_data(oid = ''){ 
            var _token = $('input[name="_token"]').val();
                $.ajax({
                    url:"{{ route('fees.fetchClassForCateYuran') }}",
                    method:"POST",
                    data:{ oid:oid,
                            _token:_token },
                    success:function(result)
                    {
                        $('#classes').empty();
                        $("#classes").append("<option value='0'> Pilih Kelas</option>");    
                        jQuery.each(result.success, function(key, value){
                            $("#classes").append("<option value='"+ value.cid +"'>" + value.cname + "</option>");
                        });
                    }   
                })    
        }

        $('#classes').change(function(){
            if($(this).val() != '')
            {
                var classid   = $("#classes option:selected").val();
                var _token    = $('input[name="_token"]').val();
            
                console.log(classid);
                $.ajax({
                    url:"{{ route('fees.fetchYuran') }}",
                    method:"POST",
                    data:{ 
                        classid: classid,
                        oid : $("#organization").val(),
                        _token: _token 
                    },
                    success:function(result)
                    {
                        $('#fees').empty();
                        $("#fees").append("<option value='0'> Pilih Yuran</option>");
                        
                        jQuery.each(result.success, function(key, value){
                            $("#fees").append("<option value='"+ value.id +"'>" + value.name + "</option>");
                        });
                    }
                })
            }
        });

        $('#organization').change(function(){
            if($(this).val() != '')
            {
                fetch_data($("#organization").val());
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
                                classid: $("#classes").val()
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
</script>
@endsection