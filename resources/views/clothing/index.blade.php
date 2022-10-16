<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noarchive">
    <title> PRIM </title>

    @include('clothing.head')
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
    </style>
</head>

<body>

    <nav class="navbar navbar-area navbar-expand-lg nav-absolute white nav-style-01">
        <div class="container nav-container">
            <div class="responsive-mobile-menu">
                <div class="logo-wrapper">
                    <!-- <a href="index.html" class="logo">
                        <img src="{{ URL::asset('assets/landing-page/img/logo-header.png') }}" alt="logo">
                    </a> -->
                    <img src="{{ URL::asset('assets/landing-page/img/logo-header.png') }}" alt="logo">
                </div>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#appside_main_menu"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
            <div class="collapse navbar-collapse" id="appside_main_menu">
                <ul class="navbar-nav">
                    <li class="current-menu-item"><a href="#">Utama</a></li>
                    <li><a href="#organization">Produk</a></li>
                    <li><a href="#contact">Hubungi Kami</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- header area start  -->
    <header class="header-area header-bg-2 style-two" id="home" style="margin-bottom: 10px;">
        <!-- <div class="header-right-image wow zoomIn" style="text-align: right">
            <img src="{{ URL::asset('assets/landing-page/img/pic-front.png') }}" alt="header right image" style="padding-bottom: 482px;
            max-width: 70%;">
        </div> -->

        <div class="container">
            {{-- <div class="row d-flex align-items-center">
                <div class="col-lg-7">
                    <div class="header-inner">
                        <h1 class="title wow fadeInDown">PRiM</h1>
                        <p>Sebuah sistem yang menyediakan perkhidmatan pembayaran dalam talian untuk pelbagai organisasi
                            berdaftar. Antara perkhidmatan yang telah kami sediakan ialah derma.</p>
                            <div class="btn-wrapper wow fadeInUp">
                                <a href="#organization" class="boxed-btn btn-rounded">Jom Derma</a>
                                
                                {{-- <a href="/register" class="boxed-btn btn-rounded">Daftar Sekarang</a>
                                    <a href="/login" class="boxed-btn btn-rounded blank">Log Masuk</a>
                            </div>
                        </div>
                </div>
                <div class="col-lg-7 align-items-center d-none d-lg-block" style="text-align: right">
                    <img src="{{ URL::asset('assets/landing-page/img/pic-front.png') }}" alt="header right image" style="max-width: 100%;">
                </div>
            </div> --}}
            <div class="row justify-content-center" style="padding-top: 150px">
                <h1 class="title wow fadeInDown">PRiM</h1>
            </div>
        </div>
    </header>
    <!-- header area end  -->

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
                        <h3 class="title">Produk</h3>
                        <p>Antara produk yang ditawarkan oleh Organisasi </p>
                    </div><!-- //. section title -->
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="how-it-work-tab-nav">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link btn-category" id="0" data-toggle="tab" href="#organisasi"
                                    role="tab" aria-controls="pakaian" aria-selected="false"><i class="fas fa-tshirt"></i>
                                    Pakaian</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link btn-category" id="8" data-toggle="tab" href="#organisasi"
                                    role="tab" aria-controls="lain" aria-selected="false"><i class="far fa-list-alt"></i>
                                    Lain-lain</a>
                            </li>
                        </ul>
                    </div>
                    <div id="productPoster" class="row d-flex justify-content-center carousel owl-theme">
                        <div class="card">
                            <img class="card-img-top product-poster" src="" alt="Card image cap">
                            <div class="card-body">
                                <div class="d-flex flex-column justify-content-center ">
                                    <a href="{{ route('clothing.url', 'TSHIRT-1') }}" class="boxed-btn btn-rounded btn-product">
                                        <i class="fas fa-shopping-cart"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--====== TESTIMONIAL PART ENDS ======-->

    <!-- team member area start -->
    <section class="team-member-area" id="team">

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
            document.getElementById("0").click();
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
        // $(document).on('click', '.btn-organization', function() {
        //     var type = $(this).attr("id");
        //     typedonation = type;
        //     $.ajax({
        //         url: "{{ route('landingpage.donation.bytabbing') }}",
        //         type: 'GET',
        //         data: {
        //             type: type,
        //         },
        //         success: function( result ){

        //             var posterExist = true;
        //             if (result === '') {
        //                 result = `<div class="d-flex justify-content-center">Tiada Maklumat Dipaparkan</div>`;
        //                 posterExist = false;
        //             }

        //             $('#donationPoster').html( result );
        //             $('#donationPoster').trigger('destroy.owl.carousel').removeClass('owl-carousel owl-loaded');
        //             $('#donationPoster').find('.owl-stage-outer').children().unwrap();
        //             $('#donationPoster').owlCarousel({
        //                 // loop:true,
        //                 dots: posterExist,
        //                 // paginationNumbers: false,
        //                 responsiveClass:true,
        //                 responsive:{
        //                     0:{
        //                         items:1,
        //                         nav:false
        //                     },
        //                     600:{
        //                         items:2,
        //                         nav:false
        //                     },
        //                     1000:{
        //                         items:3,
        //                         nav:false,
        //                         loop:false
        //                     }
        //                 }, 
        //             });
        //         }
        //     });
        // });
    });
    </script>
</body>
</html>