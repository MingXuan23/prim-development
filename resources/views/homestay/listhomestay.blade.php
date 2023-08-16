@extends('layouts.master')

@section('css')

@include('layouts.datatable');

@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Homestay</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div>
                <a style="margin: 19px; float: right;" href="{{ route('homestay.createhomestay') }}"
                    class="btn btn-primary"> <i class="fas fa-plus"></i> Tambah Homestay</a>
            </div>

            <div class="card-body">


                <div class="flash-message"></div>
                <div class="table-responsive">
                    <table id="homestaytable" class="table table-bordered table-striped dt-responsive wrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr style="text-align:center">
                                <th hidden>Homestay ID</th>
                                <th>Nama Homestay</th>
                                <th>Lokasi</th>
                                <th>No Telefon</th>
                                <th>Status</th>
                                <th>Set Promosi</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $record)
                        <tr>
                            <td hidden>{{ $record->homestayid }}</td>
                            <td style="width: 200px;">{{ $record->name }}</td>
                            <td style="width: 450px;">{{ $record->location }}</td>
                            <td>{{ $record->pno }}</td>
                            <td>{{ $record->status }}</td>
                            <td><button type="button" class="btn btn-success" id="promo">Set</button></td>
                            <td style="width: 200px;">
                            <button type="button" class="btn btn-primary" id="addroom">Add Rooms</button>
                            <button class="btn btn-success" id="editbutton">Edit</button>
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


@endsection


@section('script')
<!-- Peity chart-->
<script src="{{ URL::asset('assets/libs/peity/peity.min.js')}}"></script>

{{-- <script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script> --}}

<script>
    $(document).ready(function() {
    
        $('#homestaytable').DataTable();

        $('.alert').delay(3000).fadeOut()
});

</script>
@endsection