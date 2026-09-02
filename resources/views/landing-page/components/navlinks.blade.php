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
                <li class="{{ Request::is('*') ? 'current-menu-item' : '' }}">
                    <a href="/">Utama</a>
                </li>
                <!-- <li class="current-menu-item"><a href="#" style="font-size: 19px">Derma</a></li> -->
                <li class="{{ Request::is('derma*') ? 'menu-item-has-children current-menu-item' : 'menu-item' }}">
                    @if (Request::is('derma'))
                        <a href="#">Derma</a>
                        <ul class="sub-menu">
                            <li><a href="#organization">Poster Derma</a></li>
                            <li><a href="#social-media">Sedekah Subuh</a></li>
                        </ul>
                    @else
                        <a href="/derma">Derma</a>
                    @endif
                </li>
                <li class="{{ Request::is('yuran*') ? 'current-menu-item' : '' }}">
                    <a href="/yuran">Yuran</a>
                </li>
                <li class="menu-item-has-children">
                    <a href="#">Perniagaan</a>
                    <ul class="sub-menu">
                        <li><a href="{{ route('merchant-product.index') }}">Get&Go</a></li>
                        <li><a href="{{ route('homestay.homePage') }}">Homestay</a></li>
                    </ul>
                </li>
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
