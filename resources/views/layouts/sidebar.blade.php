<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title">Main</li>

                <li>
                    <a href="/home" class="waves-effect">
                        <i class="ti-home"></i>
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
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="fas fa-hand-holding-heart"></i>
                        <span>Derma</span>
                    </a>
                    <ul class="sub-menu mm-collapse" aria-expanded="false">
                        <li>
                            <a href="{{ route('donation.index') }}" class=" waves-effect">
                                <i class="fas fa-user-cog"></i>
                                <span>Urus Derma</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('donate.index') }}" class=" waves-effect">
                                <i class="fas fa-hand-holding-heart"></i>
                                <span>Derma</span>
                            </a>
                        </li>

                        @role('Superadmin')
                        <li>
                            <a href="{{ route('donate.donor_history') }}" class=" waves-effect">
                                <i class="ti-clipboard"></i>
                                <span>Sejarah Derma</span>
                            </a>
                        </li>
                        @endrole
                    </ul>
                </li>


                @role('Jaim')
                <li>
                    <a href="{{ route('jaim.index') }}" class=" waves-effect">
                        <i class="ti-clipboard"></i>
                        <span>JAIM</span>
                    </a>
                </li>
                @endrole

                @role('Superadmin|Admin Polimas')
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="fas fa-school"></i>
                        <span>Politeknik</span>
                    </a>

                    <ul class="sub-menu mm-collapse" aria-expanded="false">
                        <li>
                            <a href="{{ route('polimas.student') }}" class=" waves-effect">
                                <i class="fas fa-user-graduate"></i>
                                <span>Laporan Pelajar</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('polimas.batch') }}" class=" waves-effect">
                                <i class="mdi mdi-google-classroom"></i>
                                <span>Batch</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endrole

                <!-- yuqin add penjaga and warden -->
                @role('Superadmin|Pentadbir|Guru|Penjaga|Warden')

                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="fas fa-school"></i>
                        <span>Sekolah</span>
                    </a>

                    <ul class="sub-menu mm-collapse" aria-expanded="false">
                        @role('Superadmin|Pentadbir')
                        <li>
                            <a href="{{ route('teacher.index') }}" class=" waves-effect">
                                <i class="fas fa-chalkboard-teacher"></i>
                                <span>Guru</span>
                            </a>
                        </li>
                        @endrole


                        @role('Superadmin|Pentadbir')
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
                            <a href="{{ route('parent.index') }}" class=" waves-effect">
                                <i class="fas fa-user-friends"></i>
                                <span>Ibu Bapa</span>
                            </a>
                        </li>

                        @endrole


                        @role('Superadmin|Pentadbir|Guru|Warden|Penjaga')
                        <li>
                            <a href="javascript: void(0);" class="has-arrow waves-effect">
                                <i class="fas fa-hotel"></i>
                                <span>Asrama</span>
                            </a>
                            <ul class="sub-menu mm-collapse" aria-expanded="false">
                                @role('Superadmin|Pentadbir|Guru|Warden|Penjaga')
                                <li>
                                    <!-- need to change to index later -->
                                    <a href="{{ route('dorm.index') }}" class=" waves-effect">
                                        <i class="fas fa-book"></i>
                                        <span>Permintaan</span>
                                    </a>
                                </li>
                                @endrole

                                @role('Superadmin|Pentadbir|Guru|Warden')
                                <li>
                                    <a href="{{ route('dorm.indexOuting') }}" class=" waves-effect">
                                        <i class="fas fa-bus"></i>
                                        <span>Outing</span>
                                    </a>
                                </li>
                                @endrole

                                @role('Superadmin|Pentadbir|Guru|Warden')
                                <li>
                                    <a href="{{ route('teacher.wardenindex') }}" class=" waves-effect">
                                        <i class="	far fa-address-card"></i>
                                        <span>Warden</span>
                                    </a>
                                </li>
                                @endrole

                                @role('Superadmin|Pentadbir|Guru|Warden')
                                <li>
                                    <a href="{{ route('dorm.indexDorm') }}" class=" waves-effect">
                                        <i class="fas fa-building"></i>
                                        <span>Management</span>
                                    </a>
                                </li>
                                @endrole
                            </ul>
                        </li>
                        @endrole
                    </ul>
                </li>
                @endrole

                @role('Superadmin|Pentadbir|Guru|Penjaga')

                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <span>Yuran</span>
                    </a>
                    <ul class="sub-menu mm-collapse" aria-expanded="false">
                        @role('Superadmin|Penjaga')
                        <li>
                            <a href="{{ route('dependent_fees') }}" class=" waves-effect">
                                <i class="far fa-credit-card"></i>
                                <span>Bayar</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('parent.dependent') }}" class=" waves-effect">
                                <i class="fas fa-child"></i>
                                <span>Carian Tanggungan</span>
                            </a>
                        </li>
                        @endrole

                        @role('Superadmin|Pentadbir')
                        <li>
                            <a href="{{ route('fees.report') }}" class=" waves-effect" aria-expanded="true">
                                <i class="fas fa-list-ul"></i>
                                <span>Laporan</span>
                            </a>
                        </li>
                        @endrole

                        @role('Superadmin|Pentadbir|Guru')
                        <li>
                            <a href="{{ route('fees.category.report') }}" class=" waves-effect">
                                <i class="ti-clipboard"></i>
                                <span>Laporan Yuran</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('fees.searchreport') }}" class=" waves-effect" aria-expanded="true">
                                <i class="fas fa-search"></i>
                                <span>Laporan Kelas</span>
                            </a>
                        </li>
                        @endrole

                        @role('Superadmin|Pentadbir|Guru|Penjaga')

                        <li>
                            <a href="{{ route('parent.fees.history') }}" class=" waves-effect">
                                <i class="ti-clipboard"></i>
                                <span>Sejarah Bayaran</span>
                            </a>
                        </li>
                        @endrole

                        @role('Superadmin|Pentadbir')
                        <li>
                            <a href="{{ route('fees.A') }}" class=" waves-effect" aria-expanded="true">
                                <i class="fas fa-user-cog"></i>
                                <span>Kategori A</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('fees.B') }}" class=" waves-effect" aria-expanded="true">
                                <i class="fas fa-user-cog"></i>
                                <span>Kategori B</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('fees.C') }}" class=" waves-effect" aria-expanded="true">
                                <i class="fas fa-user-cog"></i>
                                <span>Kategori C</span>
                            </a>
                        </li>
                        @endrole

                    </ul>
                </li>
                @endrole
            </ul>
        </div>
    </div>
</div>