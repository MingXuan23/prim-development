<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>PRiM | Competition</title>

    <style>
        body {
            margin: 0;
            margin-bottom: 50px;
            background-color: #f8fafc;
            overflow-x: hidden;
        }

        .header {
            background: linear-gradient(135deg, #e11d48 0%, #7c3aed 75%, #4f46e5 100%);
            /* background: linear-gradient(135deg, #dbeafe 0%, #eff6ff 100%); */
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
            background-color: rgba(205, 246, 212, 0.5);
            padding: 5px;
            border-radius: 10px;
            text-align: center;
            color: green;
        }

        .modal-card {
            background-color: white;
            padding: 30px   ;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            width: 50%;
            max-width: 450px;
            text-align: center
        }

        .edit-delete .update-btn, .edit-delete .delete-btn {
            width: 400px !important;
        }

        @media (min-width: 992px) {
            .header {
                padding: 30px 0 30px 100px;
            }

            .edit-delete {
                margin-right: 100px;
            }

            .edit-delete .update-btn, .edit-delete .delete-btn {
                width: 250px !important;
            }
        }
    </style>
</head>
<body>
    @include('competition.component.nav')

    <div class="header d-flex flex-column flex-lg-row gap-3 justify-content-between align-items-center">
        <div class="col-12 col-lg-7">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('competition.host') }}" class="text-decoration-none">Pengurusan Pertandingan</a>    
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ $competition->competitionTitle }}
                    </li>
                </ol>
            </nav>
            <p class="mb-0 mt-4 category-label text-uppercase">{{ $competition->category ?? '...' }}</p>
            <h1 class="mb-3 fw-bold text-white">{{ $competition->competitionTitle }}</h1>
            @if(is_null($competition->competitionStart) || $competition->status == 'Draft')
                <p class="badge bg-danger z-3">DRAFT</p>
            @elseif($competition->competitionStart > today()->toDateString())
                <p class="badge bg-warning text-dark z-3">UPCOMING</p>
            @elseif($competition->competitionStart <= today()->toDateString() && $competition->competitionEnd >= today()->toDateString())
                <p class="badge bg-success z-3">ONGOING</p>
            @elseif($competition->competitionEnd < today()->toDateString())
                <p class="badge bg-secondary z-3">COMPLETED</p>
            @endif        
            
            @if($competition->participateType === 'Team')
                <span class="badge bg-light ms-2 text-dark border fw-normal"><i class="bi bi-people-fill me-1"></i> {{ $competition->participateType }} ({{$competition->minimumParticipate}} - {{$competition->maximumParticipate}} pax)</span>
            @elseif($competition->participateType === 'Individual')
                <span class="badge bg-light ms-2 text-dark border fw-normal"><i class="bi bi-person-fill me-1"></i> {{ $competition->participateType }}</span>
            @else
                <span class="badge bg-light ms-2 text-dark border fw-normal">...</span>
            @endif
        </div>

        @if($competition->status === 'Published' && $competition->competitionEnd < now())
            <div class="card border-0 shadow-sm rounded-3 me-0 me-md-5 p-3 bg-white">
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                    <div class="badge bg-success-subtle text-success p-2 rounded-2">
                        <i class="bi bi-check-circle-fill fs-5"></i>
                    </div>
                        <span class="fw-bold d-block text-dark small">Pertandingan Tamat</span>
                </div>

                <div class="alert alert-secondary border-0 bg-light p-2 mb-1 rounded-2 text-center" style="font-size: 0.8rem;">
                    <i class="bi bi-lock-fill me-1 text-muted"></i> 
                    <span class="text-muted">Rekod ini telah dikunci dan tidak boleh dikemaskini atau dipadam.</span>
                </div>
            </div>
        @else
            <div class="d-flex flex-column gap-2 m-0 edit-delete">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white edit-delete">
                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom text-primary">
                        <i class="bi bi-eye-fill fs-5"></i>
                        <div class="lh-1">
                            <span class="fw-bold d-block text-dark small">Mod Pratonton (Preview)</span>
                            <small class="text-muted" style="font-size: 0.75rem;">Ini adalah paparan yang akan dilihat oleh peserta.</small>
                        </div>
                    </div>

                    <div class="d-flex flex-column gap-2">
                        <a href="{{ route('competition.editstorecompetition', $competition->id) }}" 
                        class="btn btn-outline-primary btn-sm fw-semibold d-flex align-items-center justify-content-center gap-2 py-2">
                            <i class="bi bi-pencil-square"></i> Kemaskini Pertandingan
                        </a>
                        
                        @if($competition->registeredOpen >= now() || $competition->participant_registration->count() === 0)
                            <form class="w-100" id="deleteCompetition">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="btn btn-outline-danger btn-sm fw-semibold d-flex align-items-center justify-content-center gap-2 w-100 py-2">
                                    <i class="bi bi-trash"></i> Padam Pertandingan
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="row g-3 m-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white d-flex flex-row align-items-center">
                <div class="rounded-3 bg-indigo bg-opacity-10 p-2 me-3 text-indigo" style="background-color: #e0e7ff; color: #4338ca;">
                    <i class="bi bi-journal-text fs-4"></i>
                </div>

                <div>
                    <span class="text-muted d-block small fw-semibold">Jumlah Pendaftaran</span>
                    <span class="fs-4 fw-bold">{{ $competition->participant_registration->count() }}</span>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white d-flex flex-row align-items-center">
                <div class="rounded-3 p-2 me-3" style="background-color: #fef3c7; color: #d97706;">
                    <i class="bi bi-clock-history fs-4"></i>
                </div>

                <div>
                    <span class="text-muted d-block small fw-semibold">Belum Dibayar</span>
                    <span class="fs-4 fw-bold">{{ $pendingCount }}</span>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white d-flex flex-row align-items-center">
                <div class="rounded-3 p-2 me-3" style="background-color: #d1fae5; color: #059669;">
                    <i class="bi bi-check-circle fs-4"></i>
                </div>

                <div>
                    <span class="text-muted d-block small fw-semibold">Selesai Bayar</span>
                    <span class="fs-4 fw-bold">{{ $paidCount }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-3 mx-3">
        <div class="col-lg-8 order-2 order-lg-1">
            <div class="about">
                <div class="about2 d-flex align-items-center">
                    <div class="badge badge-color text-dark p-2 rounded-3 me-3">
                        <i class="bi bi-calendar-check fs-6"></i>
                    </div>
                    <h5 class="fw-bold m-0">Tentang Pertandingan</h5>
                </div>
                <div class="d-flex align-items-center mx-4">
                    <div class="badge bg-primary-subtle text-primary p-2 rounded-2 me-3">
                        <i class="bi bi-calendar3 fs-6"></i>
                    </div>

                    <div>
                        <h6 class="fw-bold mt-3 info-label">Tarikh Pendaftaran</h6>
                        @if($competition->registerOpen && $competition->registerClose)
                            <p class="text-muted">{{ \Carbon\Carbon::parse($competition->registerOpen)->format('d M Y') }} - {{ \Carbon\Carbon::parse($competition->registerClose)->format('d M Y') }}</p>
                        @else
                            <p class="text-muted">Tarikh belum ditetapkan</p>
                        @endif
                    </div>
                </div>
                    <hr>
                <div class="d-flex align-items-center mx-4">
                    <div class="badge bg-primary-subtle text-primary p-2 rounded-2 me-3">
                        <i class="bi bi-trophy fs-6"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mt-3 info-label">Tarikh Pertandingan</h6>
                        @if($competition->competitionStart && $competition->competitionEnd)
                            <p class="text-muted">{{ \Carbon\Carbon::parse($competition->competitionStart)->format('d M Y') }} - {{ \Carbon\Carbon::parse($competition->competitionEnd)->format('d M Y') }}</p>
                        @else
                            <p class="text-muted">Tarikh belum ditetapkan</p>
                        @endif
                    </div>
                </div>
                    <hr>
                <div class="d-flex align-items-center mx-4">
                    <div class="badge bg-primary-subtle text-primary p-2 rounded-2 me-3">
                        <i class="bi bi-geo-alt fs-6"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mt-3 info-label">Lokasi Pertandingan</h6>
                        <p class="text-muted">{{ $competition->venue ?? 'Lokasi belum ditetapkan' }}</p>
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
                            @if($competition->minimumAge && $competition->maximumAge)
                                {{ $competition->minimumAge }} - {{ $competition->maximumAge }} Tahun
                            @elseif($competition->minimumAge)
                                {{ $competition->minimumAge }} Tahun dan ke atas
                            @elseif($competition->maximumAge)
                                {{ $competition->maximumAge }} Tahun dan ke bawah
                            @else
                                Terbuka (Tiada Had Umur)
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <div class="description">
                <div class="description2 d-flex align-items-center">
                    <div class="badge badge-color text-dark p-2 rounded-3 me-3">
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
                <img src="{{ asset('competition-image/' . $competition->imagePoster) }}" alt="{{ $competition->competitionTitle }}">
            </div>
            <div class="fees d-none d-lg-block">
                <div class="fees2 d-flex align-items-center">
                    <div class="badge badge-color text-dark p-2 rounded-3 me-3">
                        <i class="bi bi-person-plus fs-6"></i>
                    </div>
                    <h5 class="fw-bold m-0">Yuran Pendaftaran</h5>
                </div>
                <div class="mx-4 mt-3 pb-1">
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-3 text-center border">
                                <small class="text-muted d-block mb-1 fw-semibold">Warganegara</small>
                                <span class="fs-5 fw-bold">RM {{ $competition->nationalFees ?? '0.00' }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-3 text-center border">
                                <small class="text-muted d-block mb-1 fw-semibold">Bukan Warganegara</small>
                                <span class="fs-5 fw-bold">RM {{ $competition->internationalFees ?? '0.00' }}</span>
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
                </div>
            </div>
        </div>

        <!-- Mobile -->
        <div class="col-12 order-3 d-lg-none mt-0">
            <div class="fees">
                <div class="fees2 d-flex align-items-center">
                    <div class="badge badge-color text-dark p-2 rounded-3 me-3">
                        <i class="bi bi-person-plus fs-6"></i>
                    </div>
                    <h5 class="fw-bold m-0">Yuran Pendaftaran</h5>
                </div>
                <div class="mx-4 mt-3 pb-1">
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-3 text-center border">
                                <small class="text-muted d-block mb-1 fw-semibold">Warganegara</small>
                                <span class="fs-5 fw-bold">RM {{ $competition->nationalFees ?? '0.00' }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-3 text-center border">
                                <small class="text-muted d-block mb-1 fw-semibold">Bukan Warganegara</small>
                                <span class="fs-5 fw-bold">RM {{ $competition->internationalFees ?? '0.00' }}</span>
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
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('deleteCompetition').addEventListener('submit', function (e) {
            e.preventDefault();

            let form = this;
            let formData = new FormData(form);

            let name = "{{ $competition->competitionTitle }}";

            Swal.fire({
                icon:'warning',
                title: 'Adakah anda pasti?',
                text: `Adakah anda pasti ingin memadam pertandingan ${name}?`,
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Padam',
                cancelButtonText: 'Batal'
            }).then ((result) => {
                if (result.isConfirmed)
                {
                    fetch("{{ route('competition.deletecompetition', $competition->id) }}", {
                        method: "POST",
                        body: formData,
                        headers: {
                            "Accept": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        }
                    })
                    .then (response => response.json())
                    .then(data => {
                        if (data.status === 'success')
                        {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berjaya dipadam',
                                text: `Pertandingan ${name} berjaya dipadam.`,
                                confirmButtonColor: '#4f46e5'
                            }).then (() => {
                                if (data.redirect)
                                {
                                    window.location.href = data.redirect;
                                }
                                else
                                {
                                    location.reload();
                                }
                            });
                        }
                        else
                        {
                            throw new Error(data.message);
                        }
                    })
                    .catch(error => {
                        console.error("Error: ", error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Ralat',
                            text: 'Terdapat ralat semasa memadam pendaftaran.',
                            confirmButtonColor: '#4f46e5'
                        });
                    });
                }
            });
        });
    </script>
</body>
</html>