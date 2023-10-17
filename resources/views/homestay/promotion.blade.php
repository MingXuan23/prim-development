@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ URL::asset('assets/css/datatable.css')}}">
<link rel="stylesheet" href="{{ URL::asset('assets/homestay-assets/style.css')}}">
@include('layouts.datatable')

@endsection

@section('content')
    <div class="page-title-box d-flex justify-content-between align-items-center">
      <h4 class="font-size-18 color-purple">Urus Promosi</h4>
      <div class="nav-links">
          <a href="{{route('homestay.urusbilik')}}" class="btn-dark-purple">Urus Homestay</a>
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
            <option value="" selected disabled>Pilih Homestay</option>
            @foreach($organization as $row)
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
        <a style="margin: 19px; float: right;" id="link-add-promotion" class="btn-purple"> <i
            class="fas fa-plus"></i> Tambah Promosi</a>
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
                <th style="width: 15%"> Nama Homestay </th>                
                <th style="width: 15%"> Nama Promosi</th>
                <th style="width: 10%"> Jumlah Promosi</th>
                <th style="width: 10%"> Harga Semalam Semasa Promosi(RM) </th>
                <th style="width: 15%"> Tarikh Mula </th>
                <th style="width: 15%"> Tarikh Berakhir </th>
                <th style="width: 10%"> Action 1</th>
                <th style="width: 10%"> Action 2</th>
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
$(document).ready(function() {    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    var dataTable = $('#promotionTable').DataTable({
        // ... your DataTable configuration ...
    });
    function getData(){
      var homestayid = $("#homestay option:selected").val();
      $.ajax({
            url: "{{ route('homestay.getPromotionData') }}",
            method: "GET",
            data: { homestayid: homestayid },
            success: function(result) {
                // Destroy the existing DataTable instance
                if (dataTable !== undefined) {
                    dataTable.destroy();
                }

                // Initialize the DataTable with the new data
                dataTable = $('#promotionTable').DataTable({
                data: result.promotions,
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
                      orderable: true,
                      searchable: true,
                    },
                    { 
                      data: 'dateto', 
                      orderable: true,
                      searchable: true,
                    },
                    { 
                      data: 'promotionid', render: function(data) {
                        var editUrl = `{{ route('homestay.editPromotionPage', ':promotionid') }}`.replace(':promotionid', data);
                        return `<a class="btn btn-primary" href="${editUrl}" id="btn-edit"  data-promotion-id="${data}">Edit</a>`;
                      },
                      orderable: false,
                      searchable: false, 
                    },
                    {
                      data: 'promotionid', render: function(data) {
                        return `<button class="btn btn-danger" id="btn-delete" data-promotion-id="${data}">Delete</a>`;
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
                    [1, 'asc'],
                    [2, 'asc'],
                    [3, 'asc'],
                ]
            });

            }
        });
    }
    // Bind onchange event
    $('#homestay').change(function() {
        const homestayId = $(this).val();
        const linkAddPromotion = $('#link-add-promotion');
        linkAddPromotion.attr('href', `{{ route('homestay.setpromotion', '') }}/${homestayId}`);
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
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
          const promotionId = $(this).attr('data-promotion-id');
          $.ajax({
            url: "{{route('homestay.deletePromotion')}}",
            method: 'POST',
            dataType: 'json',
            data: {
              promotionId : promotionId ,
            },
            success: function(result){
              console.log(result.success);
              getData();
            },
            error:function(){
              console.log('Delete Promotion Failed');
            }
          })
          Swal.fire(
            'Deleted!',
            'This promotion has been deleted.',
            'success'
          )
        }
      })
    });
});
</script>
@endsection