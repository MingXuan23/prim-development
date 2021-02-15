@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Organisasi</h4>
            <!-- <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Welcome to Veltrix Dashboard</li>
            </ol> -->
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            {{-- <div class="card-header">List Of Applications</div> --}}
            <div>
                {{-- route('sekolah.create')  --}}
                <a style="margin: 19px; float: right;" href="{{ route('organization.create') }}"
                    class="btn btn-primary"> <i class="fas fa-plus"></i> Tambah Organisasi</a>
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

                {{-- <div align="right">
                            <a href="{{route('admin.create')}}" class="btn btn-primary">Add</a>
                <br />
                <br />
            </div> --}}
            <div class="table-responsive">
                <table id="organzationTable" class="table table-bordered table-striped">
                    <tr style="text-align:center">
                        <th>Nama Organisasi</th>
                        <th>No Telefon</th>
                        <th>Email</th>
                        <th>Alamat</th>
                        <th>Action</th>
                    </tr>

                    @foreach($listorg as $row)
                    <tr id="row{{$row->id}}">
                        <td>{{ $row->nama }}</td>
                        <td>{{ $row->telno }}</td>
                        <td>{{ $row->email }}</td>
                        <td>{{ $row->address }}</td>
                        <td>
                            <div class="d-flex justify-content-center">
                                <a href="{{ route('organization.edit', $row->id ) }}"
                                    class="btn btn-primary m-1">Edit</a>
                                <button id="{{ $row->id }}" data-token="{{ csrf_token() }}"
                                    class="btn btn-danger m-1">Delete</button>
                            </div>
                        </td>

                    </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>

</div>
</div>
{{-- confirmation delete modal --}}
<div id="deleteConfirmationModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Delete Organization</h4>
            </div>
            <div class="modal-body">
                Are you sure want to delete this organization?
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-primary" id="delete"
                    name="delete">Delete</button>
                <button type="button" data-dismiss="modal" class="btn">Cancel</button>
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
    // csrf token for ajax
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var organization_id

    $(document).on('click', '.btn-danger', function(){
        organization_id = $(this).attr('id');
        $('#deleteConfirmationModal').modal('show');
        console.log(organization_id);
    });

    $('#delete').click(function() {
        $.ajax({
            type: "DELETE",
            url: "/organization/" + organization_id,
            beforeSend: function() {
                $('#delete').text('Deleting...');
            },
            success: function(data) {
                setTimeout(function() {
                    $('#confirmModal').modal('hide');
                }, 2000);
                
                console.log("success");
                $("#row" + organization_id).remove();
            },
            error: function (data) {
                console.log('Error:', data);
            }
        })
    });

    $('.alert').delay(3000).fadeOut();


</script>
@endsection