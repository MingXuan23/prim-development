@extends('layouts.master')

@section('css')

@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Homestay</h4>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Homestay >> Daftar Homestay</li>
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
            <form method="post" action="{{route('homestay.inserthomestay')}}" enctype="multipart/form-data"
                class="form-validation">
                {{csrf_field()}}
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <div class="form-group required">
                                <label class="control-label">Nama Homestay <span style="color:#d00"> *</span></label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Nama Homestay"
                                    data-parsley-required-message="Sila masukkan nama homestay / hotel" required>
                            </div>
                        </div>
                        
                    </div>
                    <div class="row">
                        <div class="col">
                    <div class="form-group">
                        <label class="control-label"> Lokasi / Alamat <span style="color:#d00"> *</span> </label>
                        <textarea name="location" id="location" class="form-control" rows="4" placeholder="Lokasi / Alamat"
                            data-parsley-required-message="Sila masukkan alamat homestay / hotel" required></textarea>
                    </div>
                    </div>
                    </div>
                    

                    <div class="row">
                        
                        <div class="col">
                            <div class="form-group required">
                                <label class="control-label">No Telefon <span style="color:#d00"> *</span></label>
                                <input type="text" name="pno" id="pno" class="form-control" placeholder="No Telefon"
                                    data-parsley-required-message="Sila masukkan no telefon" required>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group required">
                                <label class="control-label">Status Homestay</label>
                                <select name="stat" id="stat" class="form-control"
                                    data-parsley-required-message="Sila pilih status homestay" required>
                                    <option selected>Pilih Status Homestay</option>
                                    <option value="Available">Available</option>
                                    <option value="Disabled">Disabled</option>
                                </select>
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
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ready(function () {

        $('.alert').delay(3000).fadeOut()
    });

</script>
@endsection