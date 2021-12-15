@yield('css')

<link rel="shortcut icon" href="{{ URL::asset('assets/images/logo/fav-logo-prim.png')}}">
<!-- bootstrap -->
<link rel="stylesheet" href="{{ URL::asset('assets/landing-page/css/bootstrap.min.css') }}">
<!-- icofont -->
<link rel="stylesheet" href="{{ URL::asset('assets/landing-page/css/fontawesome.5.7.2.css') }}">
<!-- flaticon -->
<link rel="stylesheet" href="{{ URL::asset('assets/landing-page/css/flaticon.css') }}">
<link rel="stylesheet" href="{{ URL::asset('assets/landing-page/css/flaticon3.css') }}">
<!-- animate.css -->
<link rel="stylesheet" href="{{ URL::asset('assets/landing-page/css/animate.css') }}">
<!-- Owl Carousel -->
<link rel="stylesheet" href="{{ URL::asset('assets/landing-page/css/owl.carousel.min.css') }}">
<!-- magnific popup -->
<link rel="stylesheet" href="{{ URL::asset('assets/libs/magnific-popup/magnific-popup.min.css') }}">
<!-- stylesheet -->
<link rel="old stylesheet" href="{{ URL::asset('assets/landing-page/css/style.css') }}">
<!-- responsive -->
<link rel="stylesheet" href="{{ URL::asset('assets/landing-page/css/responsive.css') }}">
<!-- Slick css -->
<link rel="stylesheet" href="{{ URL::asset('assets/libs/slick/slick.css') }}">
<!-- Default CSS -->
<link rel="stylesheet" href="{{ URL::asset('assets/css/default.css') }}">

<!-- Header CSS -->
<link rel="stylesheet" href="{{ URL::asset('assets/landing-page/scss/sections/_header.scss') }}">

<link href="{{ URL::asset('assets/libs/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />


@include('layouts.datatable')
@include('layouts.datatable-responsive')

<style>
    .btn-donation {
        color: white;
        width: 100%;
        height: 100%;
        line-height: 20px;
        /* padding-top: 5px;
        padding-bottom: 5px; */
        padding: 16px 12px;
        margin-bottom: 8px;
    }

    .btn-donation2 {
        color: white;
        width: 100%;
        height: 100%;
        line-height: 20px;
        /* margin-top: 5px; */
        /* padding: 16px 24px;
         */
         padding: 16px 12px;
        margin-bottom: 8px;
        /* padding-top: 5px;
        padding-bottom: 5px; */
        background-color: #852aff;
    }

    .nav-item {
        width: 260px;
        border: none !important;
        margin: 0px 6.5px 10px !important;
    }

    .donation-poster {
        width: 320px;
        height: 450px;
    }

    .carousel{
        margin: auto;
        -webkit-overflow-scrolling:touch;
    }

    .carousel .card {
        margin: 0 10px 10px 0;
    }

    .carousel .owl-item.active:last-child .card {
        margin-right: 0;
    }

    .owl-controls {
        position: relative;
        margin-top: 30px;
        width: 100vw;
        height: 20px;

    }

    .owl-controls .owl-dots {
        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: center;
    }

    .owl-controls .owl-dots .owl-dot {
        margin: 0 5px;
    }

    .owl-controls .owl-dots .owl-dot span {
        width: 20px;
        height: 20px;
        background-color: #eee;
        color: darkgray;
        border-radius: 50%;
        display: block;
    }

    .owl-controls .owl-dots .owl-dot.active span {
        background-color: #852aff;
    }

    .header-poster{
        /* width: 230px;
        height: 625px; */
        width: 320px;
        height: 450px;
    }

    .header-area {
        padding: 140px 0 0 0;
        background-color: #fff;
        position: relative;
    }
</style>