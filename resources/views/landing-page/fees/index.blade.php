<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noarchive">
    <title> PRiM | Yuran </title>

    @include('landing-page.fees.head')
    <style>
        /* .navbar-area .nav-container .navbar-collapse ul.navbar-nav li.current-menu-item:hover {
            transform: scale(1.0);
        }

        .navbar-area .nav-container .navbar-collapse ul.navbar-nav li:hover {
            transform: scale(1.1);
        } */

        #headerhover {
            transform: scale(0.9);
            transition: transform 1s ease;
        }

        #headerhover:hover {
            transform: scale(1.2);
        }

        .form-control{
            border-color:#5e5e5e!important;
            transition: all 0.2s ease;
        }


        @media only screen and (max-width: 991px){
            .navbar-area .nav-container .navbar-collapse ul.navbar-nav li:hover {
                transform: scale(1.0);
            }

            .navbar-area .nav-container .navbar-collapse ul.navbar-nav li.slash {
                display: none;
            }
        }
    </style>

    <link rel="stylesheet" href="{{ URL::asset('assets/landing-page/css/yuran_style.css') }}">
</head>

<body>

    <nav class="navbar navbar-area navbar-expand-lg nav-absolute white nav-style-01">
        <div class="container nav-container">
            <div class="responsive-mobile-menu">
                <div class="logo-wrapper">
                    <a href="/" class="logo">
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
                    <li><a href="/">Utama</a></li>
                    <li><a href="/derma">Derma</a></li>
                    <li class="current-menu-item"><a href="#" style="font-size: 19px">Yuran</a></li>
                    <li class="menu-item-has-children">
                        <a href="#">Perniagaan</a>
                        <ul class="sub-menu">
                            <li><a href="{{route('merchant-product.index')}}">Get&Go</a></li>
                            <li><a href="{{route('homestay.homePage')}}">Homestay</a></li>
                        </ul>
                    </li>
                    {{-- <li><a href="/merchant/product">Get&Go</a></li> --}}
{{--                    <li class="slash">|</li>--}}
{{--                    <li><a href="#contact">Hubungi Kami</a></li>--}}
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

    <main role="main">
        <!--whatsapp contact button-->

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
        <!--    hero section-->
        <section id="section-hero" aria-label="Hero Section">
            <div id="hero-wrapper">
                <img id="hero-deco" style="--transition-delay: 0.45s" class="slide-from-bottom-element" alt="" src="{{URL::asset('assets/landing-page/img/images/arrow_2.png') }}">
                <article id="hero-text">
                    <h1 class="mb-2 slide-from-bottom-element">Bayar Yuran Sekolah Dengan <span class="color-primary">Mudah</span></h1>
                    <p class="mb-2 slide-from-bottom-element" style="--transition-delay: 0.15s">Urus pembayaran sekolah anak anda dalam talian dengan
                        selamat dan cepat.</p>
                    <!-- From Uiverse.io by Creatlydev -->
                    <button  id="btn-sign-up"  class="btn btn-primary slide-from-bottom-element"  style="--transition-delay: 0.3s" >
                      <span class="btn__icon-wrapper">
                        <svg
                            viewBox="0 0 14 15"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                            class="btn__icon-svg"
                            width="10"
                        >
                          <path
                              d="M13.376 11.552l-.264-10.44-10.44-.24.024 2.28 6.96-.048L.2 12.56l1.488 1.488 9.432-9.432-.048 6.912 2.304.024z"
                              fill="currentColor"
                          ></path>
                        </svg>

                        <svg
                            viewBox="0 0 14 15"
                            fill="none"
                            width="10"
                            xmlns="http://www.w3.org/2000/svg"
                            class="btn__icon-svg btn__icon-svg--copy"
                        >
                          <path
                              d="M13.376 11.552l-.264-10.44-10.44-.24.024 2.28 6.96-.048L.2 12.56l1.488 1.488 9.432-9.432-.048 6.912 2.304.024z"
                              fill="currentColor"
                          ></path>
                        </svg>
                      </span>
                        <a href="/login">
                            Log Masuk
                        </a>
                    </button>

                </article>

                <img class="slide-from-bottom-element" style="--transition-delay: 0.45s" id="img-hero" src="{{ URL::asset('assets/landing-page/img/images/hero_student.png') }}" alt="Hero Section's Image includes a picture of a student">
            </div>
        </section>
        <!--feature section-->
        <section id="section-feature" aria-label="Feature Section" class="container">
            <div id="feature-wrapper">
                <div class="mb-1-2 section-heading">
                    <img alt="" class="h2-deco-left" src="{{ URL::asset('assets/landing-page/img/images/arrow_orange.png') }}" >
                    <h2 class="h2 text-center">Cabaran & Solusi</h2>
                </div>
                <p class="text-center text-subtitle mb-4"> Transformasi cara pembayaran yuran sekolah.</p>

                <div id="features">
                    <article class="feature-card slide-from-bottom-element">
                        <img class="img-fluid mb-1-2" src="{{ URL::asset('assets/landing-page/img/images/icon_online_payment.png') }}" alt="Icon for online payment">
                        <h3 class="mb-1">Ibu bapa perlu ke sekolah untuk bayar yuran</h3>
                        <p class="text-justify">Sistem ini membolehkan ibu bapa membuat pembayaran secara dalam talian tanpa perlu hadir ke sekolah. Proses lebih mudah, cepat dan fleksibel mengikut keselesaan ibu bapa.</p>
                    </article>

                    <article class="feature-card slide-from-bottom-element" style="--transition-delay: 0.15s">
                        <img class="img-fluid mb-1-2" src="{{ URL::asset('assets/landing-page/img/images/icon_tracking.png') }}" alt="Icon for easy tracking">
                        <h3 class="mb-1">Cikgu akan kumpul tunai yang banyak dan resit akan hilang</h3>
                        <p class="text-justify">Pembayaran tanpa tunai mengurangkan beban guru untuk mengurus wang fizikal. Semua transaksi direkodkan secara automatik dan selamat dalam sistem. Dengan ini, guru tidak perlu risau resit hilang.</p>
                    </article>

                    <article class="feature-card slide-from-bottom-element" style="--transition-delay: 0.3s">
                        <img class="img-fluid mb-1-2" src="{{ URL::asset('assets/landing-page/img/images/icon_verification.png') }}" alt="Icon for payments verification">
                        <h3 class="mb-1"> Cikgu perlu ambil masa untuk tulis resit secara manual</h3>
                        <p class="text-justify">Setiap transaksi akan disertakan dengan resit digital yang dijana secara automatik. Guru tidak perlu lagi tulis resit secara manual dan menjadikan proses tersebut lebih efisien serta kurang risiko kesilapan.</p>
                    </article>
                </div>
            </div>

        </section>
        <!-- showcase section-->
        <section id="section-showcase" aria-label="Application Showcase Section" class="container">
            <div id="showcase-wrapper">
                <div class="mb-1-2 section-heading">
                    <img alt="" class="h2-deco-right" src="{{ URL::asset('assets/landing-page/img/images/arrow_orange.png') }}" >
                    <h2 class="h2 text-center">Paparan Sistem</h2>
                </div>
                <p class="text-center text-subtitle mb-4"> Setiap fungsi direka untuk memudahkan urusan pembayaran yuran.</p>

                <div id="showcase-container">
                    <div id="showcase-buttons" >
                        <button id="btn-showcase-left" class="btn-showcase">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: scaleY(-1);msFilter:progid:DXImageTransform.Microsoft.BasicImage(rotation=2, mirror=1);"><path d="M12.707 17.293 8.414 13H18v-2H8.414l4.293-4.293-1.414-1.414L4.586 12l6.707 6.707z"></path></svg>
                        </button>

                        <button id="btn-showcase-right" class="btn-showcase">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: rotate(180deg);msFilter:progid:DXImageTransform.Microsoft.BasicImage(rotation=2);"><path d="M12.707 17.293 8.414 13H18v-2H8.414l4.293-4.293-1.414-1.414L4.586 12l6.707 6.707z"></path></svg>
                        </button>
                    </div>
                    <div id="showcase-screenshots">

                        <article class="showcase-screenshot slide-from-bottom-element">
                            <img src="{{ URL::asset('assets/landing-page/img/images/screenshot_mockup_1.svg') }}" alt="Screenshot of receipt for paid school fee">
                        </article>

                        <article class="showcase-screenshot">
                            <img src="{{ URL::asset('assets/landing-page/img/images/screenshot_mockup_2.svg') }}" alt="Screenshot of past receipts table">
                        </article>

                        <article class="showcase-screenshot">
                            <img src="{{ URL::asset('assets/landing-page/img/images/screenshot_mockup_3.svg') }}" alt="Screenshot of generated fee payment report">
                        </article>
                    </div>
                </div>

            </div>

        </section>
        {{--   tutorial section     --}}
        <section id="section-tutorial" aria-label="Tutorial on how to use the application section" class="container">
            <div id="tutorial-wrapper">
                <div class="mb-1-2 section-heading">
                    <img alt="" class="h2-deco-left" src="{{ URL::asset('assets/landing-page/img/images/arrow_orange.png') }}" >
                    <h2 class="h2 text-center">Cara Penggunaan Sistem</h2>
                </div>
                <p class="text-center text-subtitle mb-4"> Video langkah demi langkah untuk membantu anda menggunakan sistem tersebut.</p>
                    <div id="video-container" class="d-flex justify-content-center align-items-center slide-from-bottom-element">
                        <iframe id="iframe-tutorial"  src="https://www.youtube.com/embed/m5GUnMwcG28?loop=1&controls=1"  allowfullscreen>
                        </iframe>
                    </div>
                </div>
        </section>
        <!-- stats section-->
        <section id="section-stat" aria-label="Statistic Section">
            <div id="stat-wrapper">
                <article class="stat slide-from-bottom-element"  >
                    <div class="stat-number mb-1-2">{{number_format($organizationCount)}}</div>
                    <div class="stat-text">Jumlah Sekolah Berdaftar</div>
                </article>

                <article class="stat slide-from-bottom-element" style="--transition-delay: 0.15s">
                    <div class="stat-number mb-1-2">{{number_format($studentCount)}}</div>
                    <div class="stat-text">Jumlah Pelajar</div>
                </article>

                <article class="stat slide-from-bottom-element" style="--transition-delay: 0.15s">
                    <div class="stat-number mb-1-2">RM{{number_format($totalFee, 2)}}</div>
                    <div class="stat-text">Jumlah Keseluruhan Yuran Dibayar</div>
                </article>

                <article class="stat slide-from-bottom-element" >
                    <div class="stat-number mb-1-2">RM{{number_format($totalFeeThisYear, 2)}}</div>
                    <div class="stat-text">Jumlah Yuran Dibayar Tahun ini</div>
                </article>
            </div>
        </section>
        <!-- member section-->
        <section id="section-member" aria-label="Member Section" class="container">
            <div id="member-wrapper">
                <div class="section-heading">
                    <img alt="" class="h2-deco-right" src="{{ URL::asset('assets/landing-page/img/images/arrow_orange.png') }}" >
                    <h2 class="h2 text-center">Ahli-ahli Berdaftar</h2>
                </div>
                <p class="text-center text-subtitle mb-4">Terokai sekolah-sekolah di Malaysia yang telah berdaftar dengan platform kami serta Bank Islam dan menikmati kemudahan pembayaran yuran yang lebih mudah, pantas, dan selamat.</p>

                <form id="form-school">
                    <div class="text-center mb-1">Pilih Negeri & Daerah</div>
                    <div id="input-selects">
                        <select name="states" id="states" class="custom-select">
                            <option value="" selected disabled>Negeri</option>
                            <option value="all">Semua Negeri</option>

                        </select>
                        <select name="district" id="district" class="custom-select">
                            <option value="" selected disabled>Daerah</option>
                            <option value="all">Semua Daerah</option>

                        </select>
                        <button type="submit" id="btn-search">Cari</button>
                    </div>
                </form>

                <div class="text-bold text-center" id="member-count">{{$organization2->count()}} <span class="text-total">Sekolah (Jumlah):</span></div>

                <!--list of registered school members-->
                <div class="members">
                    @foreach($organization2 as $key=>$org)
                        <article class="member-card" data-state="{{$org->state}}" data-district="{{$org->district}}">
                        <div class="member-card-header">
                            <img src="{{ URL::asset('organization-picture/' . $org->organization_picture ) }}" alt="{{ $org->url_name }} logo">
                            <div class="member-info-wrapper">
                                <div class="member-info mb-1-2">
{{--                                    <img src="{{ URL::asset('assets/landing-page/img/images/icon_school.png') }}" alt="Icon of school">--}}
                                    <h3>{{ $org->nama }}</h3>
                                </div>
                                <div class="member-info member-info-stats mb-1-2">

                                    <div>

{{--                                            {{ $results[$org->id]['this_year']['completed_count'] }} <span class="text-small"> pelajar bayar yuran {{ date("Y") }}</span>--}}
                                        @php
                                            // Find the organization entry with the matching URL
                                            $matchedOrg = $organization2->firstWhere('url', $org->url_name);
                                            
                                            $tcountLatest = $org->data[0]['tcount'];
                                            $tcountYearLatest = $org->data[0]['year'];
                                            $tcountPrevious = $org->data[1]['tcount'];
                                            $tcountYearPrev = $org->data[1]['year'];

                                        @endphp

                                        @if ($tcountLatest > 0) {{ $tcountLatest }} <span class="text-small"> transaksi pada {{ $tcountYearLatest }}</span> @endif

                                    </div>
                                    <div>
                                        @if($tcountPrevious > 0) {{ $tcountPrevious }} <span class="text-small"> transaksi pada {{ $tcountYearPrev }}</span> @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="member-card-footer pt-3">
                            <button  class="btn btn-primary" >
                      <span class="btn__icon-wrapper">
                        <svg
                            viewBox="0 0 14 15"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                            class="btn__icon-svg"
                            width="10"
                        >
                          <path
                              d="M13.376 11.552l-.264-10.44-10.44-.24.024 2.28 6.96-.048L.2 12.56l1.488 1.488 9.432-9.432-.048 6.912 2.304.024z"
                              fill="currentColor"
                          ></path>
                        </svg>

                        <svg
                            viewBox="0 0 14 15"
                            fill="none"
                            width="10"
                            xmlns="http://www.w3.org/2000/svg"
                            class="btn__icon-svg btn__icon-svg--copy"
                        >
                          <path
                              d="M13.376 11.552l-.264-10.44-10.44-.24.024 2.28 6.96-.048L.2 12.56l1.488 1.488 9.432-9.432-.048 6.912 2.304.024z"
                              fill="currentColor"
                          ></path>
                        </svg>
                      </span>
                                <a href="/{{ $org->url_name }}">
                                    Log Masuk
                                </a>
                            </button>
                            {{--check whether the school has ongoing donations--}}
                            @if($organizationDonations->where("organization_id" , $org->id)->first())
                                <button class="btn-derma">
                                    <a href="/sumbangan/{{$organizationDonations->where("organization_id" , $org->id)->first()->url}}">Derma</a>
                                </button>
                            @endif
                        </div>
                    </article>

                    @endforeach
                </div>
            </div>
        </section>

        <!--organization-->
        <!--        <section aria-label="Organization Section" id="section-organization">-->
        <!--            <div id="organization-wrapper" class="container">-->
        <!--                <div class="mb-1-2 section-heading">-->
        <!--                    <img alt="" class="h2-deco-left" src="assets/images/arrow_orange.png" >-->
        <!--                    <h2 class="h2 text-center">Organisasi Kami</h2>-->
        <!--                </div>-->
        <!--                <p class="text-center text-subtitle mb-4">Temui pakar-pakar di sebalik platform ini.-->
        <!--                </p>-->

        <!--                <div id="officers">-->
        <!--                    <article class="officer slide-from-bottom-element" style="&#45;&#45;transition-delay: 0.3s">-->
        <!--                        <div class="officer-image">-->
        <!--                            <img src="./assets/images/CEO(edited).png" alt="Image of CEO for Direct Pay" class="d-block img-fluid mx-auto mb-1">-->
        <!--                        </div>-->
        <!--                        <h3 class="mb-1">Yahya Bin Ibrahim</h3>-->
        <!--                        <p>Chief Executive Officer</p>-->
        <!--                    </article>-->
        <!--                    <article class="officer slide-from-bottom-element" style="&#45;&#45;transition-delay: 0.15s">-->
        <!--                        <div class="officer-image">-->
        <!--                            <img src="./assets/images/COO(edited).png" alt="Image of COO for Direct Pay"class="d-block img-fluid mx-auto mb-1">-->
        <!--                        </div>-->
        <!--                        <h3 class="mb-1">Ts. Dr. Muhammad Haziq Lim Bin Abdullah</h3>-->
        <!--                        <p>Chief Operating Officer</p>-->
        <!--                    </article>-->
        <!--                    <article class="officer slide-from-bottom-element" >-->
        <!--                        <div class="officer-image">-->
        <!--                            <img src="./assets/images/CTO(edited).png" alt="Image of CTO for Direct Pay"class="d-block img-fluid mx-auto mb-1">-->
        <!--                        </div>-->
        <!--                        <h3 class="mb-1">Chuan Chuan You</h3>-->
        <!--                        <p>Chief Technology Officer</p>-->
        <!--                    </article>-->
        <!--                </div>-->
        <!--            </div>-->
        <!--        </section>-->
        <!--collaborators-->
        <section aria-label="Platform's Collaborators Section" id="section-collab">
            <div id="collab-wrapper" class="container">
                <div class="mb-1-2 section-heading">
                    <img alt="" class="h2-deco-left" src="{{ URL::asset('assets/landing-page/img/images/arrow_orange.png') }}" >
                    <h2 class="h2 text-center">Dengan Kerjasama</h2>
                </div>
                <p class="text-center text-subtitle mb-4">Laman web ini telah diakui dan disahkan selamat untuk digunakan.
                </p>

                <div id="collaborators">
                    <img class="slide-from-bottom-element" src="{{ URL::asset('assets/landing-page/img/images/paynet_logo.png') }}" alt="Logo of Paynet Malaysia">
                    <img style="--transition-delay: 0.15s"   class="slide-from-bottom-element"  src="{{ URL::asset('assets/landing-page/img/images/bank_islam_logo.png') }}" alt="Logo of Bank Islam">
                    <img style="--transition-delay: 0.3s"  class="slide-from-bottom-element"   src="{{ URL::asset('assets/landing-page/img/images/utem_logo.png') }}" alt="Logo of Universiti Teknikal Malaysia Melaka">
                </div>
            </div>
        </section>

        <section aria-label="FAQ Section" id="section-faq">
            <div id="faq-wrapper" class="container">
                <div class="mb-1-2 section-heading">
                    <img alt="" class="h2-deco-right" src="{{ URL::asset('assets/landing-page/img/images/arrow_orange.png') }}" >
                    <h2 class="h2 text-center">Soalan Lazim</h2>
                </div>
                <p class="text-center text-subtitle mb-4">Jawapan kepada persoalan yang sering ditanya oleh pengguna
                </p>

                <div id="accordion">
                    <div class="card slide-from-bottom-element">
                        <div class="card-header collapsed" id="headingOne" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            <div class="mb-0">
                                1.	Apakah bayaran yang dikenakan kepada sekolah untuk menggunakan laman web yuran ini?
                            </div>

                            <img class="faq-arrow" src="{{ URL::asset('assets/landing-page/img/images/chevron_down.png') }}" alt="Click to show the answer for this question">
                        </div>

                        <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                            <div class="card-body">
                                Kami tidak mengenakan sebarang bayaran kepada pihak PIBG sekolah. Walaubagaimanapun, setiap kali pembayaran dibuat oleh ibu bapa atau penjaga, sistem akan mengenakan caj sebanyak 50 sen.
                            </div>
                        </div>
                    </div>


                    <div class="card slide-from-bottom-element" style="--transition-delay: 0.15s" >
                        <div class="card-header collapsed" id="headingTwo" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                            <div class="mb-0">
                                2.	Apakah langkah yang PIBG sekolah perlu buat bagi membolehkan ibu bapa membayar yuran?
                            </div>

                            <img class="faq-arrow" src="{{ URL::asset('assets/landing-page/img/images/chevron_down.png') }}" alt="Click to show the answer for this question">
                        </div>

                        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
                            <div class="card-body">
                                Ibu bapa perlu login ke dalam laman web dengan menggunakan nombor kad pengenalan. Pihak sekolah akan daftarkan setiap ibu bapa sebelum ibu bapa dibenarkan untuk log masuk.
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </section>
        <!--contact us-->
        <!--        <section aria-label="Contact Us Form Section" id="section-contact">-->
        <!--            <div id="contact-wrapper" class="container">-->
        <!--                <img class="slide-from-bottom-element" src="./assets/images/contact_us.png" alt="Image of Customer Service">-->
        <!--                    <form class="slide-from-bottom-element" style="&#45;&#45;transition-delay: 0.15s"  action="#" method="post" id="form-contact">-->
        <!--                        <div class="mb-1-2 section-heading">-->
        <!--                            <img alt="" class="h2-deco-right" src="assets/images/arrow_orange.png" >-->
        <!--                            <h2 class="h2 text-center">Hubungi Kami</h2>-->
        <!--                        </div>-->
        <!--                        <p class="mb-2"> "Perlukan Bantuan? Mahu Demonstrasi Produk Secara Bersemuka?"<br>Hubungi pasukan mesra kami, dan kami akan menghubungi anda dalam masa 2 hari bekerja.-->
        <!--                        </p>-->
        <!--                        <div class="mb-1 col-2">-->
        <!--                            <div>-->
        <!--                                <label for="name">Nama Penuh</label>-->
        <!--                                <input type="text" placeholder="John Doe" name="name" id="name" class="form-control">-->
        <!--                            </div>-->
        <!--                            <div>-->
        <!--                                <label for="phone">Nombor Telefon</label>-->
        <!--                                <input type="text" placeholder="+6011234562"  name="phone" id="phone" class="form-control">-->
        <!--                            </div>-->
        <!--                        </div>-->
        <!--                        <div class="mb-1">-->
        <!--                            <label for="email">Email</label>-->
        <!--                            <input type="email" placeholder="john@gmail.com" name="email" id="email" class="form-control">-->
        <!--                        </div>-->

        <!--                        <div class="mb-3">-->
        <!--                            <label for="message">Mesej</label>-->
        <!--                            <textarea id="message" placeholder="Sebarang pertanyaan atau maklum balas untuk kami..."  name="message" class="form-control" rows="5"></textarea>-->
        <!--                        </div>-->
        <!--                        <button type="submit" id="btn-submit"  class="btn btn-primary" >-->
        <!--                      <span class="btn__icon-wrapper">-->
        <!--                        <svg-->
        <!--                                viewBox="0 0 14 15"-->
        <!--                                fill="none"-->
        <!--                                xmlns="http://www.w3.org/2000/svg"-->
        <!--                                class="btn__icon-svg"-->
        <!--                                width="10"-->
        <!--                        >-->
        <!--                          <path-->
        <!--                                  d="M13.376 11.552l-.264-10.44-10.44-.24.024 2.28 6.96-.048L.2 12.56l1.488 1.488 9.432-9.432-.048 6.912 2.304.024z"-->
        <!--                                  fill="currentColor"-->
        <!--                          ></path>-->
        <!--                        </svg>-->

        <!--                        <svg-->
        <!--                                viewBox="0 0 14 15"-->
        <!--                                fill="none"-->
        <!--                                width="10"-->
        <!--                                xmlns="http://www.w3.org/2000/svg"-->
        <!--                                class="btn__icon-svg btn__icon-svg&#45;&#45;copy"-->
        <!--                        >-->
        <!--                          <path-->
        <!--                                  d="M13.376 11.552l-.264-10.44-10.44-.24.024 2.28 6.96-.048L.2 12.56l1.488 1.488 9.432-9.432-.048 6.912 2.304.024z"-->
        <!--                                  fill="currentColor"-->
        <!--                          ></path>-->
        <!--                        </svg>-->
        <!--                      </span>-->
        <!--                            Hantar-->
        <!--                        </button>-->
        <!--                    </form>-->
        <!--            </div>-->
        <!--        </section>-->
    </main>

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

    @include('landing-page.footer-script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const organizations = JSON.parse(@json("$organization2"));
            const heroSlideFromBottomElements = document.querySelectorAll('#section-hero .slide-from-bottom-element');
            const featureSlideFromBottomElements = document.querySelectorAll('#section-feature .slide-from-bottom-element');
            const showcaseSlideFromBottomElements = document.querySelectorAll('#section-showcase .slide-from-bottom-element');
            const tutorialSlideFromBottomElements = document.querySelectorAll('#section-tutorial .slide-from-bottom-element');
            const statSlideFromBottomElements = document.querySelectorAll('#section-stat .slide-from-bottom-element');
            let memberSlideFromBottomElements = document.querySelectorAll('#section-member .slide-from-bottom-element');
            const organizationSlideFromBottomElements = document.querySelectorAll('#section-organization   .slide-from-bottom-element');
            const collabSlideFromBottomElements = document.querySelectorAll('#section-collab .slide-from-bottom-element');
            const faqSlideFromBottomElements = document.querySelectorAll('#section-faq .slide-from-bottom-element');
            const contactSlideFromBottomElements = document.querySelectorAll('#section-contact .slide-from-bottom-element');
            //   setup intersection observers
            const observerSlideBottomElement = new IntersectionObserver((entries) => {
                for (let entry of entries) {
                    if (entry.intersectionRatio > 0) {
                        entry.target.classList.add('slide-from-bottom');
                        //force the first feature to slide in as well
                        if (entry.target.classList.contains('member-card')) {
                            // Start auto-sliding when members are being displayed
                            // startAutoSlide();
                        }

                    }
                }
            });

            for (let el of heroSlideFromBottomElements) {
                observerSlideBottomElement.observe(el);
            }
            for (let el of featureSlideFromBottomElements) {
                observerSlideBottomElement.observe(el);
            }
            for (let el of showcaseSlideFromBottomElements) {
                observerSlideBottomElement.observe(el);
            }
            for (let el of tutorialSlideFromBottomElements) {
                observerSlideBottomElement.observe(el);
            }
            for (let el of statSlideFromBottomElements) {
                observerSlideBottomElement.observe(el);
            }
            for (let el of memberSlideFromBottomElements) {
                observerSlideBottomElement.observe(el);
            }
            for (let el of organizationSlideFromBottomElements) {
                observerSlideBottomElement.observe(el);
            }
            for (let el of collabSlideFromBottomElements) {
                observerSlideBottomElement.observe(el);
            }
            for (let el of faqSlideFromBottomElements) {
                observerSlideBottomElement.observe(el);
            }
            for (let el of contactSlideFromBottomElements) {
                observerSlideBottomElement.observe(el);
            }


            //     for auto slide for members
            // const membersContainer = document.querySelector('.members');
            // const memberCards = document.querySelectorAll('.member-card');
            // let isUserInteracting = false;
            // let autoSlideInterval;
            // let currentCardIndex = 0;
            //
            // // Function to get the current visible card index
            // // Function to get the current visible card index
            // function getCurrentVisibleCardIndex() {
            //     const containerScrollLeft = membersContainer.scrollLeft;
            //     const cardWidth = parseFloat(getComputedStyle(memberCards[0]).width);
            //     const cardGap = parseFloat(getComputedStyle(membersContainer).gap);
            //     const totalCardWidth = cardWidth + cardGap;
            //
            //     // Get container width to determine visible cards
            //     const containerWidth = membersContainer.clientWidth;
            //     const visibleCardsCount = Math.floor(containerWidth / totalCardWidth);
            //
            //     // Calculate the index based on scroll position
            //     let calculatedIndex = Math.round(containerScrollLeft / totalCardWidth);
            //     // Adjust for layouts with multiple visible cards
            //     if (visibleCardsCount > 1) {
            //         // If near the end, ensure we can still scroll to the last set of cards
            //         calculatedIndex = Math.min(
            //             calculatedIndex,
            //             memberCards.length - visibleCardsCount
            //         );
            //         if(visibleCardsCount === 2 && calculatedIndex === memberCards.length - 2){
            //             calculatedIndex++;
            //         }
            //     }
            //
            //     return calculatedIndex;
            // }
            //
            //
            // // Function to slide to a specific card
            // function slideToCard(index) {
            //     if (isUserInteracting) return;
            //
            //     // Ensure index is within bounds
            //     currentCardIndex = Math.max(0, Math.min(index, memberCards.length - 1));
            //
            //     // Scroll to the exact left position of the target card
            //     membersContainer.scrollTo({
            //         left: (parseFloat(getComputedStyle(memberCards[0]).width) * currentCardIndex),
            //         behavior: 'smooth'
            //     });
            // }
            //
            // // Function to slide to the next card
            // function slideToNextCard() {
            //     if (isUserInteracting) return;
            //
            //     // Update current index based on actual visible position
            //     currentCardIndex = getCurrentVisibleCardIndex();
            //     currentCardIndex++;
            //
            //     // Reset to first card if we've reached the end
            //     if (currentCardIndex >= memberCards.length) {
            //         currentCardIndex = 0;
            //     }
            //
            //     slideToCard(currentCardIndex);
            // }
            //
            // // Start automatic sliding
            // function startAutoSlide() {
            //     pauseAutoSlide(); // Clear any existing interval
            //
            //     // Update current index based on visible position before starting
            //     currentCardIndex = getCurrentVisibleCardIndex();
            //
            //     autoSlideInterval = setInterval(slideToNextCard, 3000); // Slide every 3 seconds
            // }
            //
            // // Pause automatic sliding
            // function pauseAutoSlide() {
            //     clearInterval(autoSlideInterval);
            // }
            //
            // // Event listeners to detect user interaction
            // membersContainer.addEventListener('mouseenter', () => {
            //     isUserInteracting = true;
            //     pauseAutoSlide();
            // });
            //
            // membersContainer.addEventListener('mouseleave', () => {
            //     isUserInteracting = false;
            //     startAutoSlide();
            // });
            //
            // // Touch events for mobile support
            // membersContainer.addEventListener('touchstart', () => {
            //     isUserInteracting = true;
            //     pauseAutoSlide();
            // });
            //
            // membersContainer.addEventListener('touchend', () => {
            //     isUserInteracting = false;
            //     startAutoSlide();
            // });
            //
            // // Detect when user starts scrolling manually
            // let scrollTimeout;
            // membersContainer.addEventListener('scroll', () => {
            //     // Detect manual scrolling
            //     isUserInteracting = true;
            //     pauseAutoSlide();
            //
            //     // Reset manual scrolling after a short delay
            //     clearTimeout(scrollTimeout);
            //     scrollTimeout = setTimeout(() => {
            //         isUserInteracting = false;
            //         startAutoSlide();
            //     }, 1000);
            // });
            //
            //

            //     for screenshots showcase
            // Get the necessary elements
            const showcaseContainer = document.getElementById('showcase-screenshots');
            const leftButton = document.getElementById('btn-showcase-left');
            const rightButton = document.getElementById('btn-showcase-right');
            const screenshots = document.querySelectorAll('.showcase-screenshot');

            // Set initial values
            let currentIndex = 0;
            const totalScreenshots = screenshots.length;
            let screenshotWidth; // Width of each screenshot (from your CSS)
            // Function to update navigation buttons
            function updateNavButtons() {
                // Update button states
                // if (currentIndex === 0) {
                //     leftButton.style.opacity = '0.5';
                //     leftButton.style.pointerEvents = 'none';
                // } else {
                leftButton.style.opacity = '1';
                leftButton.style.pointerEvents = 'auto';
                // }

                // if (currentIndex === totalScreenshots - 1) {
                //     rightButton.style.opacity = '0.5';
                //     rightButton.style.pointerEvents = 'none';
                // } else
                //
                // {
                rightButton.style.opacity = '1';
                rightButton.style.pointerEvents = 'auto';
            }

            // Initialize button states
            updateNavButtons();

            // Function to scroll to a specific screenshot
            function scrollToScreenshot(index) {
                screenshotWidth = parseInt(getComputedStyle(document.querySelector("#showcase-screenshots")).width);
                const scrollPosition = index * screenshotWidth;
                showcaseContainer.scrollTo({
                    left: scrollPosition,
                    behavior: 'smooth'
                });

                currentIndex = index;
                updateNavButtons();
            }

            // Add click event for left button
            leftButton.addEventListener('click', function () {
                if (currentIndex > 0) {
                    scrollToScreenshot(currentIndex - 1);
                } else if (currentIndex <= 0) {
                    scrollToScreenshot(currentIndex = totalScreenshots - 1);
                }
            });

            // Add click event for right button
            rightButton.addEventListener('click', function () {
                if (currentIndex < totalScreenshots - 1) {
                    scrollToScreenshot(currentIndex + 1);
                } else if (currentIndex >= totalScreenshots - 1) {
                    scrollToScreenshot(currentIndex = 0);
                }
            });

            // Optional: Add keyboard navigation
            document.addEventListener('keydown', function (event) {
                if (event.key === 'ArrowLeft') {
                    if (currentIndex > 0) {
                        scrollToScreenshot(currentIndex - 1);
                    }
                } else if (event.key === 'ArrowRight') {
                    if (currentIndex < totalScreenshots - 1) {
                        scrollToScreenshot(currentIndex + 1);
                    }
                }
            });

            // Add scroll event to detect when manual scrolling occurs
            showcaseContainer.addEventListener('scroll', function () {
                // Calculate which screenshot is currently in view
                const scrollPosition = showcaseContainer.scrollLeft;
                const newIndex = Math.round(scrollPosition / screenshotWidth);

                if (newIndex !== currentIndex) {
                    currentIndex = newIndex;
                    updateNavButtons();
                }
            });

            // Optional: Add swipe gesture support for mobile
            let touchStartX = 0;
            let touchEndX = 0;

            showcaseContainer.addEventListener('touchstart', function (event) {
                touchStartX = event.changedTouches[0].screenX;
            });

            showcaseContainer.addEventListener('touchend', function (event) {
                touchEndX = event.changedTouches[0].screenX;
                handleSwipe();
            });

            function handleSwipe() {
                const swipeThreshold = 50; // Minimum distance needed to register as a swipe

                if (touchEndX - touchStartX > swipeThreshold) {
                    // Swiped right
                    if (currentIndex > 0) {
                        scrollToScreenshot(currentIndex - 1);
                    }
                }

                if (touchStartX - touchEndX > swipeThreshold) {
                    // Swiped left
                    if (currentIndex < totalScreenshots - 1) {
                        scrollToScreenshot(currentIndex + 1);
                    }
                }
            }

// for schools filtering
// Create objects to count organizations by state and district
            const stateCount = {};
            const districtsByState = {};

// Add pagination variables
            const cardsPerPage = 6;
            let currentPage = 1;
            let filteredCards = [];
            let isFirstLoad = true;
            // Global variables for state and district
            let userState = null;
            let userDistrict = null;

// Process organizations
            organizations.forEach(org => {
                // Count by state
                if (org.state) {
                    stateCount[org.state] = (stateCount[org.state] || 0) + 1;

                    // Organize districts by state
                    if (!districtsByState[org.state]) {
                        districtsByState[org.state] = {};
                    }

                    // Count by district within state
                    if (org.district) {
                        districtsByState[org.state][org.district] =
                            (districtsByState[org.state][org.district] || 0) + 1;
                    }
                }
            });

// Populate states dropdown
            const statesSelect = document.getElementById('states');
            Object.keys(stateCount).sort().forEach(state => {
                const option = document.createElement('option');
                option.value = state;
                option.textContent = `${state} (${stateCount[state]})`;
                statesSelect.appendChild(option);
            });

// Initially disable district dropdown
            const districtSelect = document.getElementById('district');
            districtSelect.disabled = true;

// Handle state selection to populate districts
            statesSelect.addEventListener('change', function () {
                const selectedState = this.value;

                // Clear previous district options except the first two (default + "All Districts")
                while (districtSelect.options.length > 2) {
                    districtSelect.remove(2);
                }

                if (selectedState !== 'all' && districtsByState[selectedState]) {
                    Object.keys(districtsByState[selectedState]).sort().forEach(district => {
                        const option = document.createElement('option');
                        option.value = district;
                        option.textContent = `${district} (${districtsByState[selectedState][district]})`;
                        districtSelect.appendChild(option);
                    });

                    districtSelect.disabled = false;
                } else {
                    // If "All States" is selected, disable district filter
                    districtSelect.disabled = true;
                }

                // Reset to first page when filter changes
                currentPage = 1;
                // filterOrganizations(selectedState, districtSelect.value);
            });

// Form submission handler
            document.getElementById('form-school').addEventListener('submit', function (e) {
                e.preventDefault();
                // Add your filter logic here
                const selectedState = document.getElementById('states').value;
                const selectedDistrict = document.getElementById('district').value;

                // Reset to first page when filter is applied
                currentPage = 1;

                // Filter your organizations based on selections
                filterOrganizations(selectedState, selectedDistrict);
            });

            function filterOrganizations(state, district) {
                if (!state) {
                    return;
                }

                const allOrgs = document.querySelectorAll('.member-card');
                let schoolCount = 0;
                filteredCards = [];

                allOrgs.forEach(org => {
                    const orgState = org.getAttribute('data-state');
                    const orgDistrict = org.getAttribute('data-district');

                    const stateMatch = state === 'all' || orgState === state;
                    const districtMatch = district === 'all' || district === '' || orgDistrict === district;

                    if (stateMatch && districtMatch) {
                        filteredCards.push(org);
                        schoolCount++;
                    }

                    // Hide all cards initially
                    org.style.display = 'none';
                });

                // Update school count display
                const resultCount = document.querySelector("#member-count");
                resultCount.innerHTML = `${schoolCount}`;
                const span = document.createElement("span");
                span.innerHTML = " Sekolah (Jumlah):";
                span.classList.add("text-total");
                resultCount.append(span);

                // Update pagination
                updatePagination();

                // Show cards for current page
                displayCurrentPageCards();
            }

// Function to display cards for the current page
            function displayCurrentPageCards() {
                const startIndex = (currentPage - 1) * cardsPerPage;
                const endIndex = Math.min(startIndex + cardsPerPage, filteredCards.length);

                // Hide all cards first
                document.querySelectorAll('.member-card').forEach(card => {
                    card.style.display = 'none';
                });

                // Show only cards for current page
                for (let i = startIndex; i < endIndex; i++) {
                    filteredCards[i].style.display = '';
                    filteredCards[i].classList.add("slide-from-bottom-element");
                    filteredCards[i].style.setProperty("--transition-delay", `${.05 * i}s`);
                }
                memberSlideFromBottomElements = document.querySelectorAll('#section-member .slide-from-bottom-element');
                for (let el of memberSlideFromBottomElements) {
                    observerSlideBottomElement.observe(el);
                }

                //move to the top of the container when current screen size is smaller than 1000
                if (window.innerWidth <= 1000 && !isFirstLoad) {
                    const target = document.querySelector("#member-count");
                    const offsetTop = target.getBoundingClientRect().top + window.scrollY;

                    window.scrollTo({
                        top: offsetTop,
                        behavior: 'smooth'
                    });
                }

                isFirstLoad = false;
            }

// Function to create and update pagination controls
            function updatePagination() {
                const totalPages = Math.ceil(filteredCards.length / cardsPerPage);

                // Get or create pagination container
                let paginationContainer = document.querySelector('#school-pagination');
                if (!paginationContainer) {
                    paginationContainer = document.createElement('div');
                    paginationContainer.id = 'school-pagination';
                    paginationContainer.className = 'mt-4';

                    // Insert after members container
                    const membersContainer = document.querySelector('.members');
                    membersContainer.parentNode.insertBefore(paginationContainer, membersContainer.nextSibling);
                }

                // Clear previous pagination
                paginationContainer.innerHTML = '';

                // Don't show pagination if only one page or no results
                if (totalPages <= 1) {
                    return;
                }

                // Create Bootstrap 4 pagination
                const nav = document.createElement('nav');
                const ul = document.createElement('ul');
                ul.className = 'pagination justify-content-center';

                // Previous button
                const prevLi = document.createElement('li');
                prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
                const prevA = document.createElement('a');
                prevA.className = 'page-link';
                prevA.href = '#';
                prevA.textContent = 'Sebelumnya';
                prevA.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (currentPage > 1) {
                        currentPage--;
                        displayCurrentPageCards();
                        updatePagination();
                    }
                });
                prevLi.appendChild(prevA);
                ul.appendChild(prevLi);

                // Page numbers
                // Determine which page numbers to show
                let startPage = Math.max(1, currentPage - 2);
                let endPage = Math.min(totalPages, startPage + 4);

                if (endPage - startPage < 4) {
                    startPage = Math.max(1, endPage - 4);
                }

                for (let i = startPage; i <= endPage; i++) {
                    const pageLi = document.createElement('li');
                    pageLi.className = `page-item ${i === currentPage ? 'active' : ''}`;

                    const pageA = document.createElement('a');
                    pageA.className = 'page-link';
                    pageA.href = '#';
                    pageA.textContent = i;

                    pageA.addEventListener('click', (e) => {
                        e.preventDefault();
                        currentPage = i;
                        displayCurrentPageCards();
                        updatePagination();
                    });

                    pageLi.appendChild(pageA);
                    ul.appendChild(pageLi);
                }

                // Next button
                const nextLi = document.createElement('li');
                nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
                const nextA = document.createElement('a');
                nextA.className = 'page-link';
                nextA.href = '#';
                nextA.textContent = 'Seterusnya';
                nextA.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (currentPage < totalPages) {
                        currentPage++;
                        displayCurrentPageCards();
                        updatePagination();
                    }
                });
                nextLi.appendChild(nextA);
                ul.appendChild(nextLi);

                nav.appendChild(ul);
                paginationContainer.appendChild(nav);
            }

            // Update layout for pagination view
            const membersContainer = document.querySelector('.members');
            membersContainer.style.display = 'grid';
            membersContainer.style.gridTemplateColumns = 'repeat(auto-fill, minmax(min(100%, 350px), 1fr))';

            // Apply initial filter and pagination
            const allOrgs = document.querySelectorAll('.member-card');
            filteredCards = Array.from(allOrgs);
            updatePagination();
            displayCurrentPageCards();

            // Update school count
            const resultCount = document.querySelector("#member-count");
            if (resultCount) {
                resultCount.innerHTML = `${filteredCards.length}`;
                const span = document.createElement("span");
                span.innerHTML = " Sekolah (Jumlah):";
                span.classList.add("text-total");
                resultCount.append(span);
            }


            navigator.geolocation.getCurrentPosition(
                function (position) {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;
                    // Step 2: Use reverse geocoding
                    getLocationDetails(lat, lon);
                },
                function (error) {
                    console.error("Error getting location:", error);
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                },
            );

            function getLocationDetails(lat, lon) {
                // Add zoom parameter to get more detailed address information
                fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lon}&format=json&zoom=18&accept-language=ms`)
                    .then(response => response.json())
                    .then(data => {
                        // console.log("Full address data:", data);

                        const address = data.address;

                        // Try various fields that might contain district information
                        const district = address.county ||
                            address.city_district ||
                            address.district ||
                            address.suburb ||
                            address.neighbourhood ||
                            address.town ||
                            address.city;

                        const state = address.state || address.province;

                        // console.log("District:", district);
                        // console.log("State:", state);

                        userState = state ?? null;
                        userDistrict = district ?? null;

                        //check if there are any states matched with the current user location
                        const statesSelect = document.getElementById('states');

                        if (userState && statesSelect) {
                            for (const option of statesSelect.options) {
                                if (option.value === userState) {
                                    option.selected = true;
                                    isFirstLoad = true;
                                    const event = new Event('change');
                                    statesSelect.dispatchEvent(event);
                                    break;
                                }
                            }
                        }
                        //check if there are any states matched with the current user location
                        if (userDistrict && districtSelect) {
                            for (const option of districtSelect.options) {
                                if (option.value === userDistrict) {
                                    option.selected = true;
                                    console.log("true");
                                    break;
                                }
                            }
                        }
                        //trigger submit
                        const event2 = new Event('submit');
                        document.getElementById('form-school').dispatchEvent(event2);

                    })
                    .catch(error => console.error("Geocoding error:", error));
            }

        });
    </script>
</body>
</html>
