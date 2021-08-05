@extends('layouts.master-without-nav')

@section('title')
Reset Kata Laluan
@endsection

@section('body')
<body>
@endsection

@section('content')
{{-- <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Reset Password') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Send Password Reset Link') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> --}}
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
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                            @if(count($errors) > 0)
                            <div id="danger" class="alert alert-danger mt-5">
                                @foreach($errors->all() as $error)
                                    {{$error}}
                                @endforeach
                            </div>
                            @endif

                            @if (session('status'))
                                <div id="success" class="alert alert-success mt-5" role="alert">
                                    {{ session('status') }}
                                </div>
                            @endif

                            <div class="alert alert-info mt-5" role="alert">
                                Masukkan email dan arahan akan dihantar melalui email!
                            </div>

                                <form class="form-horizontal mt-4" method="POST" action="{{ route('password.email') }}">
                                    @csrf

                                <div class="form-group">
                                    <label for="useremail">Email</label>
                                    <input type="email" name="email" class="form-control" id="useremail"
                                        placeholder="Masukkan email">
                                </div>

                                <div class="form-group row  mb-0">
                                    <div class="col-12 text-right">
                                        <button class="btn btn-primary w-md waves-effect waves-light"
                                            type="submit">Reset</button>
                                    </div>
                                </div>

                            </form>

                        </div>
                    </div>

                </div>

                <div class="mt-5 text-center">
                    <p>Ingat semula ? <a href="{{ route('login') }}" class="font-weight-medium text-primary"> Log Masuk</a> </p>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    setTimeout(function() {
        $('#success').fadeOut('fast');
        $('#danger').fadeOut('fast');
    }, 3000);
</script>
@endsection
