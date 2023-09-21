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
                @unless(auth()->user()->hasRole('Buyer') || auth()->user()->hasRole('Penjaga'))
                <li>
                    <a href="{{ route('organization.index') }}" class=" waves-effect">
                        <i class="mdi mdi-account-group"></i>
                        <span>Organisasi</span>
                    </a>
                </li>
                @endunless

                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="fas fa-hand-holding-heart"></i>
                        <span>Derma</span>
                    </a>
                    <ul class="sub-menu mm-collapse" aria-expanded="false">
                    @unless(auth()->user()->hasRole('Buyer') || auth()->user()->hasRole('Penjaga'))
                        <li>
                            <a href="{{ route('donation.index') }}" class=" waves-effect">
                                <i class="fas fa-user-cog"></i>
                                <span>Urus Derma</span>
                            </a>
                        </li>
                    @endunless

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
                @role('Superadmin|Pentadbir|Guru|Warden|Guard')

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

                        @if(Auth::id() == 5529 || Auth::id() == 5527 || Auth::id() == 5528 || Auth::user()->hasRole('Warden') || Auth::user()->hasRole('Guard'))
                            @role('Superadmin|Pentadbir|Penjaga|Warden|Guard')
                            <li>
                                <a href="javascript: void(0);" class="has-arrow waves-effect">
                                    <i class="fas fa-book"></i>
                                    <span>Permohonan</span>
                                </a>

                                <ul class="sub-menu mm-collapse" aria-expanded="false">
                                    @role('Superadmin')
                                    <li>
                                        <a href="{{ route('dorm.superadmin') }}" class=" waves-effect">
                                            <i class="fas fa-search"></i>
                                            <span>Paparan Superadmin</span>
                                        </a>
                                    </li>
                                    @endrole

                                    @role('Pentadbir')
                                    <li>
                                        <a href="{{ route('dorm.pentadbir') }}" class=" waves-effect">
                                            <i class="fas fa-user-cog"></i>
                                            <span>Paparan Pentadbir</span>
                                        </a>
                                    </li>
                                    @endrole

                                    @role('Penjaga')
                                    <li>
                                        <a href="{{ route('dorm.parent') }}" class=" waves-effect">
                                            <i class="fas fa-user-friends"></i>
                                            <span>Paparan Penjaga</span>
                                        </a>
                                    </li>
                                    @endrole

                                    @role('Warden')
                                    <li>
                                        <a href="{{ route('dorm.warden') }}" class=" waves-effect">
                                            <i class="fas fa-user-lock"></i>
                                            <span>Paparan Warden</span>
                                        </a>
                                    </li>
                                    @endrole

                                    @role('Guard')
                                    <li>
                                        <a href="{{ route('dorm.guard') }}" class=" waves-effect">
                                            <i class="fas fa-user-shield"></i>
                                            <span>Paparan Guard</span>
                                        </a>
                                    </li>
                                    @endrole
                                </ul>
                            </li>
                            @endrole

                            @role('Superadmin|Pentadbir|Guru|Warden|Guard')
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
                        @endif
                    </ul>
                </li>
                @endrole

                @role('Superadmin|Pentadbir|Guru|Penjaga|Koop Admin')

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

                @role('Superadmin|Pentadbir|Guru|Koop Admin')
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

                @role('Superadmin|Pentadbir|Guru|Penjaga|Koop Admin')

                <li>
                    <a href="{{ route('parent.fees.history') }}" class=" waves-effect">
                        <i class="ti-clipboard"></i>
                        <span>Sejarah Bayaran</span>
                    </a>
                </li>
                @endrole

                @role('Superadmin|Pentadbir|Koop Admin')
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
            
             @role('Superadmin|Koop Admin')
            <li>
            <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <span>Koop Admin</span>
                    </a>

            <ul class="sub-menu mm-collapse" aria-expanded="false">
                <li>
                    <a href="{{ route('koperasi.productMenu') }}" class=" waves-effect">
                    <i class="typcn typcn-pencil"></i>
                    <span>Produk</span>
                    </a>
                </li>
           
                <li>
                    <a href="{{route('koperasi.indexOpening')}}" class=" waves-effect">
                    <i class="fas fa-archway"></i>
                    <span>Hari Dibuka</span>
                    </a>
                </li>

                <li>
                    <a href="{{route('koperasi.indexConfirm')}}" class=" waves-effect">
                    <i class="fas fa-check-square"></i>
                    <span>Pengesahan</span>
                    </a>
                </li>

                <li>
                        <a href="{{ route('koperasi.adminHistory') }}" class=" waves-effect">
                            <i class="ti-clipboard"></i>
                            <span>Sejarah Koperasi</span>
                        </a>
                </li>
            </ul>
            </li>
            @endrole 
            
            @role('Regular Merchant Admin')
            <li>
                <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="mdi mdi-account-edit"></i>
                    <span>Urus Peniaga</span>
                </a>
                <ul class="sub-menu mm-collapse" aria-expanded="false">
                    <li>
                        <a href="{{ route('admin-reg.home') }}" class=" waves-effect">
                            <i class="ti-home"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin-reg.operation-hour') }}" class=" waves-effect">
                            <i class="ti-timer"></i>
                            <span>Waktu Operasi</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin-reg.product-group') }}" class=" waves-effect">
                            <i class="ti-package"></i>
                            <span>Urus Produk</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin-reg.orders') }}" class=" waves-effect">
                            <i class="ti-email"></i>
                            <span>Pesanan</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin-reg.report') }}" class=" waves-effect">
                            <i class="ti-stats-up"></i>
                            <span>Laporan</span>
                        </a>
                    </li>
                </ul>  
            </li>
            @endrole


            
            <li>
                <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="mdi mdi-store"></i>
                    <span>Get&Go</span>
                </a>
                <ul class="sub-menu mm-collapse" aria-expanded="false">
                    
                    <li>
                        <a href="{{ route('merchant-product.index') }}" class=" waves-effect">
                            <i class="ti-bag"></i>
                            <span>Produk</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('merchant-reg.index') }}" class=" waves-effect">
                            <i class="ti-bookmark-alt"></i>
                            <span>Semua Peniaga</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('merchant.all-orders') }}" class=" waves-effect">
                            <i class="ti-email"></i>
                            <span>Pesanan</span>
                        </a>
                    </li>
                </ul>  
            </li>

            {{-- <!-- @role('Superadmin|Penjaga') -syah punye
            <li>
                <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="mdi mdi-border-color"></i>
                    <span>Kooperasi</span>
                </a>
                <ul class="sub-menu mm-collapse" aria-expanded="false">
                    <li>
                        <a href="{{ route('koperasi.index') }}" class=" waves-effect">
                            <i class="mdi mdi-book"></i>
                            <span>Koperasi Sekolah</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('koperasi.order') }}" class=" waves-effect">
                            <i class="ti-email"></i>
                            <span>Pesanan Koperasi</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('koperasi.history') }}" class=" waves-effect">
                            <i class="ti-clipboard"></i>
                            <span>Sejarah Koperasi</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endrole --> --}}

            @role('Superadmin|Penjaga|Buyer') <!--haziq nye-->
            <li>
                <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="mdi mdi-border-color"></i>
                    <span>Koperasi</span>
                </a>
                <ul class="sub-menu mm-collapse" aria-expanded="false">
                    <li>
                        <a href="{{ route('koperasi.index') }}" class=" waves-effect">
                            <i class="mdi mdi-book"></i>
                            <span>Koperasi Sekolah</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('koperasi.order') }}" class=" waves-effect">
                            <i class="ti-email"></i>
                            <span>Pesanan Koperasi</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('koperasi.history') }}" class=" waves-effect">
                            <i class="ti-clipboard"></i>
                            <span>Sejarah Koperasi</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endrole 

            {{-- @role('Superadmin|Pentadbir|Guru')
                <li>
                    <a href="{{ route('chat-user') }}" class=" waves-effect">
            <i class="mdi mdi-chat-outline"></i>
            <span>Chat</span>
            </a>
            </li>
            @endrole --}}

            {{-- @role('Superadmin|Ibu|Bapa|Penjaga')
                <li>
                    <a href="{{ route('billIndex') }}" class=" waves-effect">
            <i class="mdi"></i>
            <span>Bill Design</span>
            </a>
            </li>
            @endrole --}}

             <!-- <li>
                    <a href="{{route('delivery.index')}}" class=" waves-effect">
                        <i class="ti-clipboard"></i>
                        <span>Parcel</span>
                    </a>
                </li> -->

                @role('Homestay Admin|Superadmin|Buyer')
                <li>
                <a href="javascript: void(0);7" class="has-arrow waves-effect">
                    <i class="mdi mdi-home-city-outline"></i>
                    <span>Homestay</span>
                </a>
                <ul class="sub-menu mm-collapse" aria-expanded="false">
                    <li>
                @role('Homestay Admin')
                        <a href="{{ route('homestay.urusbilik') }}" class=" waves-effect">
                            <i class="mdi mdi-room-service-outline"></i>
                            <span>Urus Bilik</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('homestay.index') }}" class=" waves-effect">
                            <i class="mdi mdi-percent"></i>
                            <span>Set Promosi</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('homestay.urustempahan') }}" class=" waves-effect">
                            <i class="ti-clipboard"></i>
                            <span>Urus Tempahan</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('homestay.tunjuksales') }}" class=" waves-effect">
                            <i class="mdi mdi-finance"></i>
                            <span>Lihat Keuntungan</span>
                        </a>
                    </li>
                    @endrole 
                    <li>
                        <a href="{{ route('homestay.bookinglist') }}" class=" waves-effect">
                            <i class="mdi mdi-book-search"></i>
                            <span>Buat Tempahan</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('homestay.tempahananda') }}" class=" waves-effect">
                            <i class="mdi mdi-cart"></i>
                            <span>Tempahan Anda</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('homestay.userhistory') }}" class=" waves-effect">
                            <i class="mdi mdi-history"></i>
                            <span>Sejarah Tempahan</span>
                        </a>
                    </li>
                    <li>
                </ul>  
            </li>
            @endrole
            
            @role('Grab Student Admin|Superadmin|Buyer')
            <li>
                <a href="javascript: void(0);7" class="has-arrow waves-effect">
                    <i class="mdi  mdi-taxi"></i>
                    <span>Grab Student</span>
                </a>
                <ul class="sub-menu mm-collapse" aria-expanded="false">
                    <li>
                    @role('Grab Student Admin')
                        <a href="{{ route('grab.setinsert') }}" class=" waves-effect">
                            <i class="mdi mdi-taxi"></i>
                            <span>Daftar Kereta</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('grab.check') }}" class=" waves-effect">
                            <i class="mdi mdi-taxi"></i>
                            <span>Urus Kereta</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('grab.setdestination') }}" class=" waves-effect">
                            <i class="mdi mdi-city"></i>
                            <span>Daftar Destinasi</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('grab.checkpassenger') }}" class=" waves-effect">
                            <i class="ti-clipboard"></i>
                            <span>Urus Tempahan</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('grab.notifypassenger') }}" class=" waves-effect">
                            <i class="mdi mdi-comment-alert"></i>
                            <span>Notify Penumpang</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('grab.checksales') }}" class=" waves-effect">
                            <i class="mdi mdi-library-books"></i>
                            <span>Semak Untung</span>
                        </a>
                    </li>
                    @endrole 
                    <li>
                        <a href="{{ route('book.grab') }}" class=" waves-effect">
                            <i class="mdi mdi-library-books"></i>
                            <span>Buat Tempahan</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('grab.bayartempahan') }}" class=" waves-effect">
                        <i class="mdi mdi-cash-multiple"></i>
                            <span>Bayar Tempahan</span>
                        </a>
                    </li>
                </ul>  
            </li>
            @endrole

            @role('Bas Admin|Superadmin|Buyer')
            <li>
                <a href="javascript: void(0);7" class="has-arrow waves-effect">
                    <i class="mdi  mdi-bus"></i>
                    <span>Bas</span>
                </a>
                <ul class="sub-menu mm-collapse" aria-expanded="false">
                    <li>
                    @role('Bas Admin')
                        <a href="{{ route('bus.setinsert') }}" class=" waves-effect">
                            <i class="mdi mdi-bus"></i>
                            <span>Daftar Bas</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('bus.manage') }}" class=" waves-effect">
                            <i class="mdi mdi-bus"></i>
                            <span>Urus Bas</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('bus.listpassenger') }}" class=" waves-effect">
                            <i class="mdi mdi-account-multiple"></i>
                            <span>Senarai Penumpang</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('bus.notifypassenger') }}" class=" waves-effect">
                            <i class="mdi mdi-comment-alert"></i>
                            <span>Notify Penumpang</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('bus.checksales') }}" class=" waves-effect">
                            <i class="mdi mdi-library-books"></i>
                            <span>Semak Untung</span>
                        </a>
                    </li>
                    @endrole 
                    <li>
                        <a href="{{ route('book.bus') }}" class=" waves-effect">
                            <i class="mdi mdi-library-books"></i>
                            <span>Buat Tempahan</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('bus.bayartempahan') }}" class=" waves-effect">
                            <i class="mdi mdi-cash-multiple"></i>
                            <span>Bayar Tempahan</span>
                        </a>
                    </li>
                </ul>  
            </li>
            @endrole

            @role('OrderS Admin|Superadmin|Buyer')
            <li>
                <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="mdi mdi-silverware"></i>
                    <span>OrderS</span>
                </a>
                <ul class="sub-menu mm-collapse" aria-expanded="false">
                    @role('OrderS Admin')
                    <li>
                        <a href="{{ route('orders.managemenu') }}" class=" waves-effect">
                            <i class="mdi mdi-table-edit"></i>
                            <span>Urus Menu</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('orders.uruspesanan') }}" class=" waves-effect">
                            <i class="mdi mdi-table-edit"></i>
                            <span>Urus Pesanan</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('orders.laporanjualan') }}" class=" waves-effect">
                            <i class="mdi mdi-chart-bar"></i>
                            <span>Laporan Jualan</span>
                        </a>
                    </li>
                    @endrole
                    <li>
                        <a href="{{ route('orders.buatpesanan') }}" class=" waves-effect">
                            <i class="mdi mdi-note-plus"></i>
                            <span>Buat Pesanan</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('orders.trackorder') }}" class=" waves-effect">
                            <i class="mdi mdi-package-variant-closed"></i>
                            <span>Pesanan</span>
                        </a>
                    </li>
                </ul>
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
    </div>
</div>