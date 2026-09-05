<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk - SIPENA</title>
    <style>
        :root { font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; background: #eef5f4; color: #102033; }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 28px; background: linear-gradient(135deg, #eef5f4, #f7fbff 56%, #eaf4ff); }
        .login { width: min(100%, 940px); display: grid; grid-template-columns: 0.95fr 1.05fr; border: 1px solid #d9e5ea; border-radius: 8px; background: white; box-shadow: 0 22px 60px rgba(15, 23, 42, 0.1); overflow: hidden; }
        .intro, .form { padding: clamp(26px, 5vw, 46px); }
        .intro { background: #0f766e; color: white; display: flex; flex-direction: column; justify-content: space-between; min-height: 520px; }
        .brand { display: flex; align-items: center; gap: 12px; font-weight: 800; }
        .mark { width: 40px; height: 40px; display: grid; place-items: center; border-radius: 8px; background: rgba(255, 255, 255, 0.16); }
        h1 { margin: 34px 0 14px; font-size: clamp(34px, 5vw, 52px); line-height: 1.05; letter-spacing: 0; }
        .intro p, .hint { line-height: 1.65; color: rgba(255, 255, 255, 0.78); }
        h2 { margin: 0; font-size: 28px; }
        .form { display: flex; flex-direction: column; justify-content: center; }
        form { display: grid; gap: 16px; margin-top: 28px; }
        label { display: grid; gap: 8px; color: #334155; font-weight: 700; font-size: 14px; }
        input { width: 100%; min-height: 46px; border: 1px solid #cbd5e1; border-radius: 7px; padding: 0 14px; color: #0f172a; outline: none; }
        input:focus { border-color: #0f766e; box-shadow: 0 0 0 4px rgba(15, 118, 110, 0.13); }
        .remember { display: flex; align-items: center; gap: 10px; color: #475569; font-weight: 600; }
        .remember input { width: 17px; min-height: 17px; }
        .error { padding: 12px 14px; border-radius: 7px; background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; font-size: 14px; }
        .btn { min-height: 46px; border: 0; border-radius: 7px; background: #0f766e; color: white; font-weight: 800; cursor: pointer; }
        .back { display: inline-flex; margin-top: 18px; color: #0f766e; font-weight: 700; text-decoration: none; }
        .demo { margin-top: 22px; padding: 14px; border-radius: 8px; background: #f8fafc; color: #475569; font-size: 14px; line-height: 1.6; }
        @media (max-width: 780px) { .login { grid-template-columns: 1fr; } .intro { min-height: auto; } }
    </style>
</head>
<body>
    <main class="login">
        <section class="intro">
            <div>
                <div class="brand"><div class="mark">S</div><span>SIPENA</span></div>
                <h1>Masuk ke server ujian lokal.</h1>
                <p>Gunakan akun yang dibuat administrator sekolah. Seluruh sesi berjalan di jaringan lokal, jadi siswa tidak membutuhkan koneksi internet.</p>
            </div>
            <div class="hint">Mode: {{ $role ? ucfirst($role) : 'Pengguna SIPENA' }}</div>
        </section>

        <section class="form">
            <h2>Masuk</h2>
            <p class="demo">Akun demo: admin/password, guru1/password, siswa1/password (username/password).</p>

            @if ($errors->any())
                <div class="error">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login.store') }}">
                @csrf
                <label>Username <input name="username" value="{{ old('username') }}" autocomplete="username" autofocus required></label>
                <label>Password <input name="password" type="password" autocomplete="current-password" required></label>
                <label class="remember"><input name="remember" type="checkbox" value="1"> Ingat sesi masuk</label>
                <button class="btn btn-primary" type="submit">Masuk</button>
            </form>

            <a class="back" href="{{ route('home') }}">Kembali ke beranda</a>
        </section>
    </main>
</body>
</html>
