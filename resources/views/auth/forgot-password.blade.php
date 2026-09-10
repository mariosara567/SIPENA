<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lupa Kata Sandi - My Asesmen</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #F8F9FA;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        /* ── Card ── */
        .forgot-card {
            width: 100%;
            max-width: 560px;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 8px 40px rgba(0, 0, 0, 0.06);
            padding: 44px 36px 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        /* Illustration */
        .forgot-illustration {
            margin-bottom: 20px;
        }

        .forgot-illustration img {
            max-width: 240px;
            max-height: 200px;
            object-fit: contain;
        }

        /* Placeholder when no image yet */
        .illustration-placeholder {
            width: 260px;
            height: 200px;
            background: linear-gradient(135deg, #dbeafe 0%, #e8f0fe 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #93b4e4;
            font-size: 13px;
            font-weight: 500;
        }

        /* Title */
        .forgot-title {
            font-size: 22px;
            font-weight: 800;
            color: #0066D4;
            margin-bottom: 10px;
        }

        /* Description */
        .forgot-description {
            font-size: 14px;
            color: #64748b;
            line-height: 1.7;
            max-width: 420px;
            margin-bottom: 28px;
        }

        /* Button */
        .btn-back {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 48px;
            padding: 0 36px;
            border: none;
            border-radius: 8px;
            background: #0066D4;
            color: #ffffff;
            font-size: 16px;
            font-weight: 700;
            font-family: inherit;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background: #004c9e;
            box-shadow: 0 4px 16px rgba(0, 102, 212, 0.3);
            transform: translateY(-1px);
        }

        .btn-back:active {
            transform: translateY(0);
        }

        /* ── Footer ── */
        .forgot-footer {
            margin-top: 32px;
            text-align: center;
            font-size: 13px;
            color: #64748b;
        }

        .forgot-footer strong {
            color: #1e293b;
            font-weight: 700;
        }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .forgot-card {
                padding: 36px 24px;
            }

            .forgot-illustration img {
                max-width: 200px;
            }
        }
    </style>
</head>
<body>

    <div class="forgot-card">
        {{-- Illustration --}}
        <div class="forgot-illustration">
            @if(file_exists(public_path('images/auth/forgot-illustration.png')))
                <img src="{{ asset('images/auth/forgot-illustration.png') }}" alt="Lupa Kata Sandi">
            @elseif(file_exists(public_path('images/auth/forgot-illustration.svg')))
                <img src="{{ asset('images/auth/forgot-illustration.svg') }}" alt="Lupa Kata Sandi">
            @else
                <div class="illustration-placeholder">
                    <span>Letakkan gambar di<br>public/images/auth/forgot-illustration.png</span>
                </div>
            @endif
        </div>

        <h1 class="forgot-title">Lupa Kata Sandi?</h1>
        <p class="forgot-description">
            Silakan hubungi Admin sekolah untuk melakukan reset password akun Anda.
        </p>

        <a href="{{ route('login') }}" class="btn-back">Kembali ke Login</a>
    </div>

    <footer class="forgot-footer">
        &copy; {{ date('Y') }} MyAsesmen. Dikembangkan oleh <strong>Yohanes Alvons</strong> &amp; <strong>Mario Hafner</strong>
    </footer>

</body>
</html>
