<div class="page-title-box d-flex justify-content-center align-items-center flex-wrap">
    <div class="admin-nav-links d-flex justify-content-center align-items-center flex-wrap">
        <a href="{{ route('homestay.urusbilik') }}" id="manage-homestay" class="btn-dark-purple m-2 waves-effect">
            <i class="mdi mdi-room-service-outline"></i>
            <span>Homestay</span>
        </a>
        <a href="{{ route('homestay.manageDate') }}" id="manage-homestay" class="btn-dark-purple m-2 waves-effect">
            <i class="fas fa-calendar-alt"></i>
            <span>Operasi</span>
        </a>
        <a href="{{route('homestay.promotionPage')}}" id="manage-promotion" class="btn-dark-purple m-2"><i class="fas fa-percentage"></i> Promosi</a>
        <a href="{{route('homestay.urustempahan')}}" id="manage-booking" class="btn-dark-purple m-2"><i class="fas fa-concierge-bell"></i> Tempahan Pelanggan</a>
        <a href="{{route('homestay.viewCustomersReview')}}"style="cursor: pointer;" id="view-customers-review" class="btn-dark-purple m-2"> <i class="fas fa-comments"></i> Nilaian Pelanggan</a>
        <a href="{{ route('homestay.viewPerformanceReport') }}" id="view-report" class="btn-dark-purple m-2 waves-effect">
            <i class="mdi mdi-finance"></i>
            <span>Lihat Prestasi</span>
        </a>
    </div>
  </div>