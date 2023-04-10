@extends('layouts.master')
@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ URL::asset('assets/css/datatable.css')}}">
<meta name="csrf-token" content="{{ csrf_token() }}">

@include('layouts.datatable')

@endsection


@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="page-title-box">
      <h4 class="font-size-18">{{ $koperasi->nama }}</h4>
      <!-- <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Welcome to Veltrix Dashboard</li>
            </ol> -->
    </div>
  <div class="col-md-12">
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



<div class="flash-message"></div>
    <div class="card">
    <div class="card-body">
      {{-- <div class="card-header">List Of Applications</div> --}}
     
      <div>
      <h4>Manage Product and Product Type</h4>
        <span style="margin-right: 15px;">
        <a  href="{{route('koperasi.createProduct')}}" class="btn btn-primary"> <i
            class="fas fa-plus" ></i> Tambah produk</a></span>
      <span > 
        <a  href="{{route('koperasi.addtype')}}" class="btn btn-primary"> <i
            class="fas fa-plus"></i> Tambah jenis produk</a></span>
      </div>
<br><br>
{{-- confirmation delete modal --}}
            <div id="deleteConfirmationModal" class="modal fade" role="dialog">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h4 class="modal-title">Padam Product</h4>
                  </div>
                  <div class="modal-body">
                    Adakah anda pasti?
                  </div>
                  <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-primary" id="delete" name="delete" onclick="selectedItems()">Padam</button>
                    <button type="button" data-dismiss="modal" class="btn">Batal</button>
                  </div>
                </div>
              </div>
            </div>
            {{-- end confirmation delete modal --}}

<div class="table-responsive">
    
    <table id="productTable" class="table table-bordered table-striped dt-responsive"
            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
            <thead>
              <tr style="text-align:center">
              <th></th>  
              <th> No. </th>
                <th> Nama Produk </th>
                <th> Penerangan </th>
                <th> Gambar </th>
                <th> Kuantiti </th>
                <th> Harga </th>
                <th> Status </th>
                <th>Type</th>
                <th> Action </th>
              </tr>
            </thead>
            <tbody id="productTableBody">
        <!-- Table rows will be dynamically generated here -->
        </tbody>
            <!-- @foreach($product as $item)
                                    <tr class ="table-row">
                                        <td> <input type="checkbox" class="product-checkbox" value ="{{$item->id}}" name="ids[]"></td>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->desc }}</td>
                                        <td>
                                          <img src="{{ URL('koperasi-item/'.$item->image)  }}" width="80px">
                                        </td>
                                        <td>{{ $item->quantity_available }}</td>
                                        <td>{{ number_format($item->price,2)}}</td>
                                        <td>
                                         @if($item->status == 0) 
                                         <div class="d-flex justify-content-center"><span class="badge badge-danger">not aivalable</span></div>
                                         @else
                                         <div class="d-flex justify-content-center"><span class="badge badge-success">aivalable</span></div>
                                         @endif
                                        </td>
                                        <td>{{$item->type_name}}</td>
                                        <td>
                                         <a href ="{{ route('koperasi.editProduct',$item->id) }}"> <button type="button" data-dismiss="modal" class="btn btn-primary" id="edit" name="edit">Edit</button></a>
                                           
                                        </td>
                                    </tr>
                                @endforeach -->
          </table>     
          
          <div class="mt-3">
          <span style="margin-right: 15px;">
          <button class="btn btn-primary" id="select-btn" onclick="selectAll()">Select All</button></span>
      <span > 
        
      <button class="btn btn-danger" id="delete-btn" style="display:none" >Delete Selected</button></span>
      </div>

        
</div>

@section('script')
<script src="{{ URL::asset('assets/libs/parsleyjs/parsleyjs.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/inputmask/inputmask.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/jquery-mask/jquery.mask.min.js')}}"></script>

<script>
  loadProducts();
  const checkboxes = document.querySelectorAll('.product-checkbox');
const deleteBtn = document.getElementById('delete-btn');
const selectBtn=document.getElementById('select-btn')
$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

if(checkboxes.length==0)
{
  selectBtn.style.display='none';
}
checkboxes.forEach(checkbox => {
  checkbox.addEventListener('click', () => {
    // Check if at least one checkbox is checked
    if(checkboxes.length==document.querySelectorAll('.product-checkbox:checked').length) {
      selectBtn.innerHTML = "Deselect All";
      deleteBtn.style.display = 'inline';
    }
    else if (document.querySelectorAll('.product-checkbox:checked').length > 0) {
      deleteBtn.style.display = 'inline';
    }
    else {
      deleteBtn.style.display = 'none';
      selectBtn.innerHTML = "Select All";
    }
  });
});



function selectAll()
{
  if (selectBtn.innerHTML === "Deselect All") {
    checkboxes.forEach((checkbox) => {
      checkbox.checked = false;
    });
    selectBtn.innerHTML = "Select All";
    deleteBtn.style.display = 'none';
  } else {
    checkboxes.forEach((checkbox) => {
      checkbox.checked = true;
      
    });
    selectBtn.innerHTML = "Deselect All";
    deleteBtn.style.display = 'inline';
  
  }
 
}

function selectedItems() {

let itemArray = [];
const selectedCheckBox = document.querySelectorAll('.product-checkbox:checked');
selectedCheckBox.forEach((checkbox) => {
  itemArray.push(checkbox.value);
});
$.ajax({
    url: '{{route("koperasi.deleteSelectedProducts")}}',
    type: 'POST',
    data: { itemArray: itemArray },
    success: function(response) {
      console.log('Success:', response);
      location.reload();
    },
    error: function(xhr, status, error) {
      alert(error);
    }
  });
}


function loadProducts() {
  
 var dataTable = $('#productTable');//.DataTable({
    // "lengthMenu": [10, 25, 50, 100],
    // "pageLength": 25,
    // "order": [3, 'asc'],
    // "columnDefs": [{
    //   "targets": [0],
    //   "className": "text-center",
    //   "width": "2%"
    // }, {
    //   "targets": [3, 4, 5, 6],
    //   "className": "text-center"
    // }, {
    //   "targets": [2],
    //   "orderable": true
    // },],
    // "language": {
    //   "paginate": {
    //     "previous": "<",
    //     "next": ">"
    //   },
    //   "search": "",
    //   "searchPlaceholder": "Search..."
    // }
  //});

  // Re-draw the table on input change
  /*$('#productTable_length').on('change', 'select', function () {
    dataTable.page.len($(this).val()).draw();
  });

  // Apply search on input change
  $('#productTable_filter').on('keyup', 'input[type="search"]', function () {
    dataTable.search($(this).val()).draw();
  });*/

  var productTable = $("#productTableBody");
  var productArray = @json($product);
  $.each(productArray, function(index, product) {
  var row = $("<tr>").addClass("table-row");
  row.append($("<td>").append($("<input>").attr({
    type: "checkbox",
    class: "product-checkbox",
    value: product.id,
    name: "ids[]"
  })));
  row.append($("<td>").text(index + 1));
  row.append($("<td>").text(product.name));
  row.append($("<td>").text(product.desc));
  row.append($("<td>").append($("<img>").attr({
    src: "{{ URL('koperasi-item') }}/" + product.image,
    width: "80px"
  })));
  row.append($("<td>").text(product.quantity_available));
  row.append($("<td>").text(product.price.toFixed(2)));
  var status = product.status == 0 ? "not available" : "available";
  row.append($("<td>").append($("<div>").addClass("d-flex justify-content-center").append($("<span>").addClass("badge badge-" + (product.status == 0 ? "danger" : "success")).text(status))));
  row.append($("<td>").text(product.type_name));
  row.append($("<td>").append($("<a>").attr("href", "{{ route('koperasi.editProduct', ':id') }}".replace(":id", product.id)).append($("<button>").attr({
    type: "button",
    "data-dismiss": "modal",
    class: "btn btn-primary",
    id: "edit",
    name: "edit"
  }).text("Edit"))));
  productTable.append(row);    
});
}




$(document).ready(function() {
  $(document).on('click', '.btn-danger', function(){
          $('#deleteConfirmationModal').modal('show');
      });

('.alert').delay(3000).fadeOut();

});


</script>
@endSection

@endsection
