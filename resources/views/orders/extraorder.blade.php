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
                <h4 class="font-size-18">Tambah Pesanan (Jika Ada)</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">

                @if(Session::has('success'))
                    <div class="alert alert-success">
                    <p>{{ Session::get('success') }}</p>
                    </div>
                @elseif(Session::has('error'))
                    <div class="alert alert-danger">
                    <p>{{ Session::get('error') }}</p>
                    </div>
                @endif
                
                <form method="post" action="{{ route('orders.extraorder', ['id' => $organizationId, 'id2' => $orderId]) }}" enctype="multipart/form-data"
                    class="form-validation">
                    {{csrf_field()}}
                    <div class="card-body">                        
                        <div class="row">
                            <div class="col">
                                <div class="form-group required">
                                    <label class="control-label"> Menu <span style="color:#d00"> *</span></label>
                                    <select name="dish" id="dish" class="form-control"
                                        data-parsley-required-message="Sila pilih jenis menu" required>
                                        <option selected>Pilih Menu</option>
                                        @foreach($dishes as $rows)
                                            <option value="{{ $rows->id }}">{{ $rows->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group required">
                                    <label class="control-label"> Kuantiti <span style="color:#d00"> *</span></label>
                                    <input type="number" class="form-control" id="quantity" name="quantity" min="1" max="9999" required>
                                </div>
                            </div>
                        </div>
                    
                        <div class="form-group mb-0">
                            <div class="text-right">
                                
                                <a type="button" href="{{ route('orders.checkout', ['id' => $orderId]) }}"
                                    class="btn btn-secondary waves-effect waves-light mr-1">
                                    Bayar
                                </a>
                                <button type="submit" class="btn btn-primary waves-effect waves-light mr-1">
                                    Hantar
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection