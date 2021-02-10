@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">{{ $getfees->nama  ?? '' }}</h4>
            <!-- <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Welcome to Veltrix Dashboard</li>
            </ol> -->
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            {{-- <div class="card-header">List Of Applications</div> --}}
            <div>

                <a style="margin: 19px; float: right;" href="{{ route('details.create', ['id' => $getfees->id] ) }}"
                    class="btn btn-primary"> <i class="fas fa-plus"></i> Tambah Butiran Yuran</a>
                {{-- <a style="margin: 19px; float: left;" href="{{ route('category.index') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Kategori</a> --}}
            </div>



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

                {{-- <div align="right">
                            <a href="{{route('admin.create')}}" class="btn btn-primary">Add</a>
                <br />
                <br />
            </div> --}}
            @foreach($getcat as $row)
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <div>
                        <label for="" style="font-size: 16px"><b>Kategori {{ $row->cnama}}</b> </label>
                    </div>
                    <tr style="text-align:center">
                        <th style="width:5%">Bil.</th>
                        <th style="width:40%">Butiran</th>
                        <th style="width:10%">Kuantiti</th>
                        <th style="width:10%">Harga (RM)</th>
                        <th style="width:20%">Jumlah (RM)</th>
                        <th style="width:15%">Action</th>
                    </tr>

                    @foreach($getdetail->where('cid', $row->cid) as $row2)
                    <tr>
                        <td>{{ $loop->iteration }}.</td>
                        <td>{{ $row2->dnama }}</td>
                        <td>{{ $row2->quantity }}</td>
                        <td>{{ $row2->price }}</td>
                        <td>{{ number_format($row2->totalamount, 2)  }}</td>
                        <td>
                            <div class="d-flex justify-content-center">
                                <a href="" class="btn btn-primary m-1">Edit</a>
                                <button class="btn btn-danger m-1"
                                    onclick="return confirm('Adakah anda pasti ?')">Buang</button>

                            </div>
                        </td>

                    </tr>
                    @endforeach

                    <tr>
                        <td></td>
                        <td colspan="3"><b>Jumlah</b> </td>
                        <td><b>{{ number_format($getdetail->sum('totalamount'), 2)  }}</b> </td>
                        <td></td>
                    </tr>
                </table>
            </div>
            @endforeach
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
@endsection