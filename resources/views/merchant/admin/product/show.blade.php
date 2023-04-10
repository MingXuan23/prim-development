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
            <h4 class="font-size-18"><a href="{{ route('admin.merchant.product') }}" class="text-muted">Urus Produk</a> <i class="fas fa-angle-right"></i> {{ $group->name }}</h4>
        </div>
    </div>
    <div class="d-flex mr-3">
        <button id="delete-product-group-modal" class="btn btn-danger mr-2">Buang Jenis Produk</button>
        <button id="edit-product-group" class="btn btn-primary mr-2">Edit Jenis Produk</button>
        <button id="add-product-item-modal" class="btn btn-primary">Tambah Produk Item</button>
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
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th class="text-center">Gambar</th>
                        <th>Nama</th>
                        <th>Deskripsi</th>
                        <th>Kuantiti Per Slot</th>
                        <th>Harga (RM)</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                
                <tbody>
                    @php($i = 0)
                    @forelse($item as $row)
                    <tr>
                      <td class="align-middle">{{ ++$i }}.</td>
                      <td class="text-center">
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
                      <td class="align-middle">{{ $row->quantity }}</td>
                      <td class="align-middle">{{ number_format($row->price, 2, '.', '') }}</td>
                      <td class="align-middle text-center">
                        <a href="{{ route('admin.merchant.edit-item', ['id' => $group->id, 'item' => $row->id]) }}" class="edit-item-modal btn btn-primary"><i class="fas fa-pencil-alt"></i></a>
                        <button data-item-id="{{ $row->id }}" data-image-url="{{ $image_url }}" class="delete-item-modal btn btn-danger"><i class="fas fa-trash-alt"></i></button>
                      </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center"><i>Tiada Item Buat Masa Sekarang.</i></td>
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
                <h4 class="modal-title">Edit Jenis Produk</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('admin.merchant.update-group') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row justify-content-between align-items-center m-2">
                        <div class="form-group required">
                            <label class="col">Nama Kategori</label>
                            <div class="col">
                                <input class="form-control" type="text" placeholder="Categori" name="name" id="name" value="{{ $group->name }}" required>
                            </div>
                        </div>
                        <div class="form-group required">
                            <label class="col">Durasi perbuatan produk (minit)</label>
                            <div class="col">
                                <input class="form-control" type="number" placeholder="Durasi" name="duration" id="duration" value="{{ $group->duration }}" required>
                            </div>
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
            <form action="{{ route('admin.merchant.destroy-group') }}" method="POST" enctype="multipart/form-data">
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
            <form action="{{ route('admin.merchant.store-item') }}" method="POST" enctype="multipart/form-data">
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
                                <input class="custom-file-input" type="file" name="item_image" id="item_image">
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
                    <div class="row">
                        <div class="col">
                            <div class="form-group required">
                                <label class="control-label">Kuantiti Setiap Durasi</label>
                                <input class="form-control" type="number" placeholder="Kuantiti Per Slot" name="item_quantity" id="item_quantity" min="1" step="1" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group required">
                                <label class="control-label">Harga (RM)</label>
                                <input class="form-control" type="number" placeholder="Harga" name="item_price" id="item_price" min="0.01" step="any" required>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="group_id" value="{{ $group->id }}">

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
            var fileName = $(this).val().split("\\").pop();
            $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
        });
        
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
            var duration = $('#duration').val()
            if(name == "" && duration == "")
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

        $('#add-item').click(function() {
            var name = $('#item_name').val()
            var quantity = $('#item_quantity').val()
            var price = $('#item_price').val()
            
            if(name == "" && quantity == "" && price == "")
            {
                $('#popup').addClass('alert-danger')
                $('#popup').empty().append('Sila isi ruangan kosong')
                $('#popup').show()
            }
        })

        var item_id, image_url
        
        $('.delete-item-modal').click(function() {
            item_id = $(this).attr('data-item-id')
            image_url = $(this).attr('data-image-url')
            console.log(image_url)
            $.ajax({
                url: "{{ route('admin.merchant.get-item-destroy') }}",
                method: "GET",
                data: {i_id:item_id},
                beforeSend:function() {
                    $('#deleteProductItemModal').modal('show')
                },
                success:function(result) {
                    $('.delete-body').empty().append(result.body)
                },
                error:function(result) {
                    console.log(result.responseText)
                }
            })
            
        })

        $('.delete-item-btn').click(function() {
            $.ajax({
                url: "{{ route('admin.merchant.destroy-item') }}",
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