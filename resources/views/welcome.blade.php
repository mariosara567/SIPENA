<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Asesmen - Sistem Asesmen Pendidikan</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary: #0066D4;
            --primary-dark: #004c9e;
            --bg: #f8fafc;
            --surface: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', -apple-system, sans-serif;
            background-color: var(--bg);
            color: var(--text-main);
            overflow-x: hidden;
        }

        /* ── Container ── */
        .container {
            width: 100%;
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 40px;
        }

        /* ── Header ── */
        header {
            background: var(--surface);
            border-bottom: 2px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 18px;
            padding-bottom: 18px;
        }

        .brand-container {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .brand-logo {
            width: 40px;
            height: 40px;
            background: var(--primary);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 20px;
            font-weight: 800;
        }

        .brand-text {
            color: var(--primary);
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .nav-menu {
            display: flex;
            align-items: center;
            gap: 32px;
        }

        .nav-link {
            text-decoration: none;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 15px;
            transition: color 0.2s;
        }

        .nav-link:hover {
            color: var(--text-main);
        }

        .btn-masuk {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--primary);
            color: #ffffff;
            text-decoration: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 15px;
            transition: opacity 0.2s;
        }

        .btn-masuk:hover {
            opacity: 0.9;
        }

        /* ── Hero Section ── */
        .hero-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
            padding-top: 40px;
            padding-bottom: 80px;
            min-height: calc(100vh - 82px - 100px);
        }

        .hero-text h1 {
            font-size: clamp(40px, 5vw, 64px);
            font-weight: 900;
            color: var(--primary);
            line-height: 1.1;
            margin-bottom: 24px;
            letter-spacing: -1px;
        }

        .hero-text p {
            font-size: 18px;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 40px;
            font-weight: 500;
            max-width: 500px;
        }

        .btn-hero {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: var(--primary-dark);
            color: #ffffff;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 16px;
            transition: opacity 0.2s;
        }

        .btn-hero:hover {
            opacity: 0.9;
        }

        .hero-illustration {
            background: #eff6ff;
            border-radius: 24px;
            width: 100%;
            aspect-ratio: 4/3;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 20px 40px -10px rgba(0,0,0,0.05);
        }

        .hero-illustration i {
            font-size: 140px;
            color: var(--primary);
        }

        /* ── Features Section ── */
        .features {
            padding: 100px 0;
            background: #ffffff;
            border-top: 1px solid var(--border);
        }

        .section-header {
            text-align: center;
            margin-bottom: 56px;
        }

        .section-header h2 {
            font-size: 36px;
            margin-bottom: 14px;
            font-weight: 800;
            color: var(--primary);
        }

        .section-header p {
            font-size: 16px;
            color: var(--text-muted);
            font-weight: 500;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 28px;
        }

        .feature-card {
            background: var(--bg);
            padding: 36px;
            border-radius: 16px;
            border: 1px solid var(--border);
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.08);
            border-color: var(--primary);
        }

        .feature-icon {
            width: 56px;
            height: 56px;
            background: #eff6ff; /* light blue */
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: var(--primary);
            margin-bottom: 20px;
        }
        
        .feature-card h3 {
            font-size: 18px;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .feature-card p {
            color: var(--text-muted);
            line-height: 1.6;
            font-size: 14px;
        }

        /* ── About Section ── */
        .about-section {
            padding: 100px 0;
            background: var(--bg);
            border-top: 1px solid var(--border);
        }

        .about-grid {
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            gap: 60px;
            align-items: center;
        }

        .about-illustration {
            width: 100%;
            height: 400px;
            background: #ffffff;
            border-radius: 20px;
            border: 2px dashed #cbd5e1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            font-weight: 600;
            text-align: center;
            padding: 20px;
        }

        .about-cards {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .dev-card {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 24px 30px;
            display: flex;
            align-items: center;
            gap: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .dev-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08);
        }

        .dev-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            flex-shrink: 0;
        }

        .dev-card.analyst .dev-icon {
            background: #e0f2fe;
            color: #0284c7;
        }

        .dev-card.developer .dev-icon {
            background: #ffe4e6;
            color: #e11d48;
        }

        .dev-info h3 {
            font-size: 18px;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 4px;
        }

        .dev-info p {
            font-size: 14px;
            color: #0284c7;
            font-weight: 600;
            margin: 0;
        }

        .dev-card.developer .dev-info p {
            color: #e11d48;
        }

        /* ── Footer ── */
        footer {
            background: #ffffff;
            padding: 40px 20px;
            text-align: center;
            color: var(--text-muted);
            font-size: 14px;
            font-weight: 500;
            border-top: 1px solid var(--border);
        }

        @media (max-width: 900px) {
            .hero-section, .about-grid {
                grid-template-columns: 1fr;
            }
            .hero-section {
                padding: 60px 40px;
            }
            .hero-text { text-align: center; }
            .hero-text p { margin: 0 auto 40px; }
            .about-grid { gap: 40px; }
        }

        @media (max-width: 600px) {
            .container { padding: 0 16px; }
            .header-inner { padding-top:12px; padding-bottom:12px; }
            .brand-logo { width:34px; height:34px; font-size:17px; }
            .brand-text { font-size:18px; }
            .btn-masuk { padding:9px 13px; font-size:13px; }
            .hero-section { padding:36px 16px 52px; gap:32px; min-height:auto; }
            .hero-text h1 { font-size:36px; margin-bottom:16px; letter-spacing:-.5px; }
            .hero-text p { font-size:15px; margin-bottom:26px; }
            .btn-hero { width:100%; justify-content:center; padding:13px 18px; }
            .hero-illustration { border-radius:16px; }
            .hero-illustration i { font-size:88px; }
            .features, .about-section { padding:56px 0; }
            .section-header { margin-bottom:30px; }
            .section-header h2 { font-size:28px; }
            .features-grid { grid-template-columns:1fr; gap:16px; }
            .feature-card { padding:24px; }
            .about-illustration { height:250px; }
            .dev-card { padding:18px; gap:14px; }
            .dev-icon { width:48px; height:48px; font-size:21px; }
            .nav-link { display: none; }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header>
        <div class="container header-inner">
            <a href="{{ url('/') }}" class="brand-container">
                <div class="brand-logo">S</div>
                <div class="brand-text">My Asesmen</div>
            </a>
            <nav class="nav-menu">
                <a href="#fitur" class="nav-link">Fitur</a>
                <a href="#tentang" class="nav-link">Tentang</a>
                @if (Route::has('login'))
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-masuk">
                            <i class="fas fa-arrow-right"></i> Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-masuk">
                            <i class="fas fa-sign-in-alt"></i> Masuk
                        </a>
                    @endauth
                @endif
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-section container">
        <div class="hero-text">
            <h1>Sistem Asesmen<br>Pendidikan</h1>
            <p>Platform terpadu untuk manajemen ujian, monitoring hasil akademik, dan penilaian digital dalam satu dashboard yang intuitif dan mudah digunakan.</p>
            @if (Route::has('login'))
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-hero">
                        <i class="fas fa-arrow-right"></i> Ke Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-hero">
                        <i class="fas fa-sign-in-alt"></i> Masuk Sekarang
                    </a>
                @endauth
            @endif
        </div>
        <div class="hero-illustration">
            <i class="fas fa-chart-line"></i>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="fitur">
        <div class="container">
            <div class="section-header">
                <h2>Fitur Utama</h2>
                <p>Kelola seluruh aspek manajemen pendidikan dengan mudah dan terpusat</p>
            </div>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-clipboard-list"></i></div>
                    <h3>Manajemen Ujian</h3>
                    <p>Buat, kelola, dan monitor ujian dengan berbagai tipe soal secara real-time.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-chart-bar"></i></div>
                    <h3>Laporan & Analitik</h3>
                    <p>Hasilkan laporan komprehensif dan analisis mendalam tentang performa siswa.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-users"></i></div>
                    <h3>Manajemen Pengguna</h3>
                    <p>Kelola guru, siswa, dan admin dengan sistem hak akses yang aman dan fleksibel.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-cloud"></i></div>
                    <h3>Online Terpusat</h3>
                    <p>Data tersimpan aman di server awan (cloud) dan dapat diakses dari mana saja.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-eye"></i></div>
                    <h3>Monitoring Realtime</h3>
                    <p>Pantau status pengerjaan, koneksi, dan aktivitas siswa secara instan.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                    <h3>Keamanan Terjamin</h3>
                    <p>Sistem anti-kecurangan dan keamanan berlapis untuk menjaga integritas ujian.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about-section" id="tentang">
        <div class="container about-grid">
            <div class="about-illustration">
                @if(file_exists(public_path('images/about-illustration.png')))
                    <img src="{{ asset('images/about-illustration.png') }}" alt="Ilustrasi Tim" style="width: 100%; max-width: 400px; height: auto; display: block; margin: 0 auto; object-fit: contain;">
                @elseif(file_exists(public_path('images/about-illustration.svg')))
                    <img src="{{ asset('images/about-illustration.svg') }}" alt="Ilustrasi Tim" style="width: 100%; max-width: 400px; height: auto; display: block; margin: 0 auto; object-fit: contain;">
                @else
                    <i class="fas fa-image" style="font-size: 48px; color: #cbd5e1; margin-bottom: 16px;"></i>
                    <p>Letakkan gambar Anda di:<br><code style="background: #e2e8f0; padding: 2px 6px; border-radius: 4px; color: #0f172a;">public/images/about-illustration.png</code></p>
                @endif
            </div>
            <div class="about-cards">
                <!-- Card 1 -->
                <div class="dev-card analyst">
                    <div class="dev-icon">
                        <i class="fas fa-palette"></i>
                    </div>
                    <div class="dev-info">
                        <h3>Yohanes Alvons Woda Sara</h3>
                        <p>System Analyst & UI/UX Designer</p>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="dev-card developer">
                    <div class="dev-icon">
                        <i class="fas fa-code"></i>
                    </div>
                    <div class="dev-info">
                        <h3>Mario Hafner Idjo Sara</h3>
                        <p>Lead Frontend & Backend Developer</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        MyAsesmen Admin Panel &ndash; Version 1.0.0
    </footer>

</body>
</html>
