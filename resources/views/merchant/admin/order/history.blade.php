@extends('layouts.master')

@section('css')
<link rel="stylesheet" href="{{ URL::asset('assets/css/datatable.css')}}">
@include('layouts.datatable')
@endsection

@section('content')

<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18"><a href="{{ route('admin.merchant.order') }}" class="text-muted">Urus Pesanan</a> <i class="fas fa-angle-right"></i> Sejarah Pesanan</h4>
        </div>
    </div>
</div>

<div class="card card-primary card-body">
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
</div>

<div class="card mb-3">
    <div class="card-header">
      <i class="ti-email mr-2"></i>Sejarah Pesanan</div>
    <div class="card-body">
      
      <div class="table-responsive">
        <table class="table table-striped table-bordered" id="orderTable" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th style="width: 2%">No.</th>
              <th style="width: 15%">Pelanggan</th>
              <th style="width: 10%">No Telefon</th>
              <th style="width: 10%">Tarikh Pengambilan</th>
              <th style="width: 10%">Jumlah (RM)</th>
              <th style="width: 10%" class="text-center">Status</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
</div>

@endsection

@section('script')

<script>
  $(document).ready(function() {
    fetch_data()

    function fetch_data(order_day = '') {
        orderTable = $('#orderTable').DataTable({
            pageLength: 5,
            lengthMenu: [[5, 15, 30, -1], [5, 15, 30, "Semua"]],
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.merchant.history-list') }}",
                data: {
                    order_day: order_day,
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
                "targets": [0, 1, 2, 3, 4], // your case first column
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
                data: 'total_price',
                name: 'total_price',
            }, {
                data: 'status',
                name: 'status',
                orderable: false,
                searchable: false,
                "className": "align-middle text-center",
            },]
        });
    }

    $('#order_day').change(function() {
        var filter = $(this).children(':selected').val()
        $('#orderTable').DataTable().destroy()
        fetch_data(filter)
    })
  })
  
</script>

@endsection