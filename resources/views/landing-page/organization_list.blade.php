<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noarchive">
    <title> PRIM | Senarai Organisasi</title>

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

    <!-- breadcrumb area start -->
    <div class="breadcrumb-area breadcrumb-bg extra">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb-inner">
                        <h1 class="page-title">Senarai Organisasi</h1>
                        {{-- <ul class="page-navigation">
                        <li><a href="#"> Home</a></li>
                        <li>Blog</li>
                    </ul> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- breadcrumb area end -->

    <div class="page-content-area padding-top-120 padding-bottom-120">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="main-box clearfix">
                        <div class="table-responsive">
                            <table class="table user-list">
                                <thead>
                                    <tr>
                                        <th><span>Nama Organisasi</span></th>
                                        <th><span>Alamat</span></th>
                                        <th><span>Email</span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <img src="{{ URL::asset('assets/landing-page/img/building-solid.svg') }}"
                                                alt="">
                                            <a href="/activity-list" class="user-link">Masjid An-Najihah <i class="fas fa-chevron-right"></i></a>
                                            <span class="user-subhead">Ayer Keroh</span>
                                        </td>
                                        <td>
                                            UTeM, Ayer Keroh
                                        </td>

                                        <td>
                                            <a href="#">mila@kunis.com</a>
                                        </td>

                                    </tr>
                                    <tr>
                                        <td>
                                            <img src="{{ URL::asset('assets/landing-page/img/building-solid.svg') }}"
                                                alt="">

                                            <a href="#" class="user-link">Masjid Al-Alami <i class="fas fa-chevron-right"></i></a>
                                            <span class="user-subhead">Ayer Keroh</span>
                                        </td>
                                        <td>
                                            UTeM, Ayer Keroh
                                        </td>

                                        <td>
                                            <a href="#">marlon@brando.com</a>
                                           
                                        </td>

                                    </tr>
                                    <tr>
                                        <td>
                                            <img src="{{ URL::asset('assets/landing-page/img/building-solid.svg') }}"
                                                alt="">

                                            <a href="#" class="user-link">Tahfiz Iman <i class="fas fa-chevron-right"></i></a>
                                            <span class="user-subhead">Bagan Serai</span>
                                        </td>
                                        <td>
                                            Bagan Serai, Perak
                                        </td>

                                        <td>
                                            <a href="#">jack@nicholson</a>
                                        </td>

                                    </tr>
                                    <tr>
                                        <td>
                                            <img src="{{ URL::asset('assets/landing-page/img/building-solid.svg') }}"
                                                alt="">

                                            <a href="#" class="user-link">Pusat Islam UTeM <i class="fas fa-chevron-right"></i></a>
                                            <span class="user-subhead">Ayer Keroh</span>
                                        </td>
                                        <td>
                                            UTeM, Ayer Keroh
                                        </td>

                                        <td>
                                            <a href="#">humphrey@bogart.com</a>
                                        </td>

                                    </tr>
                                    <tr>
                                        <td>
                                            <img src="{{ URL::asset('assets/landing-page/img/building-solid.svg') }}"
                                                alt="">

                                            <a href="#" class="user-link">Surau Telok Mas <i class="fas fa-chevron-right"></i></a>
                                            <span class="user-subhead">Telok Mas</span>
                                        </td>
                                        <td>
                                            UTeM, Ayer Keroh
                                        </td>

                                        <td>
                                            <a href="#">spencer@tracy</a>
                                        </td>

                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        {{-- <ul class="pagination pull-right">
                        <li><a href="#"><i class="fa fa-chevron-left"></i></a></li>
                        <li><a href="#">1</a></li>
                        <li><a href="#">2</a></li>
                        <li><a href="#">3</a></li>
                        <li><a href="#">4</a></li>
                        <li><a href="#">5</a></li>
                        <li><a href="#"><i class="fa fa-chevron-right"></i></a></li>
                    </ul> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>


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

    <!-- jquery -->
    @include('landing-page.footer-script')
</body>

</html>