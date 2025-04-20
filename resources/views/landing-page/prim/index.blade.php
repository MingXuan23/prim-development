<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noarchive">
    <title> PRiM </title>

    @include('landing-page.head')
    <style>
        /*whatsapp contact button*/
        #btn-whatsapp{
            position: fixed;
            right: 12px;
            bottom: 12px;
            z-index: 999
        }
        .Btn {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            width: 70px;
            height: 70px;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition-duration: 0.3s;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.199);
            background-color: #00d757;
        }

        .sign {
            width: 100%;
            transition-duration: 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sign svg {
            width: 35px;
        }

        .sign svg path {
            fill: white;
        }
        .text {
            position: absolute;
            right: 0%;
            width: 100%;
            opacity: 0;
            color: white;
            font-size: 1.1em;
            font-weight: 600;
            transition-duration: 0.3s;
        }

        .Btn:hover {
            width: 230px;
            border-radius: 40px;
            transition-duration: 0.3s;
        }

        .Btn:hover .sign {
            width: 30%;
            transition-duration: 0.3s;
            padding-left: 10px;
        }

        .Btn:hover .text {
            opacity: 1;
            width: 70%;
            transition-duration: 0.3s;
            padding-right: 10px;
        }
        .Btn:active {
            transform: translate(2px, 2px);
        }


        .map-responsive {
            overflow: hidden;
            padding-bottom: 56.25%;
            position: relative;
            height: 0;
        }

        .map-responsive iframe {
            left: 0;
            top: 0;
            height: 100%;
            width: 100%;
            position: absolute;
        }

        #headerhover {
            transform: scale(0.9);
            transition: transform 1s ease;
        }

        #headerhover:hover {
            transform: scale(1.2);
        }
        .single-feature-list{
            background-image: -webkit-linear-gradient(50deg,#5e2ced 0,#9749f8 100%)!important;
            color: white!important;
        }
        .form-control{
            border-color:#5e5e5e!important;
            transition: all 0.2s ease;
        }

        /* .navbar-area .nav-container .navbar-collapse ul.navbar-nav li.current-menu-item:hover {
            transform: scale(1.0);
        }

        .navbar-area .nav-container .navbar-collapse ul.navbar-nav li:hover {
            transform: scale(1.1);
        } */

        @media only screen and (max-width: 991px){
            .navbar-area .nav-container .navbar-collapse ul.navbar-nav li:hover {
                transform: scale(1.0);
            }

            .navbar-area .nav-container .navbar-collapse ul.navbar-nav li.slash {
                display: none;
            }
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-area navbar-expand-lg nav-absolute white nav-style-01">
        <div class="container nav-container">
            <div class="responsive-mobile-menu">
                <div class="logo-wrapper">
                    <a class="navbar-brand" href="/">
                        <img src="{{ URL::asset('assets/landing-page/img/logo-header.png') }}" alt="logo">
                    </a>
                    {{-- <img src="{{ URL::asset('assets/landing-page/img/logo-header.png') }}" alt="logo"> --}}
                </div>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#appside_main_menu"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
            <div class="collapse navbar-collapse" id="appside_main_menu">
                <ul class="navbar-nav">
                    <li class="current-menu-item"><a href="#" style="font-size: 19px">Utama</a></li>
                    <li><a href="/derma">Derma</a></li>
                    <li><a href="/yuran">Yuran</a></li>
                    <li class="menu-item-has-children">
                        <a href="#">Perniagaan</a>
                        <ul class="sub-menu">
                            <li><a href="{{route('merchant-product.index')}}">Get&Go</a></li>
                            <li><a href="{{route('homestay.homePage')}}">Homestay</a></li>
                        </ul>
                    </li>
                    {{-- <li class="menu-item-has-children">
                        <a href="#">Yuran</a>
                        <ul class="sub-menu">
                            <li class="menu-item-has-children">
                                <a href="#">Sekolah</a>
                                <ul class="sub-menu">
                                    @foreach ($schools as $school)
                                        <li><a href="/{{ $school->url_name }}">{{ $school->title }}</a></li>
                                    @endforeach
                                </ul>
                            </li>
                            <li class="menu-item-has-children">
                                <a href="#">Politeknik</a>
                                <ul class="sub-menu">
                                    @foreach ($politeknik as $poli)
                                        <li><a href="/{{ $poli->url_name }}">{{ $poli->title }}</a></li>
                                    @endforeach
                                </ul>
                            </li>
                        </ul>
                    </li> --}}
                    {{-- <li><a href="/merchant/product">Get&Go</a></li> --}}
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
    <header class="header-area header-bg-2 style-two img-fluid" id="home" style="margin-bottom: 10px; background: url('{{ asset('assets/landing-page/img/bg/header-bg-2.png') }}') no-repeat; padding-top: 150px; padding-bottom: 130px;">

        <div class="container">
            <div class="row d-flex align-items-center">
                <div class="col-lg-6 justify-content-center">
                    {{-- <div id="headerPoster" class="row d-flex justify-content-center carousel owl-theme"></div> --}}
                    <div class="header-inner">
                        <h1 class="title wow fadeInDown white">PRiM</h1>
                        <p class="white" style="font-size: 20px;">Sebuah sistem yang menyediakan perkhidmatan pembayaran dalam talian untuk pelbagai organisasi
                        berdaftar. Antara perkhidmatan yang telah kami sediakan ialah Derma, Yuran, Get&Go dan sebagainya.</p>
                        <div class="btn-wrapper wow fadeInUp">
                            <a href="/register" class="boxed-btn btn-rounded">Daftar</a>
                            <a href="/login" class="boxed-btn btn-rounded blank">Log Masuk</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 align-items-center d-none d-lg-block" style="text-align: right">
                    <img src="{{ URL::asset('assets/landing-page/img/header-mockup-yuran-2.png') }}" alt="header right image" style="max-width: 110%;" id="headerhover">
                </div>
            </div>
            {{-- <div class="row justify-content-center" style="padding-top: 120px">
                <div class="col-lg-12 justify-content-center">
                    <h3 class="title wow white">Tentang PRiM</h3>
                    <p class="white" style="font-size: 20px;">Parental Relationship Information Management (PRiM) adalah sebuah sistem untuk menghubungkan
                        ibu bapa serta penjaga dengan pihak sekolah. PRiM menyediakan gerbang pembayaran yuran
                        sekolah secara dalam talian dan juga pengumuman kelas dan sekolah. Di samping itu, PRiM juga
                        menyediakan perkhidmatan lain dalam talian seperti kutipan derma bagi organisasi berdaftar.
                    </p>
                </div>
            </div> --}}
        </div>

    </header>
    <!-- header area end  -->
    <section aria-label="Click to reach out to us at WhatsApp" id="btn-whatsapp">
        <a href="https://wa.me/139017388" target="_blank" class="social-link" >
            <div class="Btn">
                <div class="sign">
                    <svg class="socialSvg whatsappSvg" viewBox="0 0 16 16">
                        <path
                            d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z"
                        ></path>
                    </svg>
                </div>
                <div class="text">Hubungi Kami</div>
            </div>
        </a>
    </section>
    {{-- about us area --}}
    <section class="about-us-area">
        {{-- <div class="bg-shape-1">
            <img src="{{ URL::asset('assets/landing-page/img/bg/team-shape.png') }}" alt="">
        </div> --}}

        <div class="container">
            <div class="row justify-content-center" style="padding-bottom: 116px">
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

            <div class="row justify-content-center" style="">
                <div class="col-lg-10">
                    <div class="section-title">
                        {{-- <span class="subtitle">Nyot</span> --}}
                        <h3 class="title">Kelebihan PRiM</h3>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="feature-area">
                        <ul class="feature-list wow fadeInUp d-flex justify-content-center" style="visibility: visible; animation-name: fadeInUp;">
                            <li class="single-feature-list col-md-9 col-lg-4">
                                <div class="icon icon-bg-2">
                                    <i class="flaticon-checked"></i>
                                </div>
                                <div class="content">
                                    <h4 class="title">
                                        <a href="#" class="text-white">Mudah</a>
                                    </h4>
                                    <p class="text-white">Memudahkan pembayaran dalam talian untuk transaksi atau pembayaran harian maupun bulanan.</p>
                                </div>
                            </li>
                            <li class="single-feature-list  col-md-9  col-lg-4">
                                <div class="icon icon-bg-2">
                                    <i class="flaticon-layers"></i>
                                </div>
                                <div class="content">
                                    <h4 class="title">
                                        <a href="#" class="text-white">Cepat</a>
                                    </h4>
                                    <p class="text-white">Kebolehan untuk melakukan transaksi secara terus kepada penerima dengan pantas.</p>
                                </div>
                            </li>
                            <li class="single-feature-list col-md-9  col-lg-4">
                                <div class="icon icon-bg-2">
                                    <i class="flaticon-shield"></i>
                                </div>
                                <div class="content">
                                    <h4 class="title">
                                        <a href="#" class="text-white">Selamat</a>
                                    </h4>
                                    <p class="text-white">Organisasi yang berdaftar adalah organisasi-organisasi yang diiktiraf oleh Bank Islam.</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </section>
    <!--====== TESTIMONIAL PART ENDS ======-->

    <!-- team member area start -->
    <section class="team-member-area" id="ourteam">

        <div class="bg-shape-3 fa-rotate-180" style="top: 0px !important;right:0px;">
            <img src="{{ URL::asset('assets/landing-page/img/bg/team-shape.png') }}" alt="" style="max-width:45%">
        </div>
        <div class="bg-shape-2">
            <img src="{{ URL::asset('assets/landing-page/img/bg/contact-map-bg-min.jpg') }}" alt="">
        </div>
        <div class="bg-shape-3">
            {{-- <img src="{{ URL::asset('assets/landing-page/img/bg/contact-mobile-bg.png') }}" alt=""> --}}
        </div>
        <div class="container">
            <div class="container">
{{--                <div class="row justify-content-center">--}}
{{--                    <div class="col-lg-10">--}}
{{--                        <div class="section-title">--}}
{{--                            <!-- section title -->--}}
{{--                            --}}{{-- <span class="subtitle">Screenshots</span> --}}
{{--                            <h3 class="title extra">Pasukan Kami</h3>--}}
{{--                        </div><!-- //. section title -->--}}
{{--                    </div>--}}
{{--                </div>--}}
            </div>
{{--            <div class="row text-center">--}}
{{--                <div class="col-lg-12 mb-200">--}}
{{--                    <div class="row justify-content-center">--}}
{{--                        <div class="col-lg-4 p-3 text-sm-center align-self-center">--}}
{{--                            <div class="p-3">--}}
{{--                                <img src="{{ URL::asset('assets/landing-page/img/team-member/CEO.png') }}" alt="" style="max-width:70%; width: 250px">--}}
{{--                            </div>--}}
{{--                            <div class="pt-3">--}}
{{--                                <h4>Yahya Bin Ibrahim</h4>--}}
{{--                                <p>Chief Executive Officer</p>--}}
{{--                            </div>--}}
{{--                        </div>--}}

{{--                        <div class="col-lg-4 p-3 text-sm-center align-self-center">--}}
{{--                            <div class="p-3">--}}
{{--                                <img src="{{ URL::asset('assets/landing-page/img/team-member/COO.png') }}" alt="" style="max-width:70%; width: 250px">--}}
{{--                            </div>--}}
{{--                            <div class="pt-3">--}}
{{--                                <h4>Ts. Dr. Muhammad Haziq Lim Bin Abdullah</h4>--}}
{{--                                <p>Chief Operating Officer</p>--}}
{{--                            </div>--}}
{{--                        </div>--}}

{{--                        <div class="col-lg-4 p-3 text-sm-center align-self-center">--}}
{{--                            <div class="p-3">--}}
{{--                                <img src="{{ URL::asset('assets/landing-page/img/team-member/CTO.png') }}" alt="" style="max-width:70%; width: 250px">--}}
{{--                            </div>--}}
{{--                            <div class="pt-3">--}}
{{--                                <h4>Chuan Chuan You</h4>--}}
{{--                                <p>Chief Technology Officer</p>--}}
{{--                            </div>--}}
{{--                        </div>--}}

{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

            <div class="container" style="margin-bottom: 10rem">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="section-title">
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
                                <img src="{{ URL::asset('assets/landing-page/img/logo-paynet.png') }}" alt=""
                                    style="max-width:70%">
                            </div>

                            <div class="col-lg-4 p-3 text-sm-center align-self-center">
                                <img src="{{ URL::asset('assets/landing-page/img/logo-bank-islam.png') }}" alt=""
                                    style="max-width:70%">

                            </div>

                            <div class="col-lg-4 p-3 text-sm-center align-self-center">
                                <img src="{{ URL::asset('assets/landing-page/img/logo-utem-blue.png') }}" alt=""
                                    style="max-width:70%">

                            </div>

                        </div>
                    </div>
                </div>
            </div>

{{--            <div class="row">--}}
{{--                <div class="col-lg-6">--}}
{{--                    <div class="contact-area-wrapper" id="contact">--}}
{{--                        <!-- contact area wrapper -->--}}
{{--                        --}}{{-- <span class="subtitle">Contact us</span> --}}
{{--                        <h3 class="title">Hubungi Kami</h3>--}}
{{--                        <p>Untuk sebarang pertanyaan dan maklumbalas, sila isi borang ini.</p>--}}
{{--                        <form method="post" action="{{ route('feedback.store') }}" class="contact-form sec-margin"--}}
{{--                            enctype="multipart/form-data">--}}

{{--                            @csrf--}}
{{--                            <div class="row">--}}
{{--                                <div class="col-lg-12">--}}
{{--                                    <div class="form-group">--}}
{{--                                        <input type="text" class="form-control" id="uname" name="uname"--}}
{{--                                            placeholder="Nama Penuh" required>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                                <div class="col-lg-6">--}}
{{--                                    <div class="form-group">--}}
{{--                                        <input type="email" class="form-control" id="email" name="email"--}}
{{--                                            placeholder="Email" required>--}}
{{--                                    </div>--}}
{{--                                </div>--}}

{{--                                <div class="col-lg-6">--}}
{{--                                    <div class="form-group">--}}
{{--                                        <input type="text" class="form-control phone_no" id="telno" name="telno"--}}
{{--                                            placeholder="Nombor Telefon" required>--}}
{{--                                    </div>--}}
{{--                                </div>--}}

{{--                                <div class="col-lg-12">--}}
{{--                                    <div class="form-group textarea">--}}
{{--                                        <textarea name="message" id="message" class="form-control" cols="30" rows="10"--}}
{{--                                            placeholder="Mesej" required></textarea>--}}
{{--                                    </div>--}}
{{--                                    <button class="submit-btn  btn-rounded gd-bg-1" type="submit">Hantar</button>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </form>--}}
{{--                    </div><!-- //. contact area wrapper -->--}}
{{--                </div>--}}
{{--                <div class="col-lg-6">--}}
{{--                    <div class="contact-area-wrapper" id="contact">--}}
{{--                        <div class="map-responsive">--}}
{{--                            <iframe--}}
{{--                                src="https://maps.google.com/maps?q=utem%20melaka&t=&z=13&ie=UTF8&iwloc=&output=embed"--}}
{{--                                width="600" height="450" frameborder="0" style="border:0;" allowfullscreen=""--}}
{{--                                aria-hidden="false" tabindex="0">--}}
{{--                            </iframe>--}}

{{--                            <br>--}}

{{--                        </div>--}}
{{--                    </div><!-- //. contact area wrapper -->--}}
{{--                </div>--}}
{{--            </div>--}}
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
                            <a href="" style="pointer-events: none;" class="footer-logo"><img
                                    src="{{ URL::asset('assets/landing-page/img/logo-header.png') }}" alt=""></a>
                            <p>Parental Relationship Information Management (PRiM) adalah sebuah sistem untuk
                                menghubungkan ibu bapa serta penjaga dengan pihak sekolah.</p>

                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-widget about_widget">
                            <h4 class="widget-title">Hubungi</h4>
                            <p>Email : yahya@utem.edu.my </p>
                            <p>Phone : 60 13-901 7388</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-widget about_widget">
                            <h4 class="widget-title">Alamat</h4>
                            <p> Universiti Teknikal Malaysia Melaka, Hang Tuah Jaya, 76100 Durian Tunggal, Melaka
                            </p>

                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-widget about_widget" style="text-align: center">
                            <a href="#" style="pointer-events: none;" class="footer-logo"><img
                                    src="{{ URL::asset('assets/landing-page/img/logo-utem-white.png') }}" alt=""
                                    style="max-width: 70%"></a>

                            <a href="#" style="pointer-events: none;" class="footer-logo"><img
                                    src="{{ URL::asset('assets/landing-page/img/logo-ftmk.png') }}" alt=""
                                    style="max-width: 70%"></a>

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
                </div>
            </div>
        </div>
        <div class="copyright-area">
            <!-- copyright area -->
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="copyright-inner text-center">
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
{{--    <div class="back-to-top">--}}
{{--        <i class="fas fa-angle-up"></i>--}}
{{--    </div>--}}
    <!-- back to top area end -->
    @include('landing-page.footer-script')

    <script>
        var msg = '{{Session::get('alert')}}';
        var exist = '{{Session::has('alert')}}';

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
            document.getElementById("8").click();
        };

        // $.ajax({
        //     url: "{{ route('landingpage.donation.header') }}",
        //     type: 'GET',
        //     success: function( result ){

        //         $('#headerPoster').html( result );
        //         $('#headerPoster').trigger('destroy.owl.carousel').removeClass('owl-carousel owl-loaded');
        //         $('#headerPoster').find('.owl-stage-outer').children().unwrap();
        //         $('#headerPoster').owlCarousel({
        //             loop:true,
        //             autoplay:true,
        //             autoplayTimeout:5000,
        //             responsiveClass:true,
        //             responsive:{
        //                 0:{
        //                     items:1,
        //                     nav:false
        //                 },
        //                 600:{
        //                     items:1,
        //                     nav:false
        //                 },
        //                 1000:{
        //                     items:1,
        //                     nav:false,
        //                 }
        //             },
        //         });
        //     }
        // });

        $('#feedback').owlCarousel({
            loop: true,
            autoplay: true, //true if you want enable autoplay
            autoPlayTimeout: 1000,
            margin: 30,
            dots: false,
            nav: true,
            smartSpeed:3000,
            animateIn:'fadeIn',
            animateOut:"fadeOut",
            navText:['',''],
            responsive: {
                0: {
                    items: 1,
                    nav: false
                },
                414: {
                    items: 1,
                    nav: false
                },
                520: {
                    items: 2,
                    nav: false
                },
                767: {
                    items: 2,
                    nav: false
                },
                768: {
                    items: 2,
                    nav: false
                },
                960: {
                    items: 3,
                    nav:false
                },
                1200: {
                    items: 4
                },
                1920: {
                    items: 4
                }
            }
        });

        $('.phone_no').mask('00000000000');

        var typedonation;
        $(document).on('click', '.btn-organization', function() {
            var type = $(this).attr("id");
            typedonation = type;
            $.ajax({
                url: "{{ route('landingpage.donation.bytabbing') }}",
                type: 'GET',
                data: {
                    type: type,
                },
                success: function( result ){

                    var posterExist = true;
                    if (result === '') {
                        result = `<div class="d-flex justify-content-center">Tiada Maklumat Dipaparkan</div>`;
                        posterExist = false;
                    }

                    $('#donationPoster').html( result );
                    $('#donationPoster').trigger('destroy.owl.carousel').removeClass('owl-carousel owl-loaded');
                    $('#donationPoster').find('.owl-stage-outer').children().unwrap();
                    $('#donationPoster').owlCarousel({
                        // loop:true,
                        dots: posterExist,
                        // paginationNumbers: false,
                        responsiveClass:true,
                        responsive:{
                            0:{
                                items:1,
                                nav:false
                            },
                            600:{
                                items:2,
                                nav:false
                            },
                            1000:{
                                items:3,
                                nav:false,
                                loop:false
                            }
                        },
                    });
                }
            });
        });
    });
    </script>
</body>
</html>
