@extends('layouts.master-without-nav')

@section('title')
Login
@endsection

@section('body')
<body>
@endsection

@section('content')
<div class="account-pages my-5 pt-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 col-xl-5">
                <div class="card overflow-hidden">
                    <div class="bg-primary">
                        <div class="text-primary text-center p-4">
                            <h5 class="text-white font-size-20">Makluman</h5>
                            <p class="text-white-50">Tidak perlu daftar sendiri</p>
                            <a href="/" class="logo logo-admin">
                                <img src="assets/images/logo/prim-logo2.svg" height="60" alt="logo">
                            </a>
                        </div>
                    </div>
<br><br>
                    <div class="card-body p-4">
                        <div class="p-3 text-center">
                        <h4 class="mb-3 text-danger font-weight-bold">Anda Tidak Perlu Daftar Sendiri!</h4>
                            <p class="mb-4" style="font-size: 1.1rem;">
                                Jangan Risau! Akuan pelajar/penjaga untuk pembayaran yuran akan <strong>didaftar oleh guru sekolah</strong>.
                                <br>
                                <br>
                                Sila <strong>Hubungi Guru Sekolah</strong> anda jika anda belum menerima informasi akuan anda atau jika anda keliru dengan proses pembayaran yuran.
                            </p>

                            <a href="/yuran" class="btn btn-primary waves-effect waves-light">
                                Kembali ke Laman Utama
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="row justify-content-center mt-5">
                <div class="col-lg-12">
                    <div class="section-title text-center">
                        <h3 class="title extra">Kerjasama</h3>
                        <p>Laman web ini telah diakui dan disahkan selamat untuk digunakan.</p>
                    </div>
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
<!-- Optional additional scripts can be added here -->
@endsection
