@extends('layouts.master')

@section('css')
        <!-- DataTables -->
        <link href="{{ URL::asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ URL::asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />

        <!-- Responsive datatable examples -->
        <link href="{{ URL::asset('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Senarai Penderma Guna Kod untuk {{ $donation->nama }}</h4>
            <!-- <ol class="breadcrumb mb-0">
                  <li class="breadcrumb-item active">Welcome to Veltrix Dashboard</li>
              </ol> -->
        </div>
    </div>
</div>
<div class="row">

    <div class="col-md-12">
        <div class="card card-primary">

            @if(count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                    <li>{{$error}}</li>
                    @endforeach
                </ul>
            </div>
            @endif


        </div>
    </div>

    <div class="col-md-12">
        <div class="card">
        <div class="card-body">
                 <h4 class="font-size-18">Tempoh</h4>
                    <div class="input-daterange input-group" id="date">
                        <input type="text" id="startDate" class="form-control" name="startDate" placeholder="Tarikh Awal" autocomplete="off" 
                        data-parsley-required-message="Sila masukkan tarikh awal"
                        data-parsley-errors-container=".errorMessage"  onchange="fetchDataTableDonor()"/>
                        <input type="text" id="endDate" class="form-control" name="endDate" placeholder="Tarikh Akhir" autocomplete="off"
                        data-parsley-required-message="Sila masukkan tarikh akhir"
                        data-parsley-errors-container=".errorMessage"  onchange="fetchDataTableDonor()"/>
                    </div>
                    <div class="errorMessage"></div>
                    <div class="errorMessage"></div>
                </div>

        </div>
        <div class="card">

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

                <input hidden type="text" id="don" name="don" class="form-control" value="{{ $donation->id }}">
                
                <h4 class="font-size-18">Penyumbang</h4>
                <div class="table-responsive" >
                    <table id="codeTable" class="table table-bordered table-striped dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr style="text-align:center">
                                <th> No. </th>
                                <th> Kod </th>
                                <th> Pemilik Kod</th>
                                <th> Tel</th>
                                <th> Jumlah Transaksi </th>
                                <th>Jumlah Amaun (RM)</th>
                               
                            </tr>
                        </thead>
                    </table>
                </div>

                

               
            </div>
           
        </div>
        <div class="card">
             <div class="card-body">
             <h4 class="font-size-18">Sejarah Kod</h4>
                <div class="table-responsive">
                    <table id="donorTable" class="table table-bordered table-striped dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr style="text-align:center">
                                <th> No. </th>
                                <th> Kod Diguna </th>
                                <th> Pemilik Kod </th>
                                <th>Nama Penderma</th>
                                <th> FPX id </th>
                                <th> Tarikh Derma </th>
                                <th> Amaun (RM) </th>
                            </tr>
                        </thead>
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
<script src="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}" defer></script>

<!-- Responsive examples -->
<script src="{{ URL::asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }} "></script>
<script src="{{ URL::asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }} "></script>


<script>
   $(document).ready(function() {

        fetchDataTableDonor();

        $('#date').datepicker({
            toggleActive: true,
            format: 'yyyy-mm-dd',
            orientation: 'bottom'
        });
    });

    function fetchDataTableDonor(){
        
        let sDate=$("#startDate").val();
        let eDate=$("#endDate").val();
        //value will pass to controller
        let start = null;
        let end = null;
        //value just for validation

        if (sDate === "") {
            sDate = null;
        }else{
            var parts = sDate.split("-");
            start=new Date(parts[2], parts[1] - 1, parts[0]);
        }

        if (eDate === "") {
            eDate = null;
        }
        else{
            var parts = eDate.split("-");
            end=new Date(parts[2], parts[1] - 1, parts[0]);
        }
        if(start!=null &&end!=null&& start>end){
            alert("The end date cannot earlier than start date ");
            $("#endDate").val("");
            end=null;
        }
        //console.log(sDate +" "+eDate);
        else{
            let donateid = $('#don').val();

            if ($.fn.DataTable.isDataTable('#codeTable')) {
                $('#codeTable').DataTable().destroy();
            }


            let code_table = $('#codeTable').DataTable({   

                ajax: {
                    processing: true,
                    url: "{{ route('donate.code_datatable') }}",
                    data: {
                        id: donateid,
                        startDate:sDate,
                        endDate:eDate
                    },
                    type: 'GET',

                },
                'columnDefs': [{
                    "targets": [1,2, 3, 4,5],
                    "className": "text-center",
                    "width": "2%"
                },{
                    "targets": [0],
                    "className": "text-center",
                    "width": "2%"
                }],
                columns: [{
                    "data": null,
                    searchable: false,
                    "sortable": false,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }, {
                    data: "code",
                    name: 'code'
                },
                {
                    data: "owner",
                    name: 'owner'
                },
                {
                    data: "owner_tel",
                    name: 'owner_tel'
                },
                {
                    data: "transaction_count",
                    name: 'transaction_count'
                }, 
                {
                    data: "total_amount",
                    name: 'total_amount'
                }, ]
            });
            
            if ($.fn.DataTable.isDataTable('#donorTable')) {
                $('#donorTable').DataTable().destroy();
            }


            let donor_table = $('#donorTable').DataTable({
                //processing: true,
                //serverSide: true,
                //this two must comment so that can download full page of pdf and excel
                lengthChange: false,
                dom: 'Bfrtip',
                buttons:  [
                    { 
                        extend: 'excel',
                        text: 'Excel',
                        exportOptions: {
                            modifier: {
                                page: 'all'
                            }
                        }
                    },
                    {
                        extend: 'pdf',
                        text: 'PDF',
                        orientation: 'landscape',
                        exportOptions: {
                            modifier: {
                                page: 'all'
                            }
                        }
                    }
                ],
                            
                ajax: {
                    url: "{{ route('donate.donor_code_datatable') }}",
                    data: {
                        id: donateid,
                        startDate:sDate,
                        endDate:eDate
                    },
                    type: 'GET',

                },
                'columnDefs': [{
                    "targets": [2, 3, 4, 5, 6],
                    "className": "text-center",
                    "width": "2%"
                },{
                    "targets": [0],
                    "className": "text-center",
                    "width": "2%"
                },
                {
                    "targets": [1],
                    "width": "2%"
                }],
                columns: [{
                    "data": null,
                    searchable: false,
                    "sortable": false,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }, {
                    data: "code",
                    name: 'code'
                },
                {
                    data: "owner",
                    name: 'owner'
                },
                {
                    data: "username",
                    name: 'username'
                }, 
                {
                    data: "fpx_id",
                    name: 'fpx_id'
                }, 
                {
                    data: "datetime_created",
                    name: 'datetime_created',
                    // orderable: false,
                    // searchable: false
                }, {
                    data: 'amount',
                    name: 'amount',
                }, ]
            });


           
        }
        
    }
</script>
@endsection