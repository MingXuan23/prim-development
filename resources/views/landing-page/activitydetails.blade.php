<!DOCTYPE html>
<html lang="en">

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

    <!-- breadcrumb area start -->
    <div class="breadcrumb-area breadcrumb-bg extra">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb-inner">
                        <h1 class="page-title">Masjid An-Najihah</h1>
                        <ul class="page-navigation">
                            <li>Aktiviti Organisasi</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- breadcrumb area end -->

    <div class="page-content-area padding-top-120 padding-bottom-120">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="single-post-details-item">
                        <!-- blog single content -->
                        <div class="thumb">
                            <img src="{{ URL::asset('assets/landing-page/img/blog/1.jpg') }}" alt="blog image"
                                width="751" height="396">
                        </div>
                        <div class="entry-content">
                            <ul class="post-meta">
                                <li><a href="#"><i class="far fa-calendar-alt"></i> 12-01-2019</a></li>
                                <li><a href="#"><i class="far fa-user"></i> Admin</a></li>

                            </ul>
                            <h4 class="title">Gotong Royong Belia Masjid </h4>
                            <p>Lorem Ipsums is simply dummy text of the print industry. Lorem Ipsum has industry's
                                standard dummy text ever since. Lorem Ipsums is simply dummy text of the print industry.
                                Lorem Ipsum has industry's standard dummy text ever since.</p>
                            <p>Proin vitae blandit felis. Proin rhoncus imperdiet facilisis. Etiam et mauris ex.
                                Maecenas posuere ipsum orci, at imperdiet est venenatis quis. Maecenas quis fermentum
                                ipsum, ac eleifend urna. Cras sed viverra nibh. Mauris eget finibus erat. Mauris tempor
                                varius purus nec cursus. Nullam ornare eget ipsum sit amet consequat. Nunc finibus vitae
                                diam non suscipit. Praesent elementum felis sit amet urna tempus commodo. Proin congue
                                id ante ac maximus. Interdum et malesuada fames ac ante ipsum primis in faucibus. Donec
                                euismod arcu venenatis, viverra arcu eu, efficitur mauris. Aliquam sagittis sagittis
                                elit, in cursus est faucibus in.</p>
                            <blockquote>
                                Nunc blandit tincidunt consequat. Duis diam metus, suscipit ininar eget, egestas id
                                arcu. Duis a enim vel mauris ultrices. Nullam aliquet velit ac velit tempus in semper.
                            </blockquote>
                            <p>Lorem Ipsums is simply dummy text of the print industry. Lorem Ipsum has industry's
                                standard dummy text ever since. Lorem Ipsums is simply dummy text of the print industry.
                                Lorem Ipsum has industry's standard dummy text ever since.</p>
                            <p>Proin vitae blandit felis. Proin rhoncus imperdiet facilisis. Etiam et mauris ex.
                                Maecenas posuere ipsum orci, at imperdiet est venenatis quis. Maecenas quis fermentum
                                ipsum, ac eleifend urna. Cras sed viverra nibh. Mauris eget finibus erat. Mauris tempor
                                varius purus nec cursus.</p>
                        </div>
                        <div class="entry-footer">
                            <!-- entry footer -->
                            <div class="left">
                                <ul class="tags">
                                    <li class="title">Tags: </li>
                                    <li><a href="#">Marketing</a><a href="#">Design</a></li>
                                </ul>
                            </div>
                            <div class="right">
                                <ul class="social-share">
                                    <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                    <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                    <li><a href="#"><i class="fab fa-google-plus-g"></i></a></li>
                                    <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                                </ul>
                            </div>
                        </div><!-- //. entry footer -->


                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="sidebar widget-area">
                        <!-- widget area -->

                        <div class="widget widget_search">
                            <!-- widget  -->
                            <h4 class="widget-title">Senarai Derma</h4>

                            <ol class="ol2">
                                <li> <a href="# ">Derma Kilat Pemasangan Aircond <i class="fas fa-donate"></i></a></li>
                                <li> <a href="# ">Derma Kilat Pemasangan Paip <i class="fas fa-donate"></i></a></li>
                                <li> <a href="# ">Derma Kilat Pemasangan Kipas <i class="fas fa-donate"></i></a></li>
                                <li> <a href="# ">Derma Kilat Pembinaan Tandas <i class="fas fa-donate"></i></a></li>
                            </ol>

                        </div><!-- //. widget -->


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

    @include('landing-page.footer-script')
</body>

</html>