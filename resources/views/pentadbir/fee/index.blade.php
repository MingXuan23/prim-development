@extends('layouts.master')

@section('css')
    <link href="{{ URL::asset('assets/libs/chartist/chartist.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .modal-dialog {
            top: 15%;
        }

        .scrollheight {
            height: 300px;
            overflow: auto;
        }

        @media (max-width: 576px) {
            .modal-dialog {
                max-width: none !important;
                width: 90% !important;
            }
        }

    </style>
@endsection

@section('content')
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="page-title-box">
                <h4 class="font-size-18">Yuran</h4>
                <!-- <ol class="breadcrumb mb-0">
                                                                                                    <li class="breadcrumb-item active">Welcome to Veltrix Dashboard</li>
                                                                                                </ol> -->
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-3">
                <div class="card-body pb-1" style="background-color: #e6e6e6;border: 1px solid #dfdfdf;">
                    <p class="text-muted mb-1">
                        Langkah-langkah menambahkan yuran :

                    </p>
                    <ul class="text-muted">
                        <li>Tambah kategori yuran baru dengan klik butang senarai kategori.</li>
                        <li>Tambah yuran dengan klik butang tambah yuran.</li>
                        <li>Tambah perincian yuran bagi menetapkan setiap harga item bagi yuran yg dipilih.</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                {{-- <div class="card-header">List Of Applications</div> --}}
                <div>
                    {{-- route('sekolah.create') --}}
                    <a class="btn btn-secondary mt-4 ml-4" data-toggle="modal" data-target="#categoryModal"> Senarai
                        Kategori</a>

                    <a href="{{ route('fees.create') }}" class="btn btn-primary mt-4 ml-2" data-toggle="modal"
                        data-target="#feeModal"> <i class="fas fa-plus"></i>
                        Tambah
                        Yuran</a>
                </div>

                <div class="card-body">

                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if (\Session::has('success'))
                        <div class="alert alert-success">
                            <p>{{ \Session::get('success') }}</p>
                        </div>
                    @endif

                    {{-- <div align="right">
                            <a href="{{route('admin.create')}}" class="btn btn-primary">Add</a>
                <br />
                <br />
            </div> --}}
                    <div class="scrollheight">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <tr style="text-align:center">
                                    <th>Bil.</th>
                                    <th>Nama Yuran</th>
                                    <th>Jumlah Amaun (RM)</th>
                                    <th>Action</th>
                                </tr>

                                @foreach ($fees as $row)
                                    <tr>
                                        <td>{{ $loop->iteration }}.</td>
                                        <td>{{ $row->nama }}</td>
                                        <td> {{ number_format($row->totalamount, 2) ?? '0' }} </td>
                                        <td>
                                            <div class="d-flex justify-content-center">
                                                <a href="{{ route('details.getfees', ['id' => $row->id]) }}"
                                                    class="btn btn-primary m-1">Perincian</a>
                                                <button class="btn btn-danger m-1"
                                                    onclick="return confirm('Adakah anda pasti ?')">Buang</button>
                                                {{-- <a href="{{ route('pay.index', ['id' => $row->id]) }}"
                                                class="btn btn-success m-1">Bayar</a> --}}

                                            </div>
                                        </td>

                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="modal fade" id="categoryModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalLabel">Senarai Kategori</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="scrollheight">

                        <a href="{{ route('category.create') }}" class="btn btn-primary mb-3"> <i class="fas fa-plus"></i>
                            Tambah Kategori</a>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <tr style="text-align:center">
                                    <th>Bil.</th>
                                    <th>Nama Kategori</th>
                                    <th>Action</th>
                                </tr>

                                @foreach ($listcategory as $row)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $row->nama }}</td>
                                        <td>
                                            <div class="d-flex justify-content-center">
                                                <a href="{{ route('category.edit', $row->id) }}"
                                                    class="btn btn-primary m-1">Edit</a>
                                            </div>
                                        </td>

                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button> --}}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="feeModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="feeModal">Tambah Yuran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="scrollheight">

                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form method="post" action="{{ route('fees.store') }}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Nama Yuran</label>
                                    <input type="text" name="name" class="form-control" placeholder="Nama Yuran">
                                </div>
                                <div class="form-group">
                                    <label>Tahun</label>
                                    <select name="year" id="year" class="form-control">
                                        <option value="1">Tahun 1</option>
                                        <option value="2">Tahun 2</option>
                                        <option value="3">Tahun 3</option>
                                        <option value="4">Tahun 4</option>
                                        <option value="5">Tahun 5</option>
                                        <option value="6">Tahun 6</option>
                                    </select>
                                </div>


                                {{-- <div class="form-group">
                                    <label>Kategori Yuran</label>
                                    <select name="cat" id="cat" class="form-control">
                                        @foreach ($cat as $row)
                                        <option value="{{ $row->id }}">{{ $row->nama }}</option>
                                        @endforeach
                                    </select>
                                </div> --}}

                                {{-- <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="exampleCheck1">
                                <label class="form-check-label" for="exampleCheck1">Check me out</label>
                                </div> --}}
                                <div class="form-group mb-0">
                                    <div>
                                        <button type="submit" class="btn btn-primary waves-effect waves-light mr-1">
                                            Simpan
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-body -->


                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button> --}}
                </div>
            </div>
        </div>
    </div>

@endsection


@section('script')
    <!-- Peity chart-->
    <script src="{{ URL::asset('assets/libs/peity/peity.min.js') }}"></script>

    <!-- Plugin Js-->
    <script src="{{ URL::asset('assets/libs/chartist/chartist.min.js') }}"></script>

    <script src="{{ URL::asset('assets/js/pages/dashboard.init.js') }}"></script>
@endsection
