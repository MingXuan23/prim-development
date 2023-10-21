@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ URL::asset('assets/css/datatable.css')}}">
<link rel="stylesheet" href="{{ URL::asset('assets/homestay-assets/style.css')}}">
@include('layouts.datatable')
@endsection

@section('content')
<div class="page-title-box d-flex justify-content-between align-items-center flex-wrap">
  <h4 class="font-size-18 color-purple">Urus Tempahan Pelanggan</h4>
  <div class="nav-links d-flex justify-content-center align-items-center flex-wrap">
      <a href="{{route('homestay.urusbilik')}}" class="btn-dark-purple m-2">Urus Homestay</a>
      <a href="{{route('homestay.promotionPage')}}" class="btn-dark-purple m-2">Urus Promosi</a>
  </div>
</div>
<div class="row">

  <div class="col-md-12">
    <div class="card  mx-auto card-primary card-org">

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
      <div class="card-body bg-purple">
        <div class="form-group">
          <label>Nama Organisasi</label>
          <select name="homestay" id="homestay" class="form-control">
            <option value="" selected disabled>Pilih Organisasi</option>
            @foreach($data as $row)
            <option value="{{ $row->id }}">{{ $row->nama }}</option>
            @endforeach
          </select>
        </div>
      </div>

    </div>
  </div>
  <div id="customerResults" class="col-md-12 border-purple p-0">
    <div class="card  mb-0">
      <div>
        <a style="margin: 19px; float: right;cursor: pointer;" id="view-booking-history" class="btn-purple"> <i
            class="fas fa-history"></i> Sejarah Pesanan Pelanggan</a>
      </div>
      <div class="card-body">

      @if(Session::has('success'))
            <div class="alert alert-success">
              <p>{{ Session::get('success') }}</p>
            </div>
          @elseif(Session::has('error'))
            <div class="alert alert-danger">
              <p>{{ Session::get('error') }}</p>
            </div>
          @endif

        <div class="flash-message"></div>

        <div class="table-responsive">
          <table id="bookingTable" class="table table-bordered table-striped dt-responsive"
            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
            <thead class="bg-purple">
              <tr style="text-align:center">
                    <th hidden>Booking ID</th>
                    <th>Nama Pelanggan</th>
                    <th>Nombor Telefon</th>
                    <th>Daftar Masuk</th>
                    <th>Daftar Keluar</th>
                    <th>Nama Homestay</th>
                    <th>Jumlah Dibayar (RM)</th>
                    <th>Action 01</th>
                    <th>Action 02</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection


@section('script')
{{-- sweet alert --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

<script>
$(document).ready(function() {
  $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
  var dataTable;
  function getData(){
      var homestayid = $("#homestay option:selected").val();
      $.ajax({
            url: "{{ route('homestay.getBookingData') }}",
            method: "GET",
            data: { homestayid: homestayid },
            success: function(result) {
                // Destroy the existing DataTable instance
                if (dataTable !== undefined) {
                  dataTable.destroy();
                  dataTable = undefined; // Reset dataTable to undefined
                }

                // Initialize the DataTable with the new data
                dataTable = $('#bookingTable').DataTable({
                data: result.bookings,
                pageLength: 10,
                columns: [
                    { data: 'bookingid', visible: false },
                    { 
                      data: 'name', 
                      orderable: true,
                      searchable: true,
                    },                    
                    { 
                      data: 'telno',
                      orderable: true,
                      searchable: true,
                    },
                    { 
                      data: 'checkin',
                      render:function(data,type, row){
                        return `${moment(data,'YYYY-MM-DD').format('DD/MM/YYYY')}, selepas ${moment(row.check_in_after, 'HH:mm:ss').format('HH:mm')}`;
                      },
                      orderable: true,
                      searchable: true,
                    },
                    { 
                      data: 'checkout',
                      render:function(data,type, row){
                        return `${moment(data,'YYYY-MM-DD').format('DD/MM/YYYY')}, sebelum ${moment(row.check_out_before, 'HH:mm:ss').format('HH:mm')}`;
                      },
                      orderable: true,
                      searchable: true,
                    },
                    { 
                      data: 'roomname',
                      orderable: true,
                      searchable: true,
                    },                    
                    { 
                      data: 'totalprice', 
                      render: function(data,type,row){
                        return `${Number.parseFloat(data).toFixed(2)}`;
                      },
                      orderable: true,
                      searchable: true,
                    },
                    { 
                      data: 'bookingid', render: function(data) {
                        return `<button class="btn btn-primary" id="btn-checkout" data-booking-id="${data}">Daftar Keluar</a>`;
                      },
                      orderable: false,
                      searchable: false, 
                    },
                    {
                      data: 'bookingid', render: function(data) {
                        return `<button class="btn btn-danger" id="btn-cancel-booking" data-booking-id="${data}">Cancel</a>`;
                      },
                      orderable: false,
                      searchable: false,  
                    },
                ],
                columnDefs: [
                    {
                        targets: [1], // Targets the first and second columns (roomid and roomname)
                        orderable: false, // Prevent sorting on these hidden columns
                        searchable: false // Prevent searching on these hidden columns
                    }
                ],
                order:[
                    [0, 'desc'],
                ]
            });

            }
        });
    }
      // Bind onchange event
  $('#homestay').change(function() {
      const homestayId = $(this).val();
      const viewBookingHistory = $('#view-booking-history');
      viewBookingHistory.attr('href', `{{ route('homestay.viewBookingHistory', '') }}/${homestayId}`);
      getData();
  });

  $("#homestay option:nth-child(2)").prop("selected", true);
  $('#homestay').trigger('change');

  $(document).on('click','#btn-checkout', function(){
    Swal.fire({
        title: 'Are you sure?',
        text: "You want to finish this booking?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, checkout user from the homestay!'
      }).then((result) => {
        if (result.isConfirmed) {
          const bookingId = $(this).attr('data-booking-id');
          $.ajax({
            url: "{{route('homestay.checkoutHomestay')}}",
            method: 'POST',
            dataType: 'json',
            data: {
              bookingId: bookingId,
            },
            success: function(result){
              console.log(result.success);
              getData();
            },
            error:function(){
              console.log('Checkout Room Failed');
            }
          })
          Swal.fire(
            'Booking Completed!',
            'This user has been checked out from the homestay',
            'success'
          )
        }
      })
  });

  $(document).on('click','#btn-cancel-booking', function(){
    Swal.fire({
        title: 'Are you sure?',
        text: "You have to arrange proper refund for this customer",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, cancel the booking!'
      }).then((result) => {
        if (result.isConfirmed) {
          const bookingId = $(this).attr('data-booking-id');
          $.ajax({
            url: "{{route('homestay.cancelBooking')}}",
            method: 'POST',
            dataType: 'json',
            data: {
              bookingId: bookingId,
            },
            success: function(result){
              console.log(result.success);
              getData();
            },
            error:function(){
              console.log('Cancel Booking Failed');
            }
          })
          Swal.fire(
            'Booking Cancelled!',
            'This booking has been canceled',
            'success'
          )
        }
      })
  });

  $('.alert').delay(3000).fadeOut();
});
</script>
@endsection