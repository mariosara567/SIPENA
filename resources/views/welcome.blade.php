<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIPENA - Sistem Informasi Pendidikan Nasional</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary: #0f766e;
            --primary-light: #14b8a6;
            --primary-dark: #0d5d56;
            --secondary: #6366f1;
            --success: #16a34a;
            --danger: #dc2626;
            --warning: #ea580c;
            --bg: #f5f7fa;
            --surface: #ffffff;
            --text: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.07), 0 10px 13px rgba(0, 0, 0, 0.05);
            --shadow-lg: 0 20px 25px rgba(0, 0, 0, 0.1), 0 8px 16px rgba(0, 0, 0, 0.08);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html, body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            color: var(--text);
            font-weight: 500;
        }

        body {
            background: linear-gradient(135deg, rgba(15, 118, 110, 0.05) 0%, rgba(99, 102, 241, 0.05) 100%);
            background-attachment: fixed;
        }

        /* Navigation Bar */
        nav {
            position: sticky;
            top: 0;
            z-index: 50;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 36px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border-bottom: 2px solid var(--border);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 800;
            font-size: 22px;
            text-decoration: none;
            color: var(--primary);
            letter-spacing: -0.5px;
        }

        .brand-mark {
            width: 40px;
            height: 40px;
            display: grid;
            place-items: center;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            font-weight: 900;
            font-size: 18px;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 28px;
        }

        .nav-link {
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
            font-size: 14px;
        }

        .nav-link:hover {
            color: var(--primary);
        }

        .login-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 24px;
            background: var(--primary);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 700;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .login-btn:hover {
            background: var(--primary-dark);
            box-shadow: var(--shadow-lg);
            transform: translateY(-2px);
        }

        /* Main Content */
        main {
            width: min(1200px, calc(100% - 40px));
            margin: 0 auto;
            padding: 80px 20px;
        }

        /* Hero Section */
        .hero {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
            margin-bottom: 100px;
        }

        .hero-content h1 {
            font-size: clamp(40px, 6vw, 60px);
            line-height: 1.15;
            margin-bottom: 20px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 900;
            letter-spacing: -1px;
        }

        .hero-content p {
            font-size: 18px;
            color: var(--text-muted);
            line-height: 1.7;
            margin-bottom: 32px;
            font-weight: 500;
        }

        .hero-buttons {
            display: flex;
            gap: 18px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 13px 32px;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            font-size: 15px;
            letter-spacing: -0.3px;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            box-shadow: var(--shadow-lg);
            transform: translateY(-3px);
        }

        .btn-secondary {
            background: white;
            color: var(--primary);
            border: 2px solid var(--primary);
        }

        .btn-secondary:hover {
            background: var(--bg);
            transform: translateY(-3px);
        }

        .illustration {
            width: 100%;
            height: 350px;
            background: linear-gradient(135deg, rgba(15, 118, 110, 0.12), rgba(99, 102, 241, 0.12));
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 140px;
            color: var(--primary);
            opacity: 0.85;
        }

        /* Features Section */
        .features {
            margin-bottom: 100px;
        }

        .section-header {
            text-align: center;
            margin-bottom: 56px;
        }

        .section-header h2 {
            font-size: 40px;
            margin-bottom: 14px;
            font-weight: 900;
            letter-spacing: -0.5px;
        }

        .section-header p {
            font-size: 18px;
            color: var(--text-muted);
            font-weight: 500;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 28px;
        }

        .feature-card {
            background: var(--surface);
            padding: 36px;
            border-radius: 14px;
            border: 2px solid var(--border);
            transition: all 0.3s ease;
            box-shadow: var(--shadow);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary);
        }

        .feature-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, var(--primary-light), var(--primary));
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: white;
            margin-bottom: 20px;
        }

        .feature-card:nth-child(2) .feature-icon {
            background: linear-gradient(135deg, #f59e0b, #f97316);
        }

        .feature-card:nth-child(3) .feature-icon {
            background: linear-gradient(135deg, #8b5cf6, #6366f1);
        }

        .feature-card:nth-child(4) .feature-icon {
            background: linear-gradient(135deg, #ec4899, #f43f5e);
        }

        .feature-card:nth-child(5) .feature-icon {
            background: linear-gradient(135deg, #06b6d4, #0891b2);
        }

        .feature-card:nth-child(6) .feature-icon {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .feature-card h3 {
            font-size: 20px;
            margin-bottom: 10px;
            font-weight: 800;
            letter-spacing: -0.3px;
        }

        .feature-card p {
            color: var(--text-muted);
            line-height: 1.7;
            font-size: 15px;
            font-weight: 500;
        }

        /* Stats Section */
        .stats {
            background: var(--surface);
            border-radius: 16px;
            padding: 70px 50px;
            margin-bottom: 100px;
            border: 2px solid var(--border);
            box-shadow: var(--shadow);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 50px;
        }

        .stat {
            text-align: center;
        }

        .stat-number {
            font-size: 48px;
            font-weight: 900;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
            letter-spacing: -1px;
        }

        .stat-label {
            color: var(--text-muted);
            font-size: 16px;
            font-weight: 700;
            letter-spacing: -0.3px;
        }

        /* CTA Section */
        .cta {
            background: linear-gradient(135deg, var(--primary) 0%, #0d5d56 100%);
            color: white;
            border-radius: 18px;
            padding: 70px 50px;
            text-align: center;
            margin-bottom: 100px;
            box-shadow: var(--shadow-lg);
        }

        .cta h2 {
            font-size: 42px;
            margin-bottom: 18px;
            font-weight: 900;
            letter-spacing: -0.5px;
        }

        .cta p {
            font-size: 18px;
            margin-bottom: 36px;
            opacity: 0.95;
            font-weight: 500;
        }

        /* Footer */
        footer {
            background: var(--surface);
            border-top: 2px solid var(--border);
            padding: 50px 36px;
            text-align: center;
            color: var(--text-muted);
        }

        footer p {
            margin: 0;
            font-weight: 600;
            font-size: 14px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            nav {
                padding: 14px 20px;
            }

            .nav-links {
                gap: 16px;
            }

            .nav-link {
                display: none;
            }

            main {
                padding: 50px 16px;
            }

            .hero {
                grid-template-columns: 1fr;
                gap: 40px;
                margin-bottom: 60px;
            }

            .hero-content h1 {
                font-size: 36px;
            }

            .hero-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }

            .features {
                margin-bottom: 60px;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .section-header h2 {
                font-size: 32px;
            }

            .stats {
                padding: 50px 30px;
            }

            .stats-grid {
                gap: 30px;
            }

            .cta {
                padding: 50px 30px;
            }

            .cta h2 {
                font-size: 32px;
            }

            footer {
                padding: 40px 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav>
        <a href="{{ route('home') }}" class="brand">
            <span class="brand-mark">S</span>
            <span>SIPENA</span>
        </a>
        <div class="nav-links">
            <a href="#features" class="nav-link">Fitur</a>
            <a href="#about" class="nav-link">Tentang</a>
            @if (Route::has('login'))
                @auth
                    <a href="{{ route('dashboard') }}" class="login-btn">
                        <i class="fas fa-arrow-right"></i> Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="login-btn">
                        <i class="fas fa-sign-in-alt"></i> Masuk
                    </a>
                @endauth
            @endif
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-content">
                <h1>Sistem Informasi Pendidikan Nasional</h1>
                <p>Platform terpadu untuk manajemen ujian, monitoring hasil akademik, dan sinkronisasi data pendidikan dalam satu dashboard yang intuitif dan mudah digunakan.</p>
                <div class="hero-buttons">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn btn-primary">
                                <i class="fas fa-arrow-right"></i> Ke Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt"></i> Masuk Sekarang
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-secondary">
                                    <i class="fas fa-user-plus"></i> Daftar
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
            <div class="hero-image">
                <div class="illustration">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features" id="features">
            <div class="section-header">
                <h2>Fitur Utama</h2>
                <p>Kelola seluruh aspek manajemen pendidikan dengan mudah</p>
            </div>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h3>Manajemen Ujian</h3>
                    <p>Buat, kelola, dan monitor ujian dengan pertanyaan berbagai tipe secara real-time.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h3>Laporan & Analitik</h3>
                    <p>Hasilkan laporan komprehensif dan analisis mendalam tentang performa siswa.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Manajemen Pengguna</h3>
                    <p>Kelola guru, siswa, dan admin dengan sistem role dan permission yang fleksibel.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-sync-alt"></i>
                    </div>
                    <h3>Sinkronisasi Data</h3>
                    <p>Sinkronisasi otomatis dengan sistem pusat untuk integrasi data yang sempurna.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <h3>Monitoring Realtime</h3>
                    <p>Pantau jalannya ujian dan aktivitas siswa secara real-time dari dashboard.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Keamanan Tingkat Tinggi</h3>
                    <p>Sistem keamanan berlapis untuk melindungi data dan integritas ujian.</p>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="stats">
            <div class="stats-grid">
                <div class="stat">
                    <div class="stat-number">100%</div>
                    <div class="stat-label">Uptime Reliability</div>
                </div>
                <div class="stat">
                    <div class="stat-number">1000+</div>
                    <div class="stat-label">Institusi Terdaftar</div>
                </div>
                <div class="stat">
                    <div class="stat-number">50K+</div>
                    <div class="stat-label">Pengguna Aktif</div>
                </div>
                <div class="stat">
                    <div class="stat-number">1M+</div>
                    <div class="stat-label">Ujian Terselesaikan</div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta" id="about">
            <h2>Siap untuk Memulai?</h2>
            <p>Bergabunglah dengan ribuan institusi pendidikan yang telah mempercayai SIPENA.</p>
            @if (Route::has('login'))
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary" style="display: inline-flex; background: white; color: var(--primary);">
                        <i class="fas fa-arrow-right"></i> Ke Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary" style="display: inline-flex; background: white; color: var(--primary);">
                        <i class="fas fa-sign-in-alt"></i> Masuk Sekarang
                    </a>
                @endauth
            @endif
        </section>
    </main>

    <!-- Footer -->
    <footer>
        <p>&copy; 2026 SIPENA - Sistem Informasi Pendidikan Nasional. Semua hak dilindungi.</p>
    </footer>
</body>
</html>
