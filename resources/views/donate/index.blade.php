@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
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
        <a style="margin: 19px; float: right;" href="{{ route('donate.create') }}" class="btn btn-primary"> <i
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

        <div class="table-responsive">
          <table id="donationTable" class="table table-bordered table-striped dt-responsive nowrap"
            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
            <thead>
              <tr style="text-align:center">
                <th> No. </th>
                <th> Nama Derma </th>
                <th> Penerangan </th>
                <th> Tarikh Mula </th>
                <th> Tarikh Berakhir </th>
                <th> Status </th>
                <th> Action </th>
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
<!-- Peity chart-->
<script src="{{ URL::asset('assets/libs/peity/peity.min.js')}}"></script>

<!-- Plugin Js-->
<script src="{{ URL::asset('assets/libs/chartist/chartist.min.js')}}"></script>

<script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script>

<script>
  $(document).ready(function() {

      fetch_data();

      function fetch_data(oid = '') {
          $('#donationTable').DataTable({
                  processing: true,
                  serverSide: true,
                  ajax: {
                      url: "{{ route('donate.donationlist') }}",
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
                      name: 'nama'
                  }, {
                      data: "description",
                      name: 'description'
                  }, {
                      data: "date_started",
                      name: 'start_date'
                  }, {
                      data: "date_end",
                      name: 'end_date'
                  }, {
                      data: 'status',
                      name: 'status',
                      orderable: false,
                      searchable: false
                  }, {
                      data: 'action',
                      name: 'action',
                      orderable: false,
                      searchable: false
                  },]
          });
      }

      $('#organization').change(function() {
          var organizationid = $("#organization option:selected").val();
          $('#donationTable').DataTable().destroy();
          console.log(organizationid);
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
                    _method: 'DELETE'
                },
                url: "/donate/" + donation_id,
                beforeSend: function() {
                    $('#delete').text('Padam...');
                },
                success: function(data) {
                    setTimeout(function() {
                        $('#confirmModal').modal('hide');
                    }, 2000);

                    window.location.reload();
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