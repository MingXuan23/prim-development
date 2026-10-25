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

                if (selectedUserId) {
                    fetch_member(selectedUserId);
                } else {
                    fetch_member();
                }
            });

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