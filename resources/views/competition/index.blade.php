<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="{{ URL::asset('assets/images/logo/fav-logo-prim.png')}}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
            padding: 20px 0px 90px;
        }

        .header-section .container {
            padding: 100px 0px 0px;
        }

        .nav-container {
            position: fixed;
            top: 12px;
            z-index: 1030;
            width: 100%;
        }

        .nav-index {
            margin: 0px 25px;
            padding: 12px 50px;
            background: rgba(255, 255, 255, 0.45);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 18px;
            box-shadow: 0 8px 30px rgba(40, 25, 70, 0.08);
            transition: all 0.3s ease;
        }

        .nav-index a img {
            width: 160px;
            border: none;
        }

        .btn-login {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            padding: 11px 28px;
            border-radius: 50px;
            background: linear-gradient(135deg, #4f46e5, #9333ea);
            color: #ffffff;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            border: 1px solid rgba(255, 255, 255, 0.35);
            box-shadow: 0 5px 15px rgba(79, 70, 229, 0.2);
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            color: #ffffff;
            transform: translateY(-3px);
            box-shadow: 0 9px 22px rgba(79, 70, 229, 0.3);
            background: linear-gradient(135deg, #4338ca, #7e22ce);
        }

        .desc {
            color: #E0E7FF;
        }
        
        .btn-custom {
            transition: all 0.3s ease;
            font-weight: 700;
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

        .stats-section {
            margin: 100px 0 100px 0;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
            margin: 60px 0 120px 0;
        }

        .stat-card {
            position: relative;
            min-height: 175px;
            padding: 25px;
            overflow: hidden;
            border: 1px solid #eeeaf5;
            border-radius: 22px;
            background: white;
            box-shadow: 0 8px 25px rgba(43, 31, 75, 0.06);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08) !important;
        }

        .stat-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 23px;
        }

        .stat-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border-radius: 15px;
            font-size: 22px;
        }

        .stat-label {
            margin-bottom: 7px;
            color: #858195;
            font-size: 13px;
            font-weight: 600;
        }

        .stat-number {
            margin: 0;
            color: #29243b;
            font-size: clamp(1.6rem, 2.5vw, 2rem);
            font-weight: 800;
            letter-spacing: -0.8px;
        }
        
        .stat-footer {
            margin-top: 7px;
            color: #aaa6b5;
            font-size: 11px;
        }

        .stat-purple .stat-icon {
            background: #f0eaff;
            color: #8055d9;
        }

        .stat-blue .stat-icon {
            background: #e8f2ff;
            color: #4285d4;
        }

        .stat-green .stat-icon {
            background: #e5f8ef;
            color: #28a66a;
        }

        .stat-orange .stat-icon {
            background: #fff2e5;
            color: #e99437;
        }

        .section-label {
            display: inline-block;
            padding: 7px 15px;
            border-radius: 50px;
            background: #f0eaff;
            color: #7545cb;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .section-title {
            color: #29243b;
            font-size: clamp(1.7rem, 3vw, 2.3rem);
            font-weight: 800;
            letter-spacing: -0.7px;
        }

        .section-description {
            color: #858195;
            font-size: 15px;
        }

        .stat-revenue {
            background: linear-gradient(120deg, #4f46e5, #8138d8);
            border: none;
        }

        .stat-revenue .stat-label, 
        .stat-revenue .stat-footer, 
        .stat-revenue .stat-tag {
            color: rgba(255,255,255,0.75);
        }

        .stat-revenue .stat-number {
            color: white;
            font-size: clamp(2rem, 3vw, 2.7rem);
        }

        .stat-revenue .stat-icon {
            background: rgba(255,255,255,0.16);
            color: white;
        }

        .stat-revenue::after {
            content: "";
            position: absolute;
            width: 180px;
            height: 180px;
            right: -55px;
            bottom: -100px;
            border: 30px solid rgba(255,255,255,0.08);
            border-radius: 50%;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 60px;
        }

        .feature-card {
            position: relative;
            min-height: 260px;
            padding: 32px;
            overflow: hidden;
            border: 1px solid #eeeaf5;
            border-radius: 24px;
            background: white;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08) !important;
        }

        .feature-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 62px;
            height: 62px;
            margin-bottom: 25px;
            border-radius: 19px;
            font-size: 28px;
        }

        .feature-card h5 {
            margin-bottom: 12px;
            color: #29243b;
            font-size: 19px;
            font-weight: 800;
        }

        .feature-card p {
            max-width: 300px;
            margin: 0;
            color: #858195;
            font-size: 14px;
            line-height: 1.8;
        }

        .feature-number {
            position: absolute;
            right: 25px;
            bottom: 5px;
            color: rgba(100,80,150,0.06);
            font-size: 100px;
            font-weight: 900;
            line-height: 1;
        }

        .feature-purple {
            background: linear-gradient(145deg, #fff, #f5f0ff);
        }
        
        .feature-purple .feature-icon {
            background: #eadfff;
            color: #8055d9;
        }

        .feature-blue {
            background: linear-gradient(145deg, #fff, #eef6ff);
        }

        .feature-blue .feature-icon {
            background: #dcecff;
            color: #4285d4;
        }

        .feature-orange {
            background: linear-gradient(145deg, #fff, #fff5e9);
        }

        .feature-orange .feature-icon {
            background: #ffebd2;
            color: #e99437;
        }

        @media (max-width: 992px) {
            .space-btn {
                margin-bottom: 35px;
            }

            .nav-index a img {
                width: 45px;
                border: none;
            }

            .nav-index {
                padding: 12px 30px;
            }

            .stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 11px;
            }

            .stat-card {
                min-height: 155px;
                padding: 17px;
                border-radius: 17px;
            }

            .stat-top {
                margin-bottom: 17px;
            }

            .stat-icon {
                width: 40px;
                height: 40px;
                font-size: 18px;
                border-radius: 12px;
            }

            .stat-number {
                font-size: 1.45rem;
            }

            .feature-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .feature-card, .feature-card:last-child {
                grid-column: auto;
                min-height: 225px;
            }
        }
    </style>
</head>
<body>

    <div class="nav-container">
        <nav class="nav-index d-flex align-items-center justify-content-between">
            <a href="{{ route('competition.index') }}">
                <img src="{{ asset('assets/landing-page/img/competition-logo/logo-competition.png') }}" class="d-none d-lg-block" alt="ContestPro">
                <img src="{{ asset('assets/landing-page/img/competition-logo/logo-competition-mobile.png') }}" class="d-block d-lg-none" alt="ContestPro">
            </a>

            <a href="{{ route('login') }}" class="btn-login">
                Login
            </a>
        </nav>
    </div>

    <section class="header-section shadow-sm">
        <div class="text-center">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-9 col-xl-8">
                        <h1 class="display-4 fw-bold mb-3">Di Mana Bakat <br><span class="text-warning">Bertemu Peluang</span></h1>
                        <p class="lead mb-4 px-md-4 desc">Platform yang membolehkan organisasi menerbitkan pertandingan dan
                            calon menyertai pertandingan dengan mudah, telus serta terbuka kepada semua.
                        </p> <br>
                        <div class="d-flex justify-content-center gap-md-3 flex-wrap">
                            <div class="position-relative d-inline-block space-btn">
                                <a href="{{ route('competition.user', ['mode' => 'user']) }}" class="btn btn-warning btn-lg ps-4 pe-5 py-3 btn-custom rounded-pill {{ request()->get('mode', 'user') }}">
                                    <i class="bi bi-search me-2"></i> Cari Pertandingan
                                </a>
                                <img src="{{ asset('assets/landing-page/img/competition-logo/sertai.png') }}" alt="" class="btn-img">
                            </div>

                            <div class="position-relative d-inline-block">
                                <a href="{{ route('competition.host', ['mode' => 'host']) }}" class="btn btn-outline-light btn-lg ps-4 pe-5 py-3 btn-custom btn-anjur rounded-pill {{ request()->get('mode', 'host') }}">
                                    <i class="bi bi-plus-circle me-1"></i> Anjur Pertandingan
                                </a>
                                <img src="{{ asset('assets/landing-page/img/competition-logo/anjur.png') }}" alt="" class="btn-img btn-img-anjur">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="stats-section">
        <div class="container">

            <div class="text-center mb-4">
                <h2 class="section-title mt-3">Perkembangan Keseluruhan</h2>
                <p class="section-description">
                    Lihat perkembangan dan pencapaian komuniti ContestPro.
                </p>
            </div>

            <div class="stats-grid">

                <div class="stat-card stat-purple">
                    <div class="stat-top">
                        <div class="stat-icon">
                            <i class="bi bi-trophy-fill"></i>
                        </div>
                    </div>

                    <div class="stat-label">Jumlah Pertandingan</div>
                    <h3 class="stat-number" data-target="{{ $totalCompetition }}">0</h3>
                    <div class="stat-footer">Pertandingan sedia ada di platform</div>
                </div>

                <div class="stat-card stat-blue">
                    <div class="stat-top">
                        <div class="stat-icon">
                            <i class="bi bi-people-fill"></i>
                        </div>
                    </div>

                    <div class="stat-label">Jumlah Peserta</div>
                    <h3 class="stat-number" data-target="{{ $totalParticipant }}">0</h3>
                    <div class="stat-footer">Peserta yang berdaftar</div>
                </div>

                <div class="stat-card stat-green">
                    <div class="stat-top">
                        <div class="stat-icon">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                    </div>
                    
                    <div class="stat-label">Jumlah Dibayar</div>
                    <h3 class="stat-number" data-target="{{ $totalPaid }}">0</h3>
                    <div class="stat-footer">Pendaftaran yang berjaya dibayar</div>
                </div>

                <div class="stat-card stat-revenue">
                    <div class="stat-top">
                        <div class="stat-icon">
                            <i class="bi bi-wallet2"></i>
                        </div>
                    </div>

                    <div class="stat-label">Jumlah Bayaran Pertandingan</div>
                    <h3 class="stat-number" data-target="{{ $totalPrice }}">RM 0.00</h3>
                    <div class="stat-footer">Jumlah pembayaran pertandingan yang berjaya</div>
                </div>

            </div>
        </div>
    </section>

    <section class="features-section mb-5">
        <div class="container">

            <div class="text-center">
                <span class="section-label">Jom Mula Hari Ini</span>

                <h2 class="section-title mt-3 mb-3">
                    Sedia Untuk Bertanding Atau Menganjur?
                </h2>
                <p class="section-description">
                    Segalanya lebih mudah dengan ContestPro.
                    Terokai peluang dan kembangkan potensi anda.
                </p>
            </div>

            <div class="feature-grid">

                <div class="feature-card feature-purple">
                    <div class="feature-icon">
                        <i class="bi bi-lightning-charge-fill"></i>
                    </div>

                    <h5>Mudah dan Pantas</h5>
                    <p>
                        Pendaftaran pertandingan secara digital lebih mudah,
                        cepat dan teratur tanpa proses yang rumit.
                    </p>

                    <span class="feature-number">01</span>
                </div>

                <div class="feature-card feature-blue">
                    <div class="feature-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>

                    <h5>Sistem Telus</h5>
                    <p>
                        Maklumat pertandingan dan syarat kelayakan
                        disediakan dengan jelas untuk semua peserta.
                    </p>

                    <span class="feature-number">02</span>
                </div>

                <div class="feature-card feature-orange">
                    <div class="feature-icon">
                        <i class="bi bi-globe2"></i>
                    </div>

                    <h5>Akses Terbuka</h5>
                    <p>
                        Terbuka kepada pelbagai komuniti, organisasi dan
                        individu yang ingin mengasah bakat mereka.
                    </p>

                    <span class="feature-number">03</span>
                </div>

            </div>
        </div>
    </section>

    <script>
        $(document).ready(function () {
            let animate = false;

            const runCounter = () => {
                $('.stat-number').each(function () {
                    const $counter = $(this);
                    const target = parseFloat($counter.attr('data-target'));
                    const revenue = $counter.closest('.stat-revenue').length > 0;

                    $({ count: 0 }).animate({ count: target }, {
                        duration: 1500,
                        easing: 'swing',
                        step: function  (now) {
                            if (revenue)
                            {
                                $counter.text('RM ' + now.toFixed(2));
                            }
                            else
                            {
                                $counter.text(Math.floor(now));
                            }
                        },
                        complete: function() {
                            if (revenue)
                            {
                                $counter.text('RM ' + target.toFixed(2));
                            }
                            else
                            {
                                $counter.text(target);
                            }
                        }
                    });
                });
            };

            const $statSection = $('.stats-section');

            if ($statSection.length)
            {
                const observer = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting && !animate)
                        {
                            animate = true;
                            runCounter();
                            observer.unobserve(entry.target);
                        }
                    }); 
                }, { threshold: 0.3 });

                observer.observe($statSection[0]);
            }
        });
    </script>
</body>
</html>