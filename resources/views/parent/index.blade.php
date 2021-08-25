@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@include('layouts.datatable')
@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Ibu Bapa/Penjaga</h4>
            <!-- <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Welcome to Veltrix Dashboard</li>
            </ol> -->
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            {{csrf_field()}}
            <div class="card-body">

                {{-- <div class="form-group">
                    <label>Nombor Kad Pengenalan</label>
                    <input type="text" id="icno" name="icno" class="form-control"
                        placeholder="Masukkan Nombor Kad Pengenalan">
                    <p><i> *tiada "-" </i>  </p> 
                </div> --}}

                <div class="form-group">
                    <label>Nombor Telefon</label>
                    <input type="text" id="telno" name="telno" class="form-control"
                        placeholder="Masukkan Nombor Telefon">
                    {{-- <p><i> *tiada "-" </i>  </p> --}}
                </div>
            </div>

        </div>
    </div>

    <div class="col-md-12">
        <div class="card">
            {{-- <div class="card-header">List Of Applications</div> --}}
            <div>
                <a style="margin: 19px;" href="#" class="btn btn-primary" data-toggle="modal" data-target="#modelId"> <i
                        class="fas fa-plus"></i> Import</a>

                <a style="margin: 1px;" href="" class="btn btn-success"> <i class="fas fa-plus"></i> Export</a>
                <a style="margin: 19px; float: right;" href="{{ route('parent.create') }}" class="btn btn-primary"> <i
                        class="fas fa-plus"></i> Tambah Ibu Bapa</a>
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
                    <table id="parentTable" class="table table-bordered table-striped dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr style="text-align:center">
                                <th> No. </th>
                                <th>Nama Penuh</th>
                                <th>Nombor Kad pengenalan</th>
                                <th>Email</th>
                                <th>Nombor Telefon</th>
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
                        <h4 class="modal-title">Padam Ibu Bapa/Penjaga</h4>
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
        <div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Import Ibu Bapa/Penjaga</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="modal-body">

                            {{ csrf_field() }}

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
        </div>
    </div>
</div>


@endsection


@section('script')
<!-- Peity chart-->
<script src="{{ URL::asset('assets/libs/peity/peity.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/chartist/chartist.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/jquery-mask/jquery.mask.min.js')}}"></script>
<script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script>

<script>
    $(document).ready(function() {
        $('#telno').mask('+600000000000');
        $('#parentTable').DataTable().destroy();
        var timeout = null;
        $('#telno').on('keyup', function() {
            var text = this.value;
            clearTimeout(timeout);
            if(text.length == 12 || text.length == 13){
                // console.log('asdas');
                timeout = setTimeout(function() {
                // Do AJAX shit here      
                $('#parentTable').DataTable().destroy();
                    fetch_data(text);
                
                // alert(text);
                }, 100);
            }
            
        });
        var parentTable;

        function fetch_data(telno = '') {
            
            parentTable = $('#parentTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('parent.getParentDatatable') }}",
                        data: {
                            telno: telno,
                        },
                        type: 'GET',
  
                    },
                    'columnDefs': [{
                        "targets": [0], // your case first column
                        "className": "text-center",
                        "width": "2%"
                    },{
                        "targets": [3,4,5], // your case first column
                        "className": "text-center",
                    },{
                        "targets": '_all',
                        "defaultContent": ""
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
                        data: "name",
                        name: 'name',
                        orderable: false,
                        searchable: false
                    },{
                        data: "icno",
                        name: 'icno',
                        orderable: false,
                        searchable: false
                    }, {
                        data: "email",
                        name: 'email',
                        orderable: false,
                        searchable: false
                    }, {
                        data: "telno",
                        name: 'telno',
                        orderable: false,
                        searchable: false
                    }, {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },]
            });
        }
  
        // csrf token for ajax
        $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


  
    });
</script>
@endsection