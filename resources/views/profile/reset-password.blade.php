@extends('layouts.master')
@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/css/reset-password.css') }}" rel="stylesheet" type="text/css" />
@include('layouts.datatable')

@endsection

@section('content')
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Tukar Kata Laluan</h4>
        </div>
    </div>

    @if($message = Session::get('error'))
    <div class="alert alert-danger">
     <p>{{ $message }}</p>
    </div>
    @endif
    <div class="col-lg-6 setCenter">
    <div class="card">
        <div class="card-body">
            <form action="/profile/updatePwd/{{ Auth::id() }}" class="form-horizontal mt-4" method="post" >
                {{-- @method('PATCH') --}}
                {{csrf_field()}}
                <!-- old password -->
                <div class="form-group">
                    <label for="userpassword">Kata Laluan Lama</label>
                    <input type="password"
                    class="form-control @error('old_password') is-invalid @enderror" name="old_password"
                    required id="userpassword" placeholder="Kata Laluan">
                    @error('old_password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror

                </div>
                <!-- new password -->
                <div class="form-group">
                    <label for="userpassword">Kata Laluan Baharu</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" required id="userpassword" placeholder="Masukkan kata laluan">
                     @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- type again -->
                
                <div class="form-group">
                    <label for="userpassword">Pengesahan Kata Laluan</label>
                    <input id="password-confirm" type="password" name="password_confirmation"
                                            class="form-control @error('password_confirmation') is-invalid @enderror"
                                            name="password_confirmation" required placeholder="Sahkan Kata Laluan">
                </div>

                <div class="form-group row  mb-0">
                    <div class="col-12 text-right">
                        <button type="button" class="btn btn-light w-md waves-effect waves-light" onclick="window.location='{{ url("/profile") }}'">Kembali</button>
                        <button type="submit" class="btn btn-primary waves-effect waves-light mr-1">
                            Simpan
                        </button>
                </div>    

            </form>
        </div>
    </div>
</div>
@endsection