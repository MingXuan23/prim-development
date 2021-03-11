@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@include('layouts.datatable')
@endsection

@section('content')
<div class="row align-items-center">
  <div class="col-sm-6">
    <div class="page-title-box">
      <h4 class="font-size-18">Peringatan Derma</h4>
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
          <label>Nama Derma</label>
          <select name="donation" id="donation" class="form-control">
            <option value="" selected>Pilih Derma</option>
            @foreach($donations as $donation)
            <option value="{{ $donation->id }}">{{ $donation->nama }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-12">
    <div class="card">
      {{-- <div class="card-header">List Of Applications</div> --}}
      <div>
        {{-- route('sekolah.create')  --}}
        <a style="margin: 19px; float: right;" href="{{ route('reminder.create') }}" class="btn btn-primary"> <i
            class="fas fa-plus"></i> Tambah Peringatan</a>
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
        <table id="reminderTable" class="table table-bordered table-striped dt-responsive nowrap" 
          style="border-collapse: collapse; border-spacing: 0; width: 100%;">
          <thead>
            <tr style="text-align:center">
              <th> No. </th>
              <th> Nama Derma </th>
              <th> Recurrence </th>
              <th> Hari </th>
              <th> Masa </th>
              <th> Tarikh </th>
              <th> Action </th>
            </tr>
          </thead>
        </table>
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
<script src="{{ URL::asset('assets/libs/moment/moment.min.js')}}"></script>

<script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script>

<script>
  $(document).ready(function() {

      fetch_data();

      function fetch_data(donationId = '') {
          $('#reminderTable').DataTable({
                  processing: true,
                  serverSide: true,
                  ajax: {
                      url: "{{ route('reminder.getReminder') }}",
                      data: {
                          donationId : donationId,
                      },
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
                      },
                  }, {
                      data: "nama",
                      name: 'nama'
                  }, {
                      data: "recurrence",
                      name: 'description'
                  }, {
                      data: "day",
                      name: 'day'
                  }, {
                      data: "time",
                      name: 'time',
                      // render: function(data, type, row){
                      //     if(type === "sort" || type === "type"){
                      //         return data;
                      //     }
                      //     console.log(row.time);
                      //     return moment.utc(row.time).format("HH:mm");
                      // }

                  }, {
                      data: 'date',
                      name: 'date',
                  }, {
                      data: 'action',
                      name: 'action',
                      orderable: false,
                      searchable: false
                  },]
          });
      }

      $('#donation').change(function() {
          var donationId = $("#donation option:selected").val();
          $('#reminderTable').DataTable().destroy();
          console.log(donationId);
          fetch_data(donationId);
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