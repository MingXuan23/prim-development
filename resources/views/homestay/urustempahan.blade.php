@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ URL::asset('assets/css/datatable.css')}}">
<link rel="stylesheet" href="{{ URL::asset('assets/homestay-assets/style.css')}}">
@include('layouts.datatable')
@endsection

@section('content')
{{-- <div class="page-title-box d-flex justify-content-between align-items-center flex-wrap">
  <h4 class="font-size-18 color-purple">Urus Tempahan Pelanggan</h4>
  <div class="nav-links d-flex justify-content-center align-items-center flex-wrap">
      <a href="{{route('homestay.urusbilik')}}" class="btn-dark-purple m-2"><i class="mdi mdi-home-city-outline"></i> Urus Homestay</a>
      <a href="{{route('homestay.promotionPage')}}" class="btn-dark-purple m-2"><i class="fas fa-percentage"></i> Urus Promosi</a>
      <a style="cursor: pointer;" id="view-customers-review" class="btn-dark-purple m-2"> <i class="fas fa-comments"></i> Nilaian Pelanggan</a>
  </div>
</div> --}}
@include('homestay.adminNavBar')
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
          <select name="org_id" id="org_id" class="form-control">
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
      <div class="d-flex justify-content-between align-items-center flex-wrap p-2">
        <div class="d-flex align-items-center">
          <label for="homestay_id" class="mx-2">Homestay: </label>
          <select name="homestay_id" id="homestay_id" class="form-control">
              <option value="all">Semua Homestay</option>
          </select>            
        </div>
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
                    <th>Tindakan 01</th>
                    <th>Tindakan 02</th>
                    <th>Tindakan 03</th>
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
  $('.navbar-header > div:first-child()').after(`
        <img src="assets/homestay-assets/images/book-n-stay-logo(transparent).png" id="img-bns-logo">
    `);
  $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
  var dataTable,getDataCounter = 0;
  function getData(){
      var orgid = $("#org_id option:selected").val();
      $.ajax({
            url: "{{ route('homestay.getBookingData') }}",
            method: "GET",
            data: { 
              orgid: orgid, 
              homestayid: $('#homestay_id').val(),
            },
            success: function(result) {
                //only run this during the first request
                if(getDataCounter == 0){
                  //reset #homestay_id 
                  $('#homestay_id').empty();
                  // add option into #homestay_id
                  $('#homestay_id').append(`
                    <option value="all">Semua Homestay</option>
                  `);
                  $(result.homestays).each(function(i, homestay){
                    $('#homestay_id').append(`
                      <option value="${homestay.roomid}">${homestay.roomname}</option>
                    `);
                  });     
                  
                  getDataCounter++;
                }

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
                      render: function(data,type, row){
                        if(row.booked_rooms == null){
                          return data;
                        }else{
                          return `${data}<br>(x ${row.booked_rooms} Unit)`;
                        }
                      }
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
                        var detailUrl = `{{route('homestay.bookingDetails', ':bookingData')}}`.replace(':bookingData' ,data);
                        return `<a class="text-white" href="${detailUrl}"><button class="btn-dark-purple btn-detail">Butiran<?button></a>`;
                      },
                      orderable: false,
                      searchable: false, 
                    },
                    {
                      data: 'bookingid', render: function(data) {
                        return `<button class="btn btn-danger" id="btn-cancel-booking" data-booking-id="${data}">Batal</a>`;
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
  $('#org_id').change(function() {
      const orgId = $(this).val();
      $('#view-customers-review').attr('href',`{{route('homestay.viewCustomersReview')}}`);
      const viewBookingHistory = $('#view-booking-history');
      viewBookingHistory.attr('href', `{{ route('homestay.viewBookingHistory', '') }}/${orgId}`);
      getData();
  });

  $("#org_id option:nth-child(2)").prop("selected", true);
  $('#org_id').trigger('change');

  $(document).on('click','#btn-checkout', function(){
    Swal.fire({
        title: 'Adakah anda pasti?',
        text: "Anda mahu selesaikan tempahan ini?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, daftar keluar guest ini!'
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
            'Tempahan Selesai!',
            'Guest ini telah didaftar keluar daripada homestay',
            'success'
          )
        }
      })
  });

  $(document).on('click','#btn-cancel-booking', function(){
    Swal.fire({
        title: 'Adakah anda pasti?',
        text: "Anda diwajibkan menjalankan pemulangan duit tempahan kepada guest ini",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, batalkan tempahan ini!'
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
            'Tempahan dibatalkan!',
            'Tempahan ini berjaya dibatalkan',
            'success'
          )
        }
      })
  });
  $('#homestay_id').on('change', function(){
    getData();
  });
  $('.alert').delay(3000).fadeOut();
            // to add .active to the link for current page in navbar
  // Get the current URL path
  var currentPath = window.location.pathname;

  // Loop through each anchor tag in the navigation
  $('.admin-nav-links a').each(function() {
      var linkPath = $(this).attr('href');
      // Check if the link's path matches the current URL path
      if (linkPath.includes(currentPath)) {
          // Add a class to highlight the active link
          $(this).addClass('admin-active');
      }
  });
});
</script>
@endsection