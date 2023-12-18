@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ URL::asset('assets/css/datatable.css')}}">
<link rel="stylesheet" href="{{ URL::asset('assets/homestay-assets/style.css')}}">
@include('layouts.datatable')

@endsection

@section('content')
  @include('homestay.adminNavBar')

<div class="row">

  <div class="col-md-12">
    <div class="card mx-auto card-primary card-org">

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

  <div class="col-md-12 border-purple p-0 ">
    <div class="card mb-0">
      <div>
        <a style="margin: 19px; float: right;" href="{{ route('homestay.tambahbilik')}}" class="btn-purple"> <i
            class="fas fa-plus"></i> Tambah Homestay</a>
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
          <table id="bilikTable" class="table table-bordered  table-striped dt-responsive"
            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
            <thead class="bg-purple">
              <tr style="text-align:center">
                <th hidden> Room ID </th>
                <th style="width: 25%"> Nama Homestay </th>                
                <th style="width: 25%"> Gambar Homestay </th>
                <th style="width: 5%"> Kapasiti </th>
                <th style="width: 15%"> Harga Semalam (RM) </th>
                <th style="width: 10%"> Status</th>
                <th style="width: 10%"> Tindakan 1</th>
                <th style="width: 10%"> Tindakan 2</th>
              </tr>
            </thead>
            <tbody>

            </tbody>
          </table>
        </div>

      {{-- <div class="modal fade" id="roommodal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Bilik</h1>
                </div>
                <div class="modal-body" style="border-bottom: 1px solid #ccc; border-top: 1px solid #ccc;">
                    <form class="row" id="roomform" method="POST" action="{{route('editRoom')}}" enctype="multipart/form-data">
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
                            <textarea rows="5" cols="30" class="form-control" id="details" name="details"></textarea>
                        </div>
                        <div class="form-group col-12 mb-3 ">
                          <label for="address">Alamat Penuh <span style="color:#d00"> *</span></label>
                          <textarea rows="5" cols="30" name="address" id="address" class="form-control"required></textarea>                                  
                      </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Harga Semalam (RM)</label>
                            <input type="text" class="form-control" id="price" name="price">
                        </div>
                        <div class="col-12 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary mx-2">Simpan</button>
                            <button type="button" id="btn-back" class="btn btn-secondary">Kembali</button>
                        </div>
                    </form>
                </div>
        </div>
    </div> --}}
</div>
      </div>
    </div>
  </div>
</div>

@endsection


@section('script')
{{-- sweet alert --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// function initializeData(rowData) {
//     var roomid = rowData.roomid;
//     var roomname = rowData.roomname;
//     var roompax = rowData.roompax;
//     var details = rowData.details;
//     var price = rowData.price;

//     $('#roomid').val(roomid);
//     $('#roomname').val(roomname);
//     $('#roompax').val(roompax);
//     $('#details').val(details);
//     $('#price').val(price);

//     $('#roomform').attr('action', 'editroom/' + roomid);
// }

$(document).ready(function() {    
  $('.navbar-header > div:first-child()').after(`
        <img src="{{URL('assets/homestay-assets/images/book-n-stay-logo(transparent).png')}}" id="img-bns-logo">
    `);
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var dataTable = $('#bilikTable').DataTable({
        // ... your DataTable configuration ...
    });
    function getData(){
      var homestayid = $("#homestay option:selected").val();
      $.ajax({
            url: "{{ route('homestay.gettabledata') }}",
            method: "GET",
            data: { homestayid: homestayid },
            success: function(result) {
                // Destroy the existing DataTable instance
                if (dataTable !== undefined) {
                    dataTable.destroy();
                }

                // Initialize the DataTable with the new data
                dataTable = $('#bilikTable').DataTable({
                data: result.rooms,
                pageLength: 10,
                columns: [
                    { data: 'roomid', visible: false },
                    { 
                      data: 'roomname', 
                      orderable: true,
                      searchable: true,
                    },                    
                    { 
                      data: 'roomid',
                        render: function(data, type, row) {
                          for(var i = 0; i< result.roomImages.length; i++) {
                            if(data == result.roomImages[i].roomid){
                              return `<img src=" {{URL('${result.roomImages[i].image_path}')}}" alt="Image of Homestay/Room" class="img-homestay">`;
                            }
                          }
                        },
                        orderable: false,
                        searchable: false,
                    },
                    { 
                      data: 'roompax',
                      orderable: true,
                      searchable: true,
                      render: function(data, type, row) {
                        if(row.room_no != null){
                          return `${data} pax (${row.room_no} unit)`;
                        }else{
                          return `${data} pax `;
                        }
                      }
                    },

                    { 
                      data: 'price', 
                      orderable: true,
                      searchable: true,
                    },
                    { 
                      data: 'status', 
                      orderable: true,
                      searchable: true,
                      render: function(data){
                        if(data == "Available"){
                          return "Tempahan Dibuka";
                        }else{
                          return "Tempahan Ditutup";
                        }
                      }
                    },
                    { 
                      data: 'roomid', render: function(data) {
                        var editUrl = `{{ route('homestay.editRoomPage', ':roomid') }}`.replace(':roomid', data);
                        return `<a class="btn btn-primary" href="${editUrl}" id="btn-edit"  data-room-id="${data}">Sunting</a>`;
                      } 
                    },
                    {
                      data: 'roomid', render: function(data) {
                        return `<button class="btn btn-danger" id="btn-delete" data-room-id="${data}">Padam</a>`;
                      }  
                    },
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
    }
    // Bind onchange event
    $('#homestay').change(function() {
        var orgId = $(this).val();
        $('#view-customers-review').attr('href',`{{route('homestay.viewCustomersReview')}}`);
        getData();
    });
    $("#homestay option:nth-child(2)").prop("selected", true);
    $('#homestay').trigger('change');
    // Handle "Edit" button click
    $('#btn-back').on('click',function(){
      $('#roommodal').modal('hide');
    })

    $('.alert').delay(3000).fadeOut();

    // for delete functionality
    $(document).on('click','#btn-delete',function(){
      Swal.fire({
        title: 'Adakah anda pasti?',
        text: "Anda tidak akan dapat mengembalikannya!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, padamkan homestay ini!'
      }).then((result) => {
        if (result.isConfirmed) {
          const roomId = $(this).attr('data-room-id');
          $.ajax({
            url: "{{route('homestay.deleteRoom')}}",
            method: 'POST',
            dataType: 'json',
            data: {
              roomId: roomId,
            },
            success: function(result){
              console.log(result.success);
              getData();
            },
            error:function(){
              console.log('Delete Room Failed');
            }
          })
          Swal.fire(
            'Padam!',
            'Homestay ini telah dipadamkan',
            'success'
          )
        }
      })
    });
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