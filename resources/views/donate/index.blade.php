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
      <h4 class="font-size-18">Derma</h4>
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
          <label>Nama Organisasi</label>
          <select name="organization" id="organization" class="form-control">
            <option value="" selected>Semua Organisasi</option>
            @foreach($organization as $row)
            <option value="{{ $row->id }}">{{ $row->nama }}</option>
            @endforeach
          </select>
        </div>
      </div>

      {{-- <div class="">
        <button onclick="filter()" style="float: right" type="submit" class="btn btn-primary"><i
            class="fa fa-search"></i>
          Tapis</button>
      </div> --}}

    </div>
  </div>

  <div class="col-md-12">
    <div class="card">
      {{-- <div class="card-header">List Of Applications</div> --}}
      <div>
        {{-- route('sekolah.create')  --}}
        <a style="margin: 19px; float: right;" href="{{ route('donation.create') }}" class="btn btn-primary"> <i
            class="fas fa-plus"></i> Tambah Derma</a>
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
          <table id="donationTable" class="table table-bordered table-striped dt-responsive"
            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
            <thead>
              <tr style="text-align:center">
                <th> No. </th>
                <th> Nama Derma </th>
                <th> Penerangan </th>
                <th> Tarikh Mula </th>
                <th> Tarikh Berakhir </th>
                <th> Status </th>
                <th> Tindakan </th>
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
        <h4 class="modal-title">Padam Derma</h4>
      </div>
      <div class="modal-body">
        Adakah anda pasti?
      </div>
      <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn btn-primary" id="delete" name="delete">Padam</button>
        <button type="button" data-dismiss="modal" class="btn">Batal</button>
      </div>
    </div>
  </div>
</div>
{{-- end confirmation delete modal --}}

@endsection


@section('script')
<script>
  
  $( document ).ready(function() {

    var donationTable;

      fetch_data();

      function fetch_data(oid = '') {
          donationTable = $('#donationTable').DataTable({
                  processing: true,
                  serverSide: false,
                  ajax: {
                      url: "{{ route('donate.donation_list') }}",
                      data: {
                          oid: oid,
                          hasOrganization: true
                      },
                      type: 'GET',

                  },
                  'columnDefs': [{
                      "targets": [0], // your case first column
                      "className": "text-center",
                      "width": "2%"
                  },{
                      "targets": [3,4,5,6], // your case first column
                      "className": "text-center",
                  },],
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
                      name: 'nama',
                      searchable: true,
                      "width": "20%"
                  }, {
                      data: "description",
                      name: 'description',
                      searchable: true,
                      "width": "20%"
                  }, {
                      data: "date_started",
                      name: 'start_date',
                      searchable: false,
                      "className": "text-center",
                      "width": "10%"
                  }, {
                      data: "date_end",
                      name: 'end_date',
                      searchable: false,
                      "className": "text-center",
                      "width": "10%"
                  }, {
                      data: 'status',
                      name: 'status',
                      orderable: false,
                      searchable: false,
                      "className": "text-center",
                  }, {
                      data: 'action',
                      name: 'action',
                      orderable: false,
                      searchable: false,
                      "className": "text-center",
                      "width": "30%"
                  },]
          });
      }

      $('#organization').change(function() {
          var organizationid = $("#organization option:selected").val();
          $('#donationTable').DataTable().destroy();
          fetch_data(organizationid);
      });

      // csrf token for ajax
      $.ajaxSetup({
              headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });

      var donation_id;

      $(document).on('click', '.btn-danger', function(){
          donation_id = $(this).attr('id');
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
                url: "/donation/" + donation_id,
                success: function(data) {
                    setTimeout(function() {
                        $('#confirmModal').modal('hide');
                    }, 2000);

                    $('div.flash-message').html(data);

                    donationTable.ajax.reload();
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