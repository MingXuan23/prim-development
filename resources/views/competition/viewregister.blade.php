<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Document</title>

    <style>
        @media print {
            .btn {
                display: none !important;
            }

            .card-header h5 {
                color: black;
            }

            .text-end .badge {
                color: rgb(25, 135, 84);
            }

            .col-lg-4 {
                margin-top: 200px;
            }

            .card {
                box-shadow: none !important;
            }

            table {
                width: 100%;
                table-layout: auto;
                word-break: break-all;
            }

            .container {
                max-width: 100% !important;
            }

            .group {
                font-size: medium;
            }
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-0">Rekod Pendaftaran</h3>
            </div>
            
            <div class="text-end">
                @if($competition->pivot->statusPayment == 'Paid')
                    <span class="badge bg-success fs-6 px-3 py-2 mb-2">Pendaftaran Berjaya</span> 
                @else
                    <span class="badge bg-warning text-dark fs-6 px-3 py-2 mb-2">Pembayaran Belum Selesai</span> 
                @endif
                <br>
                <button onclick="window.print()" class="btn btn-primary btn-sm"><i class="bi bi-printer me-2"></i>Cetak / Simpan PDF</button>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm border-1">
                    <div class="card-header bg-secondary text-white p-3">
                        <h5 class="card-title mb-0 fw-semibold">Maklumat Pendaftaran Peserta</h5>
                    </div>

                    <div class="card-body p-4">
                        @if($competition->participateType === 'Individual')
                            <h6 class="fw-bold text-uppercase border-bottom pb-2 mb-3">1. Maklumat Peribadi</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-sm-6">
                                    <label class="text-muted small d-block">Nama Penuh</label>
                                    <span class="fw-semibold text-dark">{{ $participant->name }}</span>
                                </div>

                                <div class="col-sm-6">
                                    <label class="text-muted small d-block">Jantina</label>
                                    <span class="fw-semibold text-dark">{{ $participant->gender }}</span>
                                </div>

                                <div class="col-sm-6">
                                    <label class="text-muted small d-block">Alamat Email</label>
                                    <span class="fw-semibold text-dark">{{ $participant->email }}</span>
                                </div>

                                <div class="col-sm-6">
                                    <label class="text-muted small d-block">No. Telefon</label>
                                    <span class="fw-semibold text-dark">{{ $participant->noTel }}</span>
                                </div>

                                <div class="col-sm-6">
                                    <label class="text-muted small d-block">No IC / No Pasport</label>
                                    <span class="fw-semibold text-dark">{{ $participant->icNo }}</span>
                                </div>
                            </div>
                        @else
                            <h6 class="fw-bold text-uppercase border-bottom pb-2 mb-3">1. Senarai Ahli Kumpulan - <span class="badge bg-primary group">{{ $participant->team->groupName }}</span></h6>
                            <div class="row g-3 mb-4">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover text-center align-middle">
                                        <thead class="table-secondary text-uppercase fs-7 text-secondary">
                                            <tr class="align-middle">
                                                <th style="width: auto;">No</th>
                                                <th style="width: 30%;">Nama Penuh</th>
                                                <th style="width: auto;">No IC / No Pasport</th>
                                                <th style="width: auto;">Jantina</th>
                                                <th style="width: auto;">Alamat Email</th>
                                                <th style="width: auto;">No. Telefon</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @foreach($member as $participants)
                                                <tr>
                                                    <td class="text-center text-muted fw-semibold">{{ $loop->iteration }}</td>
                                                    <td>
                                                        <span class="fw-semibold text-dark">{{ $participants->name }} </span>
                                                        @if($loop->first)
                                                            - <span class="badge bg-warning-subtle text-warning ms-1 small">Ketua</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center text-dark">{{ $participants->icNo }}</td>
                                                    <td>
                                                        <span class="badge bg-light text-dark border fw-normal">{{ $participants->gender }}</span>
                                                    </td>
                                                    <td class="text-dark">{{ $participants->email }}</td>
                                                    <td class="text-center text-dark">{{ $participants->noTel }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        <h6 class="fw-bold text-uppercase border-bottom pb-2 mb-3">2. Maklumat Pengajian</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <label class="text-muted small d-block">Jenis Institusi</label>
                                <span class="fw-semibold text-dark">{{ $participant->institution->instituteType }}</span>
                            </div>
                            
                            <div class="col-sm-6">
                                <label class="text-muted small d-block">Nama Institusi</label>
                                <span class="fw-semibold text-dark">{{ $participant->institution->instituteName }}</span>
                            </div>

                            <div class="col-sm-6">
                                <label class="text-muted small d-block">Pengajian Semasa</label>
                                <span class="fw-semibold text-dark">{{ $participant->currentGrade }}</span>
                            </div>
                        </div>

                        <h6 class="fw-bold text-uppercase border-bottom pb-2 mb-3">3. Maklumat Penyelia</h6>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="text-muted small d-block">Nama Penyelia</label>
                                <span class="fw-semibold text-dark">{{ $participant->supervisorName }}</span>
                            </div>

                            <div class="col-sm-6">
                                <label class="text-muted small d-block">Email Penyelia</label>
                                <span class="fw-semibold text-dark">{{ $participant->supervisorEmail }}</span>
                            </div>

                            <div class="col-sm-6">
                                <label class="text-muted small d-block">No. Telefon Penyelia</label>
                                <span class="fw-semibold text-dark">{{ $participant->supervisorNoTel }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer p-3 text-center text-muted small">
                        Pendaftaran dibuat pada: {{ \Carbon\Carbon::parse($competition->pivot->registeredDate)->format('d M Y, h:i A') ?? 'N/A' }}
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-1">
                    <div class="card-body p-4">
                        <span class="badge bg-light border text-dark mb-2 me-1">{{ $competition->category }}</span>
                        @if($competition->participateType === 'Individual')
                            <span class="badge bg-light border text-dark mb-2"><i class="bi bi-person-fill me-1"></i> {{ $competition->participateType }}</span>
                        @else
                            <span class="badge bg-light border text-dark mb-2"><i class="bi bi-people-fill me-1"></i> {{ $competition->participateType }}</span>
                        @endif
                        <h5 class="fw-bold mb-3">{{ $competition->competitionTitle }}</h5>

                        <div class="mb-3">
                            <small class="text-muted d-block"><i class="bi bi-calendar-event me-2"></i>Tarikh Pertandingan</small>
                            <span class="fw-semibold">{{ \Carbon\Carbon::parse($competition->competitionStart)->format('d M Y') }} - {{ \Carbon\Carbon::parse($competition->competitionEnd)->format('d M Y') }}</span>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted d-block"><i class="bi bi-geo-alt me-2"></i>Lokasi</small>
                            <span class="fw-semibold">{{ $competition->venue }}</span>
                        </div>

                        <hr>

                        @php
                            $filterByIC = preg_replace('/[^0-9]/', '', $participant->icNo ?? '');
                            $ICFormat = (strlen($filterByIC) === 12);

                            $fee = $member->sum(function($q) use($competition) {
                                $filterByIC = preg_replace('/[^0-9]/', '', $q->icNo ?? '');
                                $ICFormat = (strlen($filterByIC) === 12);

                                return $ICFormat ? ($competition->nationalFees ?? 0) : ($competition->internationalFees ?? 0);
                            });
                        @endphp
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted d-block">Yuran Dikenakan</small>
                                @if($competition->participateType === 'Individual')
                                    <span class="badge bg-light text-dark border">{{ $ICFormat ? 'Warganegara' : 'Bukan Warganegara' }}</span>
                                @endif
                            </div>

                            <div>
                                <h4 class="fw-bold text-success mb-0">RM {{ number_format($fee, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>