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
            background: linear-gradient(135deg, #e11d48 0%, #7c3aed 75%, #4f46e5 100%);
            /* background: linear-gradient(135deg, #dbeafe 0%, #eff6ff 100%); */
            padding: 40px 20px;
            width: 100%;
            min-height: 350px;
            height: auto;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        h2 {
            font-weight: 800;
            color: white;
            font-size: 2.2rem;
        }

        h2 span {
            color: #facc15;
        }

        .desc {
            font-weight: 500;
            color: #e0e7ff;
            font-size: 1.05rem;
            max-width: 700px;
        }

        .search-form {
            max-width: 950px;
            width: 90%;
            margin: 0 auto;
        }

        .box-search {
            background-color: white;
            border: 1px solid #e2e8f0;
            border-radius: 50px;
            padding: 6px 6px 6px 24px;
            width: 100%;
        }

        .search-icon {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
        }

        .fill-input {
            flex-grow: 1;
            width: 300px;
            padding-left: 35px;
            font-size: 0.95rem;
            font-weight: 500;
            color: #0f172a;
            border: none !important;
            background: transparent !important;
            box-shadow: none !important ;
        }

        .fill-input::placeholder {
            color: #94a3b8;
            font-weight: 400;
        }

        .fill-select {
            width: 200px;
            padding-right: 30px;
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 500;
            color: #0f172a;
            border: none !important;
            box-shadow: none !important ;
        }

        .divider {
            width: 1px;
            height: 32px;
            background-color: #cbd5e1;
            margin: 0 15px;
        }

        .submit-btn {
            background-color: #facc15;
            border-color: #facc15;
            border-radius: 50px;
            padding: 10px 32px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .submit-btn:hover {
            background-color: #eab308;
            border-color: #eab308;
            transform: translateY(-1px);
        }

        .reset-btn {
            font-weight: 600;
            font-size: 0.95rem;
            border-radius: 50px;
            padding: 10px 20px;
        }

        .status-label {
            font-weight: 700;
            margin-right: 12px;
        }

        .status-btn {
            border-radius: 50px;
            padding: 6px 20px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .statusBtn-active {
            background-color: white;
            color: #4f46e5;
            font-weight: 700;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .statusBtn-inactive {
            background-color: rgba(255, 255, 255, 0.15);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(4px);
        }

        .statusBtn-inactive:hover {
            background-color: rgba(255, 255, 255, 0.25);
            color: white;
        }

        .card-layout {
            padding-left: 110px;
            padding-right: 110px;
        }

        .add-card {
            min-height: 482px;
            border: 2px dashed grey;
            background-color: rgb(240, 239, 239);
        }

        .add-card:hover {
            background-color: white;
            border-color: #4f46e5;
        }

        .add-icon-wrapper {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: #e9ecef;
            color: #6c757d;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            transition: all 0.3s ease;
        }

        .add-card:hover .add-icon-wrapper {
            background-color: #4f46e5;
            color: white;
        }

        .add-card:hover span {
            color: #4f46e5 !important;
        }

        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            transform: translateY(-5px);
        }

        .image {
            height: 250px;
            object-fit: cover;
            object-position: top;
        }

        .title {
            min-height: 3rem; 
            display: -webkit-box; 
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical; 
            overflow: hidden;
        }

        .btn-card {
            background-color: #4f46e5;
            border-color: #4f46e5;
            color: white;
        }

        .btn-card:hover {
            background-color: #4338ca;
            border-color: #4338ca;
            color: white;
        }

        @media (max-width: 992px) {
            .header {
                padding: 30px 15px;
            }

            .box-search {
                flex-direction: column;
                border-radius: 20px;
                padding: 15px;
                gap: 10px;
            }

            .input-section {
                width: 100%;
                padding-left: 20px;
            }

            .search-icon {
                left: 5%;
            }

            .divider {
                width: 100%;
                height: 1px;
                margin: 5px 0;
            }

            .fill-input,
            .fill-select,
            .submit-btn,
            .btn-container {
                width: 100% !important;
            }

            .fill-select {
                padding-left: 0;
            }

            .btn-container {
                flex-direction: column;
                gap: 8px;
            }

            .submit-btn {
                margin-left: 0 !important;
                margin-top: 0 !important;
            }

            .btn-add-mobile {
                position: fixed;
                bottom: 24px;
                right: 24px;
                width: 56px;
                height: 56px;
                background-color: #4f46e5;
                border-radius: 50px;
                z-index: 999;
                text-decoration: none;
                white-space: nowrap;
                overflow: hidden;
                padding: 0 18px;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .btn-add-mobile:hover {
                width: 220px;
                background-color: #4338ca;
            } 

            .btn-add-mobile .add-icon {
                min-width: 20px;
                margin-right: 7px;
                text-align: center;
            }

            .btn-add-mobile .btn-text {
                max-width: 0;
                opacity: 0;
                margin-right: 0;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .btn-add-mobile:hover .btn-text{
                max-width: 150px;
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    @include('competition.component.nav')

    <div class="header mb-5">
        <div>
            <h2>Anjurkan <span>Pertandingan Anda</span></h2>
            <h4 class="desc mx-auto">Mewujudkan pertandingan menarik dan memberi peluang peserta mempamerkan bakat, kreativiti dan kemahiran melalui platform yang mudah dan teratur.</h4>
        </div>

    <form action="{{ route('competition.host') }}" method="GET" class="mt-4">
        <div class="d-flex align-items-center box-search">
            <div class="input-section flex-grow-1 position-relative">
                <i class="bi bi-search search-icon text-secondary"></i>
                <input type="text" name="search" id="search" placeholder="Cari nama pertandingan..." value="{{ request('search') }}" class="form-control fill-input">
            </div>

            <div class="divider d-none d-md-block"></div>

            <div class="d-flex align-items-center input-section">
                <select name="category" id="category" class="form-select fill-select">
                    <option value="">Cari Kategori</option>
                    @foreach(['Teknologi', 'Inovasi', 'Sains', 'Akademik', 'Multimedia & Kreatif', 'Reka Bentuk', 
                              'Bahasa & Komunikasi', 'Seni & Kebudayaan', 'Lain-Lain'] as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}></div>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <div class="d-flex align-items-center btn-container w-100">
                <button type="submit" class="btn submit-btn ms-lg-2">Cari Pertandingan</button>
                @if(request()->anyFilled(['search', 'category', 'status']))
                    <a href="{{ route('competition.host') }}" class="btn btn-outline-danger reset-btn ms-lg-2">Reset <i class="bi bi-arrow-counterclockwise ms-1"></i></a>
                @endif
            </div>
        </div>

        <div class="d-flex flex-wrap justify-content-center align-items-center mt-4 pt-2 gap-2">
            <span class="status-label text-white">Status: </span>
            <a href="{{ route('competition.host', array_merge(request()->query(), ['status' => ''])) }}" class="status-btn {{ (empty(request('status')) && !request('search') && !request('category')) ? 'statusBtn-active' : 'statusBtn-inactive' }}">Semua</a>
            @foreach(['Draft', 'Upcoming', 'Ongoing', 'Completed'] as $sta)
                <a href="{{ route('competition.host', array_merge(request()->query(), ['status' => $sta])) }}" class="status-btn {{ request('status') == $sta ? 'statusBtn-active' : 'statusBtn-inactive' }}">{{ $sta }}</a>
            @endforeach
        </div>
    </form>
    </div>
    
    <div class="container">
        <p class="fw-bold text-muted"><i class="bi bi-grid-fill me-2"></i>{{ $competitions->count() }} Pertandingan Dijumpai</p>

        <a href="{{ route('competition.addcompetition') }}" class="btn-add-mobile d-flex d-md-none align-items-center shadow-lg">
            <i class="fas fa-plus text-white fs-4 add-icon"></i>
            <span class="btn-text text-white fw-semibold">Tambah Pertandingan</span>
        </a>
        
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
            <div class="col d-none d-md-block">
                <a href="{{ route('competition.addcompetition') }}" class="card h-100 text-decoration-none d-flex align-items-center justify-content-center add-card">
                    <div class="text-center">
                        <div class="add-icon-wrapper mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/>
                            </svg>
                        </div>
                        <span class="fw-bold fs-5 d-block text-secondary">Tambah Pertandingan</span>
                    </div>
                </a>
            </div>

            @foreach($competitions as $competition)
                <div class="col">
                    <div class="card h-100 position-relative overflow-hidden">
                        @if(is_null($competition->competitionStart) || $competition->status == 'Draft')
                            <p class="position-absolute end-0 m-2 badge bg-danger z-3">DRAFT</p>
                        @elseif($competition->competitionStart > now())
                            <p class="position-absolute end-0 m-2 badge bg-warning text-dark z-3">PUBLISHED</p>
                        @elseif($competition->competitionStart <= now() && $competition->competitionEnd >= now())
                            <p class="position-absolute end-0 m-2 badge bg-success z-3">ONGOING</p>
                        @elseif($competition->competitionEnd < now())
                            <p class="position-absolute end-0 m-2 badge bg-secondary z-3">COMPLETED</p>
                        @endif
                        <img src="{{ asset('competition-image/' . $competition->imagePoster) }}" alt="{{ $competition->competitionTitle }}" class="card-img-top image">
                        <div class="card-body d-flex flex-column">
                            <p class="card-text text-muted" style="font-size: 0.85rem;">{{ $competition->category ?? '.....' }}</p>
                            <h5 class="card-title fw-bold text-truncate-2 title">{{ $competition->competitionTitle }}</h5>
                            
                            <div class="d-flex gap-2 mb-3 mt-1">
                                @if($competition->participateType === 'Individual')
                                    <span class="badge bg-light text-dark border fw-normal"><i class="bi bi-people-fill me-1"></i> {{ $competition->participant_registration_count }} Peserta</span>
                                @elseif($competition->participateType === 'Team')
                                    <span class="badge bg-light text-dark border fw-normal"><i class="bi bi-people-fill me-1"></i> {{ $competition->participant_registration->unique('teamId')->count() }} Kumpulan</span>
                                @else
                                    <span class="badge bg-light text-dark border fw-normal">...</span>
                                @endif
                                <span class="badge bg-success-subtle text-success fw-semibold d-flex align-items-center">Jumlah Bayaran RM {{ number_format($competition->totalFees, 2) }}</span>
                            </div>

                            <p class="card-text small mb-2 text-secondary text-truncate"><i class="bi bi-geo-alt-fill text-danger me-2"></i>{{ $competition->venue ?? '.....' }}</p> 
                            <p class="card-text small mb-2 text-secondary"><i class="bi bi-clock-history me-1"></i> <strong>Tutup:</strong> {{ $competition->registerClose ? \Carbon\Carbon::parse($competition->registerClose)->format('d M Y') : '...' }}</p>
                            @if($competition->registerClose)
                                <p class="card-text small text-secondary text-truncate"><i class="bi bi-calendar3 text-primary me-2"></i>
                                    {{ \Carbon\Carbon::parse($competition->competitionStart)->format('d M') }} - {{ \Carbon\Carbon::parse($competition->competitionEnd)->format('d M Y') }}
                                </p>
                            @else
                                <p class="card-text small text-secondary text-truncate"><i class="bi bi-calendar3 text-primary me-2"></i>Belum ditetapkan</p>
                            @endif

                            <div class="mt-auto d-flex justify-content-end align-items-center border-top pt-3">
                                <a href="{{ route('competition.viewcompetition', $competition->id) }}" class="btn btn-card px-3 py-2 btn-sm fw-semibold">Urus <i class="bi bi-arrow-right ms-1"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</body>
</html>