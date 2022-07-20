@extends('layouts.master-without-nav')

@section('title')
Login
@endsection

@section('body')
<body>
@endsection

@section('content')
    <!-- <div class="home-btn d-none d-sm-block">
        <a href="index" class="text-dark"><i class="fas fa-home h2"></i></a>
    </div> -->
    <div class="account-pages my-5 pt-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card overflow-hidden">
                        <div class="bg-primary">
                            <div class="text-primary text-center p-4">
                                <h5 class="text-white font-size-20">Selamat Datang!</h5>
                                <p class="text-white-50">Log Masuk ke PRIM</p>
                                <a href="/derma" class="logo logo-admin">
                                    <img src="assets/images/logo/prim-logo2.svg" height="60" alt="logo">
                                </a>
                            </div>
                        </div>

                        <div class="card-body p-4">
                            <div class="p-3">
                            <form class="form-horizontal mt-4" method="POST" action="{{ route('login') }}">
                                @csrf
                                    <div class="form-group">
                                        <label for="username">Email / Nombor Telefon</label>
                                        <input name="email" type="text" class="form-control @error('email') is-invalid @enderror" @if(old('email')) value="{{ old('email') }}" @else value="" @endif  id="username" placeholder="Masukkan Email ataupun Nombor Telefon" autocomplete="email" autofocus>
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="userpassword">Kata Laluan</label>
                                        <input type="password" name="password" class="form-control  @error('password') is-invalid @enderror" id="userpassword" value="" placeholder="Kata Laluan">
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="customControlInline">
                                                {{-- <label class="custom-control-label" for="customControlInline">Remember me</label> --}}
                                            </div>
                                        </div>
                                        <div class="col-sm-6 text-right">
                                            <button class="btn btn-primary w-md waves-effect waves-light" type="submit">Log Masuk</button>
                                        </div>
                                    </div>

                                    <div class="form-group mt-2 mb-0 row">
                                        <div class="col-12 mt-4">
                                            <a href="{{ route('password.request') }}"><i class="mdi mdi-lock"></i> 	Lupa Kata Laluan?</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>

                    <div class="mt-5 text-center">
                        <p>Masih belum daftar bersama kami? <a href="/register" class="font-weight-medium text-primary"> Daftar Sekarang </a> </p>
                    </div>

                </div>
            </div>

            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="section-title text-center">
                            <!-- section title -->
                            {{-- <span class="subtitle">Screenshots</span> --}}
                            <h3 class="title extra">Kerjasama</h3>
                            <p>Laman web ini telah diakui dan disahkan selamat untuk digunakan.</p>
                        </div><!-- //. section title -->
                    </div>
                </div>
                
                <div class="row text-center">
                    <div class="col-lg-12">
                        <div class="row justify-content-center">
                            <div class="col-lg-4 p-3 text-sm-center align-self-center">
                                <img src="{{ URL::asset('assets/landing-page/img/logo-paynet.png') }}" alt="" style="max-width:70%">
                            </div>
                            <div class="col-lg-4 p-3 text-sm-center align-self-center">
                                <img src="{{ URL::asset('assets/landing-page/img/logo-bank-islam.png') }}" alt="" style="max-width:70%">
                            </div>
                            <div class="col-lg-4 p-3 text-sm-center align-self-center">
                                <img src="{{ URL::asset('assets/landing-page/img/logo-utem-blue.png') }}" alt="" style="max-width:70%">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('script')
<!-- <script src="{{ URL::asset('assets/libs/jquery-mask/jquery.mask.min.js')}}"></script>

<script>
    $(document).ready(function () {
        $('#username').mask('+600000000000');
    });
</script> -->
@endsection