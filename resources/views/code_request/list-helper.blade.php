@extends('layouts.master')

@section('css')
@include('layouts.datatable')

@endsection

@section('content')
<div class="container">
@if(\Session::has('message'))
    <div class="alert alert-success">
        <p id="success">{{ \Session::get('message') }}</p>
    </div>
    @endif
    @if(\Session::has('error'))
    <div class="alert alert-danger">
        <p id="success">{{ \Session::get('error') }}</p>
    </div>
    @endif
        <h1 class="mb-4">S-Helper Request</h1>
        
        <div class="row mb-4">
        <div class="col-md-12">
            <div class="input-group">
                <select class="form-control" name="status_category" id="status_category">
                    @foreach($status_category as $s)
                        <option value="{{ $s }}">{{ $s }}</option>
                    @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div id="results-section" class="table-responsive">
            <table id="results-table" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Name</th>
                        
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

    function load_table(){
        if ($.fn.DataTable.isDataTable('#results-table')) {
            $('#results-table').DataTable().destroy();
        }

        var table = $('#results-table').DataTable({
                processing: true,
                //serverSide: true,
                ajax: {
                    url: '{{ route("codereq.list_by_helper") }}',
                    type: 'POST',
                    data:{
                        status : $('#status_category').val()
                    }
                    
                },
                columnDefs: [
                    { width: "5%", targets: 0 },  
                    { width: "20%", targets: 1 },
                    { width: "7%", targets: 2 }, 
                    { width: "7%", targets: 3 }, 
                    { width: "7%", targets: 4 }, 
                    { width: "7%", targets: 5 }, 
                    { width: "10%", targets: 6 }, 
                    { width: "20%", targets: 7 }, 
                   
                ], 
                autoWidth: false , 
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
                    {data: 'profile', name: 'profile'},
                    
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
    }
             
            $('#status_category').on('change', function() {
                load_table();
            });

            load_table();
        });
</script>
@endsection