@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ URL::asset('assets/css/datatable.css')}}">
<link rel="stylesheet" href="{{ URL::asset('assets/homestay-assets/style.css')}}">
@include('layouts.datatable')
@endsection

@section('content')
<a href="{{url()->previous()}}" class="color-dark-purple" style="font-size: 20px;"><i class="mt-3 fas fa-chevron-left"></i>&nbsp;Kembali</a>

<div class="page-title-box d-flex justify-content-between align-items-center flex-wrap">
  <h4 class="font-size-18 color-purple">Sejarah Tempahan ({{$organization->nama}}) </h4>
  <div class="nav-links d-flex justify-content-center align-items-center flex-wrap">
      <a href="{{route('homestay.urusbilik')}}" class="btn-dark-purple m-2"><i class="mdi mdi-home-city-outline"></i> Urus Homestay</a>
      <a href="{{route('homestay.promotionPage')}}" class="btn-dark-purple m-2"><i class="fas fa-percentage"></i> Urus Promosi</a>
      <a href="{{route('homestay.urustempahan')}}" class="btn-dark-purple m-2"><i class="fas fa-concierge-bell"></i> Urus Tempahan Pelanggan</a>
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
      <input type="hidden" name="org_id" id="org_id" value="{{$organization->id}}">
    </div>
  </div>
  <div id="customerResults" class="col-md-12 border-purple p-0">
    <div class="card  mb-0">
      <div class="d-flex justify-content-between align-items-center flex-wrap p-2"> 
        <div class="d-flex align-items-center">
          <label for="homestay_id" class="mx-2">Homestay: </label>
          <select name="homestay_id" id="homestay_id" class="form-control">
              <option value="all">Semua Homestay</option>
              @foreach($homestays as $homestay)
                  <option value="{{$homestay->roomid}}">{{$homestay->roomname}}</option>
              @endforeach
          </select>            
        </div>
        <a style="margin: 19px; float: right;cursor: pointer;" href="{{route('homestay.viewCustomersReview',$organization->id)}}" id="view-booking-history" class="btn-purple"> <i class="fas fa-comments"></i> Nilaian Pelanggan</a>
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
        <input type="hidden" name="organization_id" id="organization_id" value="{{$organization->id}}">
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
                    <th>Status</th>
                    <th>Nilaian</th>
                    <th>Action 01</th>
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
      var organizationId = $("#organization_id").val();
      $.ajax({
            url: "{{ route('homestay.getBookingHistoryData') }}",
            method: "GET",
            data: { 
              organizationId:organizationId,
              homestayId: $('#homestay_id').val(), 
            },
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
                      data: 'status',
                      orderable: true,
                      searchable: true, 
                    },
                    { 
                      data: 'review_star', render: function(data) {
                        if(data != null){
                            var rating ='';
                            for(var i = 0; i < data; i++){
                                if(i < data){
                                    rating += `<span class="rated">&#9733</span>`;
                                }else{
                                    rating += `<span class="unrated">☆</span>`;  
                                }
                            }
                            return rating;
                        }else{
                            return `Tiada nilaian diberikan`;
                        }
                      },
                      orderable: true,
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
  getData();
  $('#homestay_id').on('change', function(){
    getData();
  });
  $('.alert').delay(3000).fadeOut();
});
</script>
@endsection