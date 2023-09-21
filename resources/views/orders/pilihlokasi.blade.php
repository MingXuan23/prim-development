@extends('layouts.master')

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    @include('layouts.datatable');
    <style>
        #map {
            height: 500px;
            width: 80%;
            margin-top: 20px;
        }
    </style>
@endsection

@section('content')
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="page-title-box">
                <h4 class="font-size-18">Buat Pesanan</h4>
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

                <div id="map"></div>
                <form method="post" action="{{ route('orders.addorder', ['id' => $organizationId]) }}" enctype="multipart/form-data"
                    class="form-validation">
                    {{csrf_field()}}
                    <div class="card-body">
                        <input type="hidden" name="latitude" id="latitude">
                        <input type="hidden" name="longitude" id="longitude">

                        <div class="row">
                            <div class="col">
                                <div class="form-group required">
                                    <label class="control-label"> Alamat Penghantaran <span style="color:#d00"> *</span></label>
                                    <input type="text" name="address" id="address" class="form-control" placeholder="Contoh: UTeM"
                                        data-parsley-required-message="Sila masukkan alamat penghantaran" required>
                                    </input>
                                </div>
                            </div>
                        </div>
                        
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
                                    <label class="control-label"> Kuantiti </label>
                                    <input type="number" class="form-control" id="quantity" name="quantity" min="1" max="9999" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="form-group required">
                                    <label class="control-label"> Note Tambahan <span style="color:#d00"> </span></label>
                                    <input type="text" name="description" id="description" class="form-control" placeholder="Contoh: Extra Nasi">
                                    </input>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="form-group required">
                                    <label class="control-label"> Tarikh </label>
                                    <input type="date" class="form-control" id="delivery_date" name="delivery_date" min="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group required">
                                    <label class="control-label"> Masa </label>
                                    <input type="time" class="form-control" id="delivery_time" name="delivery_time" required>
                                </div>
                            </div>
                        </div>
                    
                        <div class="form-group mb-0">
                            <div class="text-right">
                                
                                <a type="button" href="{{ url()->previous() }}"
                                    class="btn btn-secondary waves-effect waves-light mr-1">
                                    Kembali
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

@section('script')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ $apiKey }}&callback=initMap"></script>
    <script>
        function initMap() {
            var map = new google.maps.Map(document.getElementById('map'), {
                center: {lat: {{ $latitude }}, lng: {{ $longitude }}},
                zoom: {{ $zoom }}
            });

            var marker = new google.maps.Marker({
                position: {lat: {{ $latitude }}, lng: {{ $longitude }}},
                map: map,
                draggable: true
            });

            google.maps.event.addListener(marker, 'dragend', function() {
                var latLng = marker.getPosition();
                document.getElementById('latitude').value = latLng.lat();
                document.getElementById('longitude').value = latLng.lng();
            });
        }
    </script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ $apiKey }}&callback=initMap"></script>
@endsection