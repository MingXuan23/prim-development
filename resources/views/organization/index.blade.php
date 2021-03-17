@extends('layouts.master')

@section('css')

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
                                <th>No</th>
                                <th>Nama Organisasi</th>
                                <th>No Telefon</th>
                                <th>Email</th>
                                <th>Alamat</th>
                                <th>Action</th>
                            </tr>
                        </thead>
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

{{-- <script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script> --}}

<script>
    $(document).ready( function () {
        
        var organizationTable = $('#organizationTable').DataTable({
            ordering: true,
            processing: true,
            serverSide: true,
                ajax: {
                    url: "{{ route('organization.getOrganizationDatatable') }}",
                    type: 'GET',
                },
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
                    name: "nama"
                }, {
                    data: "telno",
                    name: "telno"
                }, {
                    data: "email",
                    name: "email"
                }, {
                    data: "address",
                    name: "address"
                }, {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },]
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
                    "_token": "{{ csrf_token() }}",
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

                    organizationTable.ajax.reload();
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