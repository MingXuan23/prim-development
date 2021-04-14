<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title">Main</li>

                <li>
                    <a href="/" class="waves-effect">
                        <i class="ti-home"></i>
                        {{-- <span class="badge badge-pill badge-primary float-right">2</span> --}}
                        <span>Dashboard</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('organization.index') }}" class=" waves-effect">
                        <i class="mdi mdi-account-group"></i>
                        <span>Organisasi</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('donate.index') }}" class=" waves-effect">
                        <i class="fas fa-money-bill-alt"></i>
                        <span>Urus Derma</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('donate.organizationlist') }}" class=" waves-effect">
                        <i class="fas fa-hand-holding-heart"></i>
                        <span>Derma</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('historydonor') }}" class=" waves-effect">
                        <i class="mdi mdi-history"></i>
                        <span>Sejarah Derma</span>
                    </a>
                </li>

                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="fas fa-check"></i>
                        <span>Peringatan Derma</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="true">
                        <li><a href="{{ route('reminder.index') }}">Pengurusan Peringatan</a></li>
                    </ul>
                </li>

                <li>
                    <a href="{{ route('activity.index') }}" class="waves-effect">
                        <i class="mdi mdi-format-list-checks"></i>
                        <span>Aktiviti</span>
                    </a>
                </li>

                @role('Jaim')
                <li>
                    <a href="{{ route('jaim.index') }}" class=" waves-effect">
                        <i class="ti-clipboard"></i>
                        <span>JAIM</span>
                    </a>
                </li>
                @endrole

                @role('Superadmin|Ibu|Bapa|Penjaga')
                <li>
                    <a href="{{ route('parent.index') }}" class=" waves-effect">
                        <i class="fas fa-child"></i>
                        <span>Carian Tanggungan</span>
                    </a>
                </li>
                @endrole

                @role('Superadmin|Pentadbir|Guru')
                <li>
                    <a href="{{ route('school.index') }}" class=" waves-effect">
                        <i class="fas fa-school"></i>
                        <span>Sekolah</span>
                    </a>
                </li>
                @endrole


                @role('Superadmin|Pentadbir|Guru')
                <li>
                    <a href="{{ route('teacher.index') }}" class=" waves-effect">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span>Guru</span>
                    </a>
                </li>
                @endrole


                @role('Superadmin|Pentadbir|Guru')
                <li>
                    <a href="{{ route('class.index') }}" class=" waves-effect">
                        <i class="mdi mdi-google-classroom"></i>
                        <span>Kelas</span>
                    </a>
                </li>
                @endrole
                

                @role('Superadmin|Pentadbir|Guru')
                <li>
                    <a href="{{ route('student.index') }}" class=" waves-effect">
                        <i class="fas fa-user-graduate"></i>
                        <span>Pelajar</span>
                    </a>
                </li>

                @endrole
                
                @role('Superadmin|Pentadbir|Guru')
                <li>
                    <a href="{{ route('fees.index') }}" class=" waves-effect">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <span>Yuran</span>
                    </a>
                </li>
                @endrole


                @role('Superadmin|Pentadbir|Guru')
                <li>
                    <a href="{{ route('parentpay') }}" class=" waves-effect">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <span>Yuran - ibubapa (bayar)</span>
                    </a>
                </li>
                @endrole


                @role('Superadmin|Pentadbir|Guru')
                <li>
                    <a href="{{ route('chat-user') }}" class=" waves-effect">
                        <i class="mdi mdi-chat-outline"></i>
                        <span>Chat</span>
                    </a>
                </li>
                @endrole

                @role('Superadmin|Ibu|Bapa|Penjaga')
                <li>
                    <a href="{{ route('billIndex') }}" class=" waves-effect">
                        <i class="mdi"></i>
                        <span>Bill Design</span>
                    </a>
                </li>
                @endrole


                <!-- <li>
                    <a href="" class=" waves-effect">
                        <i class="ti-clipboard"></i>
                        <span>Derma</span>
                    </a>
                </li> -->

            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->