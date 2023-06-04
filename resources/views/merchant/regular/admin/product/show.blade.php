@extends('layouts.master')

@section('css')

<link href="{{ URL::asset('assets/css/required-asterick.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{ URL::asset('assets/css/datatable.css')}}">
@include('layouts.datatable')

<style>
#img-size
{
  width: 100px;
  height: 100px;
  object-fit: cover;
}

/* style all input elements with a required attribute */
/* input:required {
    border-color: #800000;
  border-width: 3px;
} */

input:required:invalid {
    border: 1px solid red;
    opacity: 1;
}
ul.pagination{
    justify-content: flex-start!important;
}
nav .pagination{
    margin-top:20px;
    display: flex;
    justify-content: center;
}
.page-item.active .page-link{
    background-color:#5b626b!important;
    border-color: #5b626b!important;
    color:#ffffff!important;
}
.page-link {
    color:#5b626b!important;
}
.active{
    background-color: #5b626b!important;
    border: 2px solid #5b626b!important;
    color:#ffffff!important;
}
.inactive{
    background-color: #af1717!important;
    border: 2px solid #af1717!important;
    color:#ffffff!important;
}
.grey-badge{
    background-color: #5b626b!important;
    color:#ffffff!important;
}
.form-control{
    border: 2px solid #5b626b6c!important;
}
.form-control:focus{
    outline: none;
    border: 2px solid #5b626b!important;
}
.btn-grey{
    background-color: #5b626b!important;
    border: 2px solid #5b626b!important;
    color:#ffffff!important;
}
.btn-grey:hover{
    background-color: #ffffff!important;
    color:#5b626b!important;
    border: 2px solid #5b626b!important;
}
.btn-red{
    background-color: #af1717!important;
    border: 2px solid #af1717!important;
    color:#ffffff!important;
}
.btn-red:hover{
    background-color: #ffffff!important;
    color:#af1717!important;
    border: 2px solid #af1717!important;
}
.long-content  {
   max-height: 200px!important;
   overflow-y: auto!important;
   white-space: pre-wrap;
   text-align: justify;
}
input[type="file"]{
    border: none !important;
}
@media screen and (max-width: 768px) { 
  #img-size
  {
    width: 50px;
    height: 50px;
    object-fit: cover;
  }

  .desc{
    font-size: 10px;
  }
}

</style>

@endsection

@section('content')

<div class="row justify-content-between align-items-center">
    <div class="col">
        <div class="page-title-box">
            <h4 class="font-size-18"><a href="{{ route('admin-reg.product-group') }}" class="text-muted">Urus Produk</a> <i class="fas fa-angle-right"></i> {{ $group->name }}</h4>
        </div>
    </div>
    <div class="col d-flex flex-row-reverse mr-3">
        <button id="delete-product-group-modal" class="btn btn-red m-1"><i class="fas fa-trash-alt"></i> Jenis Produk</button>
        <button id="edit-product-group" class="btn btn-grey m-1"><i class="fas fa-pencil-alt"></i> Jenis Produk</button>
    </div>
</div>

<input type="hidden" name="group_id" id="group_id" value="{{ $group->id }}">

<div class="card">
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
        <div class="d-flex justify-content-end mb-3">
            <button id="import-product-item-modal" class="btn btn-grey m-1"><i class="fas fa-plus-circle"></i> Import Product</button>
            <button id="add-product-item-modal" class="btn btn-grey m-1"><i class="fas fa-plus-circle"></i> Produk Item</button>
        </div>

        <div class="table-responsive">
            <table class="table table-hover responsive" id="productTable" width="100%" >
                
                <thead class="thead-dark">
                    <tr>
                        <th style="width:1%">No.</th>
                        <th style="width:10%" class="text-center">Gambar</th>
                        <th style="width:10%">Nama</th>
                        <th style="width:20%">Deskripsi</th>
                        <th style="width:10%" class="text-center">Inventori</th>
                        <th style="width:8%" class="text-center">Harga (RM)</th>
                        <th style="width:3%" class="text-center">Status</th>
                        <th style="width:10%" class="text-center">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

{{-- Edit Product Group Modal --}}
<div id="editProductGroupModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Kemaskini Jenis Produk</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('admin-reg.update-group') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group required">
                        <label class="col">Nama Kategori</label>
                        <div class="col">
                            <input class="form-control" type="text" placeholder="Categori" name="name" id="name" value="{{ $group->name }}" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="group_id" value="{{ $group->id }}">
                    <button type="button" data-dismiss="modal" class="btn btn-grey">Tutup</button>
                    <button type="submit" id="update_group" class="btn btn-grey">Kemaskini</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Delete Product Group Modal --}}
<div id="deleteProductGroupModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Buang Jenis Produk - {{ $group->name }}</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('admin-reg.destroy-group') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('delete')
                <div class="modal-body">
                    <input type="hidden" name="group_id" value="{{ $group->id }}">
                    <input type="hidden" name="image_url" value="{{ $image_url }}">
                    Adakah anda pasti mahu buang jenis produk ini? Semua produk item jenis produk <strong>{{ $group->name }}</strong> juga akan dibuang. 
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-grey">Tutup</button>
                    <button type="submit" class="delete-group-btn btn btn-red">Buang</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Add Product Item Modal --}}
<div id="addProductItemModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title item-modal-title">Tambah Item</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('admin-reg.store-item') }}" class="form-store" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert" id="popup" style="display: none"></div>
                    <div class="row justify-content-center align-items-center">
                        <div class="col">
                            <div class="form-group required">
                                <label class="control-label">Nama</label>
                                <input class="form-control" type="text" placeholder="Nama Item" name="item_name" id="item_name" autocomplete="off" required>
                            </div>
                            
                        </div>

                        <div class="col" style="margin-bottom: 8px">
                            <div class="form-group custom-file">
                                <label class="custom-file-label" for="item_image">Gambar</label>
                                <input class="custom-file-input" type="file" name="item_image" id="item_image" accept=".jpg,.jpeg,.png">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label">Deskripsi</label>
                                <textarea class="form-control" rows="6" cols="60" placeholder="Deskripsi" name="item_desc" id="item_desc" autocomplete="off"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col">
                            <label class="col-form-label pt-0">Inventori</label>
                            <div class="col-sm-10">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="inventory" id="have_inventory" value="have inventory"checked>
                                    <label class="form-check-label" for="have_inventory">
                                        Ada
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="inventory" id="no_inventory" value="no inventory">
                                    <label class="form-check-label" for="no_inventory">
                                        Tiada
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col">
                            <label class="col-form-label pt-0">Status</label>
                            <div class="col-sm-10">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="active" value="1" checked>
                                    <label class="form-check-label" for="active">
                                        Aktif
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="inactive" value="0">
                                    <label class="form-check-label" for="inactive">
                                        Tidak Aktif
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                    </div>

                    <div class="row quantity-section" hidden>
                        <div class="col">
                            <div class="form-group required">
                                <label class="control-label">Kuantiti Dalam Inventori</label>
                                <input class="form-control" type="number" placeholder="Kuantiti" name="item_quantity" id="item_quantity" min="1" step="1" oninput="this.value = Math.round(this.value);">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <div class="form-group required">
                                <label class="control-label">Nama Unit</label>
                                <input class="form-control" type="text" placeholder="Kata Nama Kuantiti" name="collective_noun" id="collective_noun" value="Unit" autocomplete="off" required>
                            </div>
                        </div>
                        {{-- <div class="col">
                            <div class="form-group required">
                                <label class="control-label">Pakej</label>
                                <input class="form-control" type="number" placeholder="Kuantiti" name="selling_quantity" id="selling_quantity" min="1" step="1" value="1" oninput="this.value = Math.round(this.value);" required>
                            </div>
                        </div> --}}
                    </div>
                    
                    <div class="row">
                        <div class="col">
                            <div class="form-group required">
                                <label class="control-label">Harga Per Unit (RM)</label>
                                <input class="form-control" type="number" placeholder="Harga" name="item_price" id="item_price" min="0.01" step="any" pattern="^\d*(\.\d{1,2})?$" required>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="group_id" value="{{ $group->id }}">
                    <input type="hidden" name="org_id" value="{{ $group->organization_id }}">
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-grey">Tutup</button>
                    <button type="submit" id="add-item" class="btn btn-grey">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Delete Product Item Modal --}}
<div id="deleteProductItemModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Buang Item</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            
            <div class="modal-body delete-body">
            
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-grey">Tutup</button>
                <button type="button" class="delete-item-btn btn btn-red">Buang</button>
            </div>
            
        </div>
    </div>
</div>

{{--import modal--}}
<div class="modal fade" id="importProductItemModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('importMerchantProduct') }}" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <input type="hidden" name="type" id="type" value="{{$group->id}}">
                    <label>*Import Excel file that contains three column about the products(name, quantity & price)</label>
                    <div class="form-group">
                        
                        <input type="file" name="file" accept=".xls, .xlsx, .ods, .csv" required>
                    </div>
                    <input type="hidden" name="organ" id="organ" value="{{$org_id}}">
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-grey">Import</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>       
{{--end import modal--}}
@endsection

@section('script')

<script>
    $(document).ready(function() {
        $('.alert-success').delay(2000).fadeOut()
        $('.alert-danger').delay(3000).fadeOut()

        let gId = $('#group_id').val()

        fetch_data(gId)
        $(".custom-file-input").on("change", function() {
            var idxDot = this.value.lastIndexOf(".") + 1
            var extFile = this.value.substr(idxDot, this.value.length).toLowerCase()
            if (extFile=="jpg" || extFile=="jpeg" || extFile=="png"){
                var fileName = $(this).val().split("\\").pop()
                $(this).siblings(".custom-file-label").addClass("selected").html(fileName)
            }else{
                alert("Only jpg/jpeg and png files are allowed!")
                $(this).val('')
            }  
        })
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })

        function fetch_data(gId = '') {
            productTable = $('#productTable').DataTable({
                "searching": true,
                "bLengthChange": true,
                "bPaginate": true,
                "info": false,
                "orderable": false,
                "ordering": true,
                processing: true,
                serverSide: true,
                "language": {
                    "zeroRecords": "Tiada Item Buat Masa Sekarang."
                },
                ajax: {
                    url: "{{ route('admin-reg.get-pi') }}",
                    data: {
                        id:gId,
                    },
                    type: 'GET',
                },
                'columnDefs': [{
                    "targets": [0, 1, 2, 3, 4, 5, 6, 7], // your case first column
                    "className": "align-middle text-center", 
                },
                { "responsivePriority": 1, "targets": 2 },
                ],
                columns: [{
                    "data": null,
                    searchable: false,
                    orderable: false,
                    "sortable": false,
                    render: function (data, type, row, meta) {
                        let i = meta.row + meta.settings._iDisplayStart + 1;
                        return  "<b>"+i+"</b>"
                    }
                },{
                    data: "image",
                    name: 'image',
                    orderable: false,
                    searchable: false,
                }, {
                    data: "name",
                    name: 'name',
                    orderable: true,
                    searchable: true,
                }, {
                    data: "desc",
                    name: 'desc',
                    orderable: true,
                    searchable: true,
                    render: function (data, type, row) {
                        // Add a custom CSS class to the cell if the content is too long
                        var cellClass = (data.length > 50) ? 'long-content' : '';
                        return '<div class="' + cellClass + '">' + data + '</div>';
                    }
                }, {
                    data: "inventory",
                    name: 'inventory',
                    orderable: true,
                    searchable: true,
                },{
                    data: 'price',
                    name: 'price',
                    orderable: true,
                    searchable: true,
                },{
                    data: 'status',
                    name: 'status',
                    orderable: true,
                    searchable: true,
                },{
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                },],
                "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
                "pageLength": 10, // Number of items per page
            });
        }

        $('#edit-product-group').click(function() {
            $('#editProductGroupModal').modal('show')
        })

        $('#update_group').click(function() {
            var name = $('#name').val()
            if(name == "")
            {
                $('#popup').addClass('alert-danger').empty().append('Sila isi ruangan kosong').show()
            }
        })

        $('#delete-product-group-modal').click(function() {
            $('#deleteProductGroupModal').modal('show')
        })

        $('#add-product-item-modal').click(function() {
            $('#addProductItemModal').modal('show')
        })

        $('#have_inventory').click(function() {
            $('.quantity-section').prop('hidden', false)
            $("#item_quantity").prop('required',true)
        })

        $('#no_inventory').click(function() {
            $('.quantity-section').prop('hidden', true)
            $("#item_quantity").prop('required',false)
        })
        $('#import-product-item-modal').click(function() {
            $('#importProductItemModal').modal('show');
        })
        $("#item_price").on('keydown', function(e){
            var input = $(this);
            var oldVal = input.val();
            var regex = new RegExp(input.attr('pattern'), 'g');

            setTimeout(function(){
                var newVal = input.val();
                if(!regex.test(newVal)){
                input.val(oldVal); 
                }
            }, 1);
        });

        $('#add-item').click(function() {
            var name = $('#item_name').val()
            var quantity = $('#item_quantity').val()
            var price = $('#item_price').val()
            var noun = $('#collective_noun').val()
            var selling_quantity = $('#selling_quantity').val()

            if($('#have_inventory').is(':checked') == true){
                if(name == "" && quantity == "" && price == "" && noun == "" && selling_quantity == ""){
                    $('#popup').addClass('alert-danger').empty().append('Sila isi ruangan kosong').show()
                }
            } else {
                if(name == "" && price == "" && noun == "" && selling_quantity == "") {
                    $('#popup').addClass('alert-danger').empty().append('Sila isi ruangan kosong').show()
                }
            }
        })

        var item_id, image_url
        
        $(document).on('click', '.delete-item-modal', function(){
            item_id = $(this).attr('data-item-id')
            image_url = $(this).attr('data-image-url')
            
            $.ajax({
                url: "{{ route('admin-reg.destroy-body') }}",
                method: "GET",
                data: {i_id:item_id},
                beforeSend:function() {
                    $('.delete-body').empty()
                    $('#deleteProductItemModal').modal('show')
                },
                success:function(result) {
                    $('.delete-body').append(result.body)
                },
                error:function(result) {
                    console.log(result.responseText)
                }
            })
            
        })
        
        $(document).on('click', '.delete-item-btn', function(){
            $.ajax({
                url: "{{ route('admin-reg.destroy-item') }}",
                method: "DELETE",
                data: {i_id:item_id, image_url:image_url},
                beforeSend:function() {
                    $('#deleteProductItemModal').modal('show')
                },
                success:function(result) {
                    location.reload()
                },
                error:function(result) {
                    console.log(result.responseText)
                }
            })
        })
    })
</script>

@endsection