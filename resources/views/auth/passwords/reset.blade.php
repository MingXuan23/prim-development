@extends('layouts.master-without-nav')

@section('title')
Reset Kata Laluan
@endsection

@section('body')
<body>
@endsection

@section('content')
        <div class="home-btn d-none d-sm-block">
            <a href="index" class="text-dark"><i class="fas fa-home h2"></i></a>
        </div>
        <div class="account-pages my-5 pt-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card overflow-hidden">
                            <div class="bg-primary">
                                <div class="text-primary text-center p-4">
                                    <h5 class="text-white font-size-20 p-2">Reset Kata Laluan</h5>
                                    <a href="index" class="logo logo-admin">
                                        <img src="{{ URL::asset('assets/images/logo/prim-logo2.svg') }}" height="60" alt="logo">
                                    </a>
                                </div>
                            </div>
    
                            <div class="card-body p-4">
                                
                                <div class="p-3">
                                    <form class="form-horizontal mt-4" method="POST" action="{{ route('password.update') }}">
                                        @csrf
                                        <input type="hidden" name="token" value="{{ $token }}">

                                        <div class="form-group">
                                            {{-- <label for="useremail">Email</label> --}}
                                            <input type="email" class="form-control  @error('email') is-invalid @enderror" id="useremail" name="email" placeholder="Masukkan email"
                                            value="{{ urldecode(request()->get('email')) }}" hidden>
                                            @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="userpassword">Kata Laluan</label>
                                            <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" required id="userpassword" placeholder="Masukkan kata laluan">
                                            @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="userpassword">Sahkan Kata Laluan</label>
                                            <input id="password-confirm" type="password" name="password_confirmation" class="form-control @error('password') is-invalid @enderror" name="password" required placeholder="Masukkan kata laluan">
                                        </div>

                                        <div class="form-group row  mb-0">
                                            <div class="col-12 text-right">
                                                <button class="btn btn-primary w-md waves-effect waves-light" type="submit">Reset Kata Laluan</button>
                                            </div>
                                        </div>    
                                    </form>
                                </div>
                            </div>
    
                        </div>
    
                        <div class="mt-5 text-center">
                            <p>Ingat semula? <a href="/login" class="font-weight-medium text-primary"> Log Masuk </a> </p>
                        </div>
    
                    </div>
                </div>
            </div>
        </div>

@endsection