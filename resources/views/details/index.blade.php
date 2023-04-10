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

            @foreach($getcat as $row)
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <div class="pt-3">
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
                        <td style="text-align:center">{{ $loop->iteration }}.</td>
                        <td>{{ $row2->dnama }}</td>
                        <td style="text-align:center">{{ $row2->quantity }}</td>
                        <td style="text-align:center">{{ number_format( $row2->price, 2)  }}</td>
                        <td style="text-align:center">{{ number_format($row2->totalamount, 2)  }}</td>
                        <td style="text-align:center">
                            <div class="d-flex justify-content-center">
                                <button class="btn btn-danger m-1"
                                    onclick="return confirm('Adakah anda pasti ?')">Buang</button>

                            </div>
                        </td>

                    </tr>
                    @endforeach

                    <tr>
                        <td></td>
                        <td colspan="3"><b>Jumlah</b> </td>
                        <td style="text-align:center"><b>{{ number_format($getdetail->where('cid', $row->cid)->sum('totalamount'), 2)  }}</b>
                        </td>
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