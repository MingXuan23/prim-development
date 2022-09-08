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
            <h4 class="font-size-18">Urus Produk > Produk Item</h4>
        </div>
    </div>
    <div class="d-flex mr-3">
        <button id="edit-product-group" class="btn btn-primary mr-2">Edit Jenis Produk</button>
        <button id="add-product-item" class="btn btn-primary">Tambah Produk Item</button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Gambar</th>
                        <th>Nama</th>
                        <th>Deskripsi</th>
                        <th>Kuantiti Per Slot</th>
                        <th>Harga (RM)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                
                <tbody>
                    <tr>
                      <td>1.</td>
                      <td>
                        <img class="rounded img-fluid bg-dark" id="img-size" src="{{ URL('images/koperasi/default-item.png')}}">
                      </td>
                      <td>Nasi Goreng</td>
                      <td><i>Tiada Deskripsi</i></td>
                      <td>30</td>
                      <td>5.00</td>
                      <td>
                        <button id="edit-item" class="btn btn-primary">Edit</button>
                        <button id="delete-item" class="btn btn-danger">Buang</button>
                      </td>
                    </tr>
                    <tr>
                        <td colspan="7" class="text-center"><i>Tiada Item Buat Masa Sekarang.</i></td>
                    </tr>
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
            <form action="#" method="POST">
                <div class="modal-body">
                    @csrf
                    <input type="text" placeholder="Categori" name="type_name">
                    <input type="number" placeholder="Restock setiap berapa minit" name="duration">
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-light">Tutup</button>
                    <button type="submit" class="btn btn-primary">Kemaskini</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Add/Edit Product Item Modal --}}
<div id="productItemModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @csrf
                <input type="text" placeholder="Nama Item" name="item_name">
                <input type="file" name="item_image">
                <input type="text" placeholder="Deskripsi" name="item_desc">
                <input type="number" placeholder="Kuantiti Per Slot" name="item_quantity">
                <input type="number" placeholder="Harga" name="item_price">
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-light">Tutup</button>
                <button type="submit" class="btn btn-primary">Tambah</button>
            </div>
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
            <div class="modal-body">
                Mahu Buang Item Ini?
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-light">Tutup</button>
                <button type="submit" class="btn btn-danger">Buang</button>
            </div>
        </div>
    </div>
</div>


@endsection

@section('script')

<script>
    $(document).ready(function() {
        $('#edit-product-group').click(function() {
            $('#editProductGroupModal').modal('show')
        })

        $('#add-product-item').click(function() {
            $('#productItemModal').modal('show')
        })

        $('#edit-item').click(function() {
            $('#productItemModal').modal('show')
        })

        $('#delete-item').click(function() {
            $('#deleteProductItemModal').modal('show')
        })
    })
</script>

@endsection