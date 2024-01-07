@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ URL::asset('assets/css/datatable.css')}}">
<link rel="stylesheet" href="{{ URL::asset('assets/homestay-assets/style.css')}}">
@include('layouts.datatable')
<style>
    .sort-homestay{
      width: 25% !important;
    }  
  @media screen and (max-width: 768px){
    .sort-homestay{
      width: 100% !important;
    }    
  }


</style>
@endsection

@section('content')

<a href="{{url()->previous()}}" class="color-dark-purple" style="font-size: 20px;"><i class="my-3 fas fa-chevron-left"></i>&nbsp;Kembali</a>

<div class="d-flex justify-content-between align-items-center flex-wrap">
  <h4 class="font-size-18 color-purple">Sejarah Promosi ({{$organization->nama}})</h4>
  {{-- <div class="nav-links d-flex justify-content-center align-items-center flex-wrap">
      <a href="{{route('homestay.urusbilik')}}" class="btn-dark-purple m-2"><i class="mdi mdi-home-city-outline"></i> Urus Homestay</a>
      <a href="{{route('homestay.urustempahan')}}" class="btn-dark-purple m-2"><i class="fas fa-concierge-bell"></i> Urus Tempahan</a>
      <a href="{{route('homestay.viewCustomersReview',$organization->id)}}" style="cursor: pointer;" id="view-customers-review" class="btn-dark-purple m-2"> <i class="fas fa-comments"></i> Nilaian Pelanggan</a>
    </div> --}}
</div>
@include('homestay.adminNavBar')

<div class="row mb-5">

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
      <input type="hidden" id="org_id" name="org_id" value={{$organization->id}}>
    </div>
  </div>

  <div class="col-md-12 border-purple p-0">
    <div class="card mb-0">
      <div class="d-flex align-items-center justify-content-center p-2 sort-homestay">
        <label for="homestay_id" class="mx-2">Homestay: </label>
        <select name="homestay_id" id="homestay_id" class="form-control">
            <option value="all">Semua Homestay</option>
        </select>            
      </div>
      <div class="card-body ">

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
          <table id="promotionTable" class="table table-bordered  table-striped"
            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
            <thead class="bg-purple">
              <tr style="text-align:center">
                <th hidden> Room ID </th>
                <th> Nama Homestay </th>                
                <th> Nama Promosi</th>
                <th> Jumlah Promosi</th>
                <th> Harga Semalam Semasa Promosi(RM) </th>
                <th> Tarikh Mula </th>
                <th> Tarikh Berakhir </th>
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
</div>

@endsection


@section('script')
{{-- sweet alert --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

<script>
$(document).ready(function() {  
  $('.navbar-header > div:first-child()').after(`
        <img src="{{URL('assets/homestay-assets/images/book-n-stay-logo(transparent).png')}}"  height="70px">
    `);
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    var dataTable = $('#promotionTable').DataTable({
        // ... your DataTable configuration ...
    });
    var getDataCounter = 0;
    function getData(){
        var orgId = $('#org_id').val();
      $.ajax({
            url: "{{ route('homestay.getPromotionHistory') }}",
            method: "GET",
            data: { 
              orgId: orgId,
              homestayId: $('#homestay_id').val(),
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
                }

                // Initialize the DataTable with the new data
                dataTable = $('#promotionTable').DataTable({
                data: result.promotions,
                pageLength: 10,
                columns: [
                    { data: 'promotionid', visible: false },
                    { 
                      data: 'roomname', 
                      orderable: true,
                      searchable: true,
                    },                    
                    { 
                      data: 'promotionname',
                      orderable: true,
                      searchable: true,
                    },
                    { 
                      data: 'promotion_type',
                      render:function(data,type, row){
                        if(data == "discount"){
                            return `Diskaun ${row.discount}%`;
                        }else if(data == "increase"){
                            return `Naik ${row.increase}%`;
                            }
                      },
                      orderable: true,
                      searchable: true,
                    },

                    { 
                      data: 'price', 
                      render: function(data,type,row){
                        let actualPrice = 0;
                        if(row.promotion_type == "discount"){
                            actualPrice = data - (data*row.discount/100);
                        }else if(row.promotion_type == "increase"){
                            actualPrice = Number.parseFloat(data) + (data*row.increase/100);
                        }
                        return `${data} -> ${Number.parseFloat(actualPrice).toFixed(2)}`;
                      },
                      orderable: true,
                      searchable: true,
                    },
                    { 
                      data: 'datefrom', 
                      render: function(data,type,row){
                        return `${moment(data,'YYYY-MM-DD').format('DD/MM/YYYY')}`;
                      },
                      orderable: true,
                      searchable: true,
                    },
                    { 
                      data: 'dateto',
                      render: function(data,type,row){
                        return `${moment(data,'YYYY-MM-DD').format('DD/MM/YYYY')}`;
                      },
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
                    [6, 'desc'],
                ]
            });

            }
        });
    }
    getData();

    $("#homestay_id").on('change', function(){
      getData();
    })
});
</script>
@endsection