<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <title>PRiM | Competition</title>

    <style>
        body {
            margin: 0;
            margin-bottom: 50px;
            background-color: #f8fafc;
            overflow-x: hidden;
        }

        .trophy-image {
            width: 50px;
        }

        .result-header-card {
            background: linear-gradient(135deg, #f0f7ff 0%, #e8f1fd 100%);
            border: 1px solid #e1ecf8;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        }

        .icon-circle {
            width: 48px;
            height: 48px;
            flex-shrink: 0;
        }

        .header-title {
            color: #1e293b;
            letter-spacing: -0.5px;
        }

        .quote-badge {
            background: rgba(255, 255, 255, 0.85) !important;
            backdrop-filter: blur(8px);
            border: 1px solid rgba(219, 234, 254, 0.7) !important;
            transform: skewX(-4deg);
        }

        .quote-badge p {
            transform: skewX(4deg);
        }

        .header-bg-shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(40px);
            opacity: 0.6;
            z-index: 1;
        }

        .shape-1 {
            width: 150px;
            height: 150px;
            background-color: #dbeafe;
            top: -20px;
            right: 30%;
        }

        .shape-2 {
            width: 200px;
            height: 200px;
            background-color: #fef3c7;
            bottom: -50px;
            right: 10%;
        }

        .poster {
            width: 150px;
            height: 100px;
            object-fit: cover;
            object-position: top;
        }

        .fs-7 {
            font-size: 0.85rem;
        }

        /* Styling Utama Jadual Leaderboard */
.leaderboard-table {
    border-collapse: separate;
    border-spacing: 0;
}

.leaderboard-table thead th {
    background-color: #f8fafc;
    font-size: 0.85rem;
    font-weight: 600;
    padding-top: 14px;
    padding-bottom: 14px;
    border-bottom: 1px solid #e2e8f0;
}

.leaderboard-table tbody td {
    padding-top: 16px;
    padding-bottom: 16px;
    border-bottom: 1px solid #f1f5f9;
    font-size: 0.95rem;
}

.leaderboard-table tbody tr:last-child td {
    border-bottom: none;
}

/* Icon Pingat (Rank 1, 2, 3) */
.rank-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.rank-icon.gold {
    background-color: #fef3c7;
    color: #d97706;
}

.rank-icon.silver {
    background-color: #e2e8f0;
    color: #64748b;
}

.rank-icon.bronze {
    background-color: #ffedd5;
    color: #c2410c;
}

/* Lencana Keputusan (Pill Badges) */
.result-badge {
    display: inline-block;
    padding: 6px 16px;
    border-radius: 50rem;
    font-size: 0.825rem;
    font-weight: 600;
    text-align: center;
}

.badge-gold {
    background-color: #fef3c7;
    color: #b45309;
}

.badge-silver {
    background-color: #f1f5f9;
    color: #475569;
}

.badge-bronze {
    background-color: #ffedd5;
    color: #9a3412;
}

.badge-place {
    background-color: #f3e8ff;
    color: #6b21a8;
}

        .form-control:focus, .form-select:focus {
            box-shadow: none;
        }

        .input-group:focus-within .input-group-text,
        .input-group:focus-within .form-control,
        .input-group:focus-within .form-select {
            background-color: white !important;
            border-color: #4f46e5;
        }

        .cursor-pointer {
            cursor: pointer;
        }
    </style>
</head>
<body>
    @include('competition.component.nav')

    <div class="result-header-card px-5 py-3 mb-4 position-relative overflow-hidden">
        
        <div class="header-bg-shape shape-1"></div>
        <div class="header-bg-shape shape-2"></div>

        <div class="row align-items-center position-relative" style="z-index: 2;">
            <div class="col-lg-7 col-md-7 mb-3 mb-md-0">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <img class="trophy-image" src="{{ asset('assets/landing-page/img/competition-logo/trophy-logo.png') }}" alt="Trophy">
                    <h3 class="fw-bold text-dark mb-0 header-title">Keputusan Pertandingan</h3>
                </div>
            </div>

            <div class="col-lg-5 col-md-5 d-flex align-items-center justify-content-md-end justify-content-center gap-5">

                <div class="quote-badge bg-white bg-opacity-75 backdrop-blur px-4 py-3 rounded-4 border border-light shadow-sm text-center text-md-end" style="max-width: 280px;">
                    <p class="fst-italic text-primary-emphasis mb-0 small fw-medium">
                        "Every great achievement starts with a single step."
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="mt-5 mb-4">
            <button id="yourCompetitionBtn" class="btn fw-bold {{ $activeTab == 'your' ? 'btn-primary' : 'btn-outline-primary' }}">Pertandingan Disertai</button>
            <button id="allCompetitionBtn" class="btn fw-bold {{ $activeTab == 'all' ? 'btn-primary' : 'btn-outline-primary' }}">Semua Pertandingan</button>
        </div>

        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <form action="{{ route('competition.user.displayresult') }}" method="GET" id="filterForm">
                <input type="hidden" name="tab" id="activeTabInput" value="{{ $activeTab }}">
                
                <div class="row g-3 align-items-center">
                    <div class="col-md-7 col-lg-8">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 rounded-start-pill ps-3 text-secondary">
                                <i class="bi bi-search text-secondary"></i>
                            </span>
                            
                            <input type="text" name="search" id="search" placeholder="Cari nama pertandingan..." value="{{ request('search') }}" 
                            class="form-control bg-light border-start-0 rounded-end-pill py-2 text-dark">
                        </div>
                    </div>
                    
                    <div class="col-md-5 col-lg-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 rounded-start-pill ps-3 text-secondary">
                                <i class="bi bi-funnel"></i>
                            </span>

                            <select name="category" id="category" class="form-select bg-light border-start-0 rounded-end-pill py-2 text-dark cursor-pointer">
                                <option value="">Pilih Kategori</option>
                                @foreach(['Teknologi', 'Inovasi', 'Sains', 'Akademik', 'Multimedia & Kreatif', 'Reka Bentuk', 
                                'Bahasa & Komunikasi', 'Seni & Kebudayaan', 'Lain-Lain'] as $cat)
                                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div id="yourCompetition" class="{{ $activeTab == 'your' ? '' : 'd-none' }}">
            <h4 class="fw-bold my-4">Pertandingan Disertai</h4>

            @forelse($competitionById as $competitions)
                @php
                    $collapseById = 'competitionId-' . $competitions->id;
                    
                    $matchingParticipant = $participant->first(function($p) use ($competitions) {
                        return optional($p->team)->team_registration && $p->team->team_registration->contains('id', $competitions->id);
                    });

                    $userReg = optional(optional($matchingParticipant)->team)->team_registration 
                        ? optional($matchingParticipant->team->team_registration->where('id', $competitions->id)->first()) 
                        : null;
                        
                    $userRank = optional($userReg->pivot)->winnerPlacement;
                    $result = $competitions->statusResult == 'Published' || !empty($userRank);
                @endphp

                <div class="border rounded shadow-sm bg-white p-3 mb-2">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex">
                            <img class="rounded-3 poster" src="{{ asset('competition-image/' . $competitions->imagePoster) }}" alt="Poster">

                            <div class="ms-4">
                                <div class="fw-bold">{{ $competitions->competitionTitle }}</div>
                                <div>{{ $competitions->category }}</div>
                                <div><i class="bi bi-calendar-week me-1"></i> {{ \Carbon\Carbon::parse($competitions->competitionStart)->format('d M Y') }} - {{ \Carbon\Carbon::parse($competitions->competitionEnd)->format('d M Y') }}</div>
                            </div>
                        </div>

                        <div class="d-flex ms-auto">
                            <div>
                                <h6>Keputusan Anda</h6>

                                <div class="d-flex me-5">
                                    <i class="bi bi-trophy me-2"></i>
                                    @if($competitions->statusResult == 'Published')
                                        <div>{{ $userRank }}</div>
                                    @else
                                        <div>Not Available</div>
                                    @endif
                                </div>
                            </div>

                            <button type="button" data-target="#{{ $collapseById }}" 
                            class="toggle-result-btn btn btn-primary rounded-pill px-3 py-2 text-nowrap fw-semibold fs-7 shadow-sm align-self-center">
                                <i class="bi bi-eye-fill me-1"></i> Lihat Keputusan
                            </button>
                        </div>
                    </div>
                </div>

                <div id="{{ $collapseById }}" class="collapse-box d-none card border-0 shadow-sm rounded-4 overflow-hidden mb-4 p-0">
                    <div class="table-responsive">
                        <table class="table leaderboard-table align-middle text-center mb-0">
                            <thead>
                                <tr>
                                    <th scope="col" style="width: 12%;">Rank</th>
                                    <th scope="col">Name</th>
                                    <th scope="col" style="width: 20%;">Result</th>
                                </tr>
                            </thead>
                            
                            <tbody>
                                @php
                                    $isPublish = $competitions->statusResult == 'Published';
                                @endphp

                                @if($isPublish)
                                    @foreach($participantByCompetition[$competitions->id] as $participants)
                                        @php
                                            $participateType = optional($participants->team->team_registration->first())->participateType ?? 'Team';
                                            $register = $participants->team->team_registration->where('id', $competitions->id)->first();
                                            $userRank = optional($register->pivot)->winnerPlacement;

                                            $badges = ['Gold Medal'   => 'badge-gold',
                                                    'Silver Medal' => 'badge-silver',
                                                    'Bronze Medal' => 'badge-bronze',
                                                    ];
                                            $badge = $badges[$userRank] ?? 'badge-place';
                                        @endphp

                                        <tr>
                                            <td>
                                                @if($userRank == 'Gold Medal')
                                                    <div class="rank-icon gold">
                                                        <i class="bi bi-award-fill"></i>
                                                    </div>
                                                @elseif($userRank == 'Silver Medal')
                                                    <div class="rank-icon silver">
                                                        <i class="bi bi-award-fill"></i>
                                                    </div>
                                                @elseif($userRank == 'Bronze Medal')
                                                    <div class="rank-icon bronze">
                                                        <i class="bi bi-award-fill"></i>
                                                    </div>
                                                @else
                                                    <span class="fw-bold text-dark">{{ $loop->iteration }}</span>
                                                @endif
                                            </td>

                                            <td class="text-start">
                                                <span class="fw-bold d-flex">{{ $participants->name }}</span>

                                                @if($participateType == 'Team')
                                                    <span class="text-secondary fw-medium">{{ $participants->team->groupName }}</span>
                                                @endif
                                            </td>

                                            <td class="pe-4">
                                                <span class="result-badge {{ $badge }}">{{ $userRank }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            Keputusan belum direkodkan lagi.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="py-3 fw-bold text-secondary d-flex align-items-center justify-content-center fs-5">
                    <i class="bi bi-trophy fs-1 me-3"></i>Tiada pertandingan dijumpai.
                </div>
            @endforelse
        </div>

        <div id="allCompetition" class="{{ $activeTab == 'all' ? '' : 'd-none' }}">
            <h4 class="fw-bold my-4">Semua Pertandingan</h4>

            @forelse($allCompetition as $competitions)
                @php
                    $collapseById = 'competitionId-' . $competitions->id;
                    
                    $matchingParticipant = $participant->first(function($p) use ($competitions) {
                        return optional($p->team)->team_registration && $p->team->team_registration->contains('id', $competitions->id);
                    });

                    $userReg = optional(optional($matchingParticipant)->team)->team_registration 
                        ? optional($matchingParticipant->team->team_registration->where('id', $competitions->id)->first()) 
                        : null;
                        
                    $userRank = optional(optional($userReg)->pivot)->winnerPlacement;
                    $result = $competitions->statusResult == 'Published' || !empty($userRank);
                @endphp
                
                <div class="border rounded shadow-sm bg-white p-3 mb-2">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex">
                            <img class="rounded-3 poster" src="{{ asset('competition-image/' . $competitions->imagePoster) }}" alt="Poster">

                            <div class="ms-4">
                                <div class="fw-bold">{{ $competitions->competitionTitle }}</div>
                                <div>{{ $competitions->category }}</div>
                                <div><i class="bi bi-calendar-week me-1"></i> {{ \Carbon\Carbon::parse($competitions->competitionStart)->format('d M Y') }} - {{ \Carbon\Carbon::parse($competitions->competitionEnd)->format('d M Y') }}</div>
                            </div>
                        </div>

                        <div class="d-flex ms-auto">
                            <div>
                                <h6>Jumlah Penyertaan</h6>

                                <div class="d-flex me-5">
                                    <i class="bi bi-people me-2"></i>
                                    @php
                                        $count = $participantByCompetition->has($competitions->id) 
                                            ? $participantByCompetition->get($competitions->id)->count() 
                                            : 0;
                                    @endphp

                                    @if($competitions->participateType == 'Team')
                                        <div>{{ $count }} Kumpulan</div>
                                    @else
                                        <div>{{ $count }} Peserta</div>
                                    @endif
                                </div>
                            </div>

                            <button type="button" data-target="#{{ $collapseById }}"
                            class="toggle-result-btn btn btn-primary rounded-pill px-3 py-2 text-nowrap fw-semibold fs-7 shadow-sm align-self-center">
                                <i class="bi bi-eye-fill me-1"></i> Lihat Keputusan
                            </button>
                        </div>
                    </div>
                </div>

                <div id="{{ $collapseById }}" class="collapse-box d-none card border-0 shadow-sm rounded-4 overflow-hidden mb-4 p-0">
                    <div class="table-responsive">
                        <table class="table leaderboard-table align-middle text-center mb-0">
                            <thead>
                                <tr>
                                    <th scope="col" style="width: 12%;">Rank</th>
                                    <th scope="col">Name</th>
                                    <th scope="col" style="width: 20%;">Result</th>
                                </tr>
                            </thead>
                            
                            <tbody>
                                @php
                                    $isPublish = $competitions->statusResult == 'Published';
                                @endphp

                                @if($isPublish)
                                    @foreach($participantByCompetition[$competitions->id] as $participants)
                                        @php
                                            $participateType = optional($participants->team->team_registration->first())->participateType ?? 'Team';
                                            $register = $participants->team->team_registration->where('id', $competitions->id)->first();
                                            $userRank = optional($register->pivot)->winnerPlacement;

                                            $badges = ['Gold Medal'   => 'badge-gold',
                                                    'Silver Medal' => 'badge-silver',
                                                    'Bronze Medal' => 'badge-bronze',
                                                    ];
                                            $badge = $badges[$userRank] ?? 'badge-place';
                                        @endphp

                                        <tr>
                                            <td>
                                                @if($userRank == 'Gold Medal')
                                                    <div class="rank-icon gold">
                                                        <i class="bi bi-award-fill"></i>
                                                    </div>
                                                @elseif($userRank == 'Silver Medal')
                                                    <div class="rank-icon silver">
                                                        <i class="bi bi-award-fill"></i>
                                                    </div>
                                                @elseif($userRank == 'Bronze Medal')
                                                    <div class="rank-icon bronze">
                                                        <i class="bi bi-award-fill"></i>
                                                    </div>
                                                @else
                                                    <span class="fw-bold text-dark">{{ $loop->iteration }}</span>
                                                @endif
                                            </td>

                                            <td class="text-start">
                                                <span class="fw-bold d-flex">{{ $participants->name }}</span>

                                                @if($participateType == 'Team')
                                                    <span class="text-secondary fw-medium">{{ $participants->team->groupName }}</span>
                                                @endif
                                            </td>

                                            <td class="pe-4">
                                                <span class="result-badge {{ $badge }}">{{ $userRank }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            Keputusan belum direkodkan lagi.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="py-3 fw-bold text-secondary d-flex align-items-center justify-content-center fs-5">
                    <i class="bi bi-trophy fs-1 me-3"></i>Tiada pertandingan dijumpai.
                </div>
            @endforelse
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.toggle-result-btn').on('click', function(e) {
                e.preventDefault();

                var $btn = $(this);
                var targetId = $btn.data('target');
                var $targetBox = $(targetId);

                $('.collapse-box').not($targetBox).filter(':visible').each(function() {
                    var $openBox = $(this);
                    var openBoxById = '#' + $openBox.attr('id');

                    $('.toggle-result-btn[data-target="' + openBoxById + '"]').html('<i class="bi bi-eye-fill me-1"></i> Lihat Keputusan');
                    $openBox.slideUp(300);
                });

                if ($targetBox.hasClass('d-none')) {
                    $targetBox.removeClass('d-none').hide();
                }

                $targetBox.slideToggle(300, function() {
                    if ($targetBox.is(':visible'))
                    {
                        $btn.html('<i class="bi bi-eye-slash-fill me-1"></i> Tutup Keputusan');
                    }
                    else
                    {
                        $btn.html('<i class="bi bi-eye-fill me-1"></i> Lihat Keputusan');
                    }
                });
            });

            $('#yourCompetitionBtn').click(function() {
                $('#activeTabInput').val('your');
                $('#yourCompetition').removeClass('d-none').show();
                $('#allCompetition').hide();

                $(this).removeClass('btn-outline-primary').addClass('btn-primary');
                $('#allCompetitionBtn').removeClass('btn-primary').addClass('btn-outline-primary');
            });

            $('#allCompetitionBtn').click(function() {
                $('#activeTabInput').val('all');
                $('#yourCompetition').hide();
                $('#allCompetition').removeClass('d-none').show();

                $(this).removeClass('btn-outline-primary').addClass('btn-primary');
                $('#yourCompetitionBtn').removeClass('btn-primary').addClass('btn-outline-primary');
            });

            $('#search, #category').on('input change', function() {
                $('#filterForm').submit();
            });
        });
    </script>
</body>
</html>