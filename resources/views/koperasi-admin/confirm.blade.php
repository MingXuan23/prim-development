@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ URL::asset('assets/css/datatable.css')}}">
@include('layouts.datatable')
@endsection

@section('content')
<div class="row">
  <div class="col-md-12">
      <div style="padding-top: 24px" class="row">
        <div class="col-md-12 ">
            <div class=" align-items-center">
                <div class="form-group card-title">
                    <select name="org" id="org_dropdown" class="form-control col-md-12">
                        <option value="" selected disabled>Pilih Organisasi</option>
                        @foreach($koperasiList as $row)
                        <option value="{{ $row->organization_id }}">{{ $row->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
  </div>
      
      <!-- <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Welcome to Veltrix Dashboard</li>
            </ol> -->
    </div>
  <div class="col-md-12">
    <div class="card">
      {{-- <div class="card-header">List Of Applications</div> --}}


      <div class="card-body">

        @if(count($errors) > 0)
        <div class="alert alert-danger">
          <ul>
            @foreach($errors->all() as $error)
            <li>{{$error}}</li>
            @endforeach
          </ul>
        </div>
        @endif
        @if(\Session::has('success'))
        <div class="alert alert-success">
          <p>{{ \Session::get('success') }}</p>
        </div>
        @endif

        <div class="flash-message"></div>

        <div class="table-responsive">
          <table id="confirmTable" class="table table-bordered table-striped dt-responsive"
            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
            <thead>
              <tr style="text-align:center">
              <th style="width: 2%">No.</th>
              <th style="width: 15%">Nama Pelanggan</th>
              <th style="width: 10%">No Telefon Pelanggan</th>
              <th style="width: 10%">Tarikh dan Waktu Pesan</th>
              <th style="width: 10%">Tarikh Pengambilan</th>
              <th style="width: 15%">Nota</th>
              <th style="width: 10%">Jumlah (RM)</th>
              <th style="width: 10%">Status</th>
              <th style="width: 15%">Action</th>
              </tr>
            </thead>
            <tbody >
          </tboby>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('script')
<script>
  $(document).ready(function(){
    dropdownLength = $('#org_dropdown').children('option').length
    if(dropdownLength > 1) {
      var koopId = "{{ $koperasi->organization_id }}";
      // Loop through each option in the dropdown
      $('#org_dropdown option').each(function() {
          // Check if the option value matches the organization ID
          if ($(this).val() == koopId) {
              // Set the selected attribute for the matching option
              $(this).prop('selected', true);
              // Set the value of the hidden input field
              
          }
      });
      fetchConfirmOrder();
      orgId = $("#org_dropdown option:selected").val();
      $('.koperasi_id').val(orgId);
      }

        $('#org_dropdown').change(function() {   
         fetchConfirmOrder();
    })
  })

 function fetchConfirmOrder(){
  orgId = $("#org_dropdown option:selected").val();

$.ajax({
        type: 'GET',
        url: '{{ route("koperasi.fetchConfirmTable")}}',
        data: {
            koopId:orgId
        },
        success:function(response){
          loadConfirmTable(response.order);
        }
    });
 }

  function loadConfirmTable(orders){
    const confirmTableBody = document.querySelector('#confirmTable tbody');
    $("#confirmTable tbody").empty();
    const statusLabels = {
      2: '<span class="badge rounded-pill bg-warning">Sedang Diproses</span>',
      4: '<span class="badge rounded-pill bg-danger text-white">Tidak Diambil</span>',
      1: '<span class="badge rounded-pill bg-warning">In cart</span>',
    };
    if (orders.length > 0) {
    orders.forEach((order) => {
      //console.log(order)
      const row = document.createElement('tr');
      const descriptionCell = document.createElement('td');

      const viewPgngListRoute = "{{ route('koperasi.viewPgngList', [':Id',':customerID']) }}";
      const finalLink = viewPgngListRoute.replace(':Id', order.id).replace(':customerID', order.customerID);
      descriptionCell.innerHTML = `
          ${order.note ? order.note : ''} | <a href="${finalLink}">Lihat Pesanan</a>
      `;
      
      const confirmLink="{{ route('koperasi.storeConfirm', ':pgngId') }}".replace(':pgngId',order.id);
      const notconfirmLink="{{ route('koperasi.notConfirm', ':pgngId') }}".replace(':pgngId',order.id);

      const pickupDate = order.pickup_date== '0001-01-01 00:00:00'?'Wait message from seller':order.pickup_date;
      console.log(order.pickup_date,new Date(1, 0, 1, 0, 0, 0));
      row.innerHTML = `
        <td>${order.id}</td>
        <td>${order.name}</td>
        <td>${order.telno}</td>
        <td>${order.orderTime}</td>
        <td>${pickupDate}</td>
        <td>${descriptionCell.innerHTML}</td>
        <td>${order.total_price.toFixed(2)}</td>
        <td>${statusLabels[order.status]}</td>
        <td class="allign-middle">
          <div>
            <a href="${confirmLink}" style="margin: 4px" class="btn btn-primary">Telah Diambil</a>
            <a href="${notconfirmLink}" style="margin: 4px" class="btn btn-danger m1">Tidak Diambil</a>
          </div>
        </td>
      `;
      
      confirmTableBody.appendChild(row);
    
    
    });
  }
  else {
  // Display the message when there are no orders
  document.querySelector('#confirmTable tbody').innerHTML = `
    <tr>
      <td colspan="9" class="text-center"><i>Tiada Rekod Pesanan.</i></td>
    </tr>`;
}
  }
</script>
@endsection