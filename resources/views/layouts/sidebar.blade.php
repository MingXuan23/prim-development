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

                        {{-- @role('Superadmin|Admin LHDN')
                        <li>
                            <a href="{{ route('lhdn.index') }}" class=" waves-effect">
                                <i class="fas fa-donate"></i>
                                <span>Derma LHDN</span>
                            </a>
                        </li>
                        @endrole --}}
                        
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
                @role('Superadmin|Pentadbir|Guru|Penjaga|Warden|Guard')

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

                        @role('Superadmin|Pentadbir|Admin|Guru|Warden|Penjaga|Guard')
                        <li>
                            <a href="javascript: void(0);" class="has-arrow waves-effect">
                                <i class="fas fa-book"></i>
                                <span>Permohonan</span>
                            </a>

                            <ul class="sub-menu mm-collapse" aria-expanded="false">
                                @role('Superadmin')
                                <li>
                                    <!-- need to change to index later -->
                                    <a href="{{ route('dorm.indexRequest', 1) }}" class=" waves-effect">
                                        <i class="fas fa-search"></i>
                                        <span>Superadmin View</span>
                                    </a>
                                </li>
                                @endrole

                                @role('Admin')
                                <li>
                                    <!-- need to change to index later -->
                                    <a href="{{ route('dorm.indexRequest', 2) }}" class=" waves-effect">
                                        <!-- <i class="fas fa-user-friends"></i> -->
                                        <i class="fas fa-user-cog"></i>

                                        <span>Paparan Admin</span>
                                    </a>
                                </li>
                                @endrole

                                @role('Pentadbir')
                                <li>
                                    <!-- need to change to index later -->
                                    <a href="{{ route('dorm.indexRequest', 4) }}" class=" waves-effect">
                                        <!-- <i class="fas fa-list"></i> -->
                                        <i class="fas fa-user-cog"></i>
                                        <span>Paparan Pentadbir</span>
                                    </a>
                                </li>
                                @endrole

                                @role('Guru')
                                <li>
                                    <!-- need to change to index later -->
                                    <a href="{{ route('dorm.indexRequest', 5) }}" class=" waves-effect">
                                        <i class="fas fa-chalkboard-teacher"></i>
                                        <!-- <span>Permintaan(Guru)</span> -->
                                        <span>Paparan Guru</span>
                                    </a>
                                </li>
                                @endrole

                                @role('Penjaga')
                                <li>
                                    <!-- need to change to index later -->
                                    <a href="{{ route('dorm.indexRequest', 6) }}" class=" waves-effect">
                                        <i class="fas fa-user-friends"></i>
                                        <!-- <span>Permintaan Anak</span> -->
                                        <span>Paparan Penjaga</span>
                                    </a>
                                </li>
                                @endrole

                                @role('Warden')
                                <li>
                                    <!-- need to change to index later -->
                                    <a href="{{ route('dorm.indexRequest', 7) }}" class=" waves-effect">
                                        <!-- <i class="fas fa-book"></i>
                                            <span>Semak Permintaan</span> -->
                                        <!-- <i class="fas fa-id-card"></i> -->
                                        <i class="fas fa-user-lock"></i>
                                        <span>Paparan Warden</span>
                                    </a>
                                </li>
                                @endrole

                                @role('Guard')
                                <li>
                                    <!-- need to change to index later -->
                                    <a href="{{ route('dorm.indexRequest', 8) }}" class=" waves-effect">
                                        <!-- <i class="fas fa-book"></i>
                                            <span>Permintaan</span> -->
                                        <i class="fas fa-user-shield"></i>
                                        <span>Paparan Guard</span>
                                    </a>
                                </li>
                                @endrole
                            </ul>
                        </li>
                        @endrole

                        @role('Superadmin|Pentadbir|Guru|Warden|Penjaga|Guard')
                        <li>
                            <a href="javascript: void(0);" class="has-arrow waves-effect">
                                <i class="fas fa-hotel"></i>
                                <span>Asrama</span>
                            </a>
                            <ul class="sub-menu mm-collapse" aria-expanded="false">
                                <!-- used to be Permintaan -->


                                @role('Superadmin|Pentadbir|Guru|Warden')
                                <li>
                                    <a href="{{ route('dorm.indexReasonOuting') }}" class=" waves-effect">
                                        <i class="fas fa-bus-alt"></i>
                                        <span>Sebab Permintaan</span>
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

                                @role('Superadmin|Pentadbir|Guru|Warden|Guard')
                                <li>
                                    <a href="{{ route('teacher.perananindex') }}" class=" waves-effect">
                                        <i class="far fa-address-card"></i>
                                        <span>Peranan</span>
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

                                @role('Superadmin|Pentadbir|Guru|Warden')
                                <li>
                                    <a href="{{ route('dorm.indexStudentlist') }}" class=" waves-effect">
                                        <i class="fas fa-user-cog"></i>
                                        <span>Student List</span>
                                    </a>
                                </li>
                                @endrole
                            </ul>
                        </li>
                        @endrole
                        @role('Superadmin|Pentadbir|Guru|Warden')
                        <li>
                            <a href="{{ route('dorm.indexReportAll') }}" class=" waves-effect">
                                <i class="fas fa-list-ul"></i>
                                <span>Laporan</span>
                            </a>
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

                        {{-- <li>
                            <a href="{{ route('parent.dependent') }}" class=" waves-effect">
                        <i class="fas fa-child"></i>
                        <span>Carian Tanggungan</span>
                        </a>
                </li> --}}
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