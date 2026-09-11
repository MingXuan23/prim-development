<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <title>PRiM | Competition</title>
    
    <style>
        body {
            margin: 0;
            margin-bottom: 50px;
            background-color: #f8fafc;
            overflow-x: hidden;
        }

        .header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 25%, #e11d48 100%);
            /* background: linear-gradient(135deg, #6b65da 0%, #9369dc 35%, #de4a6a 100%); */
            /* background: linear-gradient(135deg, #c7d2fe 0%, #ddd6fe 50%, #fbcfe8 100%); */
            padding: 20px 20px;
            width: 100%;
            height: auto;
        }

        .breadcrumb-item, .breadcrumb-item a {
            color: rgba(255, 255, 255, 0.75) !important;
            font-weight: 500;
            font-size: 0.875rem;
        }

        .breadcrumb-item a:hover {
            color: #ffffff !important;
            text-decoration: underline !important;
        }

        .breadcrumb-item.active {
            color: rgba(255, 255, 255, 0.9) !important;
        }

        .breadcrumb-item.active::before {
            color: rgba(255, 255, 255, 0.9) !important;
        }

        .category-label {
            background-color: #f59e0b;
            color: #0f172a !important;
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 0.5px;
            padding: 4px 14px;
            border-radius: 50px;
            display: inline-block;
        }
        
        .about, .description, .fees {
            border: 1px solid #e0e7ff;
            background-color: white;
            height: auto;
            margin-bottom: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.04);
        }

        hr {
            margin: 0px 20px 0px 20px;
            color: darkgray;
        }
        
        .about2, .description2, .fees2 {
            background-color: #f5f3ff;
            height: 50px;
            padding-left: 20px;
            border-bottom: 1px solid #e0e7ff;
            border-radius: 10px 10px 0px 0px;
        }

        .badge-color {
            color: #6d28d9 !important;
            background-color: #ede9fe !important;
        }

        h5 {
            font-size: 1rem;
        }

        .info-label {
            font-size: 0.90rem;
            font-weight: 600;
            color: #475569;
        }

        img {
            width: 100%;
            height: auto;
            /* margin-bottom: 30px; */
            border: 1px solid gainsboro;
            border-radius: 10px;
        }

        .btn-daftar {
            background-color: #4f46e5;
            color: white;
        }

        .btn-daftar:hover {
            background-color: #4338ca;
            color: white;
        }

        .nation {
            /* background-color: rgba(150, 150, 150, 0.2); */
            width: 200px;
            border: 1px solid gainsboro;
            padding: 10px;
            margin: 10px 10px 20px 10px;
            text-align: center;
            margin: 10px auto 20px auto;
            border-radius: 10px;
        }

        .register {
            border-radius: 20px;
        }

        #daftarOption {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.4);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-card {
            background-color: white;
            padding: 24px;
            border-radius: 16px;
            width: 90%;
            max-width: 440px;
            margin: auto;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }

        .layout-register {
            display: flex;
            align-items: center;
            gap: 14px;
            border-radius: 12px;
            padding: 14px 18px;
            text-align: left;
            transition: all 0.2s ease;
            width: 100%;
        }

        .icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            min-width: 42px;
            border-radius: 10px;
            font-size: 1.25rem;
        }

        .register-solo .icon {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .register-team .icon {
            background: #f1f5f9;
            color: #4f46e5;
        }

        .register-solo {
            background-color: #4f46e5;
            color: white;
            border: 1px solid #4f46e5;
        }

        .register-solo:hover {
            background-color: #4338ca;
            color: white;
        }

        .register-team {
            background-color: white;
            color: #1e293b;
            border: 1.5px solid #e2e8f0;
        }

        .register-team:hover {
            background-color: #f8fafc;
            color: #0f172a;
            border-color: #cbd5e1;
        }

        .click-image {
            cursor: pointer;
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .click-image:hover {
            transform: translateY(-4px) scale(1.01);
            box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.15), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .image-container {
            display: none;
            position: fixed;
            z-index: 9999;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            backdrop-filter: blur(5px);
            background-color: rgba(0, 0, 0, 0.3);
            justify-content: center;
            align-items: center;
        }

        .modal-wrap {
            position: relative;
            display: inline-block;
            max-width: 90%;
            max-height: 90%;
        }

        .modal-image {
            width: 100%;
            height: auto;
            max-height: 85vh;
            border-radius: 8px;
            display: block;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            color: white;
            font-size: 35px;
            font-weight: bold;
            cursor: pointer;
            z-index: 1000;
            line-height: 1;
        }

        .close-btn:hover {
            color: #c6c6c6;
        }

        @media (min-width: 992px) {
            .header {
                padding: 30px 0 30px 100px;
            }
        }
    </style>
</head>
<body>
    @include('competition.component.nav')

    <div class="header">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('competition.user') }}" class="text-decoration-none">Senarai Pertandingan</a>    
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    {{ $competition->competitionTitle }}
                </li>
            </ol>
        </nav>
        <p class="mb-0 mt-4 category-label text-primary text-uppercase">{{ $competition->category }}</p>

        <h1 class="fw-bold mb-3 text-white">{{ $competition->competitionTitle }}</h1>
        
        @if($competition->competitionStart > now())
            <p class="badge bg-warning text-dark z-3 status">UPCOMING</p>
        @elseif($competition->competitionStart <= now() && $competition->competitionEnd >= now())
            <p class="badge bg-success z-3 status">ONGOING</p>
        @elseif($competition->competitionEnd < now())
            <p class="badge bg-secondary z-3 status">COMPLETED</p>
        @endif

        @if($competition->participateType === 'Team')
            @if($competition->minimumParticipate === $competition->maximumParticipate)
                <span class="badge bg-light ms-2 text-dark border fw-normal"><i class="bi bi-people-fill me-1"></i> {{ $competition->participateType }} ({{$competition->maximumParticipate}} pax)</span>
            @else
                <span class="badge bg-light ms-2 text-dark border fw-normal"><i class="bi bi-people-fill me-1"></i> {{ $competition->participateType }} ({{$competition->minimumParticipate}} - {{$competition->maximumParticipate}} pax)</span>
            @endif
        @else
            <span class="badge bg-light ms-2 text-dark border fw-normal"><i class="bi bi-person-fill me-1"></i> {{ $competition->participateType }}</span>
        @endif
    </div>

    <div class="row g-4 mt-3 mx-3">
        <div class="col-lg-8 order-2 order-lg-1">
            <div class="about">
                <div class="about2 d-flex align-items-center">
                    <div class="badge bg-light-subtle badge-color p-2 rounded-3 me-3">
                        <i class="bi bi-calendar-check fs-6"></i>
                    </div>
                    <h5 class="fw-bold m-0">Tentang Pertandingan</h5>
                </div>
                <div class="d-flex align-items-center mx-4">
                    <div class="badge bg-primary-subtle text-primary p-2 rounded-2 me-3">
                        <i class="bi bi-calendar3 fs-6"></i>
                    </div>

                    <div>
                        <div class="fw-bold mt-3 info-label">Tarikh Pendaftaran</div>
                        <p class="text-muted mt-1">{{ \Carbon\Carbon::parse($competition->registerOpen)->format('d M Y, H:i A') }} - {{ \Carbon\Carbon::parse($competition->registerClose)->format('d M Y, H:i A') }}</p>
                    </div>
                </div>
                    <hr>
                <div class="d-flex align-items-center mx-4">
                    <div class="badge bg-primary-subtle text-primary p-2 rounded-2 me-3">
                        <i class="bi bi-trophy fs-6"></i>
                    </div>
                    <div>
                        <div class="fw-bold mt-3 info-label">Tarikh Pertandingan</div>
                        <p class="text-muted mt-1">{{ \Carbon\Carbon::parse($competition->competitionStart)->format('d M Y') }} - {{ \Carbon\Carbon::parse($competition->competitionEnd)->format('d M Y') }}</p>
                    </div>
                </div>
                    <hr>
                <div class="d-flex align-items-center mx-4">
                    <div class="badge bg-primary-subtle text-primary p-2 rounded-2 me-3">
                        <i class="bi bi-geo-alt fs-6"></i>
                    </div>
                    <div>
                        <div class="fw-bold mt-3 info-label">Lokasi Pertandingan</div>
                        <p class="text-muted mt-1">{{ $competition->venue }}</p>
                    </div>
                </div>
                    <hr>
                <div class="d-flex align-items-center mx-4">
                    <div class="badge bg-primary-subtle text-primary p-2 rounded-2 me-3">
                        <i class="bi bi-person-badge fs-6"></i>
                    </div>
                    <div>
                        <div class="fw-bold mt-3 info-label">Had Umur / Kelayakan</div>
                        <p class="text-muted mt-1">
                            @if($competition->minAge && $competition->maxAge)
                                {{ $competition->minAge }} - {{ $competition->maxAge }} Tahun
                            @elseif($competition->minAge)
                                {{ $competition->minAge }} Tahun ke atas
                            @elseif($competition->maxAge)
                                Hingga {{ $competition->maxAge }} Tahun
                            @else
                                Terbuka (Tiada Had Umur)
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <div class="description">
                <div class="description2 d-flex align-items-center">
                    <div class="badge bg-light-subtle badge-color p-2 rounded-3 me-3">
                        <i class="bi bi-info-circle fs-6"></i>
                    </div>
                    <h5 class="fw-bold m-0">Maklumat Pertandingan</h5>
                </div>
                <div class="mx-4 my-3 text-break">
                    <div style="white-space: pre-wrap;">{{ e($competition->description ?? 'Tiada maklumat') }}</div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 order-1 order-lg-2">
            <div class="mb-3 mb-lg-4">
                <img src="{{ asset('competition-image/' . $competition->imagePoster) }}" alt="{{ $competition->competitionTitle }}" class="click-image" onclick="openImage(this.src)">
            </div>

            <div class="image-container" id="imageContainer" onclick="closeImage()">
                <div class="modal-wrap">
                    <span class="close-btn">&times;</span>
                    <img id="modalImage" class="modal-image" onclick="event.stopPropagation()">
                </div>
            </div>

            <div class="fees d-none d-lg-block">
                <div class="fees2 d-flex align-items-center">
                    <div class="badge bg-light-subtle badge-color p-2 rounded-3 me-3">
                        <i class="bi bi-person-plus fs-6"></i>
                    </div>
                    <h5 class="fw-bold m-0">Yuran Pendaftaran</h5>
                </div>
                <div class="mx-4 mt-3 pb-1">
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-3 text-center border">
                                <small class="text-muted d-block mb-1 fw-semibold">Warganegara</small>
                                @if($competition->status === 'Published' && $competition->nationalFees === '0.00')
                                    <span class="fs-5 fw-bold">PERCUMA</span>
                                @else
                                    <span class="fs-5 fw-bold">RM {{ $competition->nationalFees ?? '0.00' }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-3 text-center border">
                                <small class="text-muted d-block mb-1 fw-semibold">Bukan Warganegara</small>
                                @if($competition->status === 'Published' && $competition->internationalFees === '0.00')
                                    <span class="fs-5 fw-bold">PERCUMA</span>
                                @else
                                    <span class="fs-5 fw-bold">RM {{ $competition->internationalFees ?? '0.00' }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($competition->registerOpen >= now())
                        <p class="mb-2 bg-warning-subtle text-warning border border-warning-subtle text-center fw-semibold p-1 small register"><i class="bi bi-clock-history"></i>&nbsp Registration Open Soon</p>
                    @elseif($competition->registerOpen <= now() && $competition->registerClose >= now())
                        <p class="mb-2 bg-success-subtle text-success border border-success-subtle text-center fw-semibold p-1 small register"><i class="bi bi-check-circle"></i>&nbsp&nbsp Registration Open</p>
                    @elseif($competition->registerClose <= now())
                        <p class="mb-2 bg-danger-subtle text-danger border border-danger-subtle text-center fw-semibold p-1 small register"><i class="bi bi-x-circle"></i>&nbsp&nbsp Registration Closed</p>
                    @endif
                    <div class="d-flex justify-content-center">
                        @if($competition->registerOpen <= now() && $competition->registerClose >= now())
                            <button id="daftar" class="btn btn-daftar w-100 mt-1 mb-3" onclick="showRegisterOption()"><i class="bi bi-person-plus me-1"></i> Daftar Sekarang</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile -->
        <div class="col-12 order-3 d-lg-none mt-0">
            <div class="fees">
                <div class="fees2 d-flex align-items-center">
                    <div class="badge bg-light-subtle badge-color p-2 rounded-3 me-3">
                        <i class="bi bi-person-plus fs-6"></i>
                    </div>
                    <h5 class="fw-bold m-0">Yuran Pendaftaran</h5>
                </div>
                <div class="mx-4 mt-3 pb-1">
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-3 text-center border">
                                <small class="text-muted d-block mb-1 fw-semibold">Warganegara</small>
                                @if($competition->status === 'Published' && $competition->nationalFees === '0.00')
                                    <span class="fs-5 fw-bold">PERCUMA</span>
                                @else
                                    <span class="fs-5 fw-bold">RM {{ $competition->nationalFees ?? '0.00' }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-3 text-center border">
                                <small class="text-muted d-block mb-1 fw-semibold">Bukan Warganegara</small>
                                @if($competition->status === 'Published' && $competition->internationalFees === '0.00')
                                    <span class="fs-5 fw-bold">PERCUMA</span>
                                @else
                                    <span class="fs-5 fw-bold">RM {{ $competition->internationalFees ?? '0.00' }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($competition->registerOpen >= now())
                        <p class="mb-2 bg-warning-subtle text-warning border border-warning-subtle text-center fw-semibold p-1 small register"><i class="bi bi-clock-history"></i>&nbsp Registration Open Soon</p>
                    @elseif($competition->registerOpen <= now() && $competition->registerClose >= now())
                        <p class="mb-2 bg-success-subtle text-success border border-success-subtle text-center fw-semibold p-1 small register"><i class="bi bi-check-circle"></i>&nbsp&nbsp Registration Open</p>
                    @elseif($competition->registerClose <= now())
                        <p class="mb-2 bg-danger-subtle text-danger border border-danger-subtle text-center fw-semibold p-1 small register"><i class="bi bi-x-circle"></i>&nbsp&nbsp Registration Closed</p>
                    @endif
                    <div class="d-flex justify-content-center">
                        @if($competition->registerOpen <= now() && $competition->registerClose >= now())
                            <button id="daftar" class="btn btn-daftar w-100 mt-1 mb-3" onclick="showRegisterOption()">Daftar Sekarang</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
        
    <div id="daftarOption" onclick="hideRegister()">
        <div class="modal-card position-relative" onclick="event.stopPropagation()">
            <button type="button" class="btn btn-close position-absolute end-0 top-0 me-3 mt-3" onclick="hideRegister()" aria-label="Close"></button>

            <h3 class="fw-bold text-center mb-4">Pilihan Pendaftaran</h3>

            <div class="d-flex flex-column gap-3">
                @if($competition->participateType === 'Individual')
                    <a href="{{ route('competition.info', ['id' => $competition->id, 'type' => 'individual', 'mode' => 'user']) }}" class="btn register-solo layout-register">
                        <div class="icon">
                            <i class="bi bi-person-plus-fill"></i>
                        </div>
                        <div>
                            <div class="fw-bold">Tambah Peserta Individu</div>
                            <div class="small opacity-75">Daftar maklumat peserta melalui form</div>
                        </div>
                    </a>
                    <a href="{{ route('competition.info', ['id' => $competition->id, 'type' => 'bulk', 'mode' => 'user']) }}" class="btn register-team layout-register">
                        <div class="icon">
                            <i class="bi bi-file-earmark-arrow-up-fill"></i>
                        </div>
                        <div>
                            <div class="fw-bold">Muat Naik Senarai Peserta</div>
                            <div class="small text-muted">Muat naik fail Excel / CSV (Bulk)</div>
                        </div>
                    </a>
                @else
                    <a href="{{ route('competition.info', ['id' => $competition->id, 'type' => 'team', 'mode' => 'user']) }}" class="btn register-team layout-register">
                        <div class="icon">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div>
                            <div class="fw-bold">Daftar Kumpulan</div>
                            <div class="small opacity-75">Daftar ahli kumpulan untuk pertandingan ini</div>
                        </div>
                    </a>
                @endif
            </div>
        </div>
    </div>

<script>
    function showRegisterOption()
    {
        document.getElementById('daftarOption').style.display = 'flex';
    }

    function hideRegister()
    {
        document.getElementById('daftarOption').style.display = 'none';
    }

    document.getElementById('daftarOption').addEventListener('click', function (e) {
        if (e.target === this) {
            hideRegister();
        }
    });

    function openImage(imageSrc)
    {
        const modal = document.getElementById('imageContainer');
        const modalImg = document.getElementById('modalImage');

        modalImg.src = imageSrc;
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeImage()
    {
        document.getElementById('imageContainer').style.display = 'none';
        document.body.style.overflow = 'auto';
    }
</script>
</body>
</html>