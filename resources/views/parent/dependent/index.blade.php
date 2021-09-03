@extends('layouts.master')
@include('layouts.datatable');
@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Ibubapa/Penjaga</h4>
            <!-- <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Welcome to Veltrix Dashboard</li>
            </ol> -->
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
                
                <form method="post" action="{{ route('parent.storeDependent')}}" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <div class="card-body">

                        <div class="form-group">
                            <label>Nama Sekolah</label>
                            <select name="organization" id="organization" class="form-control">
                                <option value="" selected>Pilih Sekolah</option>
                                @foreach($organization as $row)
                                <option value="{{ $row->id }}">{{ $row->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <input type="hidden" name="parentid" value="{{ $userId }}">
                        </div>

                        <div id="dkelas" class="form-group">
                            <label> Kelas</label>
                            <select name="classes" id="classes" class="form-control">
                                <option value="0" disabled selected>Pilih Kelas</option>

                            </select>
                        </div>

                        <div id="dmurid" class="form-group">
                            <label> Murid</label>
                            <select name="student" id="student" class="form-control">
                                <option value="0" disabled selected>Pilih Murid</option>
                            </select>
                        </div>

                    </div>

                    <div class="">
                        <button style="margin: 19px; float: right;" type="submit" class="btn btn-primary"><i class="fas fa-save"></i>Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table id="dependentTable" class="table table-bordered table-striped dt-responsive wrap"
                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                <thead>
                    <tr style="text-align:center">
                        <th>No</th>
                        <th>Nama Tanggungan</th>
                        <th>Sekolah</th>
                        <th>Kelas</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
{{-- confirmation delete modal --}}
<div id="deleteConfirmationModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Padam Tanggungan</h4>
            </div>
            <div class="modal-body">
                Adakah anda pasti?
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-primary" id="delete"
                    name="delete">Padam</button>
                <button type="button" data-dismiss="modal" class="btn">Batal</button>
            </div>
        </div>
    </div>
</div>
{{-- end confirmation delete modal --}}
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
            var _token      = $('input[name="_token"]').val();
                // console.log(schoolid);   
                $.ajax({
                    url:"{{ route('parent.fetchClass') }}",
                    method:"POST",
                    data:{ oid:oid,
                            _token:_token },
                    success:function(result)
                    {
                        $('#classes').empty();
                        $("#classes").append("<option value='0'> Pilih Kelas</option>");    
                        jQuery.each(result.success, function(key, value){
                            // $('select[name="kelas"]').append('<option value="'+ key +'">'+value+'</option>');
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
                    url:"{{ route('parent.fetchStd') }}",
                    method:"POST",
                    data:{ cid: classid,
                            _token: _token },
                    success:function(result)
                    {
                        // $('#murid').val("0");
                        $('#student').empty();
                        $("#student").append("<option value='0'> Pilih Murid</option>");
                        
                        jQuery.each(result.success, function(key, value){
                            $("#student").append("<option value='"+ value.sid +"'>" + value.namestd + "</option>");
                        
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
        
        var dependentTable = $('#dependentTable').DataTable({
            ordering: true,
            processing: true,
            serverSide: true,
                ajax: {
                    url: "{{ route('parent.getDependentDataTable') }}",
                    type: 'GET',
                },
                'columnDefs': [{
                      "targets": [0], // your case first column
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
                    data: "sekolah",
                    name: "sekolah",
                    "width": "10%"
                }, {
                    data: "kelas",
                    name: "kelas",
                    "width": "10%"
                }, {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    "width": "10%"
                },]
          });

        // csrf token for ajax
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var ouid;

        $(document).on('click', '.btn-danger', function(){
            ouid = $(this).attr('id');
            $('#deleteConfirmationModal').modal('show');
        });

        var url = "{{route('parent.deleteDependent',  ':ouid')}}";
          $('#delete').click(function() {
            $.ajax({
                type: 'POST',
                dataType: 'html',
                data: {
                    "_token": "{{ csrf_token() }}",
                    _method: 'DELETE'
                },
                url: url.replace(':ouid', ouid),
                beforeSend: function() {
                    $('#delete').text('Padam...');
                },
                success: function(data) {
                    setTimeout(function() {
                        $('#confirmModal').modal('hide');
                    }, 2000);

                    $('div.flash-message').html(data);

                    dependentTable.ajax.reload();
                },
                error: function (data) {
                    $('div.flash-message').html(data);
                }
            })
        });

        $('.alert').delay(3000).fadeOut();

    });
</script>
@endsection