<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <title>PRiM | Competition</title>

    <style>        
        body {
            margin: 0;
            margin-bottom: 50px;
            background-color: #f8fafc;
            overflow-x: hidden;
        }

        .accent-bar {
            width: 4px;
            height: 28px;
            background: linear-gradient(135deg, #6366f1 0%, #ec4899 100%);
            border-radius: 4px;
        }

        .btn-search {
            background-color: #4f46e5;
            font-weight: 500;
            font-size: 0.9rem;
            padding: 0.5rem 1.25rem;
        }

        .btn-search:hover {
            background-color: #4338ca;
        }

        .filter-btn {
            background-color: #f1f5f9;
            color: #475569;
            padding: 6px 14px;
            font-size: 0.85rem;
            font-weight: 500;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }
        
        .filter-btn:hover {
            background-color: #e2e8f0;
            color: #1e293b;        
        }

        .filter-btn.active {
            background-color: #4f46e5;
            border-color: #4f46e5;
            color: white;
        }

        .btn-export {
            font-size: 0.85rem;
            font-weight: 500;
            padding: 6px 14px;
        }

        .table-custom {
            width: 100%;
            text-align: center;
            margin-bottom: 0;
        }
     
        .table-custom thead th {
            background-color: #f8fafc;
            color: #1e293b;
            font-weight: 700;
            font-size: 0.85rem;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            border-bottom: 2px solid #e2e8f0;
            padding: 0.85rem 1rem;
        }

        .table-custom tbody td {
            padding: 10px;
        }

        .badge-pending {
            background-color: #fef08a;
            color: #854d0e;
            font-weight: 600;
            font-size: 0.8rem;
        }

        .badge-paid {
            background-color: #dcfce7;
            color: #166534;
            font-weight: 600;
            font-size: 0.8rem;
        }

        .badge-free {
            background-color: #e0f2fe;
            color: #0369a1;
            font-weight: 600;
            font-size: 0.8rem;
        }

        .btn-outline-primary, .btn-padam {
            font-size: 0.85rem;
            padding: 0.35rem 0.75rem;
        }

        .drop-header {
            font-size: 13px;
            letter-spacing: 0.5px;
        }

        .all-institute {
            position: relative;
            overflow: hidden;
        }

        .all-institute::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 7px;
            background: linear-gradient(135deg, #6366f1 0%, #ec4899 100%);
        }

        .institution-Name {
            display: inline-block;
            max-width: 100%;
        }
        
        .toggle-clue {
            font-size: 0.70rem; 
            white-space: nowrap; 
            cursor: pointer;
        }

        @media(max-width: 992px) {
            .btn-search {
                width: 100%;
            }

            .btn-reset {
                width: 100%;
                margin-top: 10px;
            }

            .filter-group {
                width: 100%;
                display: flex;
                flex-wrap: wrap;
                gap: 0.25rem;
            }

            .filter-btn {
                flex: 1;
                padding: 8px 8px;
                font-size: 0.75rem;
            }

            .toggle-clue {
                display: none;
            }
        }
    </style>
</head>

<body>
    @include('competition.component.nav')

    <div class="container mt-5">
        <div class="d-flex align-items-center gap-2">
            <div class="accent-bar"></div>
            <h3 class="fw-bold mb-1">Urus Pendaftaran Peserta</h3>
        </div>
        <p class="text-muted small mb-0">Pengurusan dan semakan rekod pendaftaran untuk semua pertandingan.</p>

        <div class="row g-3 mt-4">
            <div class="col-md-2">
                <div class="card border-0 rounded-3 px-3 py-2 bg-white shadow-sm all-institute">
                    <div class="d-flex">
                        <span class="text-muted fw-semibold">Semua Institusi</span>
                    </div>
                    <div>
                        <span class="fw-bold fs-3 me-2">{{ $participate->count() }}</span><span class="text-muted"> Peserta</span>
                    </div>
                </div>
            </div>

            @foreach($totalRegisterByInstitution as $index => $institution)
                <div class="col-md-2 item-institute {{ $index >= 4 ? 'd-none' : '' }}">
                    <div class="card border-0 rounded-3 px-3 py-2 bg-white shadow-sm">
                        <div class="d-flex justify-content-between align-items-start overflow-hidden institution" title="{{ $institution->instituteName }}">
                            <span class="text-muted fw-semibold text-truncate institution-Name" data-name="{{ $institution->instituteName }}">
                                {{ $institution->instituteName }}
                            </span>
                            <small class="text-primary fw-bold toggle-clue d-none">(more)</small>
                            <img src="{{ $institution->instituteLogo ? asset('images/institute-logo/' . $institution->instituteLogo) : '' }}" alt="{{ $institution->insituteName }}" 
                                style="width: 23px; height:auto;">
                        </div>

                        <div>
                            <span class="fw-bold fs-3 me-2">{{ $institution->participant_count }}</span><span class="text-muted"> Peserta</span>
                        </div>
                    </div>
                </div>
            @endforeach

            @if(count($totalRegisterByInstitution) > 5)
                <div class="col-md-2" id="show-more-institution">
                    <div class="card border-0 rounded-3 px-3 py-2 bg-white shadow-sm h-100 justify-content-center align-items-center" id="toggle-show-more" style="cursor: pointer;">
                        <div class="fw-bold text-primary" id="toggle-card-text">
                            +{{ count($totalRegisterByInstitution) - 4 }} Lagi
                        </div>
                        <small class="text-muted" id="toggle-card-subtext">Lihat Semua Institusi</small>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <form action="{{ route('competition.hostrecord') }}" method="GET">
        <div class="gap-3 mb-4 container mt-4">
            <div class="row g-4 align-items-end">
                <div class="col-md-3">
                    <label for="search" class="form-label fw-semibold small mb-1">Cari</label>
                    <input type="text" name="search" id="search" placeholder="Cari nama peserta..." value="{{ request('search') }}" class="form-control">
                </div>

                <div class="col-md-3">
                    <label for="title" class="form-label fw-semibold small mb-1">Pilih Pertandingan</label>
                    <select name="title" id="title" class="form-select @error('title') is-invalid @enderror">
                        <option value="all" selected>Pilih Pertandingan</option>
                        @foreach($competitions as $competition)
                            <option value="{{ $competition }}" {{ request('title') == $competition ? 'selected' : '' }}>{{ $competition }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-auto">
                    <button type="submit" class="btn btn-search text-white"><i class="fa fa-search me-1"></i> Cari Pertandingan</button>
                    @if(request()->anyFilled(['search', 'title']))
                        <a href="{{ route('competition.hostrecord') }}" class="btn btn-outline-danger btn-reset">Reset<i class="bi bi-arrow-counterclockwise ms-1"></i></a>
                    @endif
                </div>
            </div>

            <div class="d-flex flex-column flex-lg-row gap-4 justify-content-between mt-3">
                <div class="filter-group">
                    <button type="submit" name="status" value="all" class="btn filter-btn {{ !request()->filled('search') && request('status', 'all') == 'all' ? 'active' : '' }}">Semua</button>
                    <button type="submit" name="status" value="pending" class="btn filter-btn {{ request('status') == 'pending' ? 'active' : '' }}">Menunggu Pembayaran</button>
                    <button type="submit" name="status" value="paid" class="btn filter-btn {{ request('status') == 'paid' ? 'active' : '' }}">Telah Bayar</button>
                </div>

                <div class="align-self-end align-self-lg-auto">
                    <a href="{{ route('competition.exportparticipant', request()->query()) }}" class="btn btn-success btn-export"><i class="fas fa-file-excel"></i>&nbsp Eksport ke Excel</a>
                </div>
            </div>
        </div>
    </form>

    <div class="container">
        <div class="bg-light border rounded-top py-2 px-3 d-flex flex-column flex-md-row align-items-md-center align-items-start justify-content-between gap-3 gap-md-0">
            <div>
                <span class="fw-semibold" style="color: #4f46e5;">Senarai Pertandingan</span>
                <h4 class="fw-bold mb-2">{{ request('title') && request('title') !== 'all' ? request('title') : 'Semua Pertandingan' }}</h4>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <div>
                    <span class="text-muted small fw-semibold">Jumlah Pendaftaran: </span>
                    <span class="badge bg-dark">{{ $participants->count() }}</span>
                </div>
                
                <div class="d-flex gap-2">
                    <span class="badge bg-light text-dark border">
                        <i class="bi bi-person-fill me-1"></i>
                        Individu: {{ $totalIndividu }}
                    </span>
                    <span class="badge bg-primary text-primary bg-opacity-10 border border-primary border-opacity-10">
                        <i class="bi bi-people-fill me-1"></i>
                        Kumpulan: {{ $totalTeam }}
                    </span>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-custom text-center align-middle">
                <thead class="table-secondary">
                    <tr>
                        <th scope="col" style="width: 6%;">No</th>
                        <th scope="col" style="width: 25%;">Kumpulan / Individu</th>
                        <th scope="col" style="width: 18%;">No Telefon</th>
                        <th scope="col" style="width: 17%;">Tarikh Daftar</th>
                        <th scope="col" style="width: 14%;">Status</th>
                        <th scope="col">Tindakan</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($participants as $participant)
                        @php
                            $team = $participant->team->team_registration->first();
                            $bridge = $team->pivot;
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="d-flex flex-column">
                                    @if($team->participateType === 'Individual')
                                        <span class="d-flex justify-content-start ms-2 fw-bold">
                                            {{ $participant->name ?? 'N/A' }} 
                                            <span class="badge bg-light text-secondary border fw-semibold d-flex align-items-center justify-content-center ms-2" style="font-size: 10px;">Individu</span>
                                        </span> 
                                        <small class="d-block text-truncate text-start ms-2 text-muted" style="font-size: 12px; max-width: 290px;">IC/Pasport: {{ $participant->icNo ?? 'N/A'}}&nbsp Email: {{ $participant->email ?? 'N/A' }}</small>
                                    @else
                                    <div class="d-flex align-items-center">
                                        <button type="button" class="btn btn-sm btn-light border rounded-circle ms-2 btn-toggle-team" style="width: 32px; height:32px; padding:0;">
                                            <i class="bi bi-chevron-down text-secondary"></i>
                                        </button>

                                        <div>
                                            <span class="d-flex justify-content-start ms-2 fw-bold">
                                                {{ $participant->team->groupName ?? 'N/A' }} 
                                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10 d-flex align-items-center justify-content-center ms-2" style="font-size: 10px;">Team ({{ $participant->team->participant->count() }} members)</span>
                                            </span> 
                                            <small class="d-block text-truncate text-start ms-2 text-muted" style="font-size: 12px; max-width: 290px;">Ahli: {{ $participant->formatted_members ?? 'N/A'}}</small>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </td>
                            <td class="fw-medium">{{ $participant->noTel ?? 'N/A' }}</td>
                            <td class="text-muted small">{{ $bridge->registeredDate ? \Carbon\Carbon::parse($bridge->registeredDate)->format('d M Y, h:i A') : 'N/A' }}</td>
                            <td>
                                @if($bridge->statusPayment == 'Pending')
                                    <span class="badge badge-pending text-dark">Menunggu Pembayaran</span>
                                @elseif($bridge->statusPayment == 'Paid')
                                    <span class="badge badge-paid">Selesai Bayar</span>
                                @elseif($bridge->statusPayment == 'Free')
                                    <span class="badge badge-free text-dark">Tiada Bayaran</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    @if($bridge->statusPayment == 'Pending')
                                        <a href="{{ route('competition.viewregister', $participant->id) }}" class="btn btn-outline-primary"><i class="bi bi-eye"></i> Lihat</a>
                                        <form action="{{ route('competition.deleteregister', $participant->id) }}" class="deleteRegister d-inline" data-name="{{ $participant->name ?? '' }}" data-group="{{ $participant->team->groupName ?? '' }}" data-type="{{ $team->participateType }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-padam"><i class="bi bi-trash"></i> Padam</button>
                                        </form>
                                    @else
                                        <a href="{{ route('competition.viewregister', $participant->id) }}" class="btn btn-outline-primary"><i class="bi bi-eye"></i> Lihat</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    
                        @if($team->participateType === 'Team')
                            <tr class="team-details-row d-none">
                                <td colspan="6" class="p-0 border-bottom">
                                    <div class="p-3 bg-light border-start border-end">
                                        <div class="card border shadow-sm">
                                            <div class="card-header bg-white py-2">
                                                <span class="fw-bold text-uppercase text-secondary drop-header">
                                                    <i class="bi bi-people-fill me-1 text-primary"></i> Senarai ahli kumpulan:
                                                    <span class="text-dark">{{ $participant->team->groupName }}</span>
                                                </span>
                                            </div>

                                            <div class="table-responsive">
                                                <table class="table table-sm table-hover mb-0 align-middle text-center">
                                                    <thead class="table-light text-muted">
                                                        <tr>
                                                            <th>#</th>
                                                            <th style="width: 35%;">Nama Peserta</th>
                                                            <th>IC</th>
                                                            <th>No Telefon</th>
                                                            <th>Email</th>
                                                            <th>Peranan</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        @foreach($participant->team->participant as $member)
                                                        <tr>
                                                            <td class="text-muted">{{ $loop->iteration }}</td>
                                                            <td class="text-start fw-bold">{{ $member->name }}</td>
                                                            <td class="text-muted">{{ $member->icNo }}</td>
                                                            <td class="fw-medium">{{ $member->noTel }}</td>
                                                            <td class="text-muted">{{ $member->email }}</td>
                                                            <td>
                                                                @if($member->isLeader === 'Leader')
                                                                    <span class="badge text-white" style="background-color: #4f46e5;">Ketua</span>
                                                                @else
                                                                    <span class="badge bg-secondary text-white">Ahli</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Tiada Rekod dijumpai.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.btn-toggle-team').on('click', function (e) {
                e.preventDefault();

                var $btn = $(this);
                var $row = $btn.closest('tr').next('.team-details-row');

                $row.toggleClass('d-none');

                    if (!$row.hasClass('d-none'))
                    {
                        $btn.find('i').removeClass('bi bi-chevron-down text-secondary').addClass('bi bi-chevron-up text-primary');
                    }
                    else
                    {
                        $btn.find('i').removeClass('bi bi-chevron-up text-primary').addClass('bi bi-chevron-down text-secondary');
                    }
            });

            function resetTruncate()
            {            
                $('.institution-Name').each(function () {
                    const $name = $(this);
                    const fullName = $name.data('name') || $name.text();
                    const match = fullName.match(/\(([^]+)\)/);

                    $name.addClass('text-truncate').css('white-space', 'nowrap');
                    $name.siblings('.toggle-clue').text('(more)');

                    if (match && match[1])
                    {
                        $name.text(match[1].trim());
                    }
                    else
                    {
                        $name.text(fullName.trim());
                    }
                });
            }
            
            resetTruncate();

            function checkTruncate() {
                $('.institution').each(function() {
                    const $text = $(this).find('.institution-Name');
                    const $clue = $(this).find('.toggle-clue');
                    const $noTruncate = $text[0];

                    if ($noTruncate && $noTruncate.scrollWidth > $noTruncate.clientWidth)
                    {
                        $clue.removeClass('d-none');
                        $(this).css('cursor', 'pointer');
                    }
                    else
                    {
                        $clue.addClass('d-none');
                        $(this).css('cursor', 'default');
                    }
                });
            }

            checkTruncate();

            $(document).on('click', '.institution', function () {
                const $text = $(this).find('.institution-Name');
                const $clue = $(this).find('.toggle-clue');
                const name = $text.data('name');

                if ($clue.hasClass('d-none')) return;

                $text.toggleClass('text-truncate');

                if ($text.hasClass('text-truncate'))
                {
                    $text.css('white-space', 'nowrap');
                    $clue.text('(more)');

                    const match = name ? name.match(/\(([^]+)\)/) : null;

                    if (match && match[1])
                    {
                        $text.text(match[1].trim());
                    }
                    else
                    {
                        $text.text(name.trim());
                    }
                    checkTruncate();
                }
                else
                {
                    $text.css('white-space', 'normal');

                    if (name)
                    {
                        $text.text(name);
                    }
                    $clue.text('(less)').removeClass('d-none');
                }
            });

            let rowExpand = false;
            const limitDisplay = 4;

            $('#toggle-show-more').on('click', function () {
                rowExpand = !rowExpand;

                $('.item-institute').each(function (index) {
                    if (index >= limitDisplay)
                    {
                        $(this).toggleClass('d-none', !rowExpand);
                    }
                });

                if (rowExpand)
                {
                    resetTruncate();
                    $('#toggle-card-text').text('- Sembunyikan');
                    $('#toggle-card-subtext').text('Tutup');
                    $('#show-more-institution').appendTo('#show-more-institution').parent();
                    checkTruncate();
                }
                else
                {
                    const hideCount = $('.item-institute').length - limitDisplay;
                    $('#toggle-card-text').text(`+${hideCount} Lagi`);
                    $('#toggle-card-subtext').text('Lihat Semua');
                }

                if (typeof filter === 'function')
                {
                    filter();
                }
            });
        });

        document.querySelectorAll('.deleteRegister').forEach(function (currentForm) {
            currentForm.addEventListener('submit', function (e) {
                e.preventDefault();

                let form = this;
                let formData = new FormData(form);

                let name = form.dataset.name;
                let group = form.dataset.group;
                let participateType = form.dataset.type;
                let actionUrl = form.action;

                let display = (participateType === 'Team') ? group : name;

                Swal.fire({
                    icon: 'warning',
                    title: 'Adakah anda pasti?',
                    text: `Adakah anda pasti ingin memadam rekod pendaftaran ${display}?`,
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Padam',
                    cancelButtonText: 'Batal'
                }).then ((result) => {
                    if (result.isConfirmed)
                    {
                        fetch(actionUrl, {
                            method: "POST",
                            body: formData,
                            headers: {
                                "Accept" : "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            }
                        })
                        .then (response => response.json())
                        .then (data => {
                            if (data.status === "success")
                            {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berjaya dipadam',
                                    text: `Rekod pendaftaran ${display} berjaya dipadam.`,
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
                            console.error('Error: ', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Ralat',
                                text: 'Terdapat ralat semasa memadam rekod pendaftaran.',
                                confirmButtonColor: '#4f46e5'
                            });
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>