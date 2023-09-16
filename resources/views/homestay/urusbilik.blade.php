@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ URL::asset('assets/css/datatable.css')}}">
@include('layouts.datatable')
@endsection

@section('content')
<div class="row align-items-center">
  <div class="col-sm-6">
    <div class="page-title-box">
      <h4 class="font-size-18">Bilik Homestay</h4>
      <!-- <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Welcome to Veltrix Dashboard</li>
            </ol> -->
    </div>
  </div>
</div>
<div class="row">

  <div class="col-md-12">
    <div class="card card-primary">

      @if(count($errors) > 0)
      <div class="alert alert-danger">
        <ul>
          @foreach($errors->all() as $error)
          <li>{{$error}}</li>
          @endforeach
        </ul>
      </div>
      @endif

      {{csrf_field()}}
      <div class="card-body">
        <div class="form-group">
          <label>Nama Homestay</label>
          <select name="homestay" id="homestay" class="form-control">
            <option value="" selected>Pilih Homestay</option>
            @foreach($data as $row)
            <option value="{{ $row->id }}">{{ $row->nama }}</option>
            @endforeach
          </select>
        </div>
      </div>

    </div>
  </div>

  <div class="col-md-12">
    <div class="card">
      <div>
        <a style="margin: 19px; float: right;" href="{{ route('homestay.tambahbilik')}}" class="btn btn-primary"> <i
            class="fas fa-plus"></i> Tambah Bilik</a>
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

        <div class="flash-message"></div>

        <div class="table-responsive">
          <table id="bilikTable" class="table table-bordered table-striped dt-responsive"
            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
            <thead>
              <tr style="text-align:center">
                <th hidden> Room ID </th>
                <th> Nama Bilik </th>
                <th> Kapasiti Bilik </th>
                <th> Detail Bilik </th>
                <th> Harga Semalam (RM) </th>
                <th> Status </th>
                <th> Action</th>
              </tr>
            </thead>
            <tbody>

            </tbody>
          </table>
        </div>

        <div class="modal fade" id="roommodal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Bilik</h1>
            </div>
            <div class="modal-body" style="border-bottom: 1px solid #ccc; border-top: 1px solid #ccc;">
                <form class="row g-3" id="roomform" method="POST" action="">
                    @csrf
                    <input type="text" class="form-control" name="roomid" id="roomid" hidden>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama / Nombor Bilik</label>
                        <input type="text" class="form-control" id="roomname" name="roomname" disabled>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kapasiti Bilik</label>
                        <input type="text" class="form-control" id="roompax" name="roompax">
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">Detail Bilik</label>
                        <input type="text" class="form-control" id="details" name="details">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Harga Semalam (RM)</label>
                        <input type="text" class="form-control" id="price" name="price">
                    </div>

                   

                    <div class="col-12 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <a href="{{ route('homestay.urusbilik') }}" class="btn btn-secondary" id="homestay">Kembali</a>
            </div>
        </div>
    </div>
</div>
      </div>
    </div>
  </div>
</div>

@endsection


@section('script')
<script>

function initializeData(rowData) {
    var roomid = rowData.roomid;
    var roomname = rowData.roomname;
    var roompax = rowData.roompax;
    var details = rowData.details;
    var price = rowData.price;

    $('#roomid').val(roomid);
    $('#roomname').val(roomname);
    $('#roompax').val(roompax);
    $('#details').val(details);
    $('#price').val(price);

    $('#roomform').attr('action', 'editroom/' + roomid);
}

$(document).ready(function() {
    var dataTable = $('#bilikTable').DataTable({
        // ... your DataTable configuration ...
    });

    // Bind onchange event
    $('#homestay').change(function() {
        var homestayid = $("#homestay option:selected").val();

        $.ajax({
            url: "{{ route('homestay.gettabledata') }}",
            method: "GET",
            data: { homestayid: homestayid },
            success: function(data) {
                // Destroy the existing DataTable instance
                if (dataTable !== undefined) {
                    dataTable.destroy();
                }

                // Initialize the DataTable with the new data
                dataTable = $('#bilikTable').DataTable({
                    data: data,
                    columns: [
                        { data: 'roomid', visible: false },
                        { data: 'roomname' },
                        { data: 'roompax' },
                        { data: 'details' },
                        { data: 'price' },
                        { data: 'status' },
                        { data: null, render: function() { return '<button class="btn btn-success" id="editbutton">Edit</button>'; } }
                    ],
                    columnDefs: [
                        {
                            targets: [1], // Targets the first and second columns (roomid and roomname)
                            orderable: false, // Prevent sorting on these hidden columns
                            searchable: false // Prevent searching on these hidden columns
                        }
                    ]
                });
            }
        });
    });

    // Handle "Edit" button click
    $(document).on('click', '#editbutton', function(e) {
        var tr = $(this).closest('tr');
        var row = dataTable.row(tr).data();

        // Call the initializeData function with the clicked row's data
        initializeData(row);

        // Open the modal
        $('#roommodal').modal('show');
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.alert').delay(3000).fadeOut();
});
</script>
@endsection