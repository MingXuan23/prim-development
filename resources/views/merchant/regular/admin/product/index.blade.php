@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/css/required-asterick.css')}}" rel="stylesheet">

<style>
</style>

@endsection

@section('content')

<div style="padding-top: 12px" class="row">
    <div class="col-md-12 ">
        <div class=" align-items-center">
            <div class="form-group card-title">
                <select name="org" id="org_dropdown" class="form-control col-md-12">
                    <option value="">Pilih Organisasi</option>
                    @foreach($merchant as $row)
                    <option value="{{ $row->id }}">{{ $row->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

<div class="row align-items-center">
    <div class="col">
        <div class="page-title-box">
            <h4 class="font-size-18">Urus Produk</h4>
        </div>
    </div>
    <div class="add-btn d-flex justify-content-end mr-3">
        <button id="add-product-group" class="btn btn-primary"><i class="fas fa-plus-circle"></i> Jenis Produk</button>
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
            @elseif(Session::has('error'))
            <div class="alert alert-danger">
                <p>{{ Session::get('error') }}</p>
            </div>
            @endif
            <div class="alert alert-success msg"></div>

            <div class="list-group"></div>
            
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
            @csrf
            <div class="modal-body">
                <div class="alert" id="popup" style="display: none"></div>
                <div class="form-group required">
                    <label>Nama Jenis Produk</label>
                    
                    <input class="form-control" type="text" placeholder="Jenis Produk" name="name" id="name" required>
                    
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-light">Tutup</button>
                <button type="submit" class="btn btn-primary add-pg">Tambah</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')

<script>
    $(document).ready(function() {
        $('.alert-success').delay(2000).fadeOut()

        let orgId,
            list = $('.list-group'), 
            noGroup = '<div class="row justify-content-center align-items-center"><i>Tiada Jenis Produk</i></div>'
            btnAdd = $('#add-product-group'),
            errMsg = $('.msg'),
            dropdownLength = $('#org_dropdown').children('option').length

        errMsg.hide()
        btnAdd.hide()
        list.append(noGroup)

        if(dropdownLength > 1) {
            $('#org_dropdown option')[1].selected = true
            orgId = $('#org_dropdown option')[1].value
            fetch_data(orgId)
        }

        $('#org_dropdown').change(function() {
            orgId = $("#org_dropdown option:selected").val()
            $('#org_id').val(orgId)
            fetch_data(orgId)
        })

        function fetch_data(orgId = '')
        {
            $.ajax({
                type: 'GET',
                url: '{{ route("admin-reg.get-group") }}',
                data: {id: orgId},
                success:function(result){
                    if(orgId == '') {
                        list.empty().append(noGroup)
                        btnAdd.hide()
                    } else if(result.response == '') {
                        list.empty().append(noGroup)
                        btnAdd.show()
                    } else {
                        $('.list-group').empty().append(result.response)
                        btnAdd.show()
                    }
                },
                error:function(result){
                    console.log(result.responseText)
                }
            })
        }

        $('#add-product-group').click(function() {
            $('#popup').hide().empty()
            $('#addProductGroupModal').modal('show')
        })

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })

        $(document).on('click', '.add-pg', function(){
            var name = $('#name').val()
            
            if(name == "") {
                $('#popup').addClass('alert-danger').empty().append('Sila isi ruangan kosong').show()
            } else {             
                $.ajax({
                    type: 'POST',
                    url: '{{ route("admin-reg.store-group") }}',
                    data: {id: orgId, name:name},
                    success:function(result){
                        fetch_data(orgId)
                        $('#addProductGroupModal').modal('hide')
                        $('.msg').empty().show().append(result.message)
                    },
                    error:function(result){
                        console.log(result.responseText)
                    },
                    complete:function(){
                        $('.msg').delay(2000).fadeOut()
                    }
                })
            }
        })
    })
</script>

@endsection