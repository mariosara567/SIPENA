<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk - My Asesmen</title>
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

        /* ── Login Card ── */
        .login-card {
            width: 100%;
            max-width: 800px; /* Slight increase to comfortably fit new layout */
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 8px 40px rgba(0, 0, 0, 0.06);
            display: grid;
            grid-template-columns: 1fr 1fr;
            overflow: hidden;
        }

        /* ── Left Side (Illustration) ── */
        .login-illustration {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: #f0f6ff;
            position: relative;
        }

        .login-illustration img {
            width: 100%;
            max-width: 320px;
            object-fit: contain;
            position: relative;
            z-index: 10;
        }

        /* Placeholder when no image yet */
        .illustration-placeholder {
            width: 100%;
            height: 260px;
            background: linear-gradient(135deg, #dbeafe 0%, #e8f0fe 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #93b4e4;
            font-size: 13px;
            font-weight: 500;
            text-align: center;
            line-height: 1.6;
        }

        /* ── Right Side (Form) ── */
        .login-form-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 32px 24px;
        }

        /* Logo */
        .login-logo {
            margin-bottom: 12px;
            text-align: center;
        }

        .login-logo img {
            height: 48px;
            object-fit: contain;
        }

        .login-logo-text {
            font-size: 28px;
            font-weight: 800;
            line-height: 1.1;
        }

        .login-logo-text .my {
            color: #DC2626;
            font-style: italic;
        }

        .login-logo-text .asesmen {
            color: #0066D4;
            font-style: italic;
        }

        /* Form Title */
        .login-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .login-title {
            font-size: 28px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 6px;
            letter-spacing: -0.5px;
        }
        
        .login-desc {
            font-size: 14px;
            color: #64748b;
        }

        /* Form */
        .login-form {
            width: 100%;
            max-width: 320px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group label {
            font-size: 13px;
            font-weight: 600;
            color: #475569;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: #f1f5f9;
            transition: all 0.2s ease;
            overflow: hidden;
        }

        .input-wrapper:focus-within {
            border-color: #0066D4;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(0, 102, 212, 0.1);
        }

        /* Prevent Chrome autofill from breaking border radius */
        .input-wrapper input:-webkit-autofill {
            border-radius: 8px;
        }

        .input-wrapper input {
            width: 100%;
            height: 48px;
            border: none;
            background: transparent;
            padding: 0 16px;
            font-size: 14px;
            font-family: inherit;
            color: #1e293b;
            outline: none;
        }

        .input-wrapper input::placeholder {
            color: #94a3b8;
        }

        .input-wrapper input[type="password"] {
            padding-right: 44px;
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s ease;
        }

        .toggle-password:hover {
            color: #1e293b;
        }

        /* Options Row */
        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13px;
            margin-top: -4px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            color: #475569;
            font-weight: 500;
        }

        .remember-me input {
            width: 16px;
            height: 16px;
            border-radius: 4px;
            border: 1px solid #cbd5e1;
            accent-color: #0066D4;
            cursor: pointer;
        }

        .forgot-password-link {
            color: #0066D4;
            text-decoration: none;
            font-weight: 600;
            transition: opacity 0.2s;
        }

        .forgot-password-link:hover {
            opacity: 0.8;
            text-decoration: underline;
        }

        /* Submit button */
        .btn-login {
            width: 100%;
            height: 48px;
            border: none;
            border-radius: 12px;
            background: #0066D4;
            color: #ffffff;
            font-size: 15px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 4px;
        }

        .btn-login:hover {
            background: #004c9e;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 102, 212, 0.25);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        /* Error Alert */
        .login-error {
            width: 100%;
            max-width: 320px;
            padding: 12px 14px;
            border-radius: 8px;
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
            font-size: 13px;
            margin-bottom: 16px;
            text-align: center;
        }

        /* ── Footer ── */
        .login-footer {
            margin-top: 36px;
            text-align: center;
            font-size: 13px;
            color: #64748b;
        }

        .login-footer strong {
            color: #1e293b;
            font-weight: 700;
        }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .login-card {
                grid-template-columns: 1fr;
                max-width: 400px;
            }

            .login-illustration {
                display: none;
            }

            .login-form-section {
                padding: 40px 24px;
            }
        }
    </style>
</head>
<body>

    <div class="login-card">
        {{-- Left: Illustration --}}
        <div class="login-illustration">
            @if(file_exists(public_path('images/auth/login-illustration.png')))
                <img src="{{ asset('images/auth/login-illustration.png') }}" alt="Login Illustration">
            @elseif(file_exists(public_path('images/auth/login-illustration.svg')))
                <img src="{{ asset('images/auth/login-illustration.svg') }}" alt="Login Illustration">
            @else
                <div class="illustration-placeholder">
                    <span>Letakkan gambar di<br>public/images/auth/login-illustration.png</span>
                </div>
            @endif
        </div>

        {{-- Right: Login Form --}}
        <div class="login-form-section">
            {{-- Logo --}}
            <div class="login-logo">
                @if(file_exists(public_path('images/auth/logo.png')))
                    <img src="{{ asset('images/auth/logo.png') }}" alt="My Asesmen">
                @elseif(file_exists(public_path('images/auth/logo.svg')))
                    <img src="{{ asset('images/auth/logo.svg') }}" alt="My Asesmen">
                @else
                    <div class="login-logo-text">
                        <span class="my">MY</span> <span class="asesmen">Asesmen</span>
                    </div>
                @endif
            </div>

            <div class="login-header">
                <h1 class="login-title">Selamat Datang</h1>
                <p class="login-desc">Masuk ke akun Anda untuk melanjutkan</p>
            </div>

            @if ($errors->any())
                <div class="login-error">{{ $errors->first() }}</div>
            @endif

            <form class="login-form" method="POST" action="{{ route('login.store') }}">
                @csrf

                <div class="form-group">
                    <label>Username</label>
                    <div class="input-wrapper">
                        <input
                            type="text"
                            name="username"
                            placeholder="Masukkan username Anda"
                            value="{{ old('username') }}"
                            autocomplete="username"
                            autofocus
                            required
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrapper">
                        <input
                            type="password"
                            name="password"
                            id="password-input"
                            placeholder="Masukkan kata sandi"
                            autocomplete="current-password"
                            required
                        >
                        <button type="button" class="toggle-password" onclick="togglePasswordVisibility()">
                            <!-- Eye icon initially -->
                            <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            <!-- Eye Off icon (hidden initially) -->
                            <svg id="eye-off-icon" style="display:none;" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
                                <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
                                <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
                                <line x1="2" x2="22" y1="2" y2="22"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember">
                        <span>Ingat saya</span>
                    </label>
                    <a href="{{ route('password.forgot') }}" class="forgot-password-link">Lupa Password?</a>
                </div>

                <button type="submit" class="btn-login">Masuk</button>
            </form>
        </div>
    </div>

    <footer class="login-footer">
        &copy; {{ date('Y') }} MyAsesmen. Dikembangkan oleh <strong>Yohanes Alvons</strong> &amp; <strong>Mario Hafner</strong>
    </footer>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password-input');
            const eyeIcon = document.getElementById('eye-icon');
            const eyeOffIcon = document.getElementById('eye-off-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.style.display = 'none';
                eyeOffIcon.style.display = 'block';
            } else {
                passwordInput.type = 'password';
                eyeIcon.style.display = 'block';
                eyeOffIcon.style.display = 'none';
            }
        }
    </script>
</body>
</html>
