<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noarchive">
    <title> PRiM | Derma </title>

    @include('landing-page.head')
    <style>
        /*whatsapp contact button*/
        #btn-whatsapp {
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

        .single-feature-list {
            background-image: -webkit-linear-gradient(50deg, #5e2ced 0, #9749f8 100%) !important;
            color: white !important;
        }

        .form-control {
            border-color: #5e5e5e !important;
            transition: all 0.2s ease;
        }

        /* .navbar-area .nav-container .navbar-collapse ul.navbar-nav li.current-menu-item:hover {
            transform: scale(1.0);
        }

        .navbar-area .nav-container .navbar-collapse ul.navbar-nav li:hover {
            transform: scale(1.1);
        } */
        .why-choose-area.why-choose-us-bg {
            background-image: linear-gradient(-50deg, #5e2ced, #9749f8);

        }

        .why-choose-area {
            .container {
                position: relative;

                .shape-1 {
                    position: absolute;
                    left: 5%;
                    top: 20%;
                    -webkit-animation: upndown 10s linear 2s infinite;
                    animation: upndown 10s linear 2s infinite;
                }

                .shape-2 {
                    position: absolute;
                    left: -2%;
                    top: 20%;
                    -webkit-animation: upndown 8s linear 2s infinite;
                    animation: upndown 8s linear 2s infinite;
                    opacity: .5;
                }

                .shape-3 {
                    position: absolute;
                    right: 5%;
                    bottom: 20%;
                    -webkit-animation: upndown 10s linear 2s infinite;
                    animation: upndown 10s linear 2s infinite;
                }

                .shape-4 {
                    position: absolute;
                    right: -2%;
                    bottom: 20%;
                    -webkit-animation: upndown 8s linear 2s infinite;
                    animation: upndown 8s linear 2s infinite;
                    opacity: .5;
                }
            }

        }

        section[aria-label="Donors In The Past Week"],
        section[aria-label="Top Ketua Ahli"] {
            /* background-color: #500ade; */
            padding-top: 40px;
            padding-bottom: 20px;
            /* background-image: url('assets/landing-page/img/bg/why-us-dark-bg.png'); */
            background-size: contain;
        }

        .container.container-donation {
            overflow: hidden;
        }

        .donors-container {
            display: flex;
            align-content: center;
            padding: 30px 0;
            animation: slide 70s linear infinite;
        }

        .donors-container-2 {
            animation: slide 45s linear infinite 0.5s;
        }

        .donors-container:hover {
            animation-play-state: paused;
        }

        @keyframes slide {
            from {
                transform: translateX(0%);
            }

            to {
                transform: translateX(-2400px);
            }
        }

        .donor-container {
            flex-shrink: 0;
            background-image: linear-gradient(#5e2ced 0%, #9749f8 100%);
            color: rgb(255, 255, 255);
            margin: 15px 15px 15px 0;
            padding: 12px;
            text-align: center;
            border-radius: 0.25rem;
            text-transform: capitalize;
            /*box-shadow: 1px 1px 4px  rgba(0,0,0,0.8);*/
            width: 225px;

        }

        .donor-name {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            text-transform: uppercase;
        }

        .donation-amount {
            text-align: center;
            background-image: linear-gradient(180deg, #5e2ced 0%, #9749f8 100%);
            color: white;
            padding: 12px 0;
        }

        .leader-container {
            position: relative;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            text-align: center;

            .shape-1 {
                position: absolute;
                left: 5%;
                bottom: 0;
                -webkit-animation: upndown 10s linear 2s infinite;
                animation: upndown 10s linear 2s infinite;
                z-index: -1;
            }

            .shape-2 {
                position: absolute;
                left: -2%;
                bottom: 0;
                -webkit-animation: upndown 8s linear 2s infinite;
                animation: upndown 8s linear 2s infinite;
                opacity: .5;
                z-index: -1;

            }

            .shape-3 {
                position: absolute;
                right: 5%;
                top: 0;
                -webkit-animation: upndown 10s linear 2s infinite;
                animation: upndown 10s linear 2s infinite;
                z-index: -1;

            }

            .shape-4 {
                position: absolute;
                right: -2%;
                top: 0;
                -webkit-animation: upndown 8s linear 2s infinite;
                animation: upndown 8s linear 2s infinite;
                opacity: .5;
                z-index: -1;

            }
        }

        .leader-groups {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .leader-group {
            margin: 15px;
            max-width: 400px;
            flex: 1 1 calc(33.33% - 30px);
            /* Ensure three groups per row on large screens */
        }

        .leader-list {
            list-style-type: none;
            padding: 0;
        }

        .leader-item {
            width: 100%;
            padding: 15px;
            justify-content: space-between;
            display: flex;
            border-bottom: 1px solid rgb(151, 151, 151);
            transition: all 0.3s ease;
            background-image: -webkit-linear-gradient(50deg, #6104cc 0, #5e2ced 100%) !important;
        }

        .leader-name {
            font-weight: bold;
            /*color: var(--secondary-color);*/
            color: white;
        }

        .follower-count {
            /*color: var(--secondary-color);*/
            font-size: 0.9em;
            color: white;
        }

        .leader-buttons {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }

        .single-why-us-item {
            background-color: rgba(160, 131, 226, 0.247);
            border: 2px solid #fff;
            /* background-color: rgb(118, 70, 233); */
            text-align: center;
            /* border: 2px solid rgb(118, 70, 233); */

            .icon {
                margin: 0 auto;
            }
        }

        .single-why-us-item .icon.gdbg-1 {
            background-image: -webkit-linear-gradient(50deg, #b59bff 0, #9749f8 100%) !important;
        }

        #donation-search-bar {
            border-radius: 30px;
            border: 3px solid #9749f8 !important;
            transition: all 0.2s ease;
            padding: 10px 20px;
        }

        #donation-search-bar:focus {
            border: 3px solid #5e2ced !important;
        }

        #social-media-btn {
            background-color: #9749f8 !important;
            display: block;
            margin: 25px auto;
            width: fit-content;
            border-radius: 10px;
            color: white;
            padding: 10px 20px;
            font-weight: bold;
            transition: 0.3s;
        }

        #social-media-btn:hover {
            background-color: #5e2ced !important;
            transition: 0.3s;
        }

        .social-media-area {
            position: relative;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            text-align: center;

            .shape-1 {
                position: absolute;
                left: 5%;
                top: 20%;
                -webkit-animation: upndown 10s linear 2s infinite;
                animation: upndown 10s linear 2s infinite;
            }

            .shape-2 {
                position: absolute;
                left: -2%;
                top: 20%;
                -webkit-animation: upndown 8s linear 2s infinite;
                animation: upndown 8s linear 2s infinite;
                opacity: .5;
            }

            .shape-3 {
                position: absolute;
                right: 5%;
                bottom: 20%;
                -webkit-animation: upndown 10s linear 2s infinite;
                animation: upndown 10s linear 2s infinite;
            }

            .shape-4 {
                position: absolute;
                right: -2%;
                bottom: 20%;
                -webkit-animation: upndown 8s linear 2s infinite;
                animation: upndown 8s linear 2s infinite;
                opacity: .5;
            }
        }

        .perks i {
            color: #5e2ced;
        }

        .sertai-sekarang-btn {
            color: white !important;
            margin: 10px 0;
            width: 100%;
            background-color: #5e2ced;
            transition: 0.3s;
            padding: 10px 0 !important;
        }

        .sertai-sekarang-btn:hover {
            background-color: #500ade;
        }

        #sedekah-subuh-poster {
            border-radius: 15px;
        }

        .main-text {
            font-size: 3rem !important;
            text-align: center;
        }

        /* Display rules for different screen sizes */
        @media (max-width: 799px) {
            .main-text {
                font-size: 2rem !important;
            }

            .leader-groups {
                flex-direction: column;
            }

            .leader-group {
                flex: 1 1 100%;
                max-width: 100%;
            }

            #group2,
            #group3 {
                display: none;
            }
        }

        @media (min-width: 800px) and (max-width: 1199px) {
            .leader-group {
                flex: 1 1 calc(50% - 30px);
                max-width: calc(50% - 30px);
            }

            #group3 {
                display: none;
            }
        }

        @media (min-width: 1200px) {
            .leader-group {
                flex: 1 1 calc(33.33% - 30px);
                max-width: calc(33.33% - 30px);
            }
        }

        @media only screen and (max-width: 991px) {
            .navbar-area .nav-container .navbar-collapse ul.navbar-nav li:hover {
                transform: scale(1.0);
            }

            .navbar-area .nav-container .navbar-collapse ul.navbar-nav li.slash {
                display: none;
            }

            header {
                padding-bottom: 50px !important;
            }

            .team-member-area {
                padding-top: 20px !important;
            }
        }

        @media screen and (max-width: 770px) {
            .nav-tabs {
                display: flex !important;
                flex-wrap: nowrap;
                overflow-x: auto;
                scroll-snap-type: x mandatory;
            }

            ::-webkit-scrollbar {
                width: 20px;
                height: 12px;
            }

            ::-webkit-scrollbar-thumb {
                border-radius: 0.5rem;
                background: #500ade;
            }

            .nav-tabs>li {
                white-space: nowrap;
                scroll-snap-align: center;
            }

            .nav-tabs a {
                min-width: 225px;
                margin-bottom: 0 !important;
            }
        }

        @media (max-width: 400px) {
            .main-text {
                font-size: 1.5rem !important;
            }
        }

        .image-promotion {
            border: 4px solid #9749f8;
            transition: all 0.5s ease-in-out;
        }

        .iconn {
            background-color: #9749f8;
            min-width: 40px !important;
            min-height: 30px !important;
            padding: 0.5rem;
            border-radius: 50%;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .step {
            transition: transform 0.3s ease;
        }

        .step:hover {
            transform: translateX(10px);
        }

        .step .badge {
            background-color: #9749f8 !important;
            color: #fff !important;
            padding: 0.5rem 0.75rem;
        }

        .step .bg-black {
            background-color: #000 !important;
        }

        .step .badge:hover {
            background-color: #7d1bf5 !important;
        }

        .step .bg-black:hover {
            background-color: #000000c9 !important;
        }

        .white {
            color: #fff !important;
        }

        #promotionCarousel {
            width: 100%;
        }

        #promotionCarousel .item {
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            text-align: center;
        }

        #promotionCarousel .item img,
        #promotionCarousel .item video {
            width: 100%;
            max-width: 500px;
            height: auto;
        }
    </style>
</head>

<body>

    @include('landing-page.components.navlinks')

    <!-- header area start  -->
    <header class="breadcrumb-area breadcrumb-bg style-two" id="home"
        style="padding-top: 140px; padding-bottom: 140px;">
        <div class="container">
            <h2 class="title wow fadeInDown white main-text" style="text-wrap: balance">Cara Sedekah Subuh</h2>

            <div class="row">
                <div class="col-lg-6">
                    <div id="promotionCarousel" class="owl-carousel owl-theme">

                        <div class="item">
                            <img class="image-promotion fadeInDown"
                                src="{{ URL::asset('assets/landing-page/img/sedekah-subuh-promotion-img.png') }}"
                                id="sedekah-subuh-poster" alt="Sedekah Subuh">
                        </div>

                        <div class="item">
                            <video class="image-promotion fadeInDown" controls muted loop playsinline>
                                <source src="{{ URL::asset('assets/landing-page/video/homestay-mutiara-melaka.mp4') }}"
                                    type="video/mp4">
                                Browser anda tidak menyokong video.
                            </video>
                        </div>

                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="header-inner">

                        <a href="{{ route('register') }}">
                            <div class="step d-flex align-items-center mt-2 border p-2" style="background-color: #fff;">
                                <h5 class="iconn mr-2 mb-0">1</h5>
                                <div class="d-flex w-100 flex-column flex-sm-row">
                                    <h6 class="mb-0 text-nowrap">Daftar Ahli</h6>

                                    <div class="ml-auto">
                                        <a href="{{ URL::asset('assets/landing-page/pdf/User Registration.pdf') }}"
                                            target="_blank" rel="noopener" class="badge border">
                                            Lihat PDF
                                        </a>

                                        <a href="https://youtu.be/k4M-pFNNc90" rel="noopener" target="_blank"
                                            class="badge border">
                                            Tonton Video
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </a>

                        <div class="step d-flex align-items-center mt-2 border p-2" style="background-color: #fff;">
                            <h5 class="iconn mr-2 mb-0">2</h5>
                            <div class="d-flex w-100 flex-column flex-sm-row">
                                <h6 class="mb-0 text-nowrap">Muat Turun Aplikasi</h6>

                                <div class="ml-auto">
                                    <a href="https://play.google.com/store/apps/details?id=com.prim.prim_derma_app&pcampaignid=web_share"
                                        target="_blank" class="badge border bg-black">
                                        <i class="fab fa-google-play fa-lg"></i>
                                        PlayStore
                                    </a>

                                    <a href="https://apps.apple.com/my/app/sedekah-subuh/id6760802064" target="_blank"
                                        class="badge border bg-black">
                                        <i class="fab fa-app-store fa-lg"></i>
                                        AppStore
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="step d-flex align-items-center mt-2 border p-2" style="background-color: #fff;">
                            <h5 class="iconn mr-2 mb-0">3</h5>
                            <div class="d-flex w-100 flex-column flex-sm-row">
                                <h6 class="mb-0 text-nowrap">Panduan Menderma</h6>

                                <div class="ml-auto">
                                    <a href="{{ URL::asset('assets/landing-page/pdf/Donation Steps Manual.pdf') }}"
                                        target="_blank" rel="noopener" class="badge border">
                                        Lihat PDF
                                    </a>

                                    <a href="https://youtu.be/e0bqG6Mnbwk" rel="noopener" target="_blank"
                                        class="badge border">
                                        Tonton Video
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="step d-flex align-items-center mt-2 border p-2" style="background-color: #fff;">
                            <h5 class="iconn mr-2 mb-0">4</h5>
                            <div class="d-flex w-100 flex-column flex-sm-row">
                                <h6 class="mb-0 text-nowrap">Pantau Mata Ganjaran</h6>

                                <div class="ml-auto">
                                    <a href="{{ URL::asset('assets/landing-page/pdf/Check Points Manual.pdf') }}"
                                        target="_blank" rel="noopener" class="badge border">
                                        Lihat PDF
                                    </a>

                                    <a href="https://youtu.be/oj-kjFX-T7g" rel="noopener" target="_blank"
                                        class="badge border">
                                        Tonton Video
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="step d-flex align-items-center mt-2 border p-2" style="background-color: #fff;">
                            <h5 class="iconn mr-2 mb-0">5</h5>
                            <div class="d-flex w-100 flex-column flex-sm-row">
                                <h6 class="mb-0 text-nowrap">Jemput Ahli</h6>

                                <div class="ml-auto">
                                    <a href="{{ URL::asset('assets/landing-page/pdf/Invite Members Manual.pdf') }}"
                                        target="_blank" rel="noopener" class="badge border">
                                        Lihat PDF
                                    </a>

                                    <a href="https://youtu.be/1NQMlosoMjI" rel="noopener" target="_blank"
                                        class="badge border">
                                        Tonton Video
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- header area end  -->

    <!-- how it works area start -->
    <section class="how-it-work-area" id="organization">
        <div class="shape-1"><img src="{{ URL::asset('assets/landing-page/img/shape/08.png') }}" alt="">
        </div>
        <div class="shape-2"><img src="{{ URL::asset('assets/landing-page/img/shape/09.png') }}" alt="">
        </div>
        <div class="shape-3"><img src="{{ URL::asset('assets/landing-page/img/shape/08.png') }}" alt="">
        </div>
        <div class="shape-4"><img src="{{ URL::asset('assets/landing-page/img/shape/09.png') }}" alt="">
        </div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="section-title">
                        <h3 class="title">Organisasi</h3>
                        <p>Antara organisasi derma yang berdaftar bersama PRiM.</p>
                    </div>
                    <div class="section-search mb-3">
                        <input type="text" class="form-control" id="donation-search-bar"
                            placeholder="🔍 Cari derma">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="how-it-work-tab-nav">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link btn-terkini" data-toggle="tab" href="#organisasi" role="tab"
                                    aria-controls="lain" aria-selected="false"><i class="fas fa-newspaper"></i>
                                    Terkini </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link btn-organization" id="8" data-toggle="tab"
                                    href="#organisasi" role="tab" aria-controls="lain" aria-selected="false"><i
                                        class="fas fa-building"></i>
                                    Derma Khas </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link btn-organization" id="3" data-toggle="tab"
                                    href="#organisasi" role="tab" aria-controls="tabung-covid"
                                    aria-selected="false"><i class="fas fa-university"></i>
                                    IPTA / Universiti</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link btn-organization" id="2" data-toggle="tab"
                                    href="#organisasi" role="tab" aria-controls="tabung-covid"
                                    aria-selected="false"><i class="fas fa-hammer"></i>
                                    Masjid/Surau Baru </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link btn-organization" id="1" data-toggle="tab"
                                    href="#organisasi" role="tab" aria-controls="tabung-covid"
                                    aria-selected="false"><i class="fas fa-school"></i>
                                    PIBG Sekolah </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link btn-organization" id="4" data-toggle="tab"
                                    href="#organisasi" role="tab" aria-controls="masjid"
                                    aria-selected="false"><i class="fas fa-quran"></i>
                                    Pusat Tahfiz </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link btn-organization" id="5" data-toggle="tab"
                                    href="#organisasi" role="tab" aria-controls="masjid" aria-selected="true"><i
                                        class="fas fa-mosque"></i>
                                    Imarah Masjid </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link btn-organization" id="6" data-toggle="tab"
                                    href="#organisasi" role="tab" aria-controls="ngo" aria-selected="false">
                                    <img src="{{ URL::asset('assets/landing-page/img/sedekahsubuh_logo.png') }}"
                                        alt="Sedekah Subuh Logo" class="d-inline" width="45">
                                    Sedekah Subuh</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link btn-organization" id="7" data-toggle="tab"
                                    href="#organisasi" role="tab" aria-controls="ngo" aria-selected="false"><i
                                        class="fas fa-globe"></i>
                                    NGO </a>
                            </li>
                        </ul>
                    </div>
                    <div id="donationPoster" class="row d-flex justify-content-center carousel owl-theme">

                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- how it works area end -->

    <!-- social media area start -->
    <section class="social-media-area" id="social-media">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="section-title">
                        <!-- section title -->
                        <h3 class="title">Aktiviti Sedekah Subuh</h3>
                        <p>Lihat poster derma, video dan gambar aktiviti terkini yang dikongsi oleh komuniti
                            PRIM.<br>Dapatkan maklumat, inspirasi dan sertai usaha kebaikan bersama.</p>
                        <a class="btn" id="social-media-btn" target="_blank"
                            href="{{ route('social-media.index') }}"><i class="fas fa-users"></i> &nbsp; Terokai
                            Aktiviti Sedekah Subuh &nbsp; <i class="fas fa-arrow-right"></i></a>
                        <!-- <p class="text-center small">Komuniti dalaman PRIM • Kongsi, Berinteraksi, Berinspirasi</p> -->
                    </div><!-- //. section title -->
                </div>
            </div>
        </div>
    </section>
    <!-- social media area end -->

    <!-- why choose area start -->
    <section class="why-choose-area why-choose-us-bg">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="section-title">
                        <!-- section title -->
                        <h3 class="title extra white">Kelebihan</h3>
                        <p class="white">Berikut antara kelebihan di dalam Derma PRiM.</p>
                    </div><!-- //. section title -->
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="single-why-us-item wow zoomIn p-3">
                        <div class="icon gdbg-1">
                            <i class="flaticon-tap"></i>
                        </div>
                        <div class="content">
                            <h4 class="title">Mudah dan Mesra</h4>
                            <p>Dengan hanya berkongsi <i> link </i> derma, penderma boleh terus menderma.</p>
                        </div>
                    </div>
                    <div class="single-why-us-item wow zoomIn p-3">
                        <div class="icon gdbg-2">
                            <i class="flaticon-checked"></i>
                        </div>
                        <div class="content">
                            <h4 class="title">Selamat <i>(Anti Scam)</i></h4>
                            <p>Organisasi yang mengutip derma adalah organisasi yang berdaftar bersama Bank Islam.</p>
                        </div>
                    </div>
                    <div class="single-why-us-item wow zoomIn p-3">
                        <div class="icon gdbg-3">
                            <i class="flaticon-fast-time"></i>
                        </div>
                        <div class="content">
                            <h4 class="title">Segera</h4>
                            <p>Tiada proses <i>settlement</i> dan derma akan terus dikreditkan ke dalam akaun bank yang
                                telah didaftarkan.</p>
                        </div>
                    </div>
                    <div class="single-why-us-item wow zoomIn p-3">
                        <div class="icon gdbg-4">
                            <i class="flaticon-cloud"></i>
                        </div>
                        <div class="content">
                            <h4 class="title">Sistematik</h4>
                            <p>Sistem pengurusan organisasi yang sistematik dan derma boleh dibuat secara online.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- why choose area end -->

    <section aria-label="Donors In The Past Week">
        <div class="container container-donation">
            <h3 class="my-2 text-center">Penderma</h3>
            <div class="mb-3 text-center">Paparan penderma-penderma yang terkini</div>
            <div class="donors-container donors-container-1">
                @foreach ($donors as $index => $donor)
                    {{-- @if ($index % 10 == 0 && $index > 0) --}}
                    {{-- <!-- Close the previous row and open a new one --> --}}
                    {{--
            </div>
            <div class="donors-container donors-container-2"> --}}
                    {{-- @endif --}}
                    <div class="donor-container">
                        <div class="donor-name" title="{{ $donor->username }}">{{ $donor->username }}</div>
                        <div class="donor-amount">RM{{ $donor->amount }}</div>
                        <small>{{ date('d/m/Y h:i A', strtotime($donor->datetime_created)) }}</small>
                    </div>
                @endforeach
            </div>

        </div>

    </section>

    <!-- about us area start -->

    <!-- counterup area start -->
    <section class="counterup-area">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-4 col-md-4" style="padding-top: 10px;">
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
                    <div class="single-counter-item" style="padding-top: 25px;">
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
                <div class="col-lg-4 col-md-4" style="margin-top: 25px;">
                    <div class="single-counter-item">
                        <!-- single counter item -->
                        <div class="icon">
                            <i class="flaticon-business-and-finance"></i>
                        </div>
                        <div class="content">
                            RM<span class="count-num">{{ $totalAmount }}</span>
                            <h4 class="title">Jumlah Derma Tahun Ini</h4>
                        </div>
                    </div><!-- //. single counter item -->
                    <div class="single-counter-item" style="padding-top: 25px;">
                        <!-- single counter item -->
                        <div class="icon">
                            <i class="flaticon-save-money"></i>
                        </div>
                        <div class="content">
                            RM<span class="count-num">{{ $dailyGain }}</span>
                            <h4 class="title">Jumlah Derma Hari Ini</h4>
                        </div>
                    </div><!-- //. single counter item -->
                </div>
                <div class="col-lg-4 col-md-4" style="margin-top: 25px;">
                    <div class="single-counter-item">
                        <!-- single counter item -->
                        <div class="icon">
                            <i class="flaticon-donation"></i>
                        </div>
                        <div class="content">
                            <span class="count-num">{{ $transactions }}</span>
                            <h4 class="title">Jumlah Transaksi Tahun Ini</h4>
                        </div>
                    </div><!-- //. single counter item -->
                    <div class="single-counter-item" style="padding-top: 25px;">
                        <!-- single counter item -->
                        <div class="icon">
                            <i class="flaticon-transaction"></i>
                        </div>
                        <div class="content">
                            <span class="count-num">{{ $dailyTransactions }}</span>
                            <h4 class="title">Jumlah Transaksi Hari Ini</h4>
                        </div>
                    </div><!-- //. single counter item -->
                </div>
            </div>
        </div>
    </section>
    <!-- counterup area end -->

    <div class="modal fade" id="modalPdf" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Manual PDF</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body p-0">
                    {{-- <a href="{{ URL::asset('assets/landing-page/pdf/User Manual.pdf') }}" target="_blank" rel="noopener noreferrer"></a> --}}
                    {{-- <iframe src="{{ URL::asset('assets/landing-page/pdf/User Manual.pdf') }}" width="100%" height="700" style="border: 0;"></iframe> --}}
                </div>
            </div>
        </div>
    </div>

    <!-- team member area start -->
    <section class="team-member-area" id="ourteam">

        <!-- <div class="bg-shape-3 fa-rotate-180" style="top: 0px !important;right:0px;">
            <img src="{{ URL::asset('assets/landing-page/img/bg/team-shape.png') }}" alt="" style="max-width:45%">
        </div> -->
        <div class="bg-shape-2">
            <img style="width: 100vw !important;"
                src="{{ URL::asset('assets/landing-page/img/bg/contact-map-bg-min.jpg') }}" alt="">
        </div>
        <!-- <div class="bg-shape-3"> -->
        <!-- {{-- <img src="{{ URL::asset('assets/landing-page/img/bg/contact-mobile-bg.png') }}" alt=""> --}} -->
        <!-- </div> -->
        <div class="container">
            <div class="container" style="margin-bottom: 10rem">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="section-title">
                            <h3 class="title extra">Kerjasama</h3>
                            <p>Laman web ini telah diakui dan disahkan selamat untuk digunakan.</p>
                        </div>
                    </div>
                </div>
                <div class="row text-center ">
                    <div class="col-lg-12">
                        <div class="row justify-content-center">
                            <div class="col-lg-4 p-3 text-sm-center align-self-center">
                                <img src="{{ URL::asset('assets/landing-page/img/logo-paynet.png') }}" alt=""
                                    style="max-width:70%">
                            </div>
                            <div class="col-lg-4 p-3 text-sm-center align-self-center">
                                <img src="{{ URL::asset('assets/landing-page/img/logo-bank-islam.png') }}"
                                    alt="" style="max-width:70%">
                            </div>
                            <!-- <div class="col-lg-4 p-3 text-sm-center align-self-center">
                                <img src="{{ URL::asset('assets/landing-page/img/logo-utem-blue.png') }}" alt="" style="max-width:70%">
                            </div> -->
                        </div>
                    </div>
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
                            <a href="" style="pointer-events: none;" class="footer-logo"><img
                                    src="{{ URL::asset('assets/landing-page/img/logo-header.png') }}"
                                    alt=""></a>
                            <p>People Relationship Information Management (PRiM) adalah sebuah sistem untuk
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
                            <p> Kondominium Mutiara Melaka, Jalan Pantai Puteri, Tanjung Kling, 76400 Melaka
                            </p>

                        </div>
                    </div>
                    <!-- <div class="col-lg-3 col-md-6">
                        <div class="footer-widget about_widget" style="text-align: center">
                            <a href="#" style="pointer-events: none;" class="footer-logo"><img src="{{ URL::asset('assets/landing-page/img/logo-utem-white.png') }}" alt="" style="max-width: 70%"></a>

                            <a href="#" style="pointer-events: none;" class="footer-logo"><img src="{{ URL::asset('assets/landing-page/img/logo-ftmk.png') }}" alt="" style="max-width: 70%"></a>

                            <ul class="social-icon" style="text-align: center; ">
                                <li><a href="https://www.facebook.com/MyUTeM/"><i class="fab fa-facebook-f"></i></a>
                                </li>
                                <li><a href="https://www.instagram.com/myutem/"><i class="fab fa-instagram"></i></a>
                                </li>
                                <li><a href="https://twitter.com/myutem"><i class="fab fa-twitter"></i></a></li>
                                <li><a href="https://www.youtube.com/channel/UCmJKvkfmZf4pbXwDqo2sZZg"><i class="fab fa-youtube"></i></a></li>
                            </ul>
                        </div>
                    </div> -->
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

    <section aria-label="Click to reach out to us at WhatsApp" id="btn-whatsapp">
        <a href="https://wa.me/139017388" target="_blank" class="social-link">
            <div class="Btn">
                <div class="sign">
                    <svg class="socialSvg whatsappSvg" viewBox="0 0 16 16">
                        <path
                            d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z">
                        </path>
                    </svg>
                </div>
                <div class="text">Hubungi Kami</div>
            </div>
        </a>
    </section>

    @include('landing-page.footer-script')
    <script>
        var msg = '{{ Session::get('alert') }}';
        var exist = '{{ Session::has('alert') }}';

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

            function terkini() {
                $.ajax({
                    url: "{{ route('landingpage.donation.header') }}",
                    type: 'GET',
                    success: function(result) {
                        var posterExist = true;

                        if (result === '') {
                            result =
                                `<div class="d-flex justify-content-center">Tiada Maklumat Dipaparkan</div>`;
                            posterExist = false;
                        }

                        $('#donationPoster').html(result);
                        $('#donationPoster').trigger('destroy.owl.carousel').removeClass(
                            'owl-carousel owl-loaded');
                        $('#donationPoster').find('.owl-stage-outer').children().unwrap();
                        $('#donationPoster').owlCarousel({
                            loop: true,
                            autoplay: true,
                            autoplayTimeout: 5000,
                            responsiveClass: true,
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
                                    nav: false
                                },
                            }
                        });
                    }
                });
            }

            window.onload = function() {
                $(".btn-terkini").addClass("active")
                terkini()
            };

            $('.sertai-sekarang-btn').click(function(e) {
                e.preventDefault();
                let href = $(this).attr('href');
                let target = $(href);

                if (target) {
                    target[0].scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });

            $('#promotionCarousel').owlCarousel({
                items: 1,
                loop: true,
                autoplay: true,
                autoplayTimeout: 2000,
                autoplayHoverPause: true,
                nav: false,
                dots: false
            });
            $('#feedback').owlCarousel({
                loop: true,
                autoplay: true, //true if you want enable autoplay
                autoPlayTimeout: 1000,
                margin: 30,
                dots: false,
                nav: true,
                smartSpeed: 3000,
                animateIn: 'fadeIn',
                animateOut: "fadeOut",
                navText: ['', ''],
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
                        nav: false
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

            function loadDonationCarousel(type, searchQuery = '') {
                $.ajax({
                    url: "{{ route('landingpage.donation.bytabbing') }}",
                    type: 'GET',
                    data: {
                        type: type,
                        searchQuery: searchQuery
                    },
                    success: function(result) {

                        var posterExist = true;
                        if (result === '') {
                            result =
                                `<div class="d-flex justify-content-center">Tiada Maklumat Dipaparkan</div>`;
                            posterExist = false;
                        }

                        $('#donationPoster').html(result);
                        $('#donationPoster').trigger('destroy.owl.carousel').removeClass(
                            'owl-carousel owl-loaded');
                        $('#donationPoster').find('.owl-stage-outer').children().unwrap();
                        $('#donationPoster').owlCarousel({
                            // loop:true,
                            dots: posterExist,
                            // paginationNumbers: false,
                            responsiveClass: true,
                            responsive: {
                                0: {
                                    items: 1,
                                    nav: false
                                },
                                600: {
                                    items: 2,
                                    nav: false
                                },
                                1000: {
                                    items: 3,
                                    nav: false,
                                    loop: false
                                }
                            },
                        });
                    }
                });
            }

            var typedonation;
            var searchQuery = '';
            var searchDelay;
            $(document).on('click', '.btn-organization', function() {
                var type = $(this).attr("id");
                typedonation = type;
                loadDonationCarousel(typedonation, searchQuery);
            });

            $(document).on('click', '.btn-terkini', function() {
                $(this).addClass("active")
                terkini()
            })

            $('#donation-search-bar').on('input', function() {
                searchQuery = $(this).val().trim().toLowerCase();
                clearTimeout(searchDelay);

                searchDelay = setTimeout(function() {
                    loadDonationCarousel(typedonation, searchQuery);
                }, 300);
            });

            // duplicate the donors to make the loop effect works
            var duplicate1 = document.querySelector('.donors-container-1').cloneNode(true);
            $('.donors-container-1').append($(duplicate1).children());
            var duplicate2 = document.querySelector('.donors-container-2').cloneNode(true);
            $('.donors-container-2').append($(duplicate2).children());
        });
    </script>
</body>

</html>
