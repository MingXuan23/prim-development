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

<div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
            @if(Session::has('success'))
            <div class="alert alert-success">
                <p>{{ Session::get('success') }}</p>
            </div>
            @endif
            <div class="list-group">
                @forelse($group as $row)
                <a href="{{ route('admin.merchant.product-item', $row->id) }}" class="list-group-item list-group-item-action flex-column">
                    <div class="d-flex" >
                        
                        <div class="justify-content-start align-self-center">
                            <p class="h4 mb-0">{{ $row->name }}</p>
                        </div>
                        
                        <div class="arrow-icon ml-auto justify-content-end align-self-center mb-0">
                            <p class="h4 mb-0"><i class="fas fa-angle-right"></i></p>
                        </div>
                    </div>
                </a>
                @empty
                <div class="row justify-content-center align-items-center">
                    <i>Tiada Jenis Produk</i>
                </div>
                @endforelse
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
            <form action="{{ route('admin.merchant.store-group') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert" id="popup" style="display: none"></div>
                    <div class="row justify-content-between align-items-center m-2">
                        
                        <div class="form-group required">
                            <label class="col">Nama Kategori</label>
                            <div class="col">
                                <input class="form-control" type="text" placeholder="Categori" name="name" id="name" required>
                            </div>
                        </div>
                        <div class="form-group required">
                            <label class="col">Durasi perbuatan produk (minit)</label>
                            <div class="col">
                                <input class="form-control" type="number" placeholder="Durasi" name="duration" id="duration" required>
                            </div>
                        </div>
                        
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-light">Tutup</button>
                    <button type="submit" id="add_product_group" class="btn btn-primary">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')

<script>
    $(document).ready(function() {
        $('.alert-success').delay(2000).fadeOut()

        $('#add-product-group').click(function() {
            $('#popup').hide()
            $('#popup').empty()
            $('#addProductGroupModal').modal('show')
        })

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })

        $('#add_product_group').click(function() {
            var name = $('#name').val()
            var duration = $('#duration').val()
            if(name == "" && duration == "")
            {
                $('#popup').addClass('alert-danger')
                $('#popup').empty().append('Sila isi ruangan kosong')
                $('#popup').show()
            }
        })
    })
</script>

@endsection