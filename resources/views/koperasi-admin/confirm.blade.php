@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ URL::asset('assets/css/datatable.css')}}">

<style>
  #confirmTable th,
    #confirmTable td {
        white-space: normal;
        word-wrap: break-word;
    }
    #confirmTable th {
        vertical-align: top;
    }
</style>
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
        <div class="container">
          <div class="row">
              <div class="col-md-9">
                  <input type="text" name="searchTxt" id="searchTxt" class="form-control mb-3" placeholder="" value="">
              </div>
              <div class="col-md-3">
                  <button onclick="fetchConfirmOrder()" class="btn btn-primary btn-block">Carian Nama atau No Pesanan</button>
              </div>
          </div>
      </div>
      <br>
        <div class="flash-message"></div>

        <div class="table-responsive">

        <table id="confirmTable" class="table table-bordered table-striped dt-responsive nowrap"
        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
            
            <thead>
              <tr style="text-align:center">
              <th >No.</th>
              <th >Nama Pelanggan</th>
              <th  >No Telefon</th>
              <th  >Tarikh dan Waktu Pesan</th>
              <th  >Tarikh Pengambilan</th>
              <th >Nota</th>
              <th  >Jumlah (RM)</th>
              <th  >Status</th>
              <th >Action</th>
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

<!-- Required datatable js -->
<script src="{{ URL::asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }} "></script>
<script src="{{ URL::asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }} "></script>

<!-- Buttons examples -->
<script src="{{ URL::asset('assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js') }} "></script>
<script src="{{ URL::asset('assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js') }} "></script>
<script src="{{ URL::asset('assets/libs/jszip/jszip.min.js') }} "></script>
<script src="{{ URL::asset('assets/libs/pdfmake/pdfmake.min.js') }} "></script>
<script src="{{ URL::asset('assets/libs/pdfmake/vfs_fonts.js') }} "></script>
<script src="{{ URL::asset('assets/libs/datatables.net-buttons/js/buttons.html5.min.js') }} "></script>
<script src="{{ URL::asset('assets/libs/datatables.net-buttons/js/buttons.print.min.js') }} "></script>
<script src="{{ URL::asset('assets/libs/datatables.net-buttons/js/buttons.colVis.min.js') }} "></script>


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
const key = $('#searchTxt').val();
$.ajax({
        type: 'GET',
        url: '{{ route("koperasi.fetchConfirmTable")}}',
        data: {
            koopId:orgId,
            key:key
        },
        success:function(response){
          //loadConfirmTable(response.order);
          loadConfirmTable2(response.order);
        }
    });
 }

 function SearchByName(){

 }

 let dataTable;

function loadConfirmTable2(orders) {
    if (dataTable) {
        dataTable.destroy();
    }

    dataTable = $('#confirmTable').DataTable({
      dom: 'Bfrtip',
      buttons:  [
          { 
              extend: 'excel',
              text: 'Download Excel',
              filename: 'uncompleted_order',
              exportOptions: {
                  modifier: {
                      page: 'all'
                  }
              }
          },],
        data: orders,
        columns: [
            { data: 'id', width: '2%' },
            { data: 'name', width: '10%' },
            { data: 'telno', width: '5%' },
            { data: 'orderTime', width: '5%' },
            { 
                data: 'pickup_date',
                width: '10%',
                render: function(data) {
                    return data == '0001-01-01 00:00:00' ? 'Hubungi Pelanggan untuk Mengambil Pesanan' : data;
                }
            },
            { 
                data: null,
                width: '5%',
                render: function(data) {
                    const viewPgngListRoute = "{{ route('koperasi.viewPgngList', [':Id',':customerID']) }}";
                    const finalLink = viewPgngListRoute.replace(':Id', data.id).replace(':customerID', data.customerID);
                    return `${data.note ? data.note : ''} | <a href="${finalLink}">Lihat Pesanan</a>`;
                }
            },
            { 
                data: 'total_price',
                width: '2%',
                render: function(data) {
                    return parseFloat(data).toFixed(2);
                }
            },
            {
                data: 'status',
                width: '2%',
                render: function(data) {
                    const statusLabels = {
                        2: '<span class="badge rounded-pill bg-warning">Sedang Diproses</span>',
                        4: '<span class="badge rounded-pill bg-danger text-white">Tidak Diambil</span>',
                        1: '<span class="badge rounded-pill bg-warning">In cart</span>',
                    };
                    return statusLabels[data] || '';
                }
            },
            {
                data: null,
                width: '5%',
                render: function(data) {
                    const confirmLink = "{{ route('koperasi.storeConfirm', ':pgngId') }}".replace(':pgngId', data.id);
                    return `<a href="${confirmLink}" class="btn btn-primary">Telah Diambil</a>`;
                }
            }
        ],
       
        responsive: true,
        language: {
            lengthMenu: "Papar _MENU_ rekod per halaman",
            info: "Memaparkan _START_ hingga _END_ daripada _TOTAL_ rekod",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Seterusnya",
                previous: "Sebelumnya"
            }
        },
        order: [[0, 'desc']],  // Sort by the first column (ID) in descending order
        pageLength: 50,  // Set default number of rows to 50
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],  // Provide options for number of rows
        autoWidth: false,  // Disable auto-width calculation
        columnDefs: [
            { className: "text-center", targets: "_all" }  // Center-align all columns
        ]
    });

    $('#exportExcel').on('click', function() {
        dataTable.button('.buttons-excel').trigger();
    });
}

function fetchConfirmOrder() {
    const orgId = $("#org_dropdown option:selected").val();
    const key = $('#searchTxt').val();
    $.ajax({
        type: 'GET',
        url: '{{ route("koperasi.fetchConfirmTable") }}',
        data: {
            koopId: orgId,
            key: key
        },
        success: function(response) {
            //loadConfirmTable(response.order);
            loadConfirmTable2(response.order);
        }
    });
}

//   function loadConfirmTable(orders){
//    // console.log(orders);
//     const confirmTableBody = document.querySelector('#confirmTable tbody');
//     $("#confirmTable tbody").empty();
//     const statusLabels = {
//       2: '<span class="badge rounded-pill bg-warning">Sedang Diproses</span>',
//       4: '<span class="badge rounded-pill bg-danger text-white">Tidak Diambil</span>',
//       1: '<span class="badge rounded-pill bg-warning">In cart</span>',
//     };
//     if (orders.length > 0) {
//     orders.forEach((order) => {
//       //console.log(order)
//       const row = document.createElement('tr');
//       const descriptionCell = document.createElement('td');

//       const viewPgngListRoute = "{{ route('koperasi.viewPgngList', [':Id',':customerID']) }}";
//       const finalLink = viewPgngListRoute.replace(':Id', order.id).replace(':customerID', order.customerID);
//       descriptionCell.innerHTML = `
//           ${order.note ? order.note : ''} | <a href="${finalLink}">Lihat Pesanan</a>
//       `;
      
//       const confirmLink="{{ route('koperasi.storeConfirm', ':pgngId') }}".replace(':pgngId',order.id);
//       const notconfirmLink="{{ route('koperasi.notConfirm', ':pgngId') }}".replace(':pgngId',order.id);
// //<a href="${notconfirmLink}" style="margin: 4px" class="btn btn-danger m1">Tidak Diambil</a>
//       const pickupDate = order.pickup_date== '0001-01-01 00:00:00'?'Hubungi Pelanggan untuk Mengambil Pesanan':order.pickup_date;
//       console.log(order.pickup_date,new Date(1, 0, 1, 0, 0, 0));
//       row.innerHTML = `
//         <td>${order.id}</td>
//         <td>${order.name}</td>
//         <td>${order.telno}</td>
//         <td>${order.orderTime}</td>
//         <td>${pickupDate}</td>
//         <td>${descriptionCell.innerHTML}</td>
//         <td>${order.total_price.toFixed(2)}</td>
//         <td>${statusLabels[order.status]}</td>
//         <td class="allign-middle">
//           <div>
//             <a href="${confirmLink}" style="margin: 4px" class="btn btn-primary">Telah Diambil</a>
            
//           </div>
//         </td>
//       `;
      
//       confirmTableBody.appendChild(row);
    
    
//     });
//   }
//   else {
//   // Display the message when there are no orders
//   document.querySelector('#confirmTable tbody').innerHTML = `
//     <tr>
//       <td colspan="9" class="text-center"><i>Tiada Rekod Pesanan.</i></td>
//     </tr>`;
// }
//   }
</script>
@endsection