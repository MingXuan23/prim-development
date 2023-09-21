@extends('layouts.master')

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    @include('layouts.datatable');
@endsection

@section('content')
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="page-title-box">
                <h4 class="font-size-18">Pilih Organisasi</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="list-group">
                        @foreach($data as $list)
                            <a href="{{ route('orders.listpesanan', ['id' => $list->id]) }}" class="order_modal list-group-item list-group-item-action">
                                <div class="d-flex">
                                    <div class="flex-column ml-2">
                                        <h4>{{ $list->nama }}</h4>
                                        <div class="d-flex">
                                            <div class="justify-content-center align-items-center mr-2">
                                                <i class="fas fa-map-marker-alt"></i>
                                                <p class="m-0">{{ $list->address }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="arrow-icon ml-auto justify-content-end align-self-center">
                                        <h1>
                                            <i class="fas fa-angle-right"></i>
                                        </h1>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection