@extends('layouts.master')

@section('css')

@include('layouts.datatable');

@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Organisasi >> Urus Organisasi</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">

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
                    <table id="organizationTable" class="table table-bordered table-striped dt-responsive wrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr style="text-align:center">
                                <th>ID</th>
                                <th>Nama Organisasi</th>
                                {{-- <th>Kod</th> --}}
                                <th>Alamat</th>
                                <th>Seller Id</th>
                                <th>Action</th>
                                
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

{{-- <script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script> --}}

<script>
    $(document).ready( function () {
        
        var organizationTable = $('#organizationTable').DataTable({
            ordering: true,
            processing: true,
           // serverSide: true,
                ajax: {
                    url: "{{ route('organization.getPendingOrgDatatable') }}",
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
                columns: [ {
                    data: "id",
                    name: "nama",
                    "width": "10%"
                },{
                    data: "nama",
                    name: "nama",
                    "width": "20%"
                },
                // {
                //     data: "code",
                //     name: "code"
                // }, 
                {
                    data: "address",
                    name: "address",
                    "width": "20%"
                }, {
                    data: "seller_id",
                    name: "seller_id",
                    "width": "15%"
                }, {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    "width": "10%"
                },
                
            ]
          });

        // csrf token for ajax
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var organization_id;



        $('.alert').delay(3000).fadeOut();

        
        $(document).on('click', '.update-button', function() {
            var id = $(this).data('id');
            var sellerId = $('input.seller-id[data-id="'+id+'"]').val();

            $.ajax({
                url: '{{route("organization.updateSellerId")}}',
                type: 'POST',
                data: {
                    id: id,
                    seller_id: sellerId,
                    _token: '{{ csrf_token() }}' // Include CSRF token for Laravel
                },
               success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert('An error occurred: ' + error);
                }
            });
        });

    });

</script>
@endsection