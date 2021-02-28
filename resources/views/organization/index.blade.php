@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@include('layouts.datatable');
@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Organisasi</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div>
                <a style="margin: 19px; float: right;" href="{{ route('organization.create') }}"
                    class="btn btn-primary"> <i class="fas fa-plus"></i> Tambah Organisasi</a>
            </div>

            <div class="card-body">

                @if(count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                        <li id="failed">{{$error}}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                @if(\Session::has('success'))
                <div class="alert alert-success">
                    <p id="success">{{ \Session::get('success') }}</p>
                </div>
                @endif

                <div class="flash-message"></div>

                <div class="table-responsive">
                    <table id="organizationTable" class="table table-bordered table-striped dt-responsive nowrap" 
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr style="text-align:center">
                                <th>Nama Organisasi</th>
                                <th>No Telefon</th>
                                <th>Email</th>
                                <th>Alamat</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
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
                                            class="btn btn-danger m-1">Padam</button>
                                    </div>
                                </td>

                            </tr>
                            @endforeach
                        </tbody>
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
                <h4 class="modal-title">Padam Organisasi</h4>
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
    $(document).ready( function () {
        var organizationTable = $('#organizationTable').DataTable({
            ordering: true,
        });

        // csrf token for ajax
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var organization_id;

        $(document).on('click', '.btn-danger', function(){
            organization_id = $(this).attr('id');
            $('#deleteConfirmationModal').modal('show');
        });

        $('#delete').click(function() {
            $.ajax({
                type: 'POST',
                dataType: 'html',
                data: {
                    _method: 'DELETE'
                },
                url: "/organization/" + organization_id,
                beforeSend: function() {
                    $('#delete').text('Padam...');
                },
                success: function(data) {
                    setTimeout(function() {
                        $('#confirmModal').modal('hide');
                    }, 2000);

                    $('div.flash-message').html(data);

                    window.location.reload();
                },
                error: function (data) {
                    $('div.flash-message').html(data);
                }
            })
        });

        $('.alert').delay(3000).fadeOut();

    } );

</script>
@endsection