@extends('layouts.master')

@section('css')
    <link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet" />
    @include('layouts.datatable')

@endsection

@section('content')

    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="page-title-box">
                <h4 class="font-size-18">Ahli Derma</h4>
            </div>
        </div>
    </div>
    <div class="row">

        <div class="col-md-12">
            <div class="card card-primary">
                {{csrf_field()}}
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Nama Pengguna</label>
                            <select name="member_name" id="member_name" class="form-control">
                                <option value="">Sila pilih pengguna</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Email Pengguna</label>
                            <input type="text" readonly class="form-control" id="member_email">
                        </div>
                        <div class="form-group col-md-4">
                            <label>No Telefon Pengguna</label>
                            <input type="text" readonly class="form-control" id="member_telno">
                        </div>
                    </div>
                    <div class="errorMessage"></div>

                    <label class="col-form-label">Capaian Hari Ini ({{ now()->format('d M Y') }})</label>

                    <div class="form-row" id="progressToday">
                        <div id="criteria1" class="col-md-4 col-sm-12">
                            <input type="text" readonly value="Derma anda: " class="form-control text-white" id="donation_today">
                        </div>
                        <div id="criteria2" class="col-md-4 col-sm-12">
                            <input type="text" readonly value="Derma guna kod anda: " class="form-control text-white" id="donation_member_today">
                        </div>
                        <div id="criteria3" class="col-md-4 col-sm-12">
                            <input type="text" readonly value="Derma Ahli Anda: " class="form-control text-white" id="today_follower_donation">
                        </div>
                    </div>

                    <div id="streakProgressBars">
                        <label class="col-form-label">
                            <div id="progress-bar-text">
                                <i class="fas fa-check-circle text-success" id="progress-bar-icon"></i> PRiM Medal: 0/40 Hari
                            </div>
                        </label>

                        <div class="progress mb-3" style="height: calc(1.5em + 0.75rem + 2px);">
                            <div class="progress-bar" role="progressbar" aria-valuemin="0" aria-valuemax="40">
                            </div>
                        </div>
                    </div>

                    <div id="sedekahSubuhProgressBars">
                        <label class="col-form-label">
                            <div id="progress-bar-text">
                                <i class="fas fa-check-circle text-success" id="progress-bar-icon"></i> PRiM Medal: 0/40 Hari
                            </div>
                        </label>

                        <div class="progress mb-3" style="height: calc(1.5em + 0.75rem + 2px);">
                            <div class="progress-bar" role="progressbar" aria-valuemin="0" aria-valuemax="40">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                    <table id="membersTable" class="table table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr style="text-align:center">
                                <th>No.</th>
                                <th>Nama Ahli</th>
                                <th>No Telefon</th>
                                <th>Email</th>
                                <th>Tarikh Masuk</th>
                                <th>Jumlah Sedekah</th>
                                <th>Sedekah Hari ini</th>
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
    <!-- Peity chart-->
    <script src="{{ URL::asset('assets/libs/peity/peity.min.js')}}"></script>

    <!-- Plugin Js-->
    <script src="{{ URL::asset('assets/libs/chartist/chartist.min.js')}}"></script>
    <script src="{{ URL::asset('assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script>
    <script src="{{ URL::asset('assets/libs/inputmask/inputmask.min.js')}}"></script>
    <script src="{{ URL::asset('assets/libs/dropzone/dropzone.min.js')}}"></script>
    <script src="{{ URL::asset('assets/libs/parsleyjs/parsleyjs.min.js')}}"></script>
    <script src="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}" defer></script>
    <script>

        $(document).ready(function () {
            $('#date').datepicker({
                toggleActive: true,
                format: 'dd-mm-yyyy',
                orientation: 'bottom'
            });

            $('#member_name').select2({
                placeholder: 'Sila pilih pengguna',
                allowClear: true,
                minimumInputLength: 0,
                ajax: {
                    url: "{{ route('donate.searchUsers') }}",
                    dataType: 'json',
                    delay: 300,
                    data: function (params) {
                        return {
                            q: params.term,
                            page: params.page || 1
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.data.map(function (u) {
                                return {
                                    id: u.id,
                                    text: u.name,
                                    email: u.email,
                                    telno: u.telno
                                };
                            }),
                            pagination: {
                                more: data.next_page_url !== null
                            }
                        };
                    }
                }
            });

            $('#member_name').on('select2:select', function (e) {
                var selectedData = e.params.data;
                $('#member_email').val(selectedData.email);
                $('#member_telno').val(selectedData.telno);
            });

            $('#member_name').on('select2:clear', function (e) {
                var selectedData = e.params.data;
                $('#member_email').val('');
                $('#member_telno').val('');
            });

            $('#member_name').change(function (e) {
                var selectedUserId = $(this).val();

                fetch_member(selectedUserId);
                fetchDonationStatistics(selectedUserId);
            });

            function fetchDonationStatistics(user_id = "") {
                $.ajax({
                    url: "{{ route('point.index') }}",
                    type: 'GET',
                    data: {
                        user_id: user_id
                    },
                    success: function (data) {
                        var streakData = data.streakData;
                        var sedekah_subuh = data.sedekah_subuh;

                        // update donation progress for today
                        $('#donation_today').val('Derma anda: ' + data.donation_leader_today);
                        data.donation_leader_today >= 1 ?
                            $('#donation_today').removeClass('bg-danger').addClass('bg-success') :
                            $('#donation_today').removeClass('bg-success').addClass('bg-danger');

                        // update donation by members for today
                        $('#donation_member_today').val('Derma guna kod anda: ' + data.donation_member_today);
                        data.donation_member_today > 0 ?
                            $('#donation_member_today').removeClass('bg-danger').addClass('bg-success') :
                            $('#donation_member_today').removeClass('bg-success').addClass('bg-danger');

                        // update donation by followers for today
                        $('#today_follower_donation').val('Derma Ahli Anda: ' + data.today_follower_donation + '/' + data.total_follower);
                        data.today_follower_donation >= data.total_follower ?
                            $('#today_follower_donation').removeClass('bg-danger').addClass('bg-success') :
                            $('#today_follower_donation').removeClass('bg-success').addClass('bg-danger');

                        // Update the progress bar and text for prim donation
                        $('#streakProgressBars #progress-bar-icon').removeClass('text-success text-danger');
                        if (streakData.streak_today) {
                            $('#streakProgressBars #progress-bar-icon').addClass('text-success');
                            $('#streakProgressBars #progress-bar-text').html('<i class="fas fa-check-circle text-success"></i> PRiM Medal ' + (streakData.streak_startdate ? 'Mulai ' + streakData.streak_startdate : '') + ': ' + streakData.current_streak + '/40 Hari');
                        } else {
                            $('#streakProgressBars #progress-bar-icon').addClass('text-danger');
                            $('#streakProgressBars #progress-bar-text').html('<i class="fas fa-exclamation-circle text-danger"></i> PRiM Medal ' + (streakData.streak_startdate ? 'Mulai ' + streakData.streak_startdate : '') + ': ' + streakData.current_streak + '/40 Hari');
                        }

                        var donationProgressPercentage = sedekah_subuh.current_streak * 2.5;
                        $('#streakProgressBars .progress-bar').css('width', donationProgressPercentage + '%');

                        streakData.streak_today ?
                            $('#streakProgressBars .progress-bar').removeClass('bg-danger').addClass('bg-warning') :
                            $('#streakProgressBars .progress-bar').removeClass('bg-warning').addClass('bg-danger');

                        // Update the progress bar and text for sedekah subuh
                        $('#sedekahSubuhProgressBars #progress-bar-icon').removeClass('text-success text-danger');
                        if (sedekah_subuh.streak_today) {
                            $('#sedekahSubuhProgressBars #progress-bar-icon').addClass('text-success');
                            $('#sedekahSubuhProgressBars #progress-bar-text').html('<i class="fas fa-check-circle text-success"></i> Sedekah Subuh Mulai ' + (sedekah_subuh.streak_startdate ? 'Mulai ' + sedekah_subuh.streak_startdate : '') + ': ' + sedekah_subuh.current_streak + '/40 Hari');
                        } else {
                            $('#sedekahSubuhProgressBars #progress-bar-icon').addClass('text-danger');
                            $('#sedekahSubuhProgressBars #progress-bar-text').html('<i class="fas fa-exclamation-circle text-danger"></i> Sedekah Subuh Mulai ' + (sedekah_subuh.streak_startdate ? 'Mulai ' + sedekah_subuh.streak_startdate : '') + ': ' + sedekah_subuh.current_streak + '/40 Hari');
                        }

                        var sedekahSubuhProgressPercentage = sedekah_subuh.current_streak * 2.5;
                        $('#sedekahSubuhProgressBars .progress-bar').css('width', sedekahSubuhProgressPercentage + '%');

                        sedekah_subuh.streak_today ?
                            $('#sedekahSubuhProgressBars .progress-bar').removeClass('bg-danger').addClass('bg-warning') :
                            $('#sedekahSubuhProgressBars .progress-bar').removeClass('bg-warning').addClass('bg-danger');
                    },
                    error: function (xhr, status, error) {
                        console.error('Error fetching donation statistics:', error);
                    }
                });
            }

            function fetch_member(user_id = "") {
                $('#membersTable').DataTable().destroy();
                $('#membersTable').DataTable({
                    processing: true,
                    serverSide: true,
                    searching: false,
                    ajax: {
                        url: "{{ route('point.getReferralCodeMemberDatatable') }}",
                        type: 'GET',
                        data: {
                            user_id: user_id
                        }
                    },
                    'columnDefs': [
                        {
                            "targets": [0], // your case first column
                            "className": "text-center",
                            "width": "2%"
                        },
                        {
                            "targets": [3, 4], // your case first column
                            "className": "text-center",
                        },
                    ],
                    columns: [
                        {
                            "data": null,
                            searchable: false,
                            "sortable": false,
                            render: function (data, type, row, meta) {
                                return meta.row + meta.settings._iDisplayStart + 1;
                            }
                        },
                        {
                            data: 'member_name',
                            name: 'member_name'
                        },
                        {
                            data: 'member_telno',
                            name: 'member_telno'
                        },
                        {
                            data: 'member_email',
                            name: 'member_email',

                        },
                        {
                            data: 'created_at',
                            name: 'created_at',
                        },
                        {
                            data: 'contribution',
                            name: 'contribution',
                            render: function (data, type, row) {
                                return data ? parseInt(data) : 0;
                            }
                        },
                        {
                            data: 'todayContribution',
                            name: 'todayContribution',
                            render: function (data, type, row) {
                                return data ? parseInt(data) : 0;
                            }
                        }
                    ]
                });
            }
        });
    </script>
@endsection