@extends('layouts.master')

@section('css')
<link rel="stylesheet" href="{{ URL::asset('assets/css/datatable.css')}}">
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
@include('layouts.datatable')

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
            <h4 class="font-size-18">Urus Pesanan</h4>
        </div>
    </div>
    <div class="d-flex justify-content-end mr-3">
        <a href="" class="btn btn-primary btn-history">Sejarah Pesanan</a>
    </div>
</div>

<div class="card card-primary card-body">
    <div class="row">
        <div class="col">
            <div class="form-group">
                <label>Tapis</label>
                <select class="form-control" data-parsley-required-message="Pilih Jenis Tapisan" id="filter-order" required>
                    <option value="all" selected>Semua Pesanan (0)</option>
                    <option value="receive-today">Pesanan Diterima Hari Ini (0)</option>
                    <option value="today">Pesanan Perlu Diselesaikan Hari Ini (0)</option>
                    <option value="week">Pesanan Perlu Diselesaikan Minggu ini (0)</option>
                    <option value="month">Pesanan Perlu Diselesaikan ini (0)</option>
                    <option value="date">Pesanan Perlu Diselesaikan Ikut Tarikh</option>
                </select>
            </div>
        </div>
        <div class="col date-filter" hidden>
            <div class="form-group">
                <label>Tarikh</label>
                <input type="text" class="form-control" name="date" id="datepicker"  placeholder="Pilih tarikh" readonly>
            </div>
        </div>
    </div>
</div>

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
            <table class="table table-striped table-bordered dt-responsive wrap" id="orderTable" width="100%" cellspacing="0">
    
            <thead>
                <tr>
                <th style="width: 2%">No.</th>
                <th style="width: 15%">Pelanggan</th>
                <th style="width: 10%">No Telefon</th>
                <th style="width: 10%">Tarikh Pengambilan</th>
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

<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>

<script>
    $(document).ready(function() {
        

        let orderTable, orgId, route, dropdownLength = $('#org_dropdown').children('option').length;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })

        let btnHistory = $('.btn-history')
        btnHistory.hide()

        if(dropdownLength > 1) {
            $('#org_dropdown option')[1].selected = true
            orgId = $('#org_dropdown option')[1].value
            btnHistory.show()
            btnHistory.attr('href', '')
            route = "/admin/all-orders/"+orgId+"/history"
            btnHistory.attr('href', route)
            $('#orderTable').DataTable().destroy()
            fetch_data(orgId)
            countTotalOrders(orgId)
        }

        $('#org_dropdown').change(function() {
            orgId = $("#org_dropdown option:selected").val()
            if(orgId != ''){
                btnHistory.show()
                btnHistory.attr('href', '')
                route = "/admin/orders/"+orgId+"/history"
                btnHistory.attr('href', route)
                $('#orderTable').DataTable().destroy()
                fetch_data(orgId)
                countTotalOrders(orgId)
            }else {
                btnHistory.hide()
                $('#orderTable').DataTable().destroy()
                fetch_data()
                countTotalOrders()
            }
            
        })
        
        function fetch_data(orgId = '',filterType = '', date = '') {
            orderTable = $('#orderTable').DataTable({
                pageLength: 5,
                lengthMenu: [[5, 15, 30, -1], [5, 15, 30, "Semua"]],
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin-reg.all-orders') }}",
                    data: {
                        id:orgId,
                        filterType: filterType,
                        date: date
                    },
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
                    data: "name",
                    name: 'name',
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
                },]
            });
        }

        function countTotalOrders(orgId = '')
        {
            $.ajax({
                url: "{{ route('admin-reg.count-orders') }}",
                method: "GET",
                data: {id:orgId},
                success:function(result) {
                    $('#filter-order').children('option[value=all]').text("Semua Pesanan ("+result.response.all+")")
                    $('#filter-order').children('option[value=receive-today]').text("Pesanan Diterima Hari Ini ("+result.response.received_today+")")
                    $('#filter-order').children('option[value=today]').text("Pesanan Perlu Diselesaikan Hari Ini ("+result.response.today+")")
                    $('#filter-order').children('option[value=week]').text("Pesanan Perlu Diselesaikan Minggu Ini ("+result.response.week+")")
                    $('#filter-order').children('option[value=month]').text("Pesanan Perlu Diselesaikan Bulan Ini ("+result.response.month+")")
                },
                error:function(result) {
                    console.log(result.responseText)
                },
            })
        }

        $("#datepicker").datepicker()

        $('#filter-order').change(function() {
            let filterVal = $(this).children(':selected').val()
            if(filterVal == 'date') {
                $('.date-filter').attr('hidden', false)
                $('#datepicker').change(function() {
                    let date = $('#datepicker').val()
                    $('#orderTable').DataTable().destroy()
                    fetch_data(orgId,filterVal, date)
                })
            } else {
                $('.date-filter').attr('hidden', true)
                $('#orderTable').DataTable().destroy()
                fetch_data(orgId, filterVal)
            }
        })

        // $('#order_day').change(function() {
        //     var filter = $(this).children(':selected').val()
        //     $('#orderTable').DataTable().destroy()
        //     fetch_data(filter)
        // })

        var btn = "<button type='button' data-dismiss='modal' class='btn btn-light'>Kembali</button>"
        var order_id

        $(document).on('click', '.btn-done-pickup', function(){
            order_id = $(this).attr('data-order-id')
            $('.modal-title').empty().append('Sahkan Pesanan')
            $('.modal-body').empty().append('Pesanan Ini Sudah Diambil?')
            $('.modal-footer').empty().append(btn + "<button type='button' id='confirm_order' class='btn btn-primary'>Ya</button>")
            $('#confirmationModal').modal('show')
            confirmOrder(order_id)
        })

        $(document).on('click', '.btn-cancel-order', function(){
            order_id = $(this).attr('data-order-id')
            $('.modal-title').empty().append('Batalkan Pesanan')
            $('.modal-body').empty().append('Anda Pasti Untuk Batalkan Pesanan Ini?')
            $('.modal-footer').empty().append(btn + "<button type='button' id='destroy_order' class='btn btn-danger'>Batal</button>")
            $('#confirmationModal').modal('show')
            destroyOrder(order_id)
        })

        function confirmOrder(order_id)
        {
            $('#confirm_order').click(function() {
                
                $.ajax({
                    url: "{{ route('admin-reg.order-picked-up') }}",
                    method: "POST",
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
                        // $('.loading').hide()
                        $('#confirmationModal').modal('hide')
                        countTotalOrders(orgId)
                    },
                })
            })
        }

        function destroyOrder(order_id)
        {
            $('#destroy_order').click(function() {
                $.ajax({
                    url: "{{ route('admin-reg.destroy-order') }}",
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
                        countTotalOrders(orgId)
                    },
                })
            })
        }

        $('.alert').delay(3000).fadeOut();

    })
    
</script>

@endsection