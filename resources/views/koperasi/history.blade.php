@extends('layouts.master')

@section('css')

@include('layouts.datatable');

@endsection

@section('content')
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
<div class="card mb-3">
  <div class="card-header">
    <i class="ti-clipboard mr-2"></i>Sejarah Pesanan Anda</div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">

        <thead>
          <tr>
            <th style="width: 2%">No.</th>
            <th style="width: 15%">Nama Koperasi</th>
            <th style="width: 10%">No Telefon Koperasi</th>
            <th style="width: 10%">Tarikh dan Waktu Pesan</th>
            <th style="width: 10%">Tarikh Pengambilan</th>
            <th style="width: 15%">Nota</th>
            <th style="width: 10%">Jumlah (RM)</th>
            <th style="width: 10%">Status</th>
          </tr>
        </thead>

        <tbody id="historyTable">
          
          <!-- @if(isset($order))
            @foreach($order as $row)
            @php($date = date_create($row->updated_at))
            @php($pickup = date_create($row->pickup_date))
            <tr>
              <td class="align-middle">{{ $row->id }}.</td>
              <td class="align-middle">{{ $row->koop_name }}</td>
              <td class="align-middle">{{ $row->koop_telno }}</td>
              <td class="align-middle">{{ date_format($date,"M D Y, h:m:s A") }}</td>
              <td class="align-middle">{{ date_format($pickup,"D, M d Y") }}</td>
              <td class="align-middle"> 
                @if($row->note != null)
                {{ $row->note }} |<i> Status dikemaskini oleh {{$row->confirmPerson}} pada {{$row->confirm_picked_up_time}}</i>
                @else
                <i>Status dikemaskini oleh {{$row->confirmPerson}} pada {{$row->confirm_picked_up_time}}</i>
                @endif  
              </td>           
              <td class="align-middle">
                {{ number_format($row->total_price, 2, '.', '') }} | 
                <a href="{{ route('koperasi.viewPgngList', [$row->id, $row->user_id]) }}">Lihat Pesanan</a>
              </td>
              <td>
                @if($row->status == 3)
                <span class="badge rounded-pill bg-success text-white btn-block">Berjaya Diambil</span>
                @elseif($row->status == 100 || $row->status == 200)
                <span class="badge rounded-pill bg-danger text-white btn-block">Dibatalkan</span>
                @endif
              </td>
            </tr>

            @endforeach
          @else
          <tr>
              <td colspan="8" class="text-center"><i>Tiada Sejarah Rekod Pesanan.</i></td>
          </tr>
          @endif -->
        </tbody>
      </table>
    </div>
    
  </div>
  <div class="card-footer small text-muted"></div>
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
      fetchHistory();
      orgId = $("#org_dropdown option:selected").val();
      $('.koperasi_id').val(orgId);
      }

        $('#org_dropdown').change(function() {   
          fetchHistory();
    })
  })

  function fetchHistory(){

    orgId = $("#org_dropdown option:selected").val();

    $.ajax({
            type: 'GET',
            url: '{{ route("koperasi.fetchAdminHistory")}}',
            data: {
                koopId:orgId
            },
            success:function(response){
              loadHistoryTable(response.order);
            }
        });
  }

  function loadHistoryTable(order){

    if (order.length > 0) {
  let tableBody = '';
  order.forEach((row) => {
    let date = new Date(row.updated_at);
    let pickupDate = new Date(row.pickup_date);
    let noteHtml = row.note
      ? `${row.note} |<i> Status dikemaskini oleh ${row.confirmPerson} pada ${row.confirm_picked_up_time}</i>`
      : `<i>Status dikemaskini oleh ${row.confirmPerson} pada ${row.confirm_picked_up_time}</i>`;
      const viewPgngListRoute = "{{ route('koperasi.viewPgngList', [':Id',':customerID']) }}";
      const finalLink = viewPgngListRoute.replace(':Id', row.id).replace(':customerID', row.customerID);
      console.log(row);
      pgngList = `<a href="${finalLink}">Lihat Pesanan</a>`;
    tableBody += `
      <tr>
        <td class="align-middle">${row.id}.</td>
        <td class="align-middle">${row.koop_name}</td>
        <td class="align-middle">${row.koop_telno}</td>
        <td class="align-middle">${date.toLocaleString('en-US')}</td>
        <td class="align-middle">${pickupDate.toDateString()}</td>
        <td class="align-middle">${noteHtml}</td>
        <td class="align-middle">${row.total_price.toFixed(2)} | 
          ${pgngList}
        </td>
        <td>${row.status === "3"
          ? '<span class="badge rounded-pill bg-success text-white btn-block">Berjaya Diambil</span>'
          : (row.status === "100" || row.status === "200")
          ? '<span class="badge rounded-pill bg-danger text-white btn-block">Dibatalkan</span>'
          : ''}
        </td>
      </tr>`;
  });

  // Append the table body to the table
  document.getElementById('historyTable').innerHTML = tableBody;
} else {
  // Display the message when there are no orders
  document.getElementById('historyTable').innerHTML = `
    <tr>
      <td colspan="8" class="text-center"><i>Tiada Sejarah Rekod Pesanan.</i></td>
    </tr>`;
}
  }
</script>
@endsection