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
                        <a href="/">Utama</a>
                    </li>
                    <li class="menu-item-has-children">
                        <a href="#">Organisasi</a>
                        <ul class="sub-menu">
                            <li><a href="/organization-list">Masjid</a></li>
                            <li><a href="blog-details.html">Sekolah JAIM</a></li>
                        </ul>
                    </li>
                    {{-- <li><a href="/organization-list">Derma</a></li> --}}
                    <li class="menu-item-has-children">
                        <a href="#">Derma</a>
                        <ul class="sub-menu">
                            <li><a href="/organization-list">Derma Tahfiz UTeM</a></li>
                            {{-- <li><a href="blog-details.html">Sekolah JAIM</a></li> --}}
                        </ul>
                    </li>
                    {{-- <li><a href="#pricing">Pricing</a></li> --}}
                    {{-- <li><a href="#sekolah">Sekolah</a></li> --}}
                    {{-- <li><a href="#team">Modul</a></li> --}}

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
        <div class="header-right-image  wow zoomIn" style="text-align: right">
            <img src="{{ URL::asset('assets/landing-page/img/masjid-utem.png') }}" alt="header right image" style="padding-bottom: 358px;
            max-width: 63%;">
        </div>
        <div class="container">
            <div class="row">
                <div class="col-lg-7">
                    <div class="header-inner">
                        <h1 class="title wow fadeInDown">PRiM</h1>
                        <p>Parental Relationship Information Management (PRiM) adalah sebuah sistem untuk menghubungkan
                            ibu bapa serta penjaga dengan pihak sekolah.</p>
                        <div class="btn-wrapper wow fadeInUp">
                            <a href="/register" class="boxed-btn btn-rounded">Daftar Sekarang</a>
                            <a href="/login" class="boxed-btn btn-rounded blank">Log Masuk</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- header area end  -->

    <section class="team-member-area" id="team">
        <div class="bg-shape-1">
            <img src="{{ URL::asset('assets/landing-page/img/bg/team-shape.png') }}" alt="">
        </div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="section-title">
                        <!-- section title -->
                        {{-- <span class="subtitle">Our Team</span> --}}
                        <h3 class="title">Tentang PRiM</h3>
                        <p>Parental Relationship Information Management (PRiM) adalah sebuah sistem untuk menghubungkan
                            ibu bapa serta penjaga dengan pihak sekolah. PRiM menyediakan gerbang pembayaran yuran
                            sekolah secara dalam talian dan juga pengumuman kelas dan sekolah. Di samping itu, PRiM juga
                            menyediakan perkhidmatan lain dalam talian seperti kutipan derma bagi organisasi berdaftar.
                        </p>
                    </div><!-- //. section title -->
                </div>
            </div>

            <div class="about-us-area style-two">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="section-title left-aligned">
                                <!-- section title -->
                                {{-- <span class="subtitle">Tentang PRiM</span> --}}
                                <h3 class="title extra" style="margin-top: 24px;">Derma</h3>
                                <p>Kemudahan bersepadu yang disediakan supaya penderma boleh menderma <b>24 jam </b>
                                    sehari
                                    dalam <b> 7 hari </b> seminggu untuk organisasi yang telah berdaftar di dalam
                                    sistem
                                    PRiM.</p>
                            </div><!-- //. section title -->
                        </div>
                        <div class="col-lg-6">
                            <h3 class="title extra" style="margin-bottom: 24px; margin-top: 24px;">Syarat-syarat</h3>
                            <div class="feature-area">

                                <div class="hover-inner">
                                    <div class="single-feature-list border wow zoomIn">
                                        <div class="icon icon-bg-4">
                                            <i class="flaticon-donation"></i>
                                        </div>
                                        <div class="content">
                                            <h4 class="title" style="text-align: left;">Penderma</h4>
                                            <p style="text-align: left;"> <i class="flaticon-checked"
                                                    style="margin-right: 10px"></i> Mempunyai akaun dalam bank
                                                talian <i>(online banking)</i> dengan mana-mana bank di Malaysia.</p>
                                        </div>
                                    </div>
                                </div>
                                <br><br>
                                <div class="hover-inner">
                                    <div class="single-feature-list border wow zoomIn">
                                        <div class="icon icon-bg-2">
                                            <i class="flaticon-business-and-finance"></i>
                                        </div>
                                        <div class="content">
                                            <h4 class="title" style="text-align: left;">Penerima Derma</h4>
                                            <p style="text-align: left;"> <i class="flaticon-checked"
                                                    style="margin-right: 10px"></i> Mempunyai akaun Bank Islam.</p>
                                            <p style="text-align: left;"> <i class="flaticon-checked"
                                                    style="margin-right: 10px"></i> Mendaftar dengan Paynet melalui Bank
                                                Islam.
                                                <br> <i> <a
                                                        href="{{ URL::asset('fpx-pdf/Merchant Registration Form V2.1.pdf') }}"
                                                        download> (klik untuk muat turun borang)</a> </i> </p>
                                            <p style="text-align: left;"> <i class="flaticon-checked"
                                                    style="margin-right: 10px"></i> Mendaftar sebagai organisasi di
                                                sistem PRiM.
                                            </p>
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

    <!-- about us area start -->

    {{-- <section class="about-us-area style-two">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="section-title left-aligned">
                        <!-- section title -->
                        <h3 class="title extra">Derma</h3>
                        <p>Kemudahan bersepadu yang disediakan supaya penderma boleh menderma <b>24 jam </b> sehari
                            dalam <br> <b> 7 hari </b> seminggu untuk organisasi yang telah berdaftar di dalam sistem
                            PRIM.</p>
                    </div><!-- //. section title -->
                </div>
                <div class="col-lg-6">
                    <h3 class="title extra" style="margin-bottom: 24px;">Syarat-syarat</h3>

                    <div class="feature-area">

                        <div class="hover-inner">
                            <div class="single-feature-list border wow zoomIn">
                                <div class="icon icon-bg-4">
                                    <i class="flaticon-donation"></i>
                                </div>
                                <div class="content">
                                    <h4 class="title" style="text-align: left;">Penderma</h4>
                                    <p style="text-align: left;"> <i class="flaticon-checked"
                                            style="margin-right: 10px"></i> Mempunyai akaun dalam bank
                                        talian <i>(online banking)</i> dengan mana-mana bank di Malaysia.</p>
                                </div>
                            </div>
                        </div>
                        <br><br>
                        <div class="hover-inner">
                            <div class="single-feature-list border wow zoomIn">
                                <div class="icon icon-bg-2">
                                    <i class="flaticon-business-and-finance"></i>
                                </div>
                                <div class="content">
                                    <h4 class="title" style="text-align: left;">Penerima Derma</h4>
                                    <p style="text-align: left;"> <i class="flaticon-checked"
                                            style="margin-right: 10px"></i> Mempunyai akaun Bank Islam.</p>
                                    <p style="text-align: left;"> <i class="flaticon-checked"
                                            style="margin-right: 10px"></i> Mendaftar dengan Paynet melalui Bank Islam.
                                        <br> <i> <a
                                                href="{{ URL::asset('fpx-pdf/Merchant Registration Form V2.1.pdf') }}"
    download> (klik untuk muat turun borang)</a> </i> </p>
    <p style="text-align: left;"> <i class="flaticon-checked" style="margin-right: 10px"></i> Mendaftar sebagai
        organisasi di sistem PRIM.
    </p>
    </div>
    </div>
    </div>
    </div>
    </div>
    </div>
    </div>
    </section> --}}


    <!-- counterup area start -->
    <section class="counterup-area">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-3 col-md-6">
                    <div class="single-counter-item">
                        <!-- single counter item -->
                        <div class="icon">
                            <i class="flaticon-group-1"></i>
                        </div>
                        <div class="content">
                            <span class="count-num">{{ $organization }}</span>
                            <h4 class="title">Jumlah Organisasi</h4>
                        </div>
                    </div><!-- //. single counter item -->
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="single-counter-item">
                        <!-- single counter item -->
                        <div class="icon">
                            <i class="flaticon-transaction"></i>
                        </div>
                        <div class="content">
                            <span class="count-num">{{ $transactions }}</span>
                            <h4 class="title">Jumlah transaksi</h4>
                        </div>
                    </div><!-- //. single counter item -->
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="single-counter-item">
                        <!-- single counter item -->
                        <div class="icon">
                            <i class="flaticon-donation-1"></i>
                        </div>
                        <div class="content">
                            <span class="count-num">{{ $donation }}</span>
                            <h4 class="title">Derma berdaftar</h4>
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
                        <h3 class="title extra">Kelebihan </h3>
                        <p>Berikut antara kelebihan di dalam sistem ini.</p>
                    </div><!-- //. section title -->
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-4 col-md-12">
                    <div class="single-why-us-item margin-top-60 wow zoomIn">
                        <!-- single why us item -->
                        <div class="icon gdbg-1">
                            <i class="flaticon-tap"></i>
                        </div>
                        <div class="content">
                            <h4 class="title">Mudah dan Mesra</h4>
                            <p>Dengan hanya berkongsi <i> link </i> derma, penderma boleh terus menderma.</p>
                        </div>
                    </div><!-- //. single why us item -->
                    <div class="single-why-us-item  wow zoomIn">
                        <!-- single why us item -->
                        <div class="icon gdbg-2">
                            <i class="flaticon-checked"></i>
                        </div>
                        <div class="content">
                            <h4 class="title">Selamat <i>(Anti Scam)</i></h4>
                            <p>Organisasi yang mengutip derma adalah organisasi yang berdaftar bersama Bank Islam.</p>
                        </div>
                    </div><!-- //. single why us item -->
                </div>
                <div class="col-lg-4 col-md-12">

                    <div class="single-why-us-item margin-top-60 wow zoomIn">
                        <!-- single why us item -->
                        <div class="icon gdbg-3">
                            <i class="flaticon-fast-time"></i>
                        </div>
                        <div class="content">
                            <h4 class="title">Segera</h4>
                            <p>Tiada proses <i>settlement</i> dan derma akan terus dikreditkan ke dalam akaun bank yang
                                telah didaftarkan.</p>
                        </div>
                    </div><!-- //. single why us item -->
                    <div class="single-why-us-item wow zoomIn">
                        <!-- single why us item -->
                        <div class="icon gdbg-4">
                            <i class="flaticon-cloud"></i>
                        </div>
                        <div class="content">
                            <h4 class="title">Sistematik</h4>
                            <p>Sistem pengurusan organisasi yang sistematik dan derma boleh dibuat secara online.</p>
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
                        <h3 class="title">Organisasi</h3>
                        <p>Antara organisasi yang berdaftar dengan PRiM </p>
                    </div><!-- //. section title -->
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="how-it-work-tab-nav">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active btn-organization" id="4" data-toggle="tab" href="#masjid"
                                    role="tab" aria-controls="masjid" aria-selected="true"><i class="fas fa-mosque"></i>
                                    Masjid <span class="number">1</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link btn-organization" id="5" data-toggle="tab" href="#ngo" role="tab"
                                    aria-controls="ngo" aria-selected="false"><i class="fas fa-globe"></i>
                                    NGO <span class="number">2</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link btn-organization" id="6" data-toggle="tab" href="#anakyatim"
                                    role="tab" aria-controls="anakyatim" aria-selected="false"><i
                                        class="fas fa-child"></i>Rumah Anak Yatim <span class="number">3</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link btn-organization" id="7" data-toggle="tab" href="#tahfiz" role="tab"
                                    aria-controls="tahfiz" aria-selected="false"><i class="fas fa-quran"></i> Pusat
                                    Tahfiz <span class="number">4</span></a>
                            </li>
                            <li class="nav-item btn-organization">
                                <a class="nav-link" id="8" data-toggle="tab" href="#lain" role="tab"
                                    aria-controls="lain" aria-selected="false"><i class="fas fa-building"></i> Lain-Lain
                                    <span class="number">5</span></a>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-content wow slideInUp">
                        <div class="tab-pane fade show active" id="masjid" role="tabpanel" aria-labelledby="masjid-tab">
                            <div class="how-it-works-tab-content">
                                <div class="row">
                                    <div class="table-responsive">
                                        <table id="tableOrganization" class="table table-centered table-nowrap mb-0" id="organization">
                                            <thead >
                                                <th >Nama</th>
                                                <th >No Phone</th>
                                                <th >Email</th>
                                                <th ></th>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- modal start --}}
    <div class="modal fade modal-derma" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title mt-0" id="myLargeModalLabel">Senarai Derma</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row">
                            <div class="table-responsive">
                                <table id="tableDerma" class="table table-centered table-nowrap mb-0">
                                    <thead>
                                        <th>Nama</th>
                                        <th>Action</th>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal end -->
    <!-- how it works area end -->

    <!--====== TESTIMONIAL PART ENDS ======-->

    <!-- team member area start -->
    <section class="team-member-area" id="team">
        <div class="bg-shape-2">
            <img src="{{ URL::asset('assets/landing-page/img/bg/contact-map-bg-min.jpg') }}" alt="">
        </div>
        <div class="bg-shape-3">
            {{-- <img src="{{ URL::asset('assets/landing-page/img/bg/contact-mobile-bg.png') }}" alt=""> --}}
        </div>
        <div class="container">

            <div class="row">
                <div class="col-lg-6">
                    <div class="contact-area-wrapper" id="contact">
                        <!-- contact area wrapper -->
                        {{-- <span class="subtitle">Contact us</span> --}}
                        <h3 class="title">Hubungi Kami</h3>
                        <p>Untuk sebarang pertanyaan dan maklumbalas, sila isi form ini.</p>
                        <form method="post" action="{{ route('feedback.store') }}" class="contact-form sec-margin"
                            enctype="multipart/form-data">

                            @csrf
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="uname" name="uname"
                                            placeholder="Nama Penuh" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <input type="email" class="form-control" id="email" name="email"
                                            placeholder="Email" required>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group textarea">
                                        <textarea name="message" id="message" class="form-control" cols="30" rows="10"
                                            placeholder="Mesej" required></textarea>
                                    </div>
                                    <button class="submit-btn  btn-rounded gd-bg-1" type="submit">Hantar</button>
                                </div>
                            </div>
                        </form>
                    </div><!-- //. contact area wrapper -->
                </div>
                <div class="col-lg-6">
                    <div class="contact-area-wrapper" id="contact">
                        <div class="mapouter">
                            <div class="gmap_canvas"><iframe width="500" height="500" id="gmap_canvas"
                                    src="https://maps.google.com/maps?q=utem%20melaka&t=&z=13&ie=UTF8&iwloc=&output=embed"
                                    frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe><a
                                    href="https://123movies-to.org"></a><br>
                                <style>
                                    .mapouter {
                                        position: relative;
                                        text-align: right;
                                        height: 500px;
                                        width: 600px;
                                    }
                                </style><a href="https://www.embedgooglemap.net">html code for google maps</a>
                                <style>
                                    .gmap_canvas {
                                        overflow: hidden;
                                        background: none !important;
                                        height: 500px;
                                        width: 600px;
                                    }
                                </style>
                            </div>
                        </div>
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
                <div class="row justify-content-md-center">
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-widget about_widget">
                            <a href="index.html" class="footer-logo"><img
                                    src="{{ URL::asset('assets/landing-page/img/logo-header.png') }}" alt=""></a>
                            <p>Kemudahan bersepadu yang disediakan supaya penderma boleh menderma <b>24 jam </b> sehari
                                dalam <b> 7 hari </b> seminggu untuk organisasi yang telah berdaftar di dalam sistem
                                PRiM. </p>
                            <ul class="social-icon" style="text-align: center; ">
                                <li><a href="https://www.facebook.com/MyUTeM/"><i class="fab fa-facebook-f"></i></a>
                                </li>
                                <li><a href="https://www.instagram.com/myutem/"><i class="fab fa-instagram"></i></a>
                                </li>
                                <li><a href="https://twitter.com/myutem"><i class="fab fa-twitter"></i></a></li>
                                <li><a href="https://www.youtube.com/channel/UCmJKvkfmZf4pbXwDqo2sZZg"><i
                                            class="fab fa-youtube"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-8">
                        <div class="footer-widget about_widget">
                            <h4 class="widget-title">Hubungi</h4>

                            <p>Email : admin@prim.my </p>
                            <p>Phone : 06 - 270 1000</p>


                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-widget about_widget">
                            <h4 class="widget-title">Alamat</h4>
                            <p> Universiti Teknikal Malaysia Melaka, Jalan Hang Tuah Jaya, 76100 Durian Tunggal, Melaka
                            </p>

                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-widget about_widget">
                            <a href="index.html" class="footer-logo"><img
                                    src="{{ URL::asset('assets/landing-page/img/logo-utem-white.png') }}" alt=""></a>

                            <a href="index.html" class="footer-logo"><img
                                    src="{{ URL::asset('assets/landing-page/img/logo-ftmk.png') }}" alt=""></a>

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
                                &copy; Copyrights <span id="year"></span> All rights reserved | PRiM
                            </div><!-- //. left content aera -->
                            <div class="right-content-area">
                                <!-- right content area -->
                                {{-- Designed by <strong>Love</strong> --}}
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

    <script>
        var msg = '{{Session::get('alert ')}}';var exist = '{{Session::has('alert ')}}';
    
    if (exist) {
        Swal.fire({
            title: 'Terima Kasih',
            text: 'Kerana anda telah menghubungi kami!',
            type: 'success',
            confirmButtonColor: '#556ee6',
            cancelButtonColor: "#f46a6a"
        });
    }

    $(document).ready(function() {

        window.onload = function() {
            document.getElementById("4").click();
        };

        $(document).on('click', '.btn-organization', function() {
            var type = $(this).attr("id");

            var tableOrganization = $('#tableOrganization').DataTable({
                "ordering": true,
                "processing": true,
                "serverSide": true,
                "bDestroy": true,
                "searching": false,
                "lengthChange": false,
                "bInfo": false,
                "drawCallback": function(settings) {
                    $("#tableOrganization thead").remove();
                },
                "language": {
                    "emptyTable": "Tiada maklumat untuk dipaparkan",
                    "paginate": {
                        "next": "Seterusnya",
                        "previous": "Sebelumnya"
                    }
                },
                ajax: {
                    url: "{{ route('landingpage.donation.organization') }}",
                    type: 'GET',
                    data: {
                        type: type,
                    },
                },
                order: [
                    [1, 'asc']
                ],
                responsive: {
                    details: {
                        type: 'column'
                    }
                },
                columns: [{
                    data: "nama",
                    name: "nama"
                }, {
                    data: "telno",
                    name: "telno"
                },{
                    data: "email",
                    name: "email",
                    className: "desktop"
                },{
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false

                }, ]
            });

        });

        $(document).on('click', '.btn-donation', function() {
            var id = $(this).attr("id");

            var tableDerma = $('#tableDerma').DataTable({
                "ordering": true,
                "processing": true,
                "serverSide": true,
                "bDestroy": true,
                "searching": false,
                "lengthChange": false,
                "bInfo": false,
                "drawCallback": function(settings) {
                    $("#tableDerma thead").remove();
                },
                "language": {
                    "emptyTable": "Tiada maklumat untuk dipaparkan",
                    "paginate": {
                        "next": "Seterusnya",
                        "previous": "Sebelumnya"
                    }
                },
                ajax: {
                    url: "{{ route('landingpage.donation.donation') }}",
                    type: 'GET',
                    data: {
                        id: id,
                    },
                },
                order: [
                    [1, 'asc']
                ],
                responsive: {
                    details: {
                        type: 'column'
                    }
                },
                columns: [{
                    data: "nama",
                    name: "nama"
                }, {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false

                }, ]
            });

        });
    });
    </script>
</body>

</html>