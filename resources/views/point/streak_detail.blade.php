@extends('layouts.master')
@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@include('layouts.datatable')

@endsection
@section('content')
<div class="container">
    <div class="row align-items-center">
    <h1 class="col-auto">Sejarah Derma Dalam PRiM Medal</h1> <a class="btn btn-primary col-auto" href="{{route('point.index')}}">Return</a>

    </div>
    <table class="table table-bordered data-table" id="datatable">
        <thead>
            <tr>
                <th>No</th>
                <th>Tarikh</th>
                <th>Hari</th>
                <th>SedekahSubuh</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
@endsection

@section('script')
<script src="{{ URL::asset('assets/libs/peity/peity.min.js')}}"></script>

<!-- Plugin Js-->
<script src="{{ URL::asset('assets/libs/chartist/chartist.min.js')}}"></script>

<script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script>

<script >

    
$(document).ready(function(){
    getData();
       
       
    });

    function getData (){

    table = $('#datatable').DataTable({
        processing: true,
        ajax: {
            url: "{{ $route }}",
            type: 'GET',
        },
        columnDefs: [
            {
                targets: [0, 1, 2, 3], // Assuming you have 4 columns (index 0, 1, 2, 3)
                className: 'text-center',
            },
            {
                targets: 0,
                width: '2%',
               
            },
            {
                targets: [1, 2, 3],
                width: '20%',
            }
        ],
        order: [[0, 'asc']],
        columns: [
            
            {"data": null,
                searchable: false,
                "sortable": false,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {data: 'created_at', name: 'created_at'},
            {data: 'day', name: 'day'},
            {data: 'quality_donation', name: 'quality_donation'}

        ]
    });
    
}
</script>
@endsection