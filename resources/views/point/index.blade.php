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
                        <label for="point_month" class="col-form-label">Jumlah PRiM medal:</label>
                        <input type="text" readonly value="{{ $streakData['prim_medal'] }}" class="form-control">
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-md-6">
                        <label for="total_point" class="col-form-label">Jumlah Klik:</label>
                        <input type="text" id="total_point" readonly value="{{ $referral_code->total_visit }}" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="point_month" class="col-form-label">Jumlah Sedekah Subuh</label>
                        <input type="text" readonly value="{{ $sedekah_subuh['prim_medal'] }}" class="form-control">
                    </div>
                </div>
                   
                   
                </div>

              
               <label class="col-form-label">Capaian Hari Ini</label> 
              {!!$progressToday!!}
                <br>

                
                <div id="progressBars">
               <label class="col-form-label">
                    @if($streakData['streak_today'])
                        <div >
                            <i class="fas fa-check-circle text-success"></i> PRiM Medal @if (isset($streakData['streak_startdate'])) Mulai {{$streakData['streak_startdate']}} @endif: {{ $streakData['current_streak'] }}/40 Hari 
                        </div>
                        @else
                        <div  class ="text-danger">
                            <i class="fas fa-exclamation-circle text-danger"></i> PRiM Medal @if (isset($streakData['streak_startdate'])) Mulai {{$streakData['streak_startdate']}} @endif: {{ $streakData['current_streak'] }}/40 Hari 
                         </div>
                        @endif </label>
                   
                      
                    
                    <div class="progress mb-3" style="height: calc(1.5em + 0.75rem + 2px);">
                        <div class="progress-bar {{ $streakData['streak_today'] ?  'bg-warning':'bg-danger' }}" style="width: {{ $streakData['current_streak'] * 2.5 }}%;" role="progressbar" aria-valuenow="{{ $streakData['current_streak'] }}" aria-valuemin="0" aria-valuemax="40">

                      
                        </div>
                       
                    </div>
                    <!-- @if(isset($streakData['streak_startdate']))
                    (Mulai {{$streakData['streak_startdate']}})
                    @endif -->
                    
                  
                    
                </div>
                <div id="progressBars">
               <label class="col-form-label">
                    @if($sedekah_subuh['streak_today'])
                        <div >
                            <i class="fas fa-check-circle text-success"></i> Sedekah Subuh @if (isset($sedekah_subuh['streak_startdate'])) Mulai {{$sedekah_subuh['streak_startdate']}} @endif: {{ $sedekah_subuh['current_streak'] }}/40 Hari 
                        </div>
                        @else
                        <div  class ="text-danger">
                            <i class="fas fa-exclamation-circle text-danger"></i> Sedekah Subuh  @if (isset($sedekah_subuh['streak_startdate'])) Mulai {{$sedekah_subuh['streak_startdate']}} @endif:   {{ $sedekah_subuh['current_streak'] }}/40 Hari 
                         </div>
                        @endif </label>
                   
                      
                    
                    <div class="progress mb-3" style="height: calc(1.5em + 0.75rem + 2px);">
                        <div class="progress-bar {{ $sedekah_subuh['streak_today'] ?  'bg-warning':'bg-danger' }}" style="width: {{ $sedekah_subuh['current_streak'] * 2.5 }}%;" role="progressbar" aria-valuenow="{{ $sedekah_subuh['current_streak'] }}" aria-valuemin="0" aria-valuemax="40">

                      
                        </div>
                       
                    </div>
                    <!-- @if(isset($streakData['streak_startdate']))
                    (Mulai {{$streakData['streak_startdate']}})
                    @endif -->
                    
                  
                    
                </div>


                <a href="/derma" class="btn btn-primary col-sm-12">Derma Sekarang</a>
                <!-- <div id="progressBars">
                    <div class="progress mb-3" style="height: calc(1.5em + 0.75rem + 2px);">
                        <div class="progress-bar bg-success" role="progressbar" aria-valuenow="40" style="width:100%" aria-valuemin="0" aria-valuemax="40">
                            <h4 class="text-white">Anda sudah capai peringkat 2</h4>
                        </div>
                    </div>
                </div> -->
              

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

            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#tab1" role="tab">Sejarah Mata Ganjaran PRiM</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab2" role="tab">Ahli Anda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab3" role="tab">Sejarah PRiM Medal</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab4" role="tab">Sejarah Sedekah Subuh</a>
                </li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div class="tab-pane active" id="tab1" role="tabpanel">
                    <div class="table-responsive">
                        <table id="pointHistoryTable" class="table table-bordered table-striped dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr style="text-align:center">
                                    <th>No.</th>
                                    <th>Nama</th>
                                    <th>Mata Ganjaran</th>
                                    <th>Tarikh</th>
                                    <th>Nota</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th colspan="5"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="tab-pane" id="tab2" role="tabpanel">
                    <div class="table-responsive">
                        <table id="memberTable" class="table table-bordered table-striped dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            
                            <thead>
                                <tr style="text-align:center">
                                    <th>No.</th>
                                    <th>Nama Ahli</th>
                                    <th>No Telefon</th>
                                    <th>Email</th>
                                    <th>Tarikh Masuk</th>
                                    <th>Jenis Ahli</th>
                                    <th>Jumlah Sedekah</th>
                                    <th>Sedekah Hari ini</th>
                                </tr>
                            </thead>
                           
                        </table>
                    </div>
                </div>
                <div class="tab-pane" id="tab3" role="tabpanel">
                    <div class="table-responsive">
                        <table id="streakTable" class="table table-bordered table-striped dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            
                            <thead>
                                <tr style="text-align:center">
                                    <th>No.</th>
                                    <th>Tarikh Mula</th>
                                    <th>Tarikh Tamat</th>
                                    <th>Butiran</th>
                                    <th>PRiM Medal</th>
                                    <th>Status</th>
                                    <th>Butiran</th>
                                   
                                </tr>
                            </thead>
                           
                        </table>
                    </div>
                  
                </div>
                <div class="tab-pane" id="tab4" role="tabpanel">
                    <div class="table-responsive">
                        <table id="sedekahTable" class="table table-bordered table-striped dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            
                            <thead>
                                <tr style="text-align:center">
                                    <th>No.</th>
                                    <th>Tarikh Mula</th>
                                    <th>Tarikh Tamat</th>
                                    <th>Butiran</th>
                                    <th>PRiM Medal</th>
                                    <th>Status</th>
                                    <th>Butiran</th>
                                   
                                </tr>
                            </thead>
                           
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- 
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


      
       
    </div> -->
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
        fetch_member();
        fetch_donation_streak();
        fetch_sedekah_subuh();
       
    });

    function fetch_sedekah_subuh(){
        streakTable = $('#sedekahTable').DataTable({
        processing: true,
        ajax: {
            url: "{{ route('point.getDonationStreakTable') }}",
            type: 'GET',
            data: {
                sedekahSubuh: 1,
            }
        },
        columnDefs: [
            {
                targets: [0, 1, 2, 3, 4,5,6], // Assuming you have 4 columns (index 0, 1, 2, 3)
                className: 'text-center',
            },
            {
                targets: 0,
                width: '2%',
               
            },
            {
                targets: [1, 2, 3,4,5,6],
                width: '20%',
            }
        ],
        order: [[0, 'asc']],
        columns: [
            
            {"data": null,
                searchable: false,
                "sortable": false,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { data: 'startdate', name: 'startdate' },
            { data: 'enddate', name: 'enddate' },
            { data: 'desc', name: 'desc' },
            { data: 'prim_medal', name: 'prim_medal' },
            { data: 'status', name: 'status' },
            { data: 'detail', name: 'detail' },

        ],createdRow: function(row, data, dataIndex) {
            if (data.status === 'Gagal') {
                $(row).addClass('table-danger');
            }else if(data.status === '-'){
                $(row).addClass('table-warning');
            }
            else{
                $(row).addClass('table-success');
            }
        }
    });
    }


    function fetch_donation_streak(){
        streakTable = $('#streakTable').DataTable({
        processing: true,
        ajax: {
            url: "{{ route('point.getDonationStreakTable') }}",
            type: 'GET',
            data: {
                sedekahSubuh: 0,
            }
        },
        columnDefs: [
            {
                targets: [0, 1, 2, 3, 4,5,6], // Assuming you have 4 columns (index 0, 1, 2, 3)
                className: 'text-center',
            },
            {
                targets: 0,
                width: '2%',
               
            },
            {
                targets: [1, 2, 3,4,5,6],
                width: '20%',
            }
        ],
        order: [[0, 'asc']],
        columns: [
            
            {"data": null,
                searchable: false,
                "sortable": false,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { data: 'startdate', name: 'startdate' },
            { data: 'enddate', name: 'enddate' },
            { data: 'desc', name: 'desc' },
            { data: 'prim_medal', name: 'prim_medal' },
            { data: 'status', name: 'status' },
            { data: 'detail', name: 'detail' },

        ],createdRow: function(row, data, dataIndex) {
            if (data.status === 'Gagal') {
                $(row).addClass('table-danger');
            }else if(data.status === '-'){
                $(row).addClass('table-warning');
            }
            else{
                $(row).addClass('table-success');
            }
        }
    });
    }

    function fetch_member() {
    memberTable = $('#memberTable').DataTable({
        processing: true,
        ajax: {
            url: "{{ route('point.getReferralCodeMemberDatatable') }}",
            type: 'GET',
        },
        columnDefs: [
            {
                targets: [0, 1, 2, 3, 4,5,6,7], // Assuming you have 4 columns (index 0, 1, 2, 3)
                className: 'text-center',
            },
            {
                targets: 0,
                width: '2%',
               
            },
            {
                targets: [1, 2, 3,4,5,6,7],
                width: '20%',
            }
        ],
        order: [[0, 'asc']],
        columns: [
            
            {"data": null,
                searchable: false,
                "sortable": false,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { data: 'member_name', name: 'member_name' },
            { data: 'member_telno', name: 'member_telno' },

            { data: 'member_email', name: 'member_email' },

            { data: 'created_at', name: 'created_at' },
            { data: 'level', name: 'level' },
            { data: 'contribution', name: 'contribution' },
            { data: 'todayContribution', name: 'todayContribution' },
        ]
    });
}

    
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
                        $(api.column(0).footer()).html('Jumlah Mata Ganjaran: ' + sum);
                        console.log(sum);
                    }
       
                    
            });
        }
</script>

@endsection