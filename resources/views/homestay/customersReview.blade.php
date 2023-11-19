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
  <h4 class="font-size-18 color-purple">Nilaian Pelanggan ({{$organization->nama}}) </h4>
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
    </div>
  </div>
  <div id="customerResults" class="col-md-12 border-purple p-0">
    <div class="card  mb-0">
        <div class="mt-2 mx-4 d-flex align-items-center">
            <label for="homestay_id" class="mr-2">Homestay: </label>
            <select name="homestay_id" id="homestay_id" class="form-control w-25">
                <option value="all">Semua Homestay</option>
                @foreach($homestays as $homestay)
                    <option value="{{$homestay->roomid}}">{{$homestay->roomname}}</option>
                @endforeach
            </select>            
        </div>
        <input type="hidden" name="organization_id" id="organization_id" value="{{$organization->id}}">
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
  $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
  var dataTable;
  function getData(){
      var homestayId = $("#homestay_id").val();
      $.ajax({
            url: "{{ route('homestay.getCustomersReview') }}",
            method: "GET",
            data: { 
                homestayId:homestayId,
                organizationId: $('#organization_id').val(),
            },
            success: function(result) {
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
  getData();
  $('#homestay_id').on('change', function(){
    getData();
  });
  $('.alert').delay(3000).fadeOut();
});
</script>
@endsection