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
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 35%, #e11d48 100%);
            color: white;
            /* background: linear-gradient(135deg, #dbeafe 0%, #eff6ff 100%); */
            padding: 60px 20px;
            width: 100%;
            min-height: 380px;
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
            border-radius: 50px;
            padding: 10px 32px;
            font-weight: 600;
            font-size: 0.95rem;
            background-color: #facc15;
            border-color: #facc15;
            transition: all 0.2s ease;
        }

        .submit-btn:hover {
            background-color: #eab308;
            border-color: #eab308;
            color: black;
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

        .card {
            border: 1px solid #e2e8f0;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            transform: translateY(-5px);
        }

        .image-container {
            overflow: hidden;
        }

        .image {
            height: 250px;
            object-fit: cover;
            object-position: top;
            transition: transform 0.3s ease-in-out;
        }

        .image:hover {
            transform: scale(1.1);
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

            .divider {
                width: 100%;
                height: 1px;
                margin: 5px 0;
            }

            .input-section {
                width: 100%;
                padding-left: 20px;
            }

            .search-icon {
                left: 5%;
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
        }
    </style>
</head>
<body>
    @include('competition.component.nav')

    <div class="header mb-5">
        <div>
            <h2>Cari <span>Peluang Anda</span></h2>
            <p class="desc mx-auto">Cari potensi diri dengan meneroka pertandingan yang dianjurkan dan <br>
                pilih cabaran yang sesuai untuk menguji serta menyerlahkan kemahiran anda.</p>
        </div>

        <form action="{{ route('competition.user') }}" method="GET" class="mt-4">
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
                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="d-flex align-items-center btn-container w-100">
                    <button type="submit" class="btn submit-btn ms-lg-2">Cari Pertandingan</button>
                    @if(request()->anyFilled(['search', 'category', 'status']))
                        <a href="{{ route('competition.user') }}" class="btn btn-outline-danger reset-btn ms-lg-2">Reset <i class="bi bi-arrow-counterclockwise ms-1"></i></a>
                    @endif
                </div>
            </div>

            <div class="d-flex flex-wrap justify-content-center align-items-center mt-4 pt-2 gap-2">
                <span class="status-label">Status: </span>
                <a href="{{ route('competition.user', array_merge(request()->except(['status', 'search', 'category']))) }}" class="status-btn {{ (empty(request('status')) && !request('search') && !request('category')) ? 'statusBtn-active' : 'statusBtn-inactive' }}">Semua</a>
                @foreach(['Upcoming', 'Ongoing', 'Completed'] as $sta)
                    <a href="{{ route('competition.user', array_merge(request()->query(), ['status' => $sta])) }}" class="status-btn {{ request('status') == $sta ? 'statusBtn-active' : 'statusBtn-inactive' }}">{{ $sta }}</a>
                @endforeach
            </div>
        </form>
    </div>

    <div class="container">
        @if($competitions->count() > 0)
            <p class="fw-bold text-muted"><i class="bi bi-grid-fill me-2"></i>{{ $competitions->count() }} Pertandingan Dijumpai</p>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                @foreach($competitions as $competition)
                    <div class="col">
                        <div class="card h-100 position-relative overflow-hidden">                    
                            @if($competition->competitionStart > now())
                                <p class="position-absolute end-0 m-2 badge bg-warning text-dark z-3">UPCOMING</p>
                            @elseif($competition->competitionStart <= now() && $competition->competitionEnd >= now())
                                <p class="position-absolute end-0 m-2 badge bg-success z-3">ONGOING</p>
                            @elseif($competition->competitionEnd < now())
                                <p class="position-absolute end-0 m-2 badge bg-secondary z-3">COMPLETED</p>
                            @endif
                            <div class="image-container">
                                <img src="{{ asset('competition-image/' . $competition->imagePoster) }}" alt="{{ $competition->competitionTitle }}" class="card-img-top image">
                            </div>
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start">
                                    <p class="card-text text-muted" style="font-size: 0.85rem;">{{ $competition->category }}</p>
                                    @if($competition->registerOpen >= now())
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle fw-semibold"><i class="bi bi-clock-history"></i>&nbsp Open Soon</span>
                                    @elseif($competition->registerOpen <= now() && $competition->registerClose >= now())
                                        <span class="badge bg-success-subtle text-success border border-success-subtle fw-semibold"><i class="bi bi-check-circle-fill"></i>&nbsp Open</span>
                                    @elseif($competition->registerClose <= now())
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-semibold"><i class="bi bi-x-circle-fill"></i>&nbsp Closed</span>
                                    @endif
                                </div>
                                <h5 class="card-title fw-bold text-truncate-2 title">{{ $competition->competitionTitle }}</h5>
                                
                                <div class="d-flex gap-2 mb-3 mt-1">
                                    @if($competition->participateType === 'Team')
                                        <span class="badge bg-light text-dark border fw-normal"><i class="bi bi-people-fill me-1"></i> {{ $competition->participateType }} ({{$competition->minimumParticipate}} - {{$competition->maximumParticipate}} pax)</span>
                                    @else
                                        <span class="badge bg-light text-dark border fw-normal"><i class="bi bi-person-fill me-1"></i> {{ $competition->participateType }}</span>
                                    @endif
                                    <span class="badge bg-light border text-dark fw-semibold d-flex align-items-center"><span class="fw-bold">MY</span>&nbsp {{ $competition->nationalFees > 0 ? 'RM ' . number_format($competition->nationalFees, 2) : 'Yuran percuma' }} |&nbsp<span class="fw-bold">INT</span>&nbsp {{ $competition->internationalFees > 0 ? 'RM ' . number_format($competition->internationalFees, 2) : 'Yuran percuma' }}</span>
                                </div>

                                <p class="card-text small mb-2 text-secondary text-truncate"><i class="bi bi-geo-alt-fill text-danger me-2"></i>{{ $competition->venue }}</p> 
                                <p class="card-text small text-secondary text-truncate"><i class="bi bi-calendar-event-fill text-primary me-2"></i>Tutup: {{ \Carbon\Carbon::parse($competition->registerClose)->format('d M Y') }}</p> 
                                
                                <div class="d-flex justify-content-between align-items-center border-top pt-3">
                                    @php
                                        $now = \Carbon\Carbon::now();
                                        $close = \Carbon\Carbon::parse($competition->registerClose);
                                        $open = \Carbon\Carbon::parse($competition->registerOpen);
                                        $daysLeft = $now->diffInDays($close, false);
                                        $hoursLeft = $now->diffInHours($close, false);
                                    @endphp
                                    
                                    @if($daysLeft >= 1 && $open < now())
                                        <span class="text-muted small"><i class="bi bi-hourglass-split me-1"></i>{{ $daysLeft }} Hari Lagi</span>
                                    @elseif($hoursLeft > 0 && $open < now())
                                        <span class="text-muted small"><i class="bi bi-hourglass-split me-1"></i>{{ $hoursLeft }} Jam Lagi</span>
                                    @elseif($open > now())
                                        <span class="text-warning fw-semibold small">Pendaftaran belum dibuka</span>
                                    @else
                                        <span class="text-danger fw-semibold small">Pendaftaran tutup</span>
                                    @endif
                                    <a href="{{ route('competition.info', [$competition->id, 'mode' => 'user']) }}" class="btn btn-card px-3 py-2 btn-sm fw-semibold">Lihat Detail <i class="bi bi-arrow-right ms-1"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="col-12 text-center py-1">
                <i class="bi bi-trophy text-muted display-5 mb-3 d-block"></i>
                <p class="text-muted fs-5">Tiada pertandingan dijumpai.</p>
            </div>
        @endif
    </div>
</body>
</html>