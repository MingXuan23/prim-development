@extends('layouts.master')

@section('css')
    <link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    {{-- <p>Welcome to this beautiful admin panel.</p> --}}
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="page-title-box">
                <h4 class="font-size-18">Kemaskini Saiz Baju</h4>
                {{-- <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item active">Murid >> Tambah Murid</li>
                </ol> --}}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="card col-md-12">

            @if(count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{$error}}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="form-validation" method="POST"
                action="{{ route('fees.updateShirtSize.updateShirtSize', ['fees_id' => $fee->id, 'student_id' => $studentId, 'response_id' => $responseId]) }}"
                enctype="multipart/form-data">

                @csrf
                @method("PUT")

                <div class="card-body">

                    <div class="form-group">
                        <label class="control-label">Nama Organisasi</label>
                        <select name="organization" id="organization" class="form-control"
                            data-parsley-required-message="Sila pilih organisasi" required readonly>
                            @foreach($organization as $row)
                                <option value="{{ $row->id }}">{{ $row->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Nama Yuran</label>
                        <input type="text" name="name" class="form-control"
                            data-parsley-required-message="Sila masukkan nama butiran" required placeholder="Nama Yuran"
                            readonly value="{{ $fee->name }}">
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Harga (RM)</label>
                            <input class="form-control input-mask text-left"
                                data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'"
                                im-insert="true" name="price" data-parsley-required-message="Sila masukkan harga"
                                data-parsley-errors-container=".errorMessagePrice" required readonly
                                value="{{ $fee->price }}">
                            <i>Harga per kuantiti</i>
                            <div class="errorMessagePrice"></div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Kuantiti</label>
                            <input type="text" name="quantity" class="form-control" placeholder="Kuantiti"
                                data-parsley-required-message="Sila masukkan kuantiti" required readonly
                                value="{{ $fee->quantity }}">
                        </div>

                    </div>

                    <div class="form-group">
                        <label>Pilih saiz baju</label> <br>
                        <div class="row">
                            <label class="col-lg-1 col-md-2 col-sm-4"><input type="radio" class="mr-1" value="XS" {{ $shirtSize === 'XS' ? 'checked' : '' }}
                                    name="shirt_size">XS</label>
                            <label class="col-lg-1 col-md-2 col-sm-4"><input type="radio" class="mr-1" value="S" {{ $shirtSize === 'S' ? 'checked' : '' }}
                                    name="shirt_size">S</label>
                            <label class="col-lg-1 col-md-2 col-sm-4"><input type="radio" class="mr-1" value="M" {{ $shirtSize === 'M' ? 'checked' : '' }}
                                    name="shirt_size">M</label>
                            <label class="col-lg-1 col-md-2 col-sm-4"><input type="radio" class="mr-1" value="L" {{ $shirtSize === 'L' ? 'checked' : '' }}
                                    name="shirt_size">L</label>
                            <label class="col-lg-1 col-md-2 col-sm-4"><input type="radio" class="mr-1" value="XL" {{ $shirtSize === 'XL' ? 'checked' : '' }}
                                    name="shirt_size">XL</label>
                            <label class="col-lg-1 col-md-2 col-sm-4"><input type="radio" class="mr-1" value="2XL" {{ $shirtSize === '2XL' ? 'checked' : '' }}
                                    name="shirt_size">2XL</label>
                            <label class="col-lg-1 col-md-2 col-sm-4"><input type="radio" class="mr-1" value="3XL" {{ $shirtSize === '3XL' ? 'checked' : '' }}
                                    name="shirt_size">3XL</label>
                            <label class="col-lg-1 col-md-2 col-sm-4"><input type="radio" class="mr-1" value="4XL" {{ $shirtSize === '4XL' ? 'checked' : '' }}
                                    name="shirt_size">4XL</label>
                            <label class="col-lg-1 col-md-2 col-sm-4"><input type="radio" class="mr-1" value="5XL" {{ $shirtSize === '5XL' ? 'checked' : '' }}
                                    name="shirt_size">5XL</label>
                            <label class="col-lg-1 col-md-2 col-sm-4"><input type="radio" class="mr-1" value="6XL" {{ $shirtSize === '6XL' ? 'checked' : '' }}
                                    name="shirt_size">6XL</label>
                            <label class="col-lg-1 col-md-2 col-sm-4"><input type="radio" class="mr-1" value="7XL" {{ $shirtSize === '7XL' ? 'checked' : '' }}
                                    name="shirt_size">7XL</label>
                            <label class="col-lg-1 col-md-2 col-sm-4"><input type="radio" class="mr-1" value="8XL" {{ $shirtSize === '8XL' ? 'checked' : '' }}
                                    name="shirt_size">8XL</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Penerangan</label>
                        <textarea name="description" class="form-control" placeholder="Penerangan" cols="30" rows="5"
                            readonly>{{ $fee->desc }}</textarea>
                    </div>

                    <div class="form-group mb-0">
                        <div class="text-right">
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
@endsection


@section('script')
    <!-- Peity chart-->
    <script src="{{ URL::asset('assets/libs/peity/peity.min.js')}}"></script>

    <!-- Plugin Js-->
    <script src="{{ URL::asset('assets/libs/dropzone/dropzone.min.js')}}"></script>
    <script src="{{ URL::asset('assets/libs/chartist/chartist.min.js')}}"></script>
    <script src="{{ URL::asset('assets/libs/parsleyjs/parsleyjs.min.js')}}"></script>
    <script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script>
    <script src="{{ URL::asset('assets/libs/inputmask/inputmask.min.js')}}"></script>
    <script src="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}" defer></script>

    <script>
        $(document).ready(function () {

            $('.form-validation').parsley();
            $(".input-mask").inputmask();

            var today = new Date();
            $('.yearhide').hide();
            $('.cbhide').hide();

            $('#date').datepicker({
                toggleActive: true,
                startDate: today,
                todayHighlight: true,
                format: 'dd/mm/yyyy',
                orientation: 'bottom'
            });
        });
    </script>
@endsection