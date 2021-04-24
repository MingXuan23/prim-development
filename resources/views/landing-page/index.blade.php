<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noarchive">
    <title> PRIM </title>

    @include('landing-page.head')
</head>

<body>

    <nav class="navbar navbar-area navbar-expand-lg nav-absolute white nav-style-01">
        <div class="container nav-container">
            <div class="responsive-mobile-menu">
                <div class="logo-wrapper">
                    <a href="index.html" class="logo">
                        <img src="{{ URL::asset('assets/landing-page/img/logo-header.png') }}" alt="logo">
                    </a>
                </div>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#appside_main_menu"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
            <div class="collapse navbar-collapse" id="appside_main_menu">
                <ul class="navbar-nav">
                    <li class="current-menu-item">
                        <a href="#">Utama</a>
                    </li>
                    <li><a href="#about">Perkhidmatan</a></li>
                    {{-- <li><a href="#pricing">Pricing</a></li> --}}
                    <li><a href="#team">Modul</a></li>
                    <li class="menu-item-has-children">
                        <a href="#">Organisasi</a>
                        <ul class="sub-menu">
                            <li><a href="/organization-list">Masjid</a></li>
                            <li><a href="blog-details.html">Sekolah JAIM</a></li>
                        </ul>
                    </li>
                    <li><a href="#contact">Hubungi Kami</a></li>
                </ul>
            </div>
            <div class="nav-right-content">
                <ul>
                    <li class="button-wrapper">
                        <a href="/login" class="boxed-btn btn-rounded">Log Masuk</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- header area start  -->
    <header class="header-area header-bg-2 style-two" id="home">
        <div class="header-right-image  wow zoomIn">
            <img src="{{ URL::asset('assets/landing-page/img/mobile-image-4.png') }}" alt="header right image">
        </div>
        <div class="container">
            <div class="row">
                <div class="col-lg-7">
                    <div class="header-inner">
                        <h1 class="title wow fadeInDown">PRiM</h1>
                        <p>Sebuah sistem yang menyediakan perkhidmatan untuk kutipan derma sesebuah organisasi
                            berdaftar.</p>
                        <div class="btn-wrapper wow fadeInUp">
                            <a href="#" class="boxed-btn btn-rounded">Daftar Sekarang</a>
                            <a href="#" class="boxed-btn btn-rounded blank">Log Masuk</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- header area end  -->

    <!-- about us area start -->

    <section class="about-us-area style-two">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="section-title left-aligned">
                        <!-- section title -->
                        {{-- <span class="subtitle">Tentang PRiM</span> --}}
                        <h3 class="title extra">Tentang PRiM</h3>
                        <p>Consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolor tempor
                            incididunt ut labore dolore magna.</p>
                    </div><!-- //. section title -->
                </div>
                <div class="col-lg-6">
                    <div class="feature-area">
                        <ul class="feature-list">
                            <li class="single-feature-list wow zoomIn">
                                <div class="icon icon-bg-1">
                                    <i class="flaticon-vector"></i>
                                </div>
                                <div class="content">
                                    <h4 class="title"><a href="#">Clean Design</a></h4>
                                    <p>Consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolor
                                        tempor incididunt</p>
                                </div>
                            </li>
                            <li class="single-feature-list wow zoomIn">
                                <div class="icon icon-bg-2">
                                    <i class="flaticon-responsive"></i>
                                </div>
                                <div class="content">
                                    <h4 class="title"><a href="#">Fully Respnosive</a></h4>
                                    <p>Consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labor tempor
                                        incididunt</p>
                                </div>
                            </li>
                            <li class="single-feature-list wow zoomIn">
                                <div class="icon icon-bg-3">
                                    <i class="flaticon-layers-2"></i>
                                </div>
                                <div class="content">
                                    <h4 class="title"><a href="#">Pixel Perfect</a></h4>
                                    <p>Consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolor
                                        tempor incididunt</p>
                                </div>
                            </li>
                            <li class="single-feature-list wow zoomIn">
                                <div class="icon icon-bg-4">
                                    <i class="flaticon-picture"></i>
                                </div>
                                <div class="content">
                                    <h4 class="title"><a href="#">Retina Ready</a></h4>
                                    <p>Consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolor
                                        tempor incididunt</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- about us area end -->
    <!-- portfolio area start -->
    <section class="portfolio" class="portfolio-area portfolio-four pb-100">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-10">
                    <div class="section-title text-center pb-10">
                        <h3 class="title">Organisasi</h3>
                        <p class="text">Stop wasting time and money designing and managing a website that doesn’t get
                            results. Happiness guaranteed!</p>
                    </div> <!-- section title -->
                </div>
            </div> <!-- row -->
            <div class="row">
                <div class="col-lg-3 col-md-3">
                    <div class="portfolio-menu text-center mt-50">
                        <ul>
                            <li> Semua Organisasi </li>
                            <li data-filter=".branding-4" class="active">Masjid An-Najihah </li>
                            <li data-filter=".marketing-4">Masjid Al-Alami</li>
                            <li data-filter=".planning-4">Tahfiz Iman</li>
                            <li data-filter=".research-4">Pusat Islam UTeM</li>
                            {{-- <li >Semua Organisasi</li> --}}
                        </ul>
                    </div> <!-- portfolio menu -->
                </div>
                <div class="col-lg-9 col-md-9">
                    <div class="row no-gutters grid mt-50">
                        <div class="col-lg-4 col-sm-6 branding-4 planning-4">
                            <div class="single-portfolio">
                                <div class="portfolio-image">
                                    <img src="{{ URL::asset('assets/landing-page/img/blog/1.jpg') }}" alt="blog image"
                                        width="277" height="277">
                                    <div class="portfolio-overlay d-flex align-items-center justify-content-center">
                                        <div class="portfolio-content">
                                            <div class="portfolio-icon">
                                                <a class="image-popup" href="assets/images/portfolio/1.png"><i
                                                        class="lni lni-zoom-in"></i></a>
                                                <img src="assets/images/portfolio/shape.svg" alt="shape" class="shape">
                                            </div>
                                            <div class="portfolio-icon">
                                                <a href="#"><i class="lni lni-link"></i></a>
                                                <img src="assets/images/portfolio/shape.svg" alt="shape" class="shape">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- single portfolio -->
                        </div>
                        <div class="col-lg-4 col-sm-6 marketing-4 research-4">
                            <div class="single-portfolio">
                                <div class="portfolio-image">
                                    <img src="{{ URL::asset('assets/landing-page/img/blog/2.jpg') }}" alt="blog image"
                                        width="277" height="277">
                                    <div class="portfolio-overlay d-flex align-items-center justify-content-center">
                                        <div class="portfolio-content">
                                            <div class="portfolio-icon">
                                                <a class="image-popup" href="assets/images/portfolio/2.png"><i
                                                        class="lni lni-zoom-in"></i></a>
                                                <img src="assets/images/portfolio/shape.svg" alt="shape" class="shape">
                                            </div>
                                            <div class="portfolio-icon">
                                                <a href="#"><i class="lni lni-link"></i></a>
                                                <img src="assets/images/portfolio/shape.svg" alt="shape" class="shape">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- single portfolio -->
                        </div>
                        <div class="col-lg-4 col-sm-6 branding-4 marketing-4">
                            <div class="single-portfolio">
                                <div class="portfolio-image">
                                    <img src="{{ URL::asset('assets/landing-page/img/blog/3.jpeg') }}" alt="blog image"
                                        width="277" height="277">
                                    <div class="portfolio-overlay d-flex align-items-center justify-content-center">
                                        <div class="portfolio-content">
                                            <div class="portfolio-icon">
                                                <a class="image-popup" href="assets/images/portfolio/3.png"><i
                                                        class="lni lni-zoom-in"></i></a>
                                                <img src="assets/images/portfolio/shape.svg" alt="shape" class="shape">
                                            </div>
                                            <div class="portfolio-icon">
                                                <a href="#"><i class="lni lni-link"></i></a>
                                                <img src="assets/images/portfolio/shape.svg" alt="shape" class="shape">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- single portfolio -->
                        </div>
                        <div class="col-lg-4 col-sm-6 planning-4 research-4">
                            <div class="single-portfolio">
                                <div class="portfolio-image">
                                    <img src="{{ URL::asset('assets/landing-page/img/blog/4.jpg') }}" alt="blog image"
                                        width="277" height="277">
                                    <div class="portfolio-overlay d-flex align-items-center justify-content-center">
                                        <div class="portfolio-content">
                                            <div class="portfolio-icon">
                                                <a class="image-popup" href="assets/images/portfolio/4.png"><i
                                                        class="lni lni-zoom-in"></i></a>
                                                <img src="assets/images/portfolio/shape.svg" alt="shape" class="shape">
                                            </div>
                                            <div class="portfolio-icon">
                                                <a href="#"><i class="lni lni-link"></i></a>
                                                <img src="assets/images/portfolio/shape.svg" alt="shape" class="shape">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- single portfolio -->
                        </div>
                        <div class="col-lg-4 col-sm-6 marketing-4">
                            <div class="single-portfolio">
                                <div class="portfolio-image">
                                    <img src="{{ URL::asset('assets/landing-page/img/blog/5.jpg') }}" alt="blog image"
                                        width="277" height="277">
                                    <div class="portfolio-overlay d-flex align-items-center justify-content-center">
                                        <div class="portfolio-content">
                                            <div class="portfolio-icon">
                                                <a class="image-popup" href="assets/images/portfolio/5.png"><i
                                                        class="lni lni-zoom-in"></i></a>
                                                <img src="assets/images/portfolio/shape.svg" alt="shape" class="shape">
                                            </div>
                                            <div class="portfolio-icon">
                                                <a href="#"><i class="lni lni-link"></i></a>
                                                <img src="assets/images/portfolio/shape.svg" alt="shape" class="shape">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- single portfolio -->
                        </div>
                        <div class="col-lg-4 col-sm-6 planning-4">
                            <div class="single-portfolio">
                                <div class="portfolio-image">
                                    <img src="{{ URL::asset('assets/landing-page/img/blog/2.jpg') }}" alt="blog image"
                                        width="277" height="277">
                                    <div class="portfolio-overlay d-flex align-items-center justify-content-center">
                                        <div class="portfolio-content">
                                            <div class="portfolio-icon">
                                                <a class="image-popup" href="assets/images/portfolio/6.png"><i
                                                        class="lni lni-zoom-in"></i></a>
                                                <img src="assets/images/portfolio/shape.svg" alt="shape" class="shape">
                                            </div>
                                            <div class="portfolio-icon">
                                                <a href="#"><i class="lni lni-link"></i></a>
                                                <img src="assets/images/portfolio/shape.svg" alt="shape" class="shape">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- single portfolio -->
                        </div>


                        <!-- portfolio menu -->
                        {{-- <div class="col-lg-4 col-sm-6 research-4">
                            <div class="single-portfolio">
                                <div class="portfolio-image">
                                    <img src="{{ URL::asset('assets/images/portfolio/7.png') }}" alt="">
                        <div class="portfolio-overlay d-flex align-items-center justify-content-center">
                            <div class="portfolio-content">
                                <div class="portfolio-icon">
                                    <a class="image-popup" href="assets/images/portfolio/7.png"><i
                                            class="lni lni-zoom-in"></i></a>
                                    <img src="assets/images/portfolio/shape.svg" alt="shape" class="shape">
                                </div>
                                <div class="portfolio-icon">
                                    <a href="#"><i class="lni lni-link"></i></a>
                                    <img src="assets/images/portfolio/shape.svg" alt="shape" class="shape">
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- single portfolio -->
            </div>
            <div class="col-lg-4 col-sm-6 branding-4 planning-4">
                <div class="single-portfolio">
                    <div class="portfolio-image">
                        <img src="{{ URL::asset('assets/images/portfolio/8.png') }}" alt="">
                        <div class="portfolio-overlay d-flex align-items-center justify-content-center">
                            <div class="portfolio-content">
                                <div class="portfolio-icon">
                                    <a class="image-popup" href="assets/images/portfolio/8.png"><i
                                            class="lni lni-zoom-in"></i></a>
                                    <img src="assets/images/portfolio/shape.svg" alt="shape" class="shape">
                                </div>
                                <div class="portfolio-icon">
                                    <a href="#"><i class="lni lni-link"></i></a>
                                    <img src="assets/images/portfolio/shape.svg" alt="shape" class="shape">
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- single portfolio -->
            </div>
            <div class="col-lg-4 col-sm-6 marketing-4">
                <div class="single-portfolio">
                    <div class="portfolio-image">
                        <img src="{{ URL::asset('assets/images/portfolio/9.png') }}" alt="">
                        <div class="portfolio-overlay d-flex align-items-center justify-content-center">
                            <div class="portfolio-content">
                                <div class="portfolio-icon">
                                    <a class="image-popup" href="assets/images/portfolio/9.png"><i
                                            class="lni lni-zoom-in"></i></a>
                                    <img src="assets/images/portfolio/shape.svg" alt="shape" class="shape">
                                </div>
                                <div class="portfolio-icon">
                                    <a href="#"><i class="lni lni-link"></i></a>
                                    <img src="assets/images/portfolio/shape.svg" alt="shape" class="shape">
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- single portfolio -->
            </div> --}}
        </div> <!-- row -->
        </div>
        </div> <!-- row -->
        </div>
    </section>
    <!-- video area end -->

    <!-- counterup area start -->
    <section class="counterup-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="single-counter-item">
                        <!-- single counter item -->
                        <div class="icon">
                            <i class="fa fa-users" aria-hidden="true"></i>
                        </div>
                        <div class="content">
                            <span class="count-num">14,567</span>
                            <h4 class="title">Jumlah Organisasi</h4>
                        </div>
                    </div><!-- //. single counter item -->
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="single-counter-item">
                        <!-- single counter item -->
                        <div class="icon">
                            <i class="fas fa-donate"></i>
                        </div>
                        <div class="content">
                            <span class="count-num">567</span>
                            <h4 class="title">Derma Terkumpul</h4>
                        </div>
                    </div><!-- //. single counter item -->
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="single-counter-item">
                        <!-- single counter item -->
                        <div class="icon">
                            <i class="flaticon-email"></i>
                        </div>
                        <div class="content">
                            <span class="count-num">36,778</span>
                            <h4 class="title">App Downloads</h4>
                        </div>
                    </div><!-- //. single counter item -->
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="single-counter-item">
                        <!-- single counter item -->
                        <div class="icon">
                            <i class="flaticon-trophy"></i>
                        </div>
                        <div class="content">
                            <span class="count-num">30</span>
                            <h4 class="title">Best Awards</h4>
                        </div>
                    </div><!-- //. single counter item -->
                </div>
            </div>
        </div>
    </section>
    <!-- counterup area end -->

    <!-- why choose area start -->
    <section class="why-choose-area why-choose-us-bg">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="section-title white">
                        <!-- section title -->
                        {{-- <span class="subtitle">Modul</span> --}}
                        <h3 class="title extra">Modul</h3>
                        <p>Consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolor tempor
                            incididunt ut labore dolore magna.</p>
                    </div><!-- //. section title -->
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-12">
                    <div class="single-why-us-item margin-top-60 wow zoomIn">
                        <!-- single why us item -->
                        <div class="icon gdbg-1">
                            <i class="flaticon-settings-1"></i>
                        </div>
                        <div class="content">
                            <h4 class="title">Easy Customize</h4>
                            <p>Consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore</p>
                        </div>
                    </div><!-- //. single why us item -->
                    <div class="single-why-us-item wow zoomIn">
                        <!-- single why us item -->
                        <div class="icon gdbg-2">
                            <i class="flaticon-checked"></i>
                        </div>
                        <div class="content">
                            <h4 class="title">Fast & Secure</h4>
                            <p>Consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore</p>
                        </div>
                    </div><!-- //. single why us item -->
                </div>
                <div class="col-lg-4 col-md-12">
                    <div class="single-why-us-item margin-top-60 wow zoomIn">
                        <!-- single why us item -->
                        <div class="icon gdbg-3">
                            <i class="flaticon-chat-1"></i>
                        </div>
                        <div class="content">
                            <h4 class="title">Live Chat</h4>
                            <p>Consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore</p>
                        </div>
                    </div><!-- //. single why us item -->
                    <div class="single-why-us-item wow zoomIn">
                        <!-- single why us item -->
                        <div class="icon gdbg-4">
                            <i class="flaticon-cloud"></i>
                        </div>
                        <div class="content">
                            <h4 class="title">Secure Data</h4>
                            <p>Consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore</p>
                        </div>
                    </div><!-- //. single why us item -->
                </div>
            </div>
        </div>
    </section>
    <!-- why choose area end -->

    <!-- how it works area start -->
    <section class="how-it-work-area">
        <div class="shape-1"><img src="{{ URL::asset('assets/landing-page/img/shape/08.png') }}" alt=""></div>
        <div class="shape-2"><img src="{{ URL::asset('assets/landing-page/img/shape/09.png') }}" alt=""></div>
        <div class="shape-3"><img src="{{ URL::asset('assets/landing-page/img/shape/08.png') }}" alt=""></div>
        <div class="shape-4"><img src="{{ URL::asset('assets/landing-page/img/shape/09.png') }}" alt=""></div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="section-title">
                        <!-- section title -->
                        {{-- <span class="subtitle">Working Process</span> --}}
                        <h3 class="title">Proses Sistem</h3>
                        <p>Consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolor tempor
                            incididunt ut labore dolore magna.</p>
                    </div><!-- //. section title -->
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="how-it-work-tab-nav">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="account-tab" data-toggle="tab" href="#account" role="tab"
                                    aria-controls="account" aria-selected="true"><i class="flaticon-checked"></i> Daftar
                                    Akaun <span class="number">1</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="settings-tab" data-toggle="tab" href="#settings" role="tab"
                                    aria-controls="settings" aria-selected="false"><i class="flaticon-settings-1"></i>
                                    Log Masuk <span class="number">2</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="chat-tab" data-toggle="tab" href="#chat" role="tab"
                                    aria-controls="chat" aria-selected="false"><i class="flaticon-chat-1"></i> Mula
                                    Gunakan Sistem <span class="number">3</span></a>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-content wow slideInUp">
                        <div class="tab-pane fade show active" id="account" role="tabpanel"
                            aria-labelledby="account-tab">
                            <div class="how-it-works-tab-content">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="left-content-area">
                                            <h4 class="title">Daftar </h4>
                                            <p>Innovative solutions with the best. Incididunt dolor sit amet,
                                                consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore
                                                et dolor tempor incididunt ut labore et dolore </p>
                                            <p>Adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolor
                                                tempor incididunt ut labore et dolore
                                                Innovative solutions with the best. Incididunt dolor sit amet,
                                                consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore
                                                et dolor tempor incididunt ut labore et dolore </p>
                                            <p>Adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolor
                                                tempor incididunt ut labore et dolore
                                                Innovative solutions with the best. Incididunt dolor sit amet,
                                                consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore
                                                et dolor tempor incididunt ut labore et dolore </p>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="right-content-area">
                                            <div class="img-wrapper">
                                                <img src="{{ URL::asset('assets/landing-page/img/how-it-works-image.png') }}"
                                                    alt="how it works image">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="settings" role="tabpanel" aria-labelledby="settings-tab">
                            <div class="how-it-works-tab-content">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="left-content-area">
                                            <h4 class="title">Login Account</h4>
                                            <p>Innovative solutions with the best. Incididunt dolor sit amet,
                                                consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore
                                                et dolor tempor incididunt ut labore et dolore </p>
                                            <p>Adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolor
                                                tempor incididunt ut labore et dolore
                                                Innovative solutions with the best. Incididunt dolor sit amet,
                                                consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore
                                                et dolor tempor incididunt ut labore et dolore </p>
                                            <p>Adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolor
                                                tempor incididunt ut labore et dolore
                                                Innovative solutions with the best. Incididunt dolor sit amet,
                                                consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore
                                                et dolor tempor incididunt ut labore et dolore </p>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="right-content-area">
                                            <div class="img-wrapper">
                                                <img src="{{ URL::asset('assets/landing-page/img/how-it-works-image.png') }}"
                                                    alt="how it works image">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="chat" role="tabpanel" aria-labelledby="chat-tab">
                            <div class="how-it-works-tab-content">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="left-content-area">
                                            <h4 class="title">Login Account</h4>
                                            <p>Innovative solutions with the best. Incididunt dolor sit amet,
                                                consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore
                                                et dolor tempor incididunt ut labore et dolore </p>
                                            <p>Adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolor
                                                tempor incididunt ut labore et dolore
                                                Innovative solutions with the best. Incididunt dolor sit amet,
                                                consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore
                                                et dolor tempor incididunt ut labore et dolore </p>
                                            <p>Adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolor
                                                tempor incididunt ut labore et dolore
                                                Innovative solutions with the best. Incididunt dolor sit amet,
                                                consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore
                                                et dolor tempor incididunt ut labore et dolore </p>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="right-content-area">
                                            <div class="img-wrapper">
                                                <img src="{{ URL::asset('assets/landing-page/img/how-it-works-image.png') }}"
                                                    alt="how it works image">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- how it works area end -->

    <!-- screenshort area start -->
    <section class="screenshort-area">
        <div class="shape-1"><img src="{{ URL::asset('assets/landing-page/img/shape/08.png') }}" alt=""></div>
        <div class="shape-2"><img src="{{ URL::asset('assets/landing-page/img/shape/09.png') }}" alt=""></div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="section-title">
                        <!-- section title -->
                        <span class="subtitle">Screenshots</span>
                        <h3 class="title extra">Amazing visual interface</h3>
                        <p>Consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolor tempor
                            incididunt ut labore dolore magna.</p>
                    </div><!-- //. section title -->
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="screenshort-carousel">
                        <!-- screenshort carousel -->
                        <div class="single-screenshort-item">
                            <!-- single screenshort item -->
                            <img src="{{ URL::asset('assets/landing-page/img/screenshort/screen-1.jpg') }}" alt="">
                        </div><!-- //.single screenshort item -->
                        <div class="single-screenshort-item">
                            <!-- single screenshort item -->
                            <img src="{{ URL::asset('assets/landing-page/img/screenshort/screen-2.jpg') }}" alt="">
                        </div><!-- //.single screenshort item -->
                        <div class="single-screenshort-item">
                            <!-- single screenshort item -->
                            <img src="{{ URL::asset('assets/landing-page/img/screenshort/screen-3.jpg') }}" alt="">
                        </div><!-- //.single screenshort item -->
                        <div class="single-screenshort-item">
                            <!-- single screenshort item -->
                            <img src="{{ URL::asset('assets/landing-page/img/screenshort/screen-4.jpg') }}" alt="">
                        </div><!-- //.single screenshort item -->
                    </div><!-- //. screenshort carousel -->
                </div>
            </div>
        </div>
    </section>
    <!-- screenshort area end -->

    <!-- testimonial area start -->
    {{-- <section class="testimonial-area">
            <div class="shape-1"><img src="{{ URL::asset('assets/landing-page/img/shape/08.png') }}" alt=""></div>
    <div class="shape-2"><img src="{{ URL::asset('assets/landing-page/img/shape/09.png') }}" alt=""></div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="section-title ">
                    <!-- section title -->
                    <span class="subtitle">Testimonial</span>
                    <h3 class="title extra">Testimonial</h3>
                    <p>Apa kata pengguna-pengguna kami</p>
                </div><!-- //. section title -->
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="testimonial-carousel">

                    <div class="single-testimonial-item">
                        <!-- single testimonial item -->
                        <img src="{{ URL::asset('assets/landing-page/img/testimonial/01.jpg') }}" alt="">
                        <div class="hover">
                            <!-- hover -->
                            <div class="hover-inner">
                                <div class="icon"><i class="fas fa-quote-left"></i></div>
                                <p>They provide innovative solutions with the best. tempor incididunt utla bore et
                                    dolor tempor incididunt .</p>
                                <div class="author-meta">
                                    <h4 class="name">Riley Cassidy</h4>
                                    <span class="post">Chief executive</span>
                                </div>
                            </div>
                        </div><!-- //. hover -->
                    </div><!-- //. single testimonial item -->
                    <div class="single-testimonial-item">
                        <!-- single testimonial item -->
                        <img src="{{ URL::asset('assets/landing-page/img/testimonial/02.jpg') }}" alt="">
                        <div class="hover">
                            <!-- hover -->
                            <div class="hover-inner">
                                <div class="icon"><i class="fas fa-quote-left"></i></div>
                                <p>They provide innovative solutions with the best. tempor incididunt utla bore et
                                    dolor tempor incididunt .</p>
                                <div class="author-meta">
                                    <h4 class="name">Archie Tracey</h4>
                                    <span class="post">Technician</span>
                                </div>
                            </div>
                        </div><!-- //. hover -->
                    </div><!-- //. single testimonial item -->
                    <div class="single-testimonial-item">
                        <!-- single testimonial item -->
                        <img src="{{ URL::asset('assets/landing-page/img/testimonial/03.jpg') }}" alt="">
                        <div class="hover">
                            <!-- hover -->
                            <div class="hover-inner">
                                <div class="icon"><i class="fas fa-quote-left"></i></div>
                                <p>They provide innovative solutions with the best. tempor incididunt utla bore et
                                    dolor tempor incididunt .</p>
                                <div class="author-meta">
                                    <h4 class="name">Brodie Hopley</h4>
                                    <span class="post">Chief Elevator</span>
                                </div>
                            </div>
                        </div><!-- //. hover -->
                    </div><!-- //. single testimonial item -->

                </div>
            </div>
        </div>
    </div>
    </section> --}}
    <!-- testimonial area end -->

    <!-- price plan area start -->
    <section class="pricing-plan-area pricing-plan-bg" id="pricing">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="section-title white">
                        <!-- section title -->
                        {{-- <span class="subtitle">Pricing plans</span> --}}
                        <h3 class="title extra">Testimonial</h3>
                        <p>Apa kata pengguna-pengguna kami....</p>
                    </div><!-- //. section title -->
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="single-testimonial">
                        <div class="testimonial-text">
                            <p class="text">“Praesent scelerisque, odio eu fermentum malesuada, nisi arcu
                                volutpat nisl, sit amet convallis nunc turp.”</p>
                        </div>
                        <div class="testimonial-author d-sm-flex justify-content-between">
                            <div class="author-info d-flex align-items-center">
                                <div class="author-image">
                                    <img src="{{ URL::asset('assets/images/testimonial/author-1.jpg') }}"
                                        alt="author">
                                </div>
                                <div class="author-name media-body">
                                    <h5 class="name">Mr. Jems Bond</h5>
                                    <span class="sub-title">CEO Mbuild Firm</span>
                                </div>
                            </div>
                            <div class="author-review">
                                <ul class="star">
                                    <li><i class="lni lni-star-filled"></i></li>
                                    <li><i class="lni lni-star-filled"></i></li>
                                    <li><i class="lni lni-star-filled"></i></li>
                                    <li><i class="lni lni-star-filled"></i></li>
                                    <li><i class="lni lni-star-filled"></i></li>
                                </ul>
                                <span class="review">( 7 Reviews )</span>
                            </div>
                        </div>
                    </div> <!-- single testimonial -->
                </div>
                <div class="col-lg-6">
                
                    <div class="single-testimonial">
                        <div class="testimonial-text">
                            <p class="text">“Praesent scelerisque, odio eu fermentum malesuada, nisi arcu
                                volutpat nisl, sit amet convallis nunc turp.”</p>
                        </div>
                        <div class="testimonial-author d-sm-flex justify-content-between">
                            <div class="author-info d-flex align-items-center">
                                <div class="author-image">
                                    <img src="{{ URL::asset('assets/images/testimonial/author-3.jpg') }}"
                                        alt="author">
                                </div>
                                <div class="author-name media-body">
                                    <h5 class="name">Mr. Jems Bond</h5>
                                    <span class="sub-title">CEO Mbuild Firm</span>
                                </div>
                            </div>
                            <div class="author-review">
                                <ul class="star">
                                    <li><i class="lni lni-star-filled"></i></li>
                                    <li><i class="lni lni-star-filled"></i></li>
                                    <li><i class="lni lni-star-filled"></i></li>
                                    <li><i class="lni lni-star-filled"></i></li>
                                    <li><i class="lni lni-star-filled"></i></li>
                                </ul>
                                <span class="review">( 7 Reviews )</span>
                            </div>
                        </div>
                    </div> <!-- single testimonial -->
                </div>
            </div>
        </div>
    </section>
    <!-- price plan area end -->
    <!--====== TESTIMONIAL PART START ======-->
    

    {{-- <section id="testimonial" class="testimonial-area">
        <div class="container">
            
            <div class="row">
                <div class="col-lg-6">
                    <div class="contact-area-wrapper" id="contact">
                        <!-- contact area wrapper -->
                        <span class="subtitle">Contact us</span>
                        <h3 class="title">Testimonial</h3>
                        <p>Apa kata pelanggan kami...</p>
                       
                    </div><!-- //. contact area wrapper -->
                </div>
                <div class="col-lg-6">
                    <div class="testimonial-right-content mt-50">
                        <div class="quota">
                            <i class="lni lni-quotation"></i>
                        </div>
                        <div class="testimonial-content-wrapper testimonial-active">
                            <div class="single-testimonial">
                                <div class="testimonial-text">
                                    <p class="text">“Praesent scelerisque, odio eu fermentum malesuada, nisi arcu
                                        volutpat nisl, sit amet convallis nunc turp.”</p>
                                </div>
                                <div class="testimonial-author d-sm-flex justify-content-between">
                                    <div class="author-info d-flex align-items-center">
                                        <div class="author-image">
                                            <img src="{{ URL::asset('assets/images/testimonial/author-1.jpg') }}"
                                                alt="author">
                                        </div>
                                        <div class="author-name media-body">
                                            <h5 class="name">Mr. Jems Bond</h5>
                                            <span class="sub-title">CEO Mbuild Firm</span>
                                        </div>
                                    </div>
                                    <div class="author-review">
                                        <ul class="star">
                                            <li><i class="lni lni-star-filled"></i></li>
                                            <li><i class="lni lni-star-filled"></i></li>
                                            <li><i class="lni lni-star-filled"></i></li>
                                            <li><i class="lni lni-star-filled"></i></li>
                                            <li><i class="lni lni-star-filled"></i></li>
                                        </ul>
                                        <span class="review">( 7 Reviews )</span>
                                    </div>
                                </div>
                            </div> <!-- single testimonial -->
                            <div class="single-testimonial">
                                <div class="testimonial-text">
                                    <p class="text">“Praesent scelerisque, odio eu fermentum malesuada, nisi arcu
                                        volutpat nisl, sit amet convallis nunc turp.”</p>
                                </div>
                                <div class="testimonial-author d-sm-flex justify-content-between">
                                    <div class="author-info d-flex align-items-center">
                                        <div class="author-image">
                                            <img src="{{ URL::asset('assets/images/testimonial/author-2.jpg') }}"
                                                alt="author">
                                        </div>
                                        <div class="author-name media-body">
                                            <h5 class="name">Mr. Jems Bond</h5>
                                            <span class="sub-title">CEO Mbuild Firm</span>
                                        </div>
                                    </div>
                                    <div class="author-review">
                                        <ul class="star">
                                            <li><i class="lni lni-star-filled"></i></li>
                                            <li><i class="lni lni-star-filled"></i></li>
                                            <li><i class="lni lni-star-filled"></i></li>
                                            <li><i class="lni lni-star-filled"></i></li>
                                            <li><i class="lni lni-star-filled"></i></li>
                                        </ul>
                                        <span class="review">( 7 Reviews )</span>
                                    </div>
                                </div>
                            </div> <!-- single testimonial -->
                            <div class="single-testimonial">
                                <div class="testimonial-text">
                                    <p class="text">“Praesent scelerisque, odio eu fermentum malesuada, nisi arcu
                                        volutpat nisl, sit amet convallis nunc turp.”</p>
                                </div>
                                <div class="testimonial-author d-sm-flex justify-content-between">
                                    <div class="author-info d-flex align-items-center">
                                        <div class="author-image">
                                            <img src="{{ URL::asset('assets/images/testimonial/author-3.jpg') }}"
                                                alt="author">
                                        </div>
                                        <div class="author-name media-body">
                                            <h5 class="name">Mr. Jems Bond</h5>
                                            <span class="sub-title">CEO Mbuild Firm</span>
                                        </div>
                                    </div>
                                    <div class="author-review">
                                        <ul class="star">
                                            <li><i class="lni lni-star-filled"></i></li>
                                            <li><i class="lni lni-star-filled"></i></li>
                                            <li><i class="lni lni-star-filled"></i></li>
                                            <li><i class="lni lni-star-filled"></i></li>
                                            <li><i class="lni lni-star-filled"></i></li>
                                        </ul>
                                        <span class="review">( 7 Reviews )</span>
                                    </div>
                                </div>
                            </div> <!-- single testimonial -->
                        </div> <!-- testimonial content wrapper -->
                    </div> <!-- testimonial right content -->
                </div>
            </div> <!-- row -->
        </div> <!-- container -->
    </section> --}}

    <!--====== TESTIMONIAL PART ENDS ======-->

    <!-- team member area start -->
    <section class="team-member-area" id="team">
        <div class="bg-shape-1">
            <img src="{{ URL::asset('assets/landing-page/img/bg/team-shape.png') }}" alt="">
        </div>
        <div class="bg-shape-2">
            <img src="{{ URL::asset('assets/landing-page/img/bg/contact-map-bg.jpg') }}" alt="">
        </div>
        <div class="bg-shape-3">
            <img src="{{ URL::asset('assets/landing-page/img/bg/contact-mobile-bg.png') }}" alt="">
        </div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="section-title">
                        <!-- section title -->
                        <span class="subtitle">Our Team</span>
                        <h3 class="title">Meet The Team</h3>
                        <p>Consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolor tempor
                            incididunt ut labore dolore magna.</p>
                    </div><!-- //. section title -->
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="team-carousel">
                        <!-- team carousel -->
                        <div class="single-team-member">
                            <!-- single team member -->
                            <div class="thumb">
                                <img src="{{ URL::asset('assets/landing-page/img/team-member/01.jpg') }}"
                                    alt="team member image">
                                <div class="hover">
                                    <ul class="social-icon">
                                        <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                        <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                        <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="content">
                                <h4 class="title">Eiusmod Tempor</h4>
                                <span class="post">CEO, Appside</span>
                            </div>
                        </div><!-- //. single team member -->
                        <div class="single-team-member">
                            <!-- single team member -->
                            <div class="thumb">
                                <img src="{{ URL::asset('assets/landing-page/img/team-member/02.jpg') }}"
                                    alt="team member image">
                                <div class="hover">
                                    <ul class="social-icon">
                                        <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                        <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                        <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="content">
                                <h4 class="title">Maria Hexa</h4>
                                <span class="post">CEO, Appside</span>
                            </div>
                        </div><!-- //. single team member -->
                        <div class="single-team-member">
                            <!-- single team member -->
                            <div class="thumb">
                                <img src="{{ URL::asset('assets/landing-page/img/team-member/03.jpg') }}"
                                    alt="team member image">
                                <div class="hover">
                                    <ul class="social-icon">
                                        <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                        <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                        <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="content">
                                <h4 class="title">Scotty Hedge</h4>
                                <span class="post">Creative Designer</span>
                            </div>
                        </div><!-- //. single team member -->
                        <div class="single-team-member">
                            <!-- single team member -->
                            <div class="thumb">
                                <img src="{{ URL::asset('assets/landing-page/img/team-member/04.jpg') }}"
                                    alt="team member image">
                                <div class="hover">
                                    <ul class="social-icon">
                                        <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                        <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                        <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="content">
                                <h4 class="title">Lara Croft</h4>
                                <span class="post">CEO, Appside</span>
                            </div>
                        </div><!-- //. single team member -->
                        <div class="single-team-member">
                            <!-- single team member -->
                            <div class="thumb">
                                <img src="{{ URL::asset('assets/landing-page/img/team-member/05.jpg') }}"
                                    alt="team member image">
                                <div class="hover">
                                    <ul class="social-icon">
                                        <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                        <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                        <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="content">
                                <h4 class="title">Eiusmoy Smith</h4>
                                <span class="post">Developer</span>
                            </div>
                        </div><!-- //. single team member -->
                    </div><!-- //. team carousel -->
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="download-area-wrapper margin-top-120">
                        <!-- download area wrapper -->
                        <span class="subtitle">Download now</span>
                        <h3 class="title">Available for all device</h3>
                        <p>Consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolor tempor
                            incididunt ut labore dolore magna.</p>
                        <div class="btn-wrapper">
                            <a href="#" class="boxed-btn btn-rounded gd-bg-1"><i class="flaticon-apple-1"></i> App
                                Store</a>
                            <a href="#" class="boxed-btn btn-rounded gd-bg-2"><i class="flaticon-android-logo"></i> Play
                                Store</a>
                            <a href="#" class="boxed-btn btn-rounded gd-bg-3"><i class="flaticon-windows"></i>
                                Windows</a>
                        </div>
                    </div><!-- //. download area wrapper -->
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="contact-area-wrapper" id="contact">
                        <!-- contact area wrapper -->
                        {{-- <span class="subtitle">Contact us</span> --}}
                        <h3 class="title">Hubungi Kami</h3>
                        <p>Untuk sebarang pertanyaan dan pendaftaran, sila hubungi kami.</p>
                        <form action="index.html" id="contact_form_submit" class="contact-form sec-margin">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="uname" placeholder="Your Name">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="email" placeholder="Your Email">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group textarea">
                                        <textarea name="message" id="message" class="form-control" cols="30" rows="10"
                                            placeholder="Message"></textarea>
                                    </div>
                                    <button class="submit-btn  btn-rounded gd-bg-1" type="submit">Submit Now</button>
                                </div>
                            </div>
                        </form>
                    </div><!-- //. contact area wrapper -->
                </div>
            </div>
        </div>
    </section>
    <!-- team member area end -->

    <!-- footer area start -->
    <footer class="footer-area">
        <div class="footer-top">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-widget about_widget">
                            <a href="index.html" class="footer-logo"><img
                                    src="{{ URL::asset('assets/landing-page/img/logo-white.png') }}" alt=""></a>
                            <p>Within coming figure sex things are. Pretended concluded did repulsive education
                                smallness yet yet described. Had country man his pressed shewing. </p>
                            <ul class="social-icon">
                                <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                                <li><a href="#"><i class="fab fa-pinterest-p"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-widget nav_menus_widget">
                            <h4 class="widget-title">Useful Links</h4>
                            <ul>
                                <li><a href="index.html"><i class="fas fa-chevron-right"></i> Home</a></li>
                                <li><a href="#"><i class="fas fa-chevron-right"></i> About Us</a></li>
                                <li><a href="#"><i class="fas fa-chevron-right"></i> Service</a></li>
                                <li><a href="#"><i class="fas fa-chevron-right"></i> Blog</a></li>
                                <li><a href="#"><i class="fas fa-chevron-right"></i> Contact</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-widget nav_menus_widget">
                            <h4 class="widget-title">Need Help?</h4>
                            <ul>
                                <li><a href="#"><i class="fas fa-chevron-right"></i> Faqs</a></li>
                                <li><a href="#"><i class="fas fa-chevron-right"></i> Privacy</a></li>
                                <li><a href="#"><i class="fas fa-chevron-right"></i> Policy</a></li>
                                <li><a href="#"><i class="fas fa-chevron-right"></i> Support</a></li>
                                <li><a href="#"><i class="fas fa-chevron-right"></i> Temrs</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-widget nav_menus_widget">
                            <h4 class="widget-title">Download</h4>
                            <ul>
                                <li><a href="#"><i class="fas fa-chevron-right"></i> For IOS</a></li>
                                <li><a href="#"><i class="fas fa-chevron-right"></i> For Android</a></li>
                                <li><a href="#"><i class="fas fa-chevron-right"></i> For Mac</a></li>
                                <li><a href="#"><i class="fas fa-chevron-right"></i> For Window</a></li>
                                <li><a href="#"><i class="fas fa-chevron-right"></i> For Linax</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="copyright-area">
            <!-- copyright area -->
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="copyright-inner">
                            <!-- copyright inner wrapper -->
                            <div class="left-content-area">
                                <!-- left content area -->
                                &copy; Copyrights 2019 Appside All rights reserved.
                            </div><!-- //. left content aera -->
                            <div class="right-content-area">
                                <!-- right content area -->
                                Designed by <strong>Love</strong>
                            </div><!-- //. right content area -->
                        </div><!-- //.copyright inner wrapper -->
                    </div>
                </div>
            </div>
        </div><!-- //. copyright area -->
    </footer>
    <!-- footer area end -->

    <!-- preloader area start -->
    <div class="preloader-wrapper" id="preloader">
        <div class="preloader">
            <div class="sk-circle">
                <div class="sk-circle1 sk-child"></div>
                <div class="sk-circle2 sk-child"></div>
                <div class="sk-circle3 sk-child"></div>
                <div class="sk-circle4 sk-child"></div>
                <div class="sk-circle5 sk-child"></div>
                <div class="sk-circle6 sk-child"></div>
                <div class="sk-circle7 sk-child"></div>
                <div class="sk-circle8 sk-child"></div>
                <div class="sk-circle9 sk-child"></div>
                <div class="sk-circle10 sk-child"></div>
                <div class="sk-circle11 sk-child"></div>
                <div class="sk-circle12 sk-child"></div>
            </div>
        </div>
    </div>

    <!-- preloader area end -->

    <!-- back to top area start -->
    <div class="back-to-top">
        <i class="fas fa-angle-up"></i>
    </div>
    <!-- back to top area end -->
    @include('landing-page.footer-script')
</body>

</html>