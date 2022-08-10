@extends('layouts.master')
<!-- display不到data -->
@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@include('layouts.datatable')

@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Outing</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
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


            </div>

            {{-- <div class="">
                <button onclick="filter()" style="float: right" type="submit" class="btn btn-primary">
                <i class="fa fa-search"></i>Tapis</button>
            </div> --}}

        </div>
    </div>
    
    <div class="col-md-12">
        <div class="card">
            {{-- <div class="card-header">Senarai Tarikh Outing</div> --}}
            <div>
                <a style="margin: 19px;" href="#" class="btn btn-primary" data-toggle="modal" data-target="#modelId"> <i
                        class="fas fa-plus"></i> Import</a>
                <a style="margin: 1px;" href="#" class="btn btn-success" data-toggle="modal" data-target="#modelId1"> <i
                class="fas fa-plus"></i> Export</a>

                {{-- href="{{ route('dorm.createOuting') }}" {{ route('exportkelas') }}--}}
                <a style="margin: 19px; float: right;" href="{{ route('dorm.createOuting') }}" class="btn btn-primary"> <i
                        class="fas fa-plus"></i> Tambah Outing</a>
            </div>

            <div class="card-body">

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
                    <table id="outingTable" class="table table-bordered table-striped dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr style="text-align:center">
                                <th> No. </th>
                                <th>Tarikh dan Masa Keluar</th>
                                <th>Tarikh dan Masa Masuk</th>
                                <th>Details</th>
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
                        <h4 class="modal-title">Padam Tarikh Outing</h4>
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

        <!-- Modal -->
        <!-- <div class="modal fade" id="modelId1" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Export Kelas</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    {{-- {{ route('exportclass') }} --}}
                    <form action="{{ route('exportclass') }}" method="post">
                        <div class="modal-body">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label>Organisasi</label>
                                <select name="organ" id="organ" class="form-control">
                                    @foreach($organization as $row)
                                        <option value="{{ $row->id }}" selected>{{ $row->nama }}</option>
                                    @endforeach
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
        <div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Import Kelas</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    {{-- {{ route('importkelas')}} --}}
                    <form action="{{ route('importclass') }}" method="post" enctype="multipart/form-data">
                        <div class="modal-body">

                            {{ csrf_field() }}
                            {{-- <div class="form-group">
                                            <label>Nama Kelas</label>
                                            <select name="tahap" id="tahap" class="form-control">
                                                <option value="1">Tahap 1</option>
                                            </select>
                                        </div> --}}
                            <div class="form-group">
                                <label>Organisasi</label>
                                <select name="organ" id="organ" class="form-control">
                                    @foreach($organization as $row)
                                        <option value="{{ $row->id }}" selected>{{ $row->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="file" name="file" required>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Import</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div> -->
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
    $(document).ready(function() {
  
      var outingTable;
      
        if($("#organization").val() != ""){
            $("#organization").prop("selectedIndex", 1).trigger('change');
            fetch_data($("#organization").val());
        }
        
        function fetch_data(oid = '') {
            
            outingTable = $('#outingTable').DataTable({
                    processing: true,
                    serverSide: true,
                    
                    ajax: {
                        url: "{{ route('dorm.getOutingsDatatable') }}",
                        data: {
                            oid: oid,
                            hasOrganization: true
                        },
                        type: 'GET',
  
                    },
                    'columnDefs': [{
                        "targets": [0], // your case first column
                        "className": "text-center",
                        "width": "2%"
                    },{
                        "targets": [1,2,3], // your case first column
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
                    },{
                        data: 'start_date_time',
                        name: 'start_date_time'
                    },{
                        data: 'end_date_time',
                        name: 'end_date_time'
                    },{
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },]
                    
            });
        }
        
        $('#organization').change(function() {
            var organizationid = $("#organization option:selected").val();
            $('#outingTable').DataTable().destroy();
            console.log(organizationid);
            fetch_data(organizationid);
        });
        
        // csrf token for ajax
        $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
  
        var outing_id;
        
        $(document).on('click', '.btn-danger', function(){
            outing_id = $(this).attr('id');
            $('#deleteConfirmationModal').modal('show');
        });
        
        $('#delete').click(function() {
              $.ajax({
                  type: 'POST',
                  dataType: 'html',
                  data: {
                      "_token": "{{ csrf_token() }}",
                      _method: 'DELETE'
                  },
                  url: "/dorm/dorm/indexOuting" + outing_id,
                  success: function(data) {
                      setTimeout(function() {
                          $('#confirmModal').modal('hide');
                      }, 2000);
  
                      $('div.flash-message').html(data);
  
                      outingTable.ajax.reload();
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