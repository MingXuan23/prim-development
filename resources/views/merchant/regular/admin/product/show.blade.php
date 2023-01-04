@extends('layouts.master')

@section('css')

<style>
#img-size
{
  width: 100px;
  height: 100px;
  object-fit: cover;
}

</style>

@endsection

@section('content')

<div class="row align-items-center">
    <div class="col">
        <div class="page-title-box">
            <h4 class="font-size-18"><a href="{{ route('admin-reg.product-group') }}" class="text-muted">Urus Produk</a> <i class="fas fa-angle-right"></i> {{ $group->name }}</h4>
        </div>
    </div>
    <div class="d-flex justify-content-end mr-3">
        <button id="delete-product-group-modal" class="btn btn-danger mr-2"><i class="fas fa-trash-alt"></i> Jenis Produk</button>
        <button id="edit-product-group" class="btn btn-primary mr-2"><i class="fas fa-pencil-alt"></i> Jenis Produk</button>
    </div>
</div>

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
            <button id="add-product-item-modal" class="btn btn-primary"><i class="fas fa-plus-circle"></i> Produk Item</button>
        </div>

        <div class="table-responsive">
            <table class="table table-striped" id="dataTable" width="100%" cellspacing="0">
                
                <thead class="thead-dark">
                    <tr>
                        <th style="width:1%" scope="col">No.</th>
                        <th style="width:12%" class="text-center">Gambar</th>
                        <th style="width:10%">Nama</th>
                        <th style="width:12%">Deskripsi</th>
                        <th style="width:12%" class="text-center">Inventori</th>
                        <th style="width:12%" class="text-center">Harga (RM)</th>
                        <th style="width:5%" class="text-center">Status</th>
                        <th style="width:10%" class="text-center">Action</th>
                    </tr>
                </thead>
                
                <tbody>
                    @forelse($item as $row)
                    <tr>
                        <td class="align-middle" scope="row"><b>{{ $loop->iteration }}</b></td>
                        <td class="align-middle text-center">
                            <img class="rounded img-fluid bg-dark" id="img-size" src="
                            @if($row->image == null)
                            {{ URL('images/koperasi/default-item.png') }}
                            @else
                            {{ URL($image_url.$row->image) }}
                            @endif
                            ">
                        </td>
                        <td class="align-middle">{{ $row->name }}</td>
                        <td class="align-middle">{!! $row->desc ?: "<i>Tiada Deskripsi</i>" !!}</td>
                        <td class="align-middle text-center">
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Inventori
                                  <span class="badge badge-primary badge-pill">{!! $row->type == "have inventory" ? $row->quantity_available : "Tiada Inventori" !!}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Kuantiti Dijual
                                  <span class="badge badge-primary badge-pill">{{ $row->selling_quantity }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Kata Nama Kuantiti
                                  <span class="badge badge-primary badge-pill">{{ $row->collective_noun }}</span>
                                </li>
                              </ul>
                        </td>
                        <td class="align-middle text-center">
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Seunit
                                  <span class="badge badge-primary badge-pill">{{ number_format($row->price, 2, '.', '') }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Setiap Kuantiti
                                  <span class="badge badge-primary badge-pill">{{ number_format($row->price * $row->selling_quantity, 2, '.', '') }}</span>
                                </li>
                              </ul>
                        </td>
                        <td class="text-center align-middle">
                            {!! ($row->status == 1) 
                                ? "<span class='badge rounded-pill bg-success text-white p-2'>Aktif</span>"
                                : "<span class='badge rounded-pill bg-danger text-white p-2'>Tidak Aktif</span>" !!}
                        </td>
                        <td class="align-middle text-center">
                            <a href="{{ route('admin-reg.edit-item', ['id' => $group->id, 'item' => $url_name[$row->id]]) }}" class="edit-item-modal btn btn-primary"><i class="fas fa-pencil-alt"></i></a>
                            <button data-item-id="{{ $row->id }}" data-image-url="{{ $image_url }}" class="delete-item-modal btn btn-danger"><i class="fas fa-trash-alt"></i></button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center"><i>Tiada Item Buat Masa Sekarang.</i></td>
                    </tr>
                    @endforelse
                </tbody>
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
                    <button type="button" data-dismiss="modal" class="btn btn-light">Tutup</button>
                    <button type="submit" id="update_group" class="btn btn-primary">Kemaskini</button>
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
                    <button type="button" data-dismiss="modal" class="btn btn-light">Tutup</button>
                    <button type="submit" class="delete-group-btn btn btn-danger">Buang</button>
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
                                <label class="control-label">Nama Item</label>
                                <input class="form-control" type="text" placeholder="Nama Item" name="item_name" id="item_name" required>
                            </div>
                            
                        </div>

                        <div class="col" style="margin-top: 13px">
                            <div class="form-group required custom-file">
                                <label class="custom-file-label" for="item_image">Gambar Item</label>
                                <input class="custom-file-input" type="file" name="item_image" id="item_image" accept=".jpg,.jpeg,.png">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <div class="form-group required">
                                <label class="control-label">Deskripsi Item</label>
                                <input class="form-control" type="text" placeholder="Deskripsi" name="item_desc" id="item_desc">
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col">
                            <label class="col-form-label pt-0">Inventori</label>
                            <div class="col-sm-10">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="inventory" id="no_inventory" value="no inventory" checked>
                                    <label class="form-check-label" for="no_inventory">
                                        Tiada
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="inventory" id="have_inventory" value="have inventory">
                                    <label class="form-check-label" for="have_inventory">
                                        Ada
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
                                <input class="form-control" type="number" placeholder="Kuantiti" name="item_quantity" id="item_quantity" min="1" step="1">
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col">
                            <label class="control-label">Kata Nama Kuantiti</label>
                            <input class="form-control" type="text" placeholder="Kata Nama Kuantiti" name="collective_noun" id="collective_noun" value="Unit" required>
                        </div>
                        <div class="col">
                            <label class="control-label">Kuantiti Yang Ingin Dijual</label>
                            <input class="form-control" type="number" placeholder="Kuantiti" name="selling_quantity" id="selling_quantity" min="1" step="1" value="1" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col">
                            <div class="form-group required">
                                <label class="control-label">Harga (RM)</label>
                                <input class="form-control" type="number" placeholder="Harga" name="item_price" id="item_price" min="0.01" step="any" pattern="^\d*(\.\d{1,2})?$" required>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="group_id" value="{{ $group->id }}">
                    <input type="hidden" name="org_id" value="{{ $group->organization_id }}">
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-light">Tutup</button>
                    <button type="submit" id="add-item" class="btn btn-primary">Tambah</button>
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
                <button type="button" data-dismiss="modal" class="btn btn-light">Tutup</button>
                <button type="button" class="delete-item-btn btn btn-danger">Buang</button>
            </div>
            
        </div>
    </div>
</div>


@endsection

@section('script')

<script>
    $(document).ready(function() {
        $('.alert-success').delay(2000).fadeOut()
        $('.alert-danger').delay(3000).fadeOut()

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

        $('#edit-product-group').click(function() {
            $('#editProductGroupModal').modal('show')
        })

        $('#update_group').click(function() {
            var name = $('#name').val()
            if(name == "")
            {
                $('#popup').addClass('alert-danger')
                $('#popup').empty().append('Sila isi ruangan kosong')
                $('#popup').show()
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
                    $('#popup').addClass('alert-danger')
                    $('#popup').empty().append('Sila isi ruangan kosong')
                    $('#popup').show()
                }
            } else {
                if(name == "" && price == "" && noun == "" && selling_quantity == "") {
                    $('#popup').addClass('alert-danger')
                    $('#popup').empty().append('Sila isi ruangan kosong')
                    $('#popup').show()
                }
            }
        })

        var item_id, image_url
        
        $('.delete-item-modal').click(function() {
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

        $('.delete-item-btn').click(function() {
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