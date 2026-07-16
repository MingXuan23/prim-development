<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">

                <li>
                    <a href="{{ route('social-media.index') }}" class="waves-effect">
                        <i class="ti-home"></i>
                        <span>Laman Utama</span>
                    </a>
                </li>
                <li>
                    <a class=" waves-effect">
                        <i class="fas fa-play-circle"></i>
                        <span>Terokai</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('social-media.donationPostsIndex') }}" class=" waves-effect">
                        <i class="fas fa-hand-holding-heart"></i>
                        <span>Derma</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('social-media.searchUserIndex') }}" class="waves-effect">
                        <i class="fas fa-search"></i>
                        <span>Cari Pengguna</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('social-media.saves') }}" class="waves-effect">
                        <i class="fas fa-bookmark"></i>
                        <span>Simpan</span>
                    </a>
                </li>
                <!-- <li>
                    <a class="waves-effect">
                        <i class="far fa-bell"></i>
                        <span>Notifikasi</span>
                    </a>
                </li>
                <li>
                    <a class="waves-effect">
                        <i class="far fa-envelope"></i>
                        <span>Mesej</span>
                    </a>
                </li> -->
            </ul>

        </div>

        <div class="text-center">
            <a href="{{ route('social-media.createPost') }}" class="btn btn-primary w-75"><i class="fas fa-plus"></i> Buat Post</a>
        </div>
    </div>
</div>