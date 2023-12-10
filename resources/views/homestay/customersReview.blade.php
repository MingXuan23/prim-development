@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ URL::asset('assets/css/datatable.css')}}">
<link rel="stylesheet" href="{{ URL::asset('assets/homestay-assets/style.css')}}">
@include('layouts.datatable')
@endsection

@section('content')
{{-- <a href="{{url()->previous()}}" class="color-dark-purple" style="font-size: 20px;"><i class="mt-3 fas fa-chevron-left"></i>&nbsp;Kembali</a> --}}
{{-- <div class="page-title-box d-flex justify-content-between align-items-center flex-wrap">
  <h4 class="font-size-18 color-purple">Nilaian Pelanggan ({{$organization->nama}}) </h4>
  <div class="nav-links d-flex justify-content-center align-items-center flex-wrap">
      <a href="{{route('homestay.urusbilik')}}" class="btn-dark-purple m-2"><i class="mdi mdi-home-city-outline"></i> Urus Homestay</a>
      <a href="{{route('homestay.promotionPage')}}" class="btn-dark-purple m-2"><i class="fas fa-percentage"></i> Urus Promosi</a>
      <a href="{{route('homestay.urustempahan')}}" class="btn-dark-purple m-2"><i class="fas fa-concierge-bell"></i> Urus Tempahan Pelanggan</a>
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
            @foreach($organizations as $row)
            <option value="{{ $row->id }}">{{ $row->nama }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
  </div>
  <div id="customerResults" class="col-md-12 border-purple p-0">
    <div class="card  mb-0">
        <div class="mt-2 mx-4 d-flex align-items-center">
            <label for="homestay_id" class="mr-2">Homestay: </label>
            <select name="homestay_id" id="homestay_id" class="form-control w-25">
              <option value="all">Semua Homestay</option>
          </select>            
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
                    <th hidden>Homestay ID</th>
                    <th>Nama Pelanggan</th>
                    <th>Tarikh</th>
                    <th>Nilaian</th>
                    <th>Komen</th>
                    <th>Nama Homestay</th>
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
  var dataTable;
  var getDataCounter = 0;
  function getData(){
      var homestayId = $("#homestay_id").val();
      $.ajax({
            url: "{{ route('homestay.getCustomersReview') }}",
            method: "GET",
            data: { 
                homestayId:homestayId,
                organizationId: $('#org_id').val(),
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
                data: result.reviews,
                pageLength: 10,
                columns: [
                    { data: 'roomid', visible: false },
                    { 
                      data: 'name', 
                      orderable: true,
                      searchable: true,
                    },                    
                    { 
                      data: 'updated_at',
                      render: function (data,type, row){
                        return`${moment(data,'YYYY-MM-DD HH:mm:ss').format('DD/MM/YYYY HH:mm')}`;
                      },
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
                      data: 'review_comment',
                      render: function(data,type,row){
                        var comment = "";
                        if(data == null){
                            comment = `<div>Tiada komen</div>`;
                        }else{
                            comment =data;
                        }
                        return comment;
                      },
                      orderable: false,
                      searchable: true,
                    },                    

                    { 
                      data: 'roomname',
                      orderable: true,
                      searchable: true, 
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
                    [2, 'desc'],
                ]
            });

            }
        });
    }
     // Bind onchange event
     $('#org_id').change(function() {
        const homestayId = $(this).val();

        $('#view-customers-review').attr('href',`{{route('homestay.viewCustomersReview')}}`);
        $('#view-promotion-history').attr('href',`{{route('homestay.viewPromotionHistory','')}}/${homestayId}`);
        const linkAddPromotion = $('#link-add-promotion');
        linkAddPromotion.attr('href', `{{ route('homestay.setpromotion', '') }}/${homestayId}`);
        getData();
    });

    $("#org_id option:nth-child(2)").prop("selected", true);
    $('#org_id').trigger('change');
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