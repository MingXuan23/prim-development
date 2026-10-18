<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ URL::asset('assets/images/logo/fav-logo-prim.png')}}">
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

        .input-group {
            overflow: hidden;
        }

        .input-group .form-control {
            border-color: #cbd5e1;
            font-size: 0.9rem;
        }

        .input-group .form-control:focus {
            border-color: #3b82f6;
            box-shadow: none;
        }

        .btn-search {
            background-color: #4f46e5;
            color: white;
            font-weight: 500;
            font-size: 0.9rem;
            padding: 0.5rem 1.25rem;
        }

        .btn-search:hover {
            color: white;
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

        .registration-card {
            border: 1px solid rgba(0, 0, 0, 0.08);
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

        .btn-outline-primary, .btn-padam, .btn-light {
            font-size: 0.85rem;
            padding: 0.35rem 0.75rem;
        }

        .table-scroll-container {
            max-height: 620px;
            overflow-y: auto;
        }

        .btn-pay:disabled {
            color: white;
            background-color: #4f46e5;
        }

        .btn-pay {
            color: white;
            background-color: #4f46e5;
        }

        .btn-pay:hover {
            color: white;
            background-color: #4338ca;
        }

        .btn-sijil {
            color: grey;
        }

        .btn-sijil:hover {
            background-color: #6366f1;
            color: white;
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
    </style>
</head>
<body>
    @include('competition.component.nav')

    <div class="container mt-5">
        <div class="d-flex align-items-center gap-2">
        <div class="accent-bar"></div>
            <h3 class="fw-bold mb-1">Rekod Pendaftaran</h3>
        </div>
        <p class="text-muted small mb-0">Pengurusan status pendaftaran dan transaksi pembayaran bagi pertandingan yang disertai.</p>

        <div class="bg-white rounded-4 mb-4 mt-5 shadow-sm">
            <form action="{{ route('competition.userrecord') }}" method="GET">
                <div class="d-flex flex-wrap align-items-end justify-content-between gap-4 gap-md-3 p-4">
                    <div class="d-flex align-items-center gap-2 flex-grow-1" style="max-width: 500px;">
                        <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                            <span class="input-group-text bg-white border-0 ps-3 pe-2">
                                    <i class="fa fa-search text-muted"></i>
                            </span>                        
                            <input type="text" name="search" id="search" placeholder="Cari pertandingan / nama pendaftaran..." value="{{ request('search') }}" class="form-control search-input border-0">

                            <button type="submit" class="btn btn-search align-items-center"> Cari Pendaftaran</button>
                        </div>
                        @if(request()->anyFilled(['search']))
                            <a href="{{ route('competition.userrecord') }}" class="btn btn-outline-danger d-inline-flex">Reset<i class="bi bi-arrow-counterclockwise ms-1"></i></a>
                        @endif
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" name="status" value="all" class="btn filter-btn {{ !request()->filled('search') && request('status', 'all') == 'all' ? 'active' : '' }}">Semua</button>
                        <button type="submit" name="status" value="pending" class="btn filter-btn {{ request('status') == 'pending' ? 'active' : '' }}">Belum Bayar</button>
                        <button type="submit" name="status" value="paid" class="btn filter-btn {{ request('status') == 'paid' ? 'active' : '' }}">Telah Bayar</button>
                        <button type="submit" name="status" value="free" class="btn filter-btn {{ request('status') == 'free' ? 'active' : '' }}">Tiada Bayaran</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="card bg-white registration-card overflow-hidden mb-4 shadow-sm">
            <form method="POST" action="{{ route('directpayIndex') }}">
                @csrf
                <input type="hidden" name="desc" id="desc" value="Competition">

                <div class="table-responsive table-scroll-container">
                    <table class="table table-bordered table-hover text-center table-custom align-middle">
                        <thead class="sticky-top" style="z-index: 10;">
                            <tr>
                                <th><input type="checkbox" name="selectAllPayment" id="selectAllPayment" class="form-check-input"></th>
                                <th style="width: 6%;">No</th>
                                <th style="width: 25%;">Kumpulan / Individu</th>
                                <th style="width: 20%;">Pertandingan</th>
                                <th style="width: 14%;">Tarikh Daftar</th>
                                <th style="width: 14%;">Status</th>
                                <th>Tindakan</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($participants as $participant)
                                @php
                                    $member = $participant->first();
                                    $team = $member->team->team_registration->first();
                                    $bridge = $team->pivot;
                                    
                                    $fee = $participant->sum(function($q) use($team) {
                                        $filterByIC = preg_replace('/[^0-9]/', '', $q->icNo ?? '');

                                        $ICFormat = (strlen($filterByIC) === 12);

                                        return $ICFormat ? ($team->nationalFees ?? 0) : ($team->internationalFees ?? 0);
                                    });
                                @endphp
                                
                                <tr>
                                    <td>
                                        @if($bridge->statusPayment == 'Pending')
                                            <input type="checkbox" name="selectPayById[]" id="selectPayById" data-fee="{{ $fee }}" data-participant="{{ $participant->count() }}" data-org-id="{{ $team->organizationId }}" value="{{ $bridge->id }}" class="form-check-input participant-checkbox">
                                        @else   
                                            <input type="checkbox" class="form-check-input" hidden disabled>
                                        @endif
                                    </td>
                                    <td class="fw-medium">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            @if($team->participateType == 'Individual')
                                                <span class="d-flex align-items-center ms-2 mb-1 fw-bold">
                                                    <span class="d-inline-block text-truncate" style="max-width: 235px;">{{ $member->name ?? 'N/A' }}</span>
                                                    <span class="badge bg-light text-secondary border fw-semibold d-flex align-items-center justify-content-center ms-2" style="font-size: 10px;">Individu</span>
                                                </span>
                                                <small class="d-block text-truncate text-start ms-2 text-muted" style="font-size: 12px; max-width: 290px;">IC/Pasport: {{ $member->icNo ?? 'N/A'}}&nbsp Email: {{ $member->email ?? 'N/A' }}</small>
                                            @else
                                                <span class="d-flex justify-content-start ms-2 mb-1 fw-bold">{{ $member->team->groupName ?? 'N/A' }} <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10 d-flex align-items-center justify-content-center ms-2" style="font-size: 10px;">Team ({{ $member->team->participant->count() }} members)</span></span> 
                                                <small class="d-block text-truncate text-start ms-2 text-muted" style="font-size: 12px; max-width: 290px;">Ahli: {{ $participant->formatted_members ?? 'N/A'}}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="fw-medium">{{ $team->competitionTitle ?? 'N/A' }}</td>
                                    <td class="text-muted small">{{ $bridge->registeredDate ? \Carbon\Carbon::parse($bridge->registeredDate)->format('d M Y, h:i A') : 'N/A' }}</td>
                                    <td>
                                        @if($bridge->statusPayment == 'Pending')
                                            <span class="badge badge-pending text-dark">Belum Bayar</span>
                                        @elseif($bridge->statusPayment == 'Paid')
                                            <span class="badge badge-paid">Selesai Bayar</span>
                                        @elseif($bridge->statusPayment == 'Free')
                                            <span class="badge badge-free text-dark">Tiada Bayaran</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($bridge->statusPayment == 'Pending')
                                            <div class="d-flex justify-content-center align-items-center gap-2">
                                                <a href="{{ route('competition.editregister', $member->id) }}" class="btn btn-outline-primary"><i class="bi bi-pencil-square me-1"></i> Edit</a>
                                                <button type="button" class="btn btn-outline-danger btn-padam" data-url="{{ route('competition.deleteregister', $member->id) }}" data-name="{{ $member->name ?? '' }}" data-group="{{ $member->team->groupName ?? '' }}" data-type="{{ $team->participateType }}">
                                                    <i class="bi bi-trash me-1"></i> Padam
                                                </button>

                                            </div>
                                        @elseif($bridge->statusPayment == 'Paid' || $bridge->statusPayment == 'Free')
                                            <button type="button" id="daftar" class="btn btn-light border px-3 py-1 text-secondary fw-medium" onclick="showRegisterOption()"><i class="bi bi-eye me-1"></i> Lihat</button>
                                            <!-- @if($team->statusResult == 'Published' && in_array($bridge->statusPayment, ['Paid', 'Free']))
                                                <button type="button" class="btn btn-light px-3 py-1 border fw-medium btn-sijil mt-2" data-bs-toggle="modal" data-bs-target="#sijilModal" 
                                                        data-participant-id="{{ $participant->first()->id }}" data-participant-name="{{ $participant->first()->name }}" 
                                                        data-participant-title="{{ $participant->first()->team->team_registration->first()->competitionTitle }}"
                                                        data-participant-startDate="{{ \Carbon\Carbon::parse($participant->first()->team->team_registration->first()->competitionStart)->format('d F Y') }}"
                                                        data-participant-endDate="{{ \Carbon\Carbon::parse($participant->first()->team->team_registration->first()->competitionEnd)->format('d F Y') }}">
                                                        <i class="bi bi-download me-1"></i> Muat Turun Sijil
                                                </button>
                                            @endif -->
                                        @endif
                                    </td>
                                </tr>
                            
                                <!-- Receipt Payment -->
                                <div id="daftarOption" onclick="hideRegister()">
                                    <div class="modal-card position-relative" onclick="event.stopPropagation()">
                                        <button type="button" class="btn btn-close position-absolute end-0 top-0 me-3 mt-3" onclick="hideRegister()" aria-label="Close"></button>

                                        <h3 class="fw-bold text-center mb-4">Pilihan Rekod</h3>

                                        <div class="d-flex flex-column gap-3">
                                            <a href="{{ route('competition.viewregister', $member->id) }}" class="btn register-solo layout-register">
                                                <div class="icon">
                                                    <i class="bi bi-person-lines-fill"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold">Rekod Pendaftaran</div>
                                                    <div class="small opacity-75">Semak butiran peserta</div>
                                                </div>
                                            </a>

                                            <a href="{{ route('competition.receipt') }}" class="btn register-team layout-register">
                                                <div class="icon">
                                                    <i class="bi bi-file-earmark-text-fill"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold">Resit Pembayaran</div>
                                                    <div class="small text-muted">Lihat resit pembayaran</div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Tiada Rekod dijumpai.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>    
                </div>
                
                @if($participants->count() !== 0)
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center bg-light p-3 border gap-3">
                        <div>
                            <span class="fw-bold">Dipilih: </span><span id="selectedParticipant">0</span> Peserta &nbsp&nbsp |
                            <span class="ms-3 fw-bold">Jumlah Bayaran:</span><span class="fw-bold fs-5"> <span id="totalAmount" style="color: #4f46e5;">RM 0.00</span></span>
                        </div>

                        <div>
                            <button type="submit" class="btn btn-pay py-2 w-100 w-md-auto rounded-5" id="btnPay" disabled><i class="bi bi-wallet2 me-1"></i> Teruskan ke Pembayaran</button>
                        </div>
                    </div>
                @endif
            </form>
        </div>

        <!-- Sijil -->
        <div id="sijilModal" class="modal fade" tabindex="-1" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content rounded-4 shadow border-0">

                    <div class="modal-header border-0 pb-0">
                        <div>
                            <h5 class="modal-title fw-bold text-dark">Pilih Template Sijil</h5>
                            <p class="text-muted small mb-0">Sila pilih template pilihan anda sebelum memuat turun. Pilihan tidak boleh diubah.</p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="{{ route('competition.generatesijil') }}" method="POST" target="_blank">
                        @csrf
                        <input type="hidden" name="participantId" id="participantId">

                        <div class="modal-body py-4">
                            <div class="row g-3 mb-3">

                            @foreach(['Template1', 'Template2', 'Template3', 'Template4'] as $template)
                                <div class="col-md-6 col-6">
                                    <label for="template{{ $loop->iteration }}" class="sijil-card border rounded-3 p-2 d-block position-relative cursor-pointer">
                                        <div class="sijil-preview position-relative overflow-hidden rounded bg-secondary">
                                            <a href="{{ asset('assets/images/template-sijil-competition/' . $template . '.png') }}" target="_blank" class="d-block text-decoration-none">
                                                <img src="{{ asset('assets/images/template-sijil-competition/' . $template . '.png') }}" alt="Template {{ $loop->iteration }}" class="w-100 h-auto d-block">
                                            </a>
                                        </div>

                                        <div class="text-center mt-2 fw-semibold text-dark">Template {{ $loop->iteration }}</div>
                                    </label>
                                    <div class="d-flex justify-content-center mt-2">
                                        <input type="radio" name="templateId" id="template{{ $loop->iteration }}" value="{{ $template }}" class="" required>
                                    </div>
                                </div>
                            @endforeach
                            </div>
                        </div>

                        <div class="modal-footer border-0 pt-0">
                            <button type="submit" class="btn btn-primary px-4">Save & Muat Turun Sijil</button>
                        </div>
                    </form>
                </div>
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

        function hideMessage()
        {
            $('.hide-message').fadeOut(500, function () {
                $(this).remove();
            });    
        }   

        const selectAll = document.getElementById('selectAllPayment');
        const checkbox = document.querySelectorAll('.participant-checkbox');
        const selectedCount = document.getElementById('selectedParticipant');
        const totalAmount = document.getElementById('totalAmount');
        const btnPay = document.getElementById('btnPay');

        function update() 
        {
            let count = 0;
            let total = 0;

            checkbox.forEach (cb => {
                if(cb.checked)
                {
                    count += parseInt(cb.getAttribute('data-participant') || 1, 10);
                    total += parseFloat(cb.getAttribute('data-fee') || 0);
                }
            });
            selectedCount.textContent = count;
            totalAmount.textContent = 'RM ' + total.toFixed(2);
            btnPay.disabled = (count === 0);
        }

        if(selectAll)
        {
            selectAll.addEventListener('change', function() {
                checkbox.forEach(cb => {
                    cb.checked = this.checked;
                });
                update();
            });
        }

        checkbox.forEach(cb => {
            cb.addEventListener('change', function() {
                if(!this.checked && selectAll)
                {
                    selectAll.checked = false;
                }
                update();
            });
        });

        $(document).ready(function() {
            $('.participant-checkbox').on('change', function() {
                let checkbox = $('.participant-checkbox:checked');

                if (checkbox.length > 1)
                {
                    let firstId = checkbox.first().data('org-id');
                    let isMismatch = false;

                    checkbox.each(function () {
                        if ($(this).data('org-id') !== firstId)
                        {
                            isMismatch = true;
                            return false;
                        }
                    });

                    if (isMismatch)
                    {
                        $(this).prop('checked', false);

                        Swal.fire({
                            icon: 'warning',
                            title: 'Organisasi Pertandingan Berbeza',
                            text: 'Anda tidak boleh membuat pembayaran sekaligus untuk pertandingan dari organisasi yang berbeza.',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#f7b814'
                        });

                        $(this).trigger('change');
                        return;
                    }
                }
            });

            $(document).on('click', '.btn-padam', function (e) {
                e.preventDefault();

                let $form = $(this);

                let actionUrl = $form.data('url');
                let name = $form.data('name');
                let group = $form.data('group');
                let participateType = $form.data('type');

                let display = (participateType === 'Team') ? group : name;

                Swal.fire({
                    icon: 'warning',
                    title: 'Adakah anda pasti?',
                    text: `Adakah anda pasti ingin memadam pendaftaran ${display}?`,
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Padam',
                    cancelButtonText: 'Batal'
                }).then ((result) => {
                    if (result.isConfirmed)
                    {
                        $.ajax ({
                            url: actionUrl,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                'Accept': 'application/json'
                            },
                            dataType: 'json',
                            success: function(data) {
                                if (data.status === 'success')
                                {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berjaya dipadam',
                                        text: `Pendaftaran ${display} berjaya dipadam.`,
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
                            },
                            error: function(xhr) 
                            {
                                console.error('Error: ', xhr.responseText);
                                Swal.fire ({
                                    icon: 'error',
                                    title: 'Ralat',
                                    text: 'Terdapat ralat semasa memadam pendaftaran.',
                                    confirmButtonColor: '#4f46e5'
                                });
                            }
                        });
                    }
                });
            });

            $('#sijilModal').on('show.bs.modal', function (event) {
                const $button = $(event.relatedTarget);
                
                const pId = $button.data('participant-id');
                const pName = $button.data('participant-name');
                const cTitle = $button.data('participant-title');
                const sDate = $button.data('participant-startDate');
                const eDate = $button.data('participant-endDate');

                $('#participantId').val(pId);
            });
        });
    </script>
</body>
</html>