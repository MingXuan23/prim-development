@extends('layouts.master')

@section('css')

<style>
</style>

@endsection

@section('content')

<div class="row align-items-center">
    <div class="col">
        <div class="page-title-box">
            <h4 class="font-size-18">Urus Produk</h4>
        </div>
    </div>
    <div class="d-flex justify-content-end mr-3">
        <button id="add-product-group" class="btn btn-primary">Tambah Jenis Produk</button>
    </div>
</div>

<h5>Isi Pesanan</h5>

<div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="list-group">
            
            <a href="{{ route('admin.merchant.product-item') }}" class="list-group-item list-group-item-action flex-column">
                <div class="d-flex" >
                    
                    <div class="justify-content-start align-self-center">
                        <p class="h4 mb-0">Menu Utama</p>
                    </div>
                    
                    <div class="arrow-icon ml-auto justify-content-end align-self-center mb-0">
                         <p class="h4 mb-0"><i class="fas fa-angle-right"></i></p>
                    </div>
                </div>
            </a>

          </div>
        </div>
      </div>
    </div>
</div>

{{-- Add Product Group Modal --}}
<div id="addProductGroupModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Jenis Produk</h4>
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
                    <button type="submit" class="btn btn-primary">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')

<script>
    $(document).ready(function() {
        $('#add-product-group').click(function() {
            $('#addProductGroupModal').modal('show')
        })
    })
</script>

@endsection