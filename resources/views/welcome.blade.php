@extends('template')

@section('title', 'wasteCare - Landing Page')

@section('styles')
    <style>
        .hero-landing {
            position: relative;
            overflow: hidden;
            background: radial-gradient(circle at 0% 0%, rgba(16, 185, 129, 0.08) 0%, transparent 50%),
                        radial-gradient(circle at 100% 100%, rgba(59, 130, 246, 0.05) 0%, transparent 50%),
                        #ffffff;
            padding: 2.5rem 0 3.5rem; /* reduced top padding for tighter header */
            border-bottom: 1px solid rgba(15, 23, 42, 0.04);
        }

        /* Ambient glowing circles */
        .hero-landing::before {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.12) 0%, transparent 70%);
            top: -40px;
            right: 8%;
            z-index: 0;
            pointer-events: none;
        }

        .hero-landing::after {
            content: '';
            position: absolute;
            width: 420px;
            height: 420px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.08) 0%, transparent 70%);
            bottom: -80px;
            left: -30px;
            z-index: 0;
            pointer-events: none;
        }

        .hero-container {
            position: relative;
            z-index: 2;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.55rem 1rem;
            background: rgba(16, 185, 129, 0.08);
            border: 1px solid rgba(16, 185, 129, 0.15);
            color: #065f46;
            border-radius: 999px;
            margin-bottom: 1rem; /* reduce space under badge */
            font-size: 0.85rem;
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        @media (min-width: 992px) {
            .hero-landing {
                padding: 3.5rem 0 4rem;
            }
        }

        .hero-title {
            font-size: clamp(2.8rem, 4.5vw, 4.2rem);
            line-height: 1.1;
            letter-spacing: -0.04em;
            color: #0f172a;
            font-weight: 800;
        }

        /* Iconic brand styling */
        .hero-title span.brand {
            font-family: 'Outfit', sans-serif;
            font-weight: 900;
            letter-spacing: -0.05em;
            background: linear-gradient(135deg, #10b981, #059669);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-copy {
            max-width: 580px;
            color: #475569;
            font-size: 1.15rem;
            line-height: 1.65;
        }

        .hero-actions .btn {
            padding: 0.85rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.25s ease;
        }

        .hero-actions .btn-success {
            box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.3);
        }

        .hero-actions .btn-outline-success {
            border: 2px solid var(--eco-primary) !important;
            color: var(--eco-primary) !important;
        }

        .hero-actions .btn-outline-success:hover {
            color: #ffffff !important;
        }

        .feature-card {
            border: 1px solid rgba(15, 23, 42, 0.06);
            border-radius: 24px;
            padding: 2.5rem 2.25rem;
            background: #ffffff;
            box-shadow: var(--eco-card-shadow);
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px -15px rgba(16, 185, 129, 0.12), 0 1px 3px rgba(15, 23, 42, 0.04);
            border-color: rgba(16, 185, 129, 0.2);
        }

        .feature-icon {
            width: 64px;
            height: 64px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            background: rgba(16, 185, 129, 0.08);
            color: var(--eco-primary);
            font-size: 1.8rem;
            margin-bottom: 1.75rem;
            transition: all 0.3s ease;
        }

        .feature-card:hover .feature-icon {
            background: var(--eco-primary);
            color: #ffffff;
            transform: scale(1.05) rotate(5deg);
        }

        .feature-title {
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 0.85rem;
            font-size: 1.25rem;
            letter-spacing: -0.01em;
        }

        .feature-text {
            color: #64748b;
            line-height: 1.7;
            font-size: 0.95rem;
        }

        .promo-banner {
            border-radius: 28px;
            padding: 3.5rem 4rem;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 30px 60px -15px rgba(15, 23, 42, 0.3);
            position: relative;
            overflow: hidden;
        }

        .promo-banner::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 250px;
            height: 250px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.15) 0%, transparent 70%);
            pointer-events: none;
        }

        .promo-banner h3 {
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: #ffffff;
            line-height: 1.3;
        }

        .promo-banner p {
            color: #94a3b8;
            font-size: 1.1rem;
            line-height: 1.6;
        }

        .feature-spark {
            display: inline-block;
            font-weight: 800;
            font-size: 1.15rem;
            color: #10b981;
            padding: 0.5rem 1.2rem;
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.2);
            border-radius: 99px;
            letter-spacing: 0.02em;
        }

        .welcome-hero-visual {
            width: 100%;
            border-radius: 28px;
            background: #f1f5f9;
            overflow: hidden;
            box-shadow: 0 30px 70px -10px rgba(15, 23, 42, 0.15);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(15, 23, 42, 0.08);
            transition: all 0.4s ease;
            min-height: 440px; /* ensure full-frame visual */
        }

        .welcome-hero-visual:hover {
            transform: translateY(-4px) scale(1.01);
            box-shadow: 0 35px 80px -10px rgba(16, 185, 129, 0.18);
        }

        .welcome-hero-visual img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 28px;
        }

        @media (min-width: 992px) {
            .welcome-hero-visual { min-height: 320px; }
        }

        .welcome-footer-note {
            color: #64748b;
            margin-top: 2.5rem;
            font-size: 0.9rem;
            border-left: 3px solid #10b981;
            padding-left: 1rem;
        }
    </style>
@endsection

@section('content')
    <section class="hero-landing">
        <div class="container hero-container">
            <div class="row align-items-center gy-5">
                <div class="col-lg-6">
                    <div class="hero-badge">🌿 wasteCare Web-GIS</div>
                    <h1 class="hero-title">Sistem Pemantauan Sampah Cerdas <span class="brand">wasteCare</span></h1>
                    <p class="hero-copy mt-4">Laporkan titik sampah secara langsung, kelola jalur armada truk secara visual, dan pantau cakupan wilayah lingkungan dengan presisi spasial tinggi.</p>
                    <div class="d-flex flex-column flex-sm-row gap-3 hero-actions mt-4">
                        <a href="{{ route('map') }}" class="btn btn-success btn-lg shadow-sm">Buka Peta Publik</a>
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-success btn-lg">Dashboard Admin</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-success btn-lg">Login Admin</a>
                        @endauth
                    </div>
                    <p class="welcome-footer-note">wasteCare menggabungkan kecanggihan data spasial (GIS) dan pemetaan interaktif untuk mendukung lingkungan yang bersih, higienis, dan berkelanjutan.</p>
                </div>
                <div class="col-lg-6">
                    <div class="welcome-hero-visual">
                        <?php $landing = isset($landingImage) && $landingImage ? $landingImage : asset('uploads/landing.jpg'); ?>
                        <img src="{{ $landing }}" alt="wasteCare Visual" />
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="container my-4 py-4">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="feature-card h-100">
                    <div class="feature-icon">📍</div>
                    <h5 class="feature-title">Pelaporan Presisi</h5>
                    <p class="feature-text">Klik peta interaktif untuk menentukan titik tumpukan sampah secara geografis, unggah bukti foto, dan kirim laporan secara real-time.</p>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="feature-card h-100">
                    <div class="feature-icon">🚚</div>
                    <h5 class="feature-title">Rute Pengangkutan</h5>
                    <p class="feature-text">Gambarkan dan rencanakan rute armada pengangkut sampah secara visual di atas peta untuk mengefisiensikan jalur operasional harian.</p>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="feature-card h-100">
                    <div class="feature-icon">📊</div>
                    <h5 class="feature-title">Zona Monitoring</h5>
                    <p class="feature-text">Pantau luas cakupan wilayah bersih, sebaran titik laporan masuk, dan status penanganan sampah melalui panel dashboard terintegrasi.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="container pb-4 mb-3">
        <div class="promo-banner">
            <div class="row align-items-center gy-4">
                <div class="col-md-8">
                    <h3>Solusi Geospasial Modern untuk Mewujudkan Lingkungan Asri</h3>
                    <p class="mb-0 mt-2">wasteCare menghubungkan warga, pengurus, dan pengelola kebersihan melalui peta kolaboratif yang transparan dan dapat dipantau setiap saat.</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <span class="feature-spark">Terintegrasi · Bersih · Responsif</span>
                </div>
            </div>
        </div>
    </section>

    <style>
        /* tighten footer spacing inside welcome page */
        .promo-banner { margin-bottom: 0.5rem; }
        .welcome-footer-note { margin-top: 1.5rem; }
    </style>
@endsection
