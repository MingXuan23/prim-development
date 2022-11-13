@extends('layouts.master')

@section('css')
<link rel="stylesheet" href="{{ URL::asset('assets/css/datatable.css')}}">
@include('layouts.datatable')
@endsection

@section('content')

<div class="row align-items-center">
    <div class="col">
        <div class="page-title-box">
            <h4 class="font-size-18">Semua Pesanan Anda</h4>
        </div>
    </div>
    <div class="d-flex justify-content-end mr-3">
        <a href="{{ route('merchant.order-history') }}" class="btn btn-primary">Sejarah Pesanan</a>
    </div>
</div>

{{-- <div class="card card-primary card-body">
    <div class="row">
        <div class="col">
            <div class="form-group">
                <label>Pesanan Berdasarkan Hari</label>
                <select class="form-control" data-parsley-required-message="Sila pilih hari" id="order_day" required>
                    <option value="" selected>Semua Pesanan</option>
                    <option value="1">Isnin</option>
                    <option value="2">Selasa</option>
                    <option value="3">Rabu</option>
                    <option value="4">Khamis</option>
                    <option value="5">Jumaat</option>
                    <option value="6">Sabtu</option>
                    <option value="0">Ahad</option>
                </select>
            </div>
        </div>
    </div>
</div> --}}

<div class="card mb-3">
    <div class="card-header">
      <i class="ti-email mr-2"></i>Pesanan Anda</div>
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

        <div class="flash-message"></div>

        <div class="table-responsive">
            <table class="table table-striped table-bordered" id="orderTable" width="100%" cellspacing="0">
    
            <thead>
                <tr>
                <th style="width: 2%">No.</th>
                <th style="width: 15%">Peniaga</th>
                <th style="width: 10%">No Telefon</th>
                <th style="width: 10%">Tarikh</th>
                <th style="width: 15%">Nota</th>
                <th style="width: 10%">Jumlah (RM)</th>
                <th style="width: 10%" class="text-center">Status</th>
                <th style="width: 15%" class="text-center">Action</th>
                </tr>
            </thead>
            </table>
        </div>
    </div>
</div>

{{-- confirmation modal --}}
<div id="confirmationModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-light">Kembali</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')

<script>
    $(document).ready(function() {
        var orderTable;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })

        fetch_data()

        function fetch_data() {
            orderTable = $('#orderTable').DataTable({
                pageLength: 5,
                lengthMenu: [[5, 15, 30, -1], [5, 15, 30, "Semua"]],
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('merchant.get-all-orders') }}",
                    data: {},
                    type: 'GET',

                },
                language : {
                    "infoEmpty": "Tiada Rekod",
                    "emptyTable": "<i>Tiada Pesanan Buat Masa Sekarang</i>",
                    "lengthMenu": "Papar _MENU_ rekod setiap halaman",
                    "zeroRecords": "<i>Tiada Pesanan Buat Masa Sekarang</i>",
                    "info": "Memaparkan halaman _PAGE_ daripada _PAGES_",
                    "paginate": {
                        "next":       "Seterusnya",
                        "previous":   "Sebelumnya"
                    },
                    "search": "Cari:",
                },
                'columnDefs': [{
                    "targets": [0, 1, 2, 3, 4, 5, 7], // your case first column
                    "className": "align-middle",
                },],
                columns: [{
                    "data": null,
                    searchable: false,
                    "sortable": false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1 + ".";
                    }
                }, {
                    data: "nama",
                    name: 'nama',
                }, {
                    data: "telno",
                    name: 'telno',
                }, {
                    data: "pickup_date",
                    name: 'pickup_date',
                }, {
                    data: "note",
                    name: 'note',
                }, {
                    data: 'total_price',
                    name: 'total_price',
                }, {
                    data: 'status',
                    name: 'status',
                    orderable: false,
                    searchable: false,
                    "className": "align-middle text-center",
                }, {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                },],
            });
        }

        // $('#order_day').change(function() {
        //     var filter = $(this).children(':selected').val()
        //     $('#orderTable').DataTable().destroy()
        //     fetch_data(filter)
        // })

        var btn = "<button type='button' data-dismiss='modal' class='btn btn-light'>Kembali</button>"
        var order_id

        $(document).on('click', '.btn-cancel-order', function(){
            order_id = $(this).attr('data-order-id')
            $('.modal-title').empty().append('Batalkan Pesanan')
            $('.modal-body').empty().append('Anda Pasti Untuk Batalkan Pesanan Ini?')
            $('.modal-footer').empty().append(btn + "<button type='button' id='destroy_order' class='btn btn-danger'>Batal</button>")
            $('#confirmationModal').modal('show')
            destroyOrder(order_id)
        })

        function destroyOrder(order_id)
        {
            $('#destroy_order').click(function() {
                $.ajax({
                    url: "{{ route('merchant.delete-order') }}",
                    method: "DELETE",
                    data: {o_id:order_id},
                    beforeSend:function() {

                    },
                    success:function(result) {
                        $('div.flash-message').html(result)

                        orderTable.ajax.reload()
                    },
                    error:function(result) {
                        $('div.flash-message').html(result)
                        console.log(result.responseText)
                    },
                    complete:function() {
                        $('#confirmationModal').modal('hide')
                    },
                })
            })
        }

        $('.alert').delay(3000).fadeOut();

    })
    
</script>

@endsection