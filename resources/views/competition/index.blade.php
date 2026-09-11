<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>PRiM | Competition</title>

    <style>
        body {
            background-color: #f8fafc;
            overflow-x: hidden;
        }

        .header-section {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 35%, #e11d48 100%);
            /* background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%); */
            color: white;
            padding: 50px 0px 90px;
        }

        .desc {
            color: #E0E7FF;
        }
        
        .btn-custom {
            transition: all 0.3s ease;
            font-weight: 600;
            padding-right: 80px !important;
        }
        
        .btn-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .btn-img {
            position: absolute;
            height: 95px;
            width: auto;
            right: -5px;
            top: 40%;
            transform: translateY(-50%);
            pointer-events: none;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
            transition: all 0.3s ease;
        }

        .position-relative:hover .btn-img {
            transform: translateY(calc(-50% - 5px)) scale(1.05);
            filter: drop-shadow(0 8px 15px rgba(0, 0, 0, 0.15));
        }

        .btn-img-anjur {
            top: 25%;
        }

        .btn-anjur {
            background-color: rgba(255, 255, 255, 0.12);
            border: rgba(255, 255, 255, 0.3);
        }

        .start-text {
            display: inline-block;
            background-color: #f3e8ff;
            color: #7c3aed;
            font-weight: 700;
            font-size: 0.75rem;
            padding: 6px 16px;
            border-radius: 50px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .feature-card {
            border: none;
            border-radius: 16px;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08) !important;
        }
    </style>
</head>
<body>
    <section class="header-section text-center postition-relative shadow-sm">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9 col-xl-8">
                    <h1 class="display-4 fw-bold mb-3">Di Mana Bakat <br><span class="text-warning">Bertemu Peluang</span></h1>
                    <p class="lead mb-4 px-md-4 desc">Platform yang membolehkan organisasi menerbitkan pertandingan dan
                        calon menyertai pertandingan dengan mudah, telus serta terbuka kepada semua.
                    </p> <br>
                    <div class="d-flex justify-content-center gap-md-3 gap-5 flex-wrap">
                        <div class="position-relative d-inline-block">
                            <a href="{{ route('competition.user', ['mode' => 'user']) }}" class="btn btn-warning btn-lg ps-4 pe-5 py-3 btn-custom rounded-pill {{ request()->get('mode', 'user') }}"><i class="bi bi-search me-2"></i> Cari Pertandingan</a>
                            <img src="{{ asset('competition-image/sertai.png') }}" alt="" class="btn-img">
                        </div>
                        <div class="position-relative d-inline-block">
                            <a href="{{ route('competition.host', ['mode' => 'host']) }}" class="btn btn-outline-light btn-lg ps-4 pe-5 py-3 btn-custom btn-anjur rounded-pill {{ request()->get('mode', 'host') }}"><i class="bi bi-plus-circle me-1"></i> Anjur Pertandingan</a>
                            <img src="{{ asset('competition-image/anjur.png') }}" alt="" class="btn-img btn-img-anjur">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="container my-5 py-4">
        <div class="text-center mb-5">
            <span class="start-text">Jom Mula Hari Ini</span>
            <h2 class="fw-bold">Sedia Untuk Bertanding Atau Menganjur?</h2>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card card-body p-4 text-center shadow-sm h-100 feature-card">
                    <div class="mb-3">
                        <span class="bg-primary-subtle text-primary px-3 py-2 rounded-circle d-inline-block">
                            <i class="bi bi-lightning-fill fs-3"></i>
                        </span>
                    </div>
                    <h5 class="fw-bold">Mudah dan Pantas</h5>
                    <p class="text-muted small mb-0">Pendaftaran pertandingan secara digital <br>yang cepat, mudah dan teratur.</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card card-body p-4 text-center shadow-sm h-100 feature-card">
                    <div class="mb-3">
                        <span class="bg-success-subtle text-success px-3 py-2 rounded-circle d-inline-block">
                            <i class="bi bi-shield-check fs-3"></i>
                        </span>
                    </div>
                    <h5 class="fw-bold">Sistem Telus</h5>
                    <p class="text-muted small mb-0">Maklumat acara dan syarat kelayakan <br>dimaklumkan secara jelas.</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card card-body p-4 text-center shadow-sm h-100 feature-card">
                    <div class="mb-3">
                        <span class="bg-warning-subtle text-warning px-3 py-2 rounded-circle d-inline-block">
                            <i class="bi bi-globe fs-3"></i>
                        </span>
                    </div>
                    <h5 class="fw-bold">Akses Terbuka</h5>
                    <p class="text-muted small mb-0">Terbuka kepada pelbagai komuniti, organisasi dan <br>individu yang ingin mengasah bakat mereka.</p>
                </div>
            </div>
        </div>
    </section>
</body>
</html>