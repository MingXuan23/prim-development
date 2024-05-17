@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@include('layouts.datatable')

@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Pemilik Kod: {{$referral_code->username}}</h4>
            <h4 class="font-size-18">Kod Anda: {{$referral_code->code}}</h4>
            <!-- <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Welcome to Veltrix Dashboard</li>
            </ol> -->
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-body">

                <div class="form-group">
                <div class="form-row">
                    <div class="col-md-6">
                        <label for="total_point" class="col-form-label">Jumlah Mata Ganjaran:</label>
                        <input type="text" id="total_point" readonly value="{{ $referral_code->total_point }}" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="point_month" class="col-form-label">Mata Ganjaran Bulanan:</label>
                        <input type="text" readonly value="{{ number_format($point_month, 2) }}" class="form-control">
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-md-6">
                        <label for="total_point" class="col-form-label">Jumlah Klik:</label>
                        <input type="text" id="total_point" readonly value="{{ $referral_code->total_visit }}" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="point_month" class="col-form-label">Jumlah Transaksi:</label>
                        <input type="text" readonly value="RM {{ number_format($total_transaction, 2) }}" class="form-control">
                    </div>
                </div>
                   
                   
                </div>

               @if($referral_code->phase_level == 1)
               <label class="col-form-label">Capaian Hari Ini</label>
              {!!$progressToday!!}
                <br>

                
                <div id="progressBars">
               <label class="col-form-label">Jumlah Hari: {{ $referral_code->donation_streak }}/40</label>

                    <div class="progress mb-3" style="height: calc(1.5em + 0.75rem + 2px);">
                        <div class="progress-bar {{ $referral_code->streakToday ?  'bg-warning':'bg-danger' }}" style="width: {{ $referral_code->donation_streak * 2.5 }}%;" role="progressbar" aria-valuenow="{{ $referral_code->donation_streak }}" aria-valuemin="0" aria-valuemax="40"></div>
                    </div>
                    
                </div>

                @else 

                <div id="progressBars">
                    <div class="progress mb-3" style="height: calc(1.5em + 0.75rem + 2px);">
                        <div class="progress-bar bg-success" role="progressbar" aria-valuenow="40" style="width:100%" aria-valuemin="0" aria-valuemax="40">
                            <h4 class="text-white">Anda sudah capai peringkat 2</h4>
                        </div>
                    </div>
                </div>
                @endif

               </div>

            </div>

        </div>
    </div>

    <div class="col-md-12">
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

                <div class="table-responsive">
                    <table id="pointHistoryTable" class="table table-bordered table-striped dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr style="text-align:center">
                                <th> No. </th>
                                <th>Nama</th>
                                <th>Mata Ganjaran</th>
                                <th>Tarikh</th>
                                <th>Nota</th>
                            </tr>
                        </thead>

                        <tfoot>
                            <tr>
                                <th colspan="5" ></th>

                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>


      
       
    </div>
</div>
@endsection


@section('script')
<!-- Peity chart-->
<script src="{{ URL::asset('assets/libs/peity/peity.min.js')}}"></script>

<!-- Plugin Js-->
<script src="{{ URL::asset('assets/libs/chartist/chartist.min.js')}}"></script>

<script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script>


<script>

    $(document).ready(function(){
        fetch_data();
    });

   function fetch_data() {
    pointHistoryTable = $('#pointHistoryTable').DataTable({
                    processing: true,
                    //serverSide: true,
                    ajax: {
                        url: "{{ route('point.getPointHistoryDatatable') }}",
                        type: 'GET',
                    },
                    'columnDefs': [{
                        "targets": [0], // your case first column
                        "className": "text-center",
                        "width": "2%"
                    },{
                        "targets": [1,2,3,4], // your case first column
                        "className": "text-center",
                        "width":"20%"
                    },],
                    order: [
                        [0, 'asc']
                    ],
                    columns: [{
                        "data": null,
                        searchable: false,
                        "sortable": false,
                        render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    }, {
                        data: 'username',
                        name: 'username'
                    },{
                        data: 'points',
                        name: 'points',
                    }, {
                        data: 'datetime',
                        name: 'datetime',

                    }, {
                        data: 'desc',
                        name: 'desc',
                    },],
                    footerCallback: function(row, data, start, end, display) {
                        var api = this.api(),
                            sum = api.column(2, { // points column index
                                search: 'applied'
                            }).data().reduce(function(a, b) {
                                return parseFloat(a) + parseFloat(b);
                            }, 0);
                        $(api.column(0).footer()).html('Jumlah Mata Ganjarano: ' + sum);
                        console.log(sum);
                    }
       
                    
            });
        }
</script>

@endsection