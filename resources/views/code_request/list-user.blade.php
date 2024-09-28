@extends('layouts.master')

@section('css')
@include('layouts.datatable')
       <style>
            .navbar-header .btn {
                display: none;
            }

       </style>
@endsection

@section('content')
<div class="container">
        <h1 class="mb-4">Track Your Request Status</h1>
        
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="input-group">
                    <input type="email" id="email-input" class="form-control" placeholder="Enter your email" required>
                    <button id="track-status-btn" class="btn btn-primary ml-3">Track Status</button>
                </div>
            </div>
        </div>

        <div id="results-section" class="table-responsive">
            <table id="results-table" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Language</th>
                        <th>Package</th>
                        <th>Request Time</th>
                        <th>Final Price</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be populated here -->
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('script')
<script src="{{ URL::asset('assets/libs/jquery-mask/jquery.mask.min.js')}}"></script>

<script src="{{ URL::asset('assets/libs/peity/peity.min.js')}}"></script>
<script>
  $(document).ready(function() {

    $.ajaxSetup({
                    headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

             var table = $('#results-table').DataTable({
                processing: true,
                //serverSide: true,
                ajax: {
                    url: '{{ route("codereq.list_by_email") }}',
                    type: 'POST',
                    data: function(d) {
                        d.email = $('#email-input').val();
                    }
                },
                
                columns: [
                    //{data: 'id', name: 'id'},
                    {
                        "data": null,
                        searchable: false,
                        "sortable": false,
                        render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {data: 'name', name: 'name'},
                    {data: 'email', name: 'email'},
                    {data: 'phone', name: 'phone'},
                    {data: 'language', name: 'language'},
                    {data: 'package', name: 'package'},
                    {data: 'created_at', name: 'created_at'},

                    {data: 'final_price', name: 'final_price', render: function(data, type, row) {
                        return 'RM' + parseFloat(data).toFixed(2);
                    }},
                    {data:'status',name:'status'},
                    {data:'action',name: 'action'}
                ]
            });
            $('#track-status-btn').on('click', function() {
                var email = $('#email-input').val();
                if (email) {
                    $('#results-section').removeClass('hidden');
                    table.ajax.reload();
                } else {
                    alert('Please enter a valid email address.');
                }
            });
        });
</script>
@endsection