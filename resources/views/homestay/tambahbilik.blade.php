@extends('layouts.master')

@section('css')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Bilik</h4>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Bilik >> Tambah Bilik</li>
            </ol>
        </div>
    </div>
</div>
<div class="row">
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

            @if(\Session::has('error'))
                <div class="alert alert-danger">
                    <p>{{ \Session::get('error') }}</p>
                </div>
            @endif
            <form method="post" action="{{route('homestay.addroom')}}" enctype="multipart/form-data"
                class="form-validation">
                {{csrf_field()}}
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <div class="form-group required">
                                <label class="control-label">Nama Homestay <span style="color:#d00"> *</span></label>
                                <select name="homestayid" id="homestayid" class="form-control"
                                    data-parsley-required-message="Sila pilih status homestay" required>
                                    <option selected>Pilih Homestay</option>
                                    @foreach($data as $rows)
                                    <option value="{{ $rows->id }}">{{ $rows->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                    </div>

                    <div class="row">
                        <div class="col">
                    <div class="form-group">
                        <label class="control-label"> Nama Bilik <span style="color:#d00"> *</span> </label>
                        <input type="text" name="roomname" id="roomname" class="form-control" placeholder="Nama / Nombor Bilik"
                            data-parsley-required-message="Sila masukkan nama / nombor bilik" required>
                            </input>
                    </div>
                    </div>
                    </div>
                    
                    <div class="row">
                        
                        <div class="col">
                            <div class="form-group required">
                                <label class="control-label">Kapasiti Bilik <span style="color:#d00"> *</span></label>
                                <input type="text" class="form-control" id="roompax" name="roompax">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group required">
                                <label class="control-label">Harga Semalam (RM) </label>
                                <input type="text" class="form-control" id="price" name="price">
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col">
                    <div class="form-group">
                        <label class="control-label"> Detail Bilik <span style="color:#d00"> *</span> </label>
                        <input type="text" name="details" id="details" class="form-control" placeholder="Contoh : 2 Bilik 1 Bilik Air Wifi Disediakan Tempat Parking Banyak"
                            data-parsley-required-message="Sila masukkan jumlah diskaun" required>
                            </input>
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
                                Simpan
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
<script src="{{ URL::asset('assets/libs/parsleyjs/parsleyjs.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/inputmask/inputmask.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/jquery-mask/jquery.mask.min.js')}}"></script>


<script>

$(document).ready(function () {
        $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
        $('.alert').delay(3000).fadeOut()
    });

</script>
@endsection