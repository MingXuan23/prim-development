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
    
        section[aria-label="Donors In The Past Week"] , section[aria-label="Top Ketua Ahli"]{
            background-color: #500ade;
            padding-top: 80px;
            padding-bottom: 80px;
            background-image: url('assets/landing-page/img/bg/why-us-dark-bg.png');
            background-size: contain;
            border: 4px solid #B4B1F4;

        }
        .container.container-donation{
            overflow: hidden;
        }
        .donors-container{
            display: flex;
            align-content: center;
            padding: 30px 0;
            animation: slide 45s linear infinite;
        }
        .donors-container-2{
            animation: slide 45s linear infinite 0.5s;
        }
        .donors-container:hover {
            animation-play-state: paused;
        }
        @keyframes slide{
            from{
                transform: translateX(0%);
            }
            to{
                transform: translateX(-2400px);
            }
        }
        .donor-container{
            flex-shrink:0;
            background-image: linear-gradient(#5e2ced 0%,#9749f8 100%);
            color: rgb(255, 255, 255) ;
            margin:15px 15px 15px 0;
            padding: 12px;
            text-align: center;
            border-radius: 0.25rem;
            text-transform: capitalize;
            box-shadow: 1px 1px 4px  rgba(0,0,0,0.8);
            width: 225px;

        }
        .donor-name{
            font-weight: bold;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            text-transform: uppercase;
        }
        .donation-amount{
            text-align: center;
            background-image: linear-gradient(180deg ,#5e2ced 0%,#9749f8 100% );
            color: white;
            padding: 12px 0;
        }

        .leader-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            text-align: center;
        }

        .leader-groups {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .leader-group {
            margin: 15px;
            max-width: 400px;
            flex: 1 1 calc(33.33% - 30px); /* Ensure three groups per row on large screens */
        }

        .leader-list {
            list-style-type: none;
            padding: 0;
        }

        .leader-item {
            width: 100%;
            padding: 15px;
            justify-content: space-between; 
            display:flex;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

  
        .leader-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateX(10px);
        }

        .leader-name {
            font-weight: bold;
            color: #fff;
        }

        .follower-count {
            color: #e0e0e0;
            font-size: 0.9em;
        }

        .leader-buttons {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }

        /* Display rules for different screen sizes */
        @media (max-width: 799px) {
            .leader-groups {
            flex-direction: column;
            }

            .leader-group {
            flex: 1 1 100%;
            max-width: 100%;
            }

            #group2, #group3 {
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

        @media only screen and (max-width: 991px){
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

        @media screen and (max-width: 770px){
            .nav-tabs{
                display:flex!important;
                flex-wrap: nowrap;
                overflow-x: auto;
                scroll-snap-type: x mandatory;
            }
            ::-webkit-scrollbar{
                width: 20px;
                height: 12px;
            }
            ::-webkit-scrollbar-thumb{
                border-radius: 0.5rem;
                background: #500ade;
            }
            .nav-tabs >li{
                white-space: nowrap;
                scroll-snap-align: center;
            }
            .nav-tabs a{
                min-width: 225px;
                margin-bottom: 0!important;
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
            {{-- <div class="collapse navbar-collapse" id="appside_main_menu">
                <ul class="navbar-nav">
                    <li class="current-menu-item"><a href="#">Derma</a></li>
                    <li><a href="#team">Info</a></li>
                    <li><a href="#organization">Organisasi</a></li>
                    <li><a href="#ourteam">Kerjasama</a></li>
                    <li><a href="#contact">Hubungi Kami</a></li>
                </ul>
            </div> --}}
            <div class="collapse navbar-collapse" id="appside_main_menu">
                <ul class="navbar-nav">
                    <li><a href="/">Utama</a></li>
                    <li class="current-menu-item"><a href="#" style="font-size: 19px">Derma</a></li>
                    <li><a href="/yuran">Yuran</a></li>
                    <li class="menu-item-has-children">
                        <a href="#">Perniagaan</a>
                        <ul class="sub-menu">
                            <li><a href="{{route('merchant-product.index')}}">Get&Go</a></li>
                            <li><a href="{{route('homestay.homePage')}}">Homestay</a></li>
                        </ul>
                    </li>
                    {{-- <li><a href="/merchant/product">Get&Go</a></li> --}}
                    <li class="slash">|</li>
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
    <header class="breadcrumb-area breadcrumb-bg style-two" id="home" style="padding-top: 170px; padding-bottom: 170px;">
        <!-- <div class="header-right-image wow zoomIn" style="text-align: right">
            <img src="{{ URL::asset('assets/landing-page/img/pic-front.png') }}" alt="header right image" style="padding-bottom: 482px;
            max-width: 70%;">
        </div> -->

        <div class="container">
            <div class="row d-flex align-items-center">
                <div class="col-lg-6 align-items-center d-none d-lg-block" style="text-align: center">
                    <img src="{{ URL::asset('assets/landing-page/img/header-derma.png') }}" alt="header right image" style="max-width: 110%;" id="headerhover">
                </div>
                <div class="col-lg-6 justify-content-center">
                    <div class="header-inner">
                        {{-- <h1 class="title wow fadeInDown white">Derma</h1>
                        <p class="white">Kita digalakkan untuk bersedekah setiap hari terutamanya di waktu subuh. Allah menjanjikan 
                            banyak kelebihan dan ganjarannya. Kami sediakan Kemudahan bersepadu supaya kita boleh bersedekah atau menderma TANPA NAMA serendah RM2
                            untuk pelbagai masjid serta organisasi yang telah berdaftar di dalam laman web prim.my/derma.</p>
                        <div class="btn-wrapper wow fadeInUp">
                            <a href="#organization" class="boxed-btn btn-rounded">Jom Derma</a>
                        </div> --}}
                        <h1 class="title wow fadeInDown white">Derma</h1>
                        <p class="white" style="font-size: 20px;">Kita digalakkan untuk bersedekah setiap hari terutamanya di waktu Subuh. Allah menjanjikan 
                            banyak kelebihan dan ganjarannya. 
                            Kami sediakan Kemudahan bersepadu supaya kita boleh bersedekah atau menderma TANPA NAMA serendah RM2
                            untuk pelbagai masjid serta organisasi yang telah berdaftar di dalam laman web prim.my/derma.</p>
                        <div class="btn-wrapper wow fadeInUp">
                            <a href="#organization" class="boxed-btn btn-rounded">Jom Derma</a>
                        </div>
                    </div>
                </div>
                
                {{-- <div class="col-lg-7">
                    <h1 class="title wow fadeInDown white ">PRiM</h1>
                    <p class=" white" style="font-size: 20px">Sebuah sistem yang menyediakan perkhidmatan pembayaran dalam talian untuk pelbagai organisasi
                    berdaftar. Antara perkhidmatan yang telah kami sediakan ialah derma.</p>
                    <div class="btn-wrapper wow fadeInUp ">
                        <a href="#organization" class="boxed-btn btn-rounded">Jom Derma</a>
                        <a href="/login" class="boxed-btn btn-rounded blank">Log Masuk</a>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div id="headerPoster" class="row d-flex justify-content-center carousel owl-theme"></div>
                </div> --}}
                {{-- <div class="col-lg-5">
                    <div id="headerPoster" class="row d-flex justify-content-center carousel owl-theme"></div>
                </div>
                <div class="col-lg-7 align-items-center d-none d-lg-block" style="text-align: center">
                    <img src="{{ URL::asset('assets/landing-page/img/masjid-utem.png') }}" alt="header right image" style="max-width: 100%;">
                </div>
            </div>
            <div class="row justify-content-center" style="padding-top: 150px">
                <h1 class="title wow fadeInDown">PRiM</h1>
                <p class="text-center">Sebuah sistem yang menyediakan perkhidmatan pembayaran dalam talian untuk pelbagai organisasi
                berdaftar. Antara perkhidmatan yang telah kami sediakan ialah derma.</p>
                <div class="btn-wrapper wow fadeInUp">
                    <a href="#organization" class="boxed-btn btn-rounded">Jom Derma</a>
                </div>
            </div> --}}
        </div>
    </header>
    <!-- header area end  -->

    <section aria-label="Donors In The Past Week">
        <div class="container container-donation">
            <h3 class="my-2 text-white text-center">Penderma</h3>
            <div class="mb-3 text-white text-center">Paparan penderma-penderma yang terkini</div>
            <div class="donors-container donors-container-1">
                @foreach ($donors as $index => $donor)
                    @if ($index % 10 == 0 && $index > 0)
                        <!-- Close the previous row and open a new one -->
                        </div><div class="donors-container donors-container-2">
                    @endif
                    <div class="donor-container">
                        <div class="donor-name" title="{{ $donor->username }}">{{ $donor->username }}</div>
                        <div class="donor-amount">RM{{ $donor->amount }}</div>
                        <small>{{date('d/m/Y h:i A',strtotime($donor->datetime_created))}}</small>
                    </div>
                @endforeach            
        </div>

        </div>

    </section>
    <section aria-label="Top Ketua Ahli">
        <div class="leader-container">
            <h3 class="my-2 text-white text-center">Senarai Ketua dengan Bilangan Pengikut (follower)</h3>

            <!-- Containers for all groups -->
            <div class="leader-groups">
                @foreach ($leaders as $index => $leader)
                    <?php
                        $groupNumber = floor($index / 5) + 1;
                    ?>
                    <!-- Create the group divs dynamically based on the group number -->
                    @if($index % 5 == 0)
                        <div class="leader-group" id="group{{ $groupNumber }}">
                            <ul class="leader-list">
                    @endif
                    
                    <!-- Leader item -->
                    <li class="leader-item" style="animation-delay: {{ $index * 0.1 }}s;">
                        <span class="leader-name">{{ $index + 1 }}. {{ $leader->name }}</span>
                        <span class="follower-count">{{ $leader->member_count }} Ahli</span>
                    </li>

                    <!-- Close the group divs when the last leader of the group is reached -->
                    @if($index % 5 == 4 || $index == count($leaders) - 1)
                            </ul>
                        </div>
                    @endif
                @endforeach
            </div>
<!-- 
            <div class="leader-buttons">
                <button class="btn btn-info" onclick="showInfo()">Maklumat Lanjut</button>
                <button class="btn btn-register" onclick="register()">Daftar</button>
            </div> -->
        </div>
    </section>

    <!-- <section aria-label="Donors In The Past Week">
        <div class="container container-donation">
            <h3 class="my-2 text-white text-center">Ketua Ahli</h3>
            
            <div class="donors-container">
                @foreach ($leaders as $index => $leader)

                    <div class="donor-container">
                        <div class="donor-name" title="{{ $leader->name }}">{{ $leader->name}}</div>
                        <div class="donor-amount">Ahli: {{ $leader->member_count  }}</div>
                       
                    </div>
                @endforeach            
        </div>

        </div>

    </section> -->
    <section class="team-member-area" id="team">
        <div class="bg-shape-1">
            <img src="{{ URL::asset('assets/landing-page/img/bg/team-shape.png') }}" alt="" style="max-width: 90%";>
        </div>
        <div class="container">
            
            {{-- <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="section-title">
                        <!-- section title -->
                        
                        <h3 class="title">Tentang PRiM</h3>
                        <p>Parental Relationship Information Management (PRiM) adalah sebuah sistem untuk menghubungkan
                            ibu bapa serta penjaga dengan pihak sekolah. PRiM menyediakan gerbang pembayaran yuran
                            sekolah secara dalam talian dan juga pengumuman kelas dan sekolah. Di samping itu, PRiM juga
                            menyediakan perkhidmatan lain dalam talian seperti kutipan derma bagi organisasi berdaftar.
                        </p>
                    </div><!-- //. section title -->
                </div>
            </div> --}}

            <div class="about-us-area style-two" style="padding-top:20px">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="section-title left-aligned">
                                <h3 class="title extra" style="margin-top: 24px;font-size: 40px;">Terkini</h3>
                                <div id="headerPoster" class="row d-flex justify-content-center carousel owl-theme"></div>
                            </div>
                        </div>
                        {{-- <div class="col-lg-6">
                            <div class="section-title left-aligned">
                                
                                <h3 class="title extra" style="margin-top: 24px;">Derma</h3>
                                <p>Kemudahan bersepadu yang disediakan supaya penderma boleh menderma <b>24 jam </b>
                                    sehari
                                    dalam <b> 7 hari </b> seminggu untuk organisasi yang telah berdaftar di dalam
                                    sistem
                                    PRiM.</p>
                            </div><!-- //. section title -->
                        </div> --}}
                        <div class="col-lg-6">
                            <h3 class="title extra" style="margin-bottom: 24px; margin-top: 24px;font-size: 40px;">Syarat-syarat</h3>
                            <div class="feature-area">

                                <div class="hover-inner">
                                    <div class="single-feature-list border wow zoomIn">
                                        <div class="icon icon-bg-3">
                                            <i class="flaticon-donation"></i>
                                        </div>
                                        <div class="content">
                                            <h4 class="title text-white" style="text-align: left;">Penderma</h4>
                                            <p style="text-align: left;" class="text-white"> <i class="flaticon-checked"
                                                    style="margin-right: 10px"></i> Mempunyai akaun dalam bank
                                                talian <i>(online banking)</i> dengan mana-mana bank di Malaysia.</p>
                                            <p style="text-align: left;" class="text-white"> <i class="flaticon-checked"
                                                    style="margin-right: 10px"></i> Penderma akan menerima email
                                                daripada pihak FPX dan sistem PRiM sebagai bukti pembayaran.</p>

                                        </div>
                                    </div>
                                </div>
                                <br><br>
                                <div class="hover-inner">
                                    <div class="single-feature-list border wow zoomIn">
                                        <div class="icon icon-bg-3">
                                            <i class="flaticon-business-and-finance"></i>
                                        </div>
                                        <div class="content">
                                            <h4 class="title text-white" style="text-align: left;">Penerima Derma</h4>
                                            <p style="text-align: left;" class="text-white"> <i class="flaticon-checked"
                                                    style="margin-right: 10px"></i> Mempunyai akaun Bank Islam.</p>
                                            <p style="text-align: left;" class="text-white"> <i class="flaticon-checked"
                                                    style="margin-right: 10px"></i> Mendaftar dengan Paynet melalui Bank
                                                Islam.
                                                <br> <i> <a
                                                        href="{{ URL::asset('fpx-pdf/Merchant Registration Form V2.1.pdf') }}"
                                                        download class="text-white"> (klik untuk muat turun borang)</a> </i> </p>
                                            <p style="text-align: left;" class="text-white"> <i class="flaticon-checked"
                                                    style="margin-right: 10px"></i> Mendaftar sebagai organisasi di
                                                sistem PRiM.
                                            </p>
                                            <p style="text-align: left;" class="text-white"> <i class="flaticon-checked"
                                                    style="margin-right: 10px"></i> Organisasi akan menerima email
                                                daripada pihak FPX dan sistem PRiM sebagai bukti menerima bayaran.
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

    <!-- counterup area start -->
    <section class="counterup-area">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-4 col-md-4" style="padding-top: 25px;">
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

    <!-- why choose area start -->
    <section class="why-choose-area why-choose-us-bg">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="section-title white">
                        <!-- section title -->
                        {{-- <span class="subtitle">Modul</span> --}}
                        <h3 class="title extra">Kelebihan</h3>
                        <p>Berikut antara kelebihan di dalam Derma PRiM.</p>
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
    <section class="how-it-work-area" id="organization">
        <div class="shape-1"><img src="{{ URL::asset('assets/landing-page/img/shape/08.png') }}" alt=""></div>
        <div class="shape-2"><img src="{{ URL::asset('assets/landing-page/img/shape/09.png') }}" alt=""></div>
        <div class="shape-3"><img src="{{ URL::asset('assets/landing-page/img/shape/08.png') }}" alt=""></div>
        <div class="shape-4"><img src="{{ URL::asset('assets/landing-page/img/shape/09.png') }}" alt=""></div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="section-title">
                        <!-- section title -->
                        <h3 class="title">Organisasi</h3>
                        <p>Antara organisasi derma yang berdaftar bersama PRiM.</p>
                    </div><!-- //. section title -->
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="how-it-work-tab-nav">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link btn-organization" id="8" data-toggle="tab" href="#organisasi"
                                    role="tab" aria-controls="lain" aria-selected="false"><i class="fas fa-building"></i>
                                    Derma Khas </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link btn-organization" id="3" data-toggle="tab" href="#organisasi"
                                    role="tab" aria-controls="tabung-covid" aria-selected="false"><i class="fas fa-university"></i>
                                    IPTA / Universiti</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link btn-organization" id="2" data-toggle="tab" href="#organisasi"
                                    role="tab" aria-controls="tabung-covid" aria-selected="false"><i class="fas fa-hammer"></i>
                                    Masjid/Surau Baru </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link btn-organization" id="1" data-toggle="tab" href="#organisasi"
                                    role="tab" aria-controls="tabung-covid" aria-selected="false"><i class="fas fa-school"></i>
                                    PIBG Sekolah </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link btn-organization" id="4" data-toggle="tab" href="#organisasi"
                                    role="tab" aria-controls="masjid" aria-selected="false"><i class="fas fa-quran"></i>
                                    Pusat Tahfiz </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link btn-organization" id="5" data-toggle="tab" href="#organisasi"
                                    role="tab" aria-controls="masjid" aria-selected="true"><i class="fas fa-mosque"></i>
                                    Imarah Masjid </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link btn-organization" id="6" data-toggle="tab" href="#organisasi"
                                    role="tab" aria-controls="ngo" aria-selected="false"><i class="fas fa-solid fa-hammer"></i>
                                    Wakaf MAIM</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link btn-organization" id="7" data-toggle="tab" href="#organisasi"
                                    role="tab" aria-controls="ngo" aria-selected="false"><i class="fas fa-globe"></i>
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
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="section-title">
                            <!-- section title -->
                            {{-- <span class="subtitle">Screenshots</span> --}}
                            <h3 class="title extra">Pasukan Kami</h3>
                        </div><!-- //. section title -->
                    </div>
                </div>
            </div>
            <div class="row text-center">
                <div class="col-lg-12 mb-200">
                    <div class="row justify-content-center">
                        <div class="col-lg-4 p-3 text-sm-center align-self-center">
                            <div class="p-3">
                                <img src="{{ URL::asset('assets/landing-page/img/team-member/CEO.png') }}" alt="" style="max-width:70%; width: 250px">
                            </div>
                            <div class="pt-3">
                                <h4>Yahya Bin Ibrahim</h4>
                                <p>Chief Executive Officer</p>
                            </div>
                        </div>

                        <div class="col-lg-4 p-3 text-sm-center align-self-center">
                            <div class="p-3">
                                <img src="{{ URL::asset('assets/landing-page/img/team-member/COO.png') }}" alt="" style="max-width:70%; width: 250px">
                            </div>
                            <div class="pt-3">
                                <h4>Ts. Dr. Muhammad Haziq Lim Bin Abdullah</h4>
                                <p>Chief Operating Officer</p>
                            </div>
                        </div>

                        <div class="col-lg-4 p-3 text-sm-center align-self-center">
                            <div class="p-3">
                                <img src="{{ URL::asset('assets/landing-page/img/team-member/CTO.png') }}" alt="" style="max-width:70%; width: 250px">
                            </div>
                            <div class="pt-3">
                                <h4>Chuan Chuan You</h4>
                                <p>Chief Technology Officer</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="container">
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

            <div class="row">
                <div class="col-lg-6">
                    <div class="contact-area-wrapper" id="contact">
                        <!-- contact area wrapper -->
                        {{-- <span class="subtitle">Contact us</span> --}}
                        <h3 class="title">Hubungi Kami</h3>
                        <p>Untuk sebarang pertanyaan dan maklumbalas, sila isi borang ini.</p>
                        <form method="post" action="{{ route('feedback.store') }}" class="contact-form sec-margin"
                            enctype="multipart/form-data">

                            @csrf
                            <div class="row">
                                <div class="col-lg-12">
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

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <input type="text" class="form-control phone_no" id="telno" name="telno"
                                            placeholder="Nombor Telefon" required>
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
                        <div class="map-responsive">
                            <iframe
                                src="https://maps.google.com/maps?q=utem%20melaka&t=&z=13&ie=UTF8&iwloc=&output=embed"
                                width="600" height="450" frameborder="0" style="border:0;" allowfullscreen=""
                                aria-hidden="false" tabindex="0">
                            </iframe>

                            <br>

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
    <div class="back-to-top">
        <i class="fas fa-angle-up"></i>
    </div>
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

        $.ajax({
            url: "{{ route('landingpage.donation.header') }}",
            type: 'GET',
            success: function( result ){

                $('#headerPoster').html( result );
                $('#headerPoster').trigger('destroy.owl.carousel').removeClass('owl-carousel owl-loaded');
                $('#headerPoster').find('.owl-stage-outer').children().unwrap();
                $('#headerPoster').owlCarousel({
                    loop:true,
                    autoplay:true,
                    autoplayTimeout:5000,
                    responsiveClass:true,
                    responsive:{
                        0:{
                            items:1,
                            nav:false
                        },
                        600:{
                            items:1,
                            nav:false
                        },
                        1000:{
                            items:1,
                            nav:false,
                        }
                    }, 
                });
            }
        });

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

        // duplicate the donors to make the loop effect works
        var duplicate1 = document.querySelector('.donors-container-1').cloneNode(true);
        $('.donors-container-1').append($(duplicate1).children());
        var duplicate2 = document.querySelector('.donors-container-2').cloneNode(true);
        $('.donors-container-2').append($(duplicate2).children());
    });
    </script>
</body>
</html>